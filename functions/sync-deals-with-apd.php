<?php 

/**
 * Sync deals with APD website
 * 
 * Requires Woocommerce and ACF plugins.
 */

 class VST_SyncDealsWithAPD {

    public static $version = '0.1';
    public static $secret_key = 'vstbuzz_apd_secret_key';
    
    function __construct() {
        add_action( 'rest_api_init', function () {
            register_rest_route( 'vstbuzz/v1', '/deals', array(
              'methods' => 'GET',
              'callback' => array( $this, 'rest_api_send_current_deals' ),
            ) );
          } );

        add_action( 'rest_api_init', function () {
            register_rest_route( 'vstbuzz/v1', '/boxes', array(
                'methods' => 'GET',
                'callback' => array( $this, 'rest_api_send_current_boxes' ),
            ) );
        } );
    }

    public function get_current_deals() {

        $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));
        $post_id = 77;

        $deals = array();

        // Set up WP_Query that fetches single post with ID = $post_id
        $query = new WP_Query( array(
            'p' => $post_id,
            'post_type' => 'page',
        ) );

        if ( $query->have_posts() ) {

            // Gather data from ACF fields attached to the post
            $query->the_post();

            // check if the flexible content field has rows of data
            if ( have_rows('content_blocks') ) {

                while ( have_rows('content_blocks') ) {
                    the_row(); 
                    
                    if ( get_row_layout() == 'box_list' ) {
                        $boxes = get_sub_field('boxes');

                        foreach ( $boxes as $box ) {
                            $box_type = $box['box_type'];

                            if ( $box_type == 'Deal' ) {
                                $deal_id = $box['deal'];

                                $deal_product = new WC_product($deal_id);
                                $product_prices = vstbuzz_get_product_prices( $deal_product );

                                /**
                                 * @var number $sale_price
                                 * @var number $regular_price
                                 * @var number $save_price
                                 */
                                extract($product_prices);

                                if ( $sale_price == 0 ) {
                                    $bundled_items = $wpdb->get_results("select * from {$wpdb->prefix}woocommerce_bundled_items where bundle_id = $deal_id and menu_order = 0");
                                    foreach ( $bundled_items as $bundled_item ) {
                                        $instance = new WC_product($deal_id);
                                        $first_bundled_product = wc_get_product($bundled_item->product_id);
                                        $sale_price = $first_bundled_product->get_sale_price();
                                        if ( ! $sale_price ) {
                                            $sale_price = $first_bundled_product->get_attribute( 'sale_price' );
                                        }
                                        $save_price = $instance->get_regular_price() - $first_bundled_product->get_sale_price();
                                    }
                                }

                                $deal_data = array(
                                    'link'                   => get_permalink($deal_id),
                                    'off'                    => get_field('content_tab_off', $deal_id),
                                    'item_title'             => get_field('content_tab_by', $deal_id),
                                    'sale_price_dates_from'  => get_post_meta($deal_id, '_sale_price_dates_from', true),
                                    'sale_price_dates_to'    => get_post_meta($deal_id, '_sale_price_dates_to', true),
                                    'image'                  => get_the_post_thumbnail_url($deal_id, 'medium'),
                                    'save_price'             => $save_price,
                                    'regular_price'          => $regular_price,
                                    'sale_price'             => $sale_price,
                                    'sales_amount'           => $this->get_cached_product_sales_for_period( $deal_id, $start_date )
                                );

                                $deals[] = $deal_data;
                            }
                        }
                    }
                }
            }


            // Finished processing ACF fields, reset post data
            wp_reset_postdata();

            return $deals;
        }
        
    }

    /**
     * Get the contents of current boxes (which are added via ACF Flexible Content)
     * on a specific page which is currently set as a home page.
     * 
     * @return array
     */
    public function get_current_boxes() {

        global $wpdb;

        $boxes = array();

        $post_id = 552236;

        $prefix = 'content_blocks_1_deals_';

        $boxes_content_query = $wpdb->get_results(
            "SELECT * FROM {$wpdb->prefix}postmeta WHERE post_id = '" . $post_id . "' AND meta_key LIKE '$prefix%'", 
            ARRAY_A
        );

        // e.g. 2025-05-16 09:01:00
        // TODO figure out the timezone
        $next_deal_time = $wpdb->get_var(
            "SELECT meta_value FROM {$wpdb->prefix}postmeta WHERE post_id = '" . $post_id . "' AND meta_key = 'content_blocks_1_next_deal' "
        );

        $next_deal_timestamp = strtotime($next_deal_time);

        $start_date = date('Y-m-d 00:00:00', strtotime('-7 days'));

        foreach ( $boxes_content_query as $box_data ) {
            
            $chunks = explode('_',str_replace( $prefix, '', $box_data['meta_key']));

            $box_number = intval($chunks[0]);

            $deal_id = $box_data['meta_value'];

            $deal_product = new WC_product($deal_id);
            $product_prices = vstbuzz_get_product_prices( $deal_product );

            /**
             * @var number $sale_price
             * @var number $regular_price
             * @var number $save_price
             */
            extract($product_prices);
            
            $box_data = array(
                'link'                   => get_permalink($deal_id),
                'off'                    => get_field('content_tab_off', $deal_id),
                'item_title'             => get_field('content_tab_by', $deal_id),
                'sale_price_dates_from'  => get_post_meta($deal_id, '_sale_price_dates_from', true),
                'sale_price_dates_to'    => get_post_meta($deal_id, '_sale_price_dates_to', true),
                'image'                  => get_the_post_thumbnail_url($deal_id, 'medium'),
                'save_price'             => $save_price,
                'regular_price'          => $regular_price,
                'sale_price'             => $sale_price,
                'sales_amount'           => $this->get_cached_product_sales_for_period( $deal_id, $start_date )
            );

            $boxes[$box_number] = $box_data;
        }

        $boxes['next_deal_date'] = $next_deal_timestamp;
                 
        return $boxes;
        
        
    }

    public static function get_cached_product_sales_for_period( $product_id, $start_date ) {
        $cache_key = 'product_sales_' . $product_id . '_' . $start_date;
        $cached_data = get_transient($cache_key);
    
        if ($cached_data !== false) {
          return $cached_data;
        }
    
        $sales_amount = self::get_product_sales_for_period($product_id, $start_date);
    
        set_transient($cache_key, $sales_amount, 60 * 60 * 24);
    
        return $sales_amount;
      }
      
    
      /**
       * Get the number of sales for a product in a given period (from $start_date to today)
       * 
       * @param int $product_id The ID of the product
       * @param string $start_date The start date of the period
       * @return int The number of sales
       */
      public static function get_product_sales_for_period( $product_id, $start_date ) {
        global $wpdb;
        $wp = $wpdb->prefix;
    
        $end_date = date('Y-m-d 23:59:59');
    
        $query_sql = "SELECT 
          p.ID AS order_id,
          p.post_date AS order_date,
          oi.order_item_name AS product_name,
          oim3.meta_value AS line_total
          FROM 
              {$wp}posts AS p
          JOIN 
              {$wp}woocommerce_order_items AS oi ON p.ID = oi.order_id
          JOIN 
              {$wp}woocommerce_order_itemmeta AS oim1 ON oi.order_item_id = oim1.order_item_id AND oim1.meta_key = '_product_id'
          JOIN 
              {$wp}woocommerce_order_itemmeta AS oim3 ON oi.order_item_id = oim3.order_item_id AND oim3.meta_key = '_line_total'
          WHERE 
              p.post_type = 'shop_order'
              AND p.post_status = 'wc-completed'
              AND oim1.meta_value = $product_id
              AND p.post_date BETWEEN '$start_date' AND '$end_date'
          ORDER BY p.post_date ASC";
    
        $results = $wpdb->get_results($query_sql, ARRAY_A);
    
        $total_sales_amount = 0;
    
        foreach ($results as $result) {
          $total_sales_amount += $result['line_total'];
        }
    
        return $total_sales_amount; 
      }

    public function rest_api_send_current_deals( WP_REST_Request $request ) {
        $key = $request->get_param( 'send_deals_to_apd' );

        if ( $key === self::$secret_key ) {
            $vstbuzz_deals = $this->get_current_deals();

           return $vstbuzz_deals;
        }
    
        return new WP_Error( 'no_key', 'Invalid key', array( 'status' => 404 ) );
    }


    public function rest_api_send_current_boxes( WP_REST_Request $request ) {
        $key = $request->get_param( 'send_deals_to_apd' );

        if ( $key === self::$secret_key ) {
            $vstbuzz_boxes = $this->get_current_boxes();

           return $vstbuzz_boxes;
        }
    
        return new WP_Error( 'no_key', 'Invalid key', array( 'status' => 404 ) );
    }
 }


 $sync_apd = new VST_SyncDealsWithAPD();