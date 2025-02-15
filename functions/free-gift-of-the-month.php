<?php

/**
 * Adds "Free gift of the month" feature 
 * to the VST theme
 * 
 * Requires Woocommerce plugin.
 */

class VST_FreeGifts {

    public static $version = '0.2';
    
    public const META_FIELD = '_product__is_freegift';
    
    function __construct() {
        
        add_action( 'woocommerce_after_cart_ul_list', array( $this, 'show_freegift_offers') );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts') );
        
        add_action( 'wp_ajax_claim_freegift', array( $this, 'ajax_put_freegift_in_cart') );
        add_action( 'wp_ajax_nopriv_claim_freegift', array( $this, 'ajax_put_freegift_in_cart') );
        
        add_action( 'woocommerce_order_status_processing', array( $this, 'remove_invalid_freegifts') );
        add_action( 'woocommerce_order_status_completed', array( $this, 'mark_freegifts_claimed') );
        add_action( 'wp_login', array( $this, 'remove_invalid_freegifts_from_cart'), 10, 2 );
        add_action( 'woocommerce_cart_item_removed', array( $this, 'check_to_remove_all_freegifts_from_cart'), 10, 2 );
        
        // Add & handle custom meta field to products
        add_action( 'woocommerce_product_options_general_product_data', array( $this, 'add_freegift_meta_field'), 10, 1 );
        add_action( 'woocommerce_process_product_meta', array( $this, 'save_freegift_meta_field'), 10, 1 );
        
        // TODO: get correct name for this filter 
        add_filter( 'woocommerce_vst_cart_price', array( $this, 'display_freegift_price'), 10, 2 );
        add_filter( 'woocommerce_cart_item_subtotal', array( $this, 'display_freegift_price_on_checkout'), 100, 3 );
        
        add_action( 'woocommerce_add_to_cart_validation', array( $this, 'check_if_allowed_product' ), 10, 3 );

        $freegifts_data = array(
            'ajax_url'        => admin_url( 'admin-ajax.php' ),
            'all_freegifts'    => self::get_all_freegift_product_ids()
        );

        wp_register_script( 'freegifts-frontend', get_template_directory_uri() . '/js/free-gift-of-the-month.js', array( 'jquery' ), self::$version, true );    
        wp_localize_script( 'freegifts-frontend', 'freegifts_data', $freegifts_data );
        
    }

    /**
     * Callback for 'woocommerce_product_options_general_product_data'
     * @param int $post_id
     */
    public function add_freegift_meta_field( $post_id ) {
      
        echo '<div class="options_group">';
        
        woocommerce_wp_checkbox(
            array(
                'id' => self::META_FIELD,
                'label' => __( 'Free Gift of the Month', 'woocommerce' ),
                'desc_tip' => 'true',
                'description' => __('Check this field for the free gift product', 'woocommerce' )
            ));
        
        echo '</div>';
    }
    
    
    /**
     * Callback for 'woocommerce_process_product_meta'
     * @param int $post_id
     */
    public function save_freegift_meta_field( $post_id ) {
        $is_freegift_product = isset( $_POST[self::META_FIELD] ) ? 'yes' : 'no';
        update_post_meta( $post_id, self::META_FIELD, $is_freegift_product );
    }

    /**
     * Callback for `woocommerce_add_to_cart_validation` filter-hook.
     * 
     * @param boolean $passed_validation True if the item passed validation.
     * @param integer $product_id        Product ID being validated.
     * @param integer $quantity          Quantity added to the cart.
     *
     * @return boolean
     */
    function check_if_allowed_product( $passed_validation, $product_id, $quantity ) {

        $cart = WC()->cart;
        
        $is_freegift = self::check_if_freegift( $product_id );
        
        if ( $is_freegift ) {
  
            // do not allow adding freegift products in empty carts, and for carts where are no deal products
            if ( $cart->is_empty() || ! self::check_if_deal_product_in_cart() ) {
                 $passed_validation = false;
            }
        }
        
        return $passed_validation;
      }
    
    public function enqueue_scripts() {
        wp_enqueue_script( 'freegifts-frontend' );
    }
    
    static function get_all_freegift_product_ids() {
        global $wpdb;
        $wp = $wpdb->prefix;

        $freegift_product_ids = array();

        $query_sql = "SELECT p.ID from {$wp}posts AS p "
                . " LEFT JOIN `{$wp}postmeta` AS pm on p.`ID` = pm.`post_id`"
                . " LEFT JOIN `{$wp}postmeta` AS pm_stock on p.`ID` = pm_stock.`post_id`"
                . " WHERE pm.meta_key = '" . self::META_FIELD . "' AND pm.meta_value = 'yes' "    // select only freegift products
                . " AND pm_stock.meta_key = '_stock_status' AND pm_stock.meta_value = 'instock' "  // select only products in stock 
                . " AND p.post_type = 'product' AND p.post_status = 'publish' ";

        $rows = $wpdb->get_results( $query_sql, ARRAY_A );

        foreach ( $rows as $row ) {
            $freegift_product_ids[] = $row['ID'];
        }

        return $freegift_product_ids;
    }

    static function get_all_freegift_products() {
        $products = array();
        $ids = self::get_all_freegift_product_ids();
        foreach ( $ids as $id ) {
            $products[$id] = get_product($id); 
        }

        return $products;
    }

    public function display_freegift_price( $price, $product_id ) {
        if ( $product_id && self::check_if_freegift( $product_id) ) {
            return 'Free';
        }
        return '$' . $price;
    }

    public function display_freegift_price_on_checkout( $price, $cart_item, $cart_item_key ) {
        $product_id = $cart_item['product_id'];
        if ( $product_id && self::check_if_freegift( $product_id) ) {
            return 'Free';
        }
        return $price;
    }
     
    public function show_freegift_offers() {
        
        $user_id = get_current_user_id();

        $available_freegifts = self::get_available_freegift_products( $user_id );
                
        $eligible_for_freegifts = self::check_if_deal_product_in_cart() && count($available_freegifts) > 0; 

        if ( $eligible_for_freegifts ) {

            ?>
            <li class="cart_item freegifts-header">
                <?php if ( count($available_freegifts) == 1 ) : ?>
                    <span><?php _e('You can also claim this Free Gift of the Month', 'vstbuzz' ); ?> &darr;&darr;&darr;</span>
                <?php else: ?>
                    <span><?php _e('You can also claim any of these Free Gifts of the Month', 'vstbuzz' ); ?> &darr;&darr;&darr;</span>  
                <?php endif; ?>
            </li>

            <?php

            foreach( $available_freegifts as $freegift_product ) {

                $product_id                    = $freegift_product->get_id();
                $meta_box_tech_note            = get_post_meta( $product_id, "meta-box-tech-note", true );
                $meta_box_tech_note_additional = get_post_meta( $product_id, "meta-box-tech-note-additional", true );
                $product_link                  = $freegift_product->get_permalink();

                $claim_link = sprintf( '<a href="%s" class="claim_free_gift" title="%s" aria-label="%s" data-freegift-id="%s">%s</a>',
                        "#", 
                        "Claim free gift",
                        "Claim free gift",
                        esc_attr( $product_id ),
                        __( 'GET IT', 'vstbuzz' )
            );
                ?>
                <li class="cart_item freegift-product-row">

                    <div class="cart_item__thumbnail">
                        <?php
                        
                        $thumbnail = $freegift_product->get_image();
                        if ( ! $product_link ) {
                            echo $thumbnail;
                        } else {
                            printf( '<a href="%s">%s</a>', esc_url( $product_link ), $thumbnail );
                        }
                        ?>
                    </div>

                    <div class="cart_item__name" data-title="<?php esc_attr_e( 'Free Gift', 'woocommerce' ); ?>">
                    <?php
                    if ( ! $product_link ) {
                      echo wp_kses_post( $freegift_product->get_name() . '&nbsp;' );
                    } else {
                      echo wp_kses_post( sprintf( '<a href="%s">%s</a>', $product_link, $freegift_product->get_name() ) );

                    }

                    if ( $meta_box_tech_note || $meta_box_tech_note_additional ) {
                      echo "<span class='tech-note-text'>*</span>";
                    }

                    ?>
                </div>
                <?php

                  $sym            = get_woocommerce_currency_symbol();
                  $product_prices = vstbuzz_get_product_prices( $freegift_product );

                  
                  /**
                   * @var number $sale_price
                   * @var number $regular_price
                   * @var number $save_price
                   */
                  extract($product_prices);
                  
                  if ( ! empty( $save_price ) ) {
                      $total_savings += $save_price;
                  }

                  ?>
                        <div class="cart_item__total">
                  <?php
                  
                  $cart_item_you_save      = $sym . number_format( $regular_price, 2 );

                  if ( ! empty( $cart_item_you_save ) ) {
                    $cart_item_you_save = '<span>' . __( 'You Save:', 'vstbuzz' ) . '</span>' . $cart_item_you_save;
                  }
                  
                  ?>
                  <div class="cart_item__total-savings">
                    <div class="free-gift-price" ><?php _e( 'FREE', 'vstbuzz' ); ?></div>
                    <div class="cart_item__total-savings-save"><?php echo $cart_item_you_save; ?></div>
                  </div>
                  <div class="cart_item__total-price">
                    <?php echo $claim_link; //TODO add loader gif there ?>
                  </div>
                  
                </div>
                
                <?php
                  if ( $meta_box_tech_note ) {
                    ?>
                            <div class="cart_item__tech-note">
                    <?php
                    $tech_note_text = '<span class=\'cart_item__tech-note-text tech-note__text\'>' . get_post_meta( $product_id, "meta-box-tech-note", true ) . '</span>';
                    echo "<span class='cart_item__tech-note-star'>*</span> <strong>Please Note: </strong>" . $tech_note_text;
                    ?>
                </div>
              <?php
            }

            if ( $meta_box_tech_note_additional ) {
              ?>
              <div class="cart_item__tech-note">
                <span class='cart_item__tech-note-text tech-note__text'> 
                   <?php echo $meta_box_tech_note_additional; ?>
                </span>
              </div>
              <?php } ?>
            
            </li>
            
            <?php
            }
        }


    }


    public function ajax_put_freegift_in_cart() {
        if (isset($_POST['freegift_id']) && absint($_POST['freegift_id']) > 0 ) {

            $product_id = absint($_POST['freegift_id']);
            if ( self::check_if_freegift($product_id) && ! self::check_if_freegift_in_cart($product_id) ) { // must be an freegift. and not yet in cart
                $user_id = get_current_user_id();

                if ( $user_id ) {
                    $available_freegifts = self::get_available_freegift_products( $user_id );
                    if ( array_key_exists( $product_id, $available_freegifts ) ) { // check if this freegift has been already claimed 
                        WC()->cart->add_to_cart($product_id, 1);
                        wp_send_json_success('OK');
                    }
                    else wp_send_json_error('404');
                }
                else {
                    $available_freegifts = self::get_all_freegift_product_ids();
                    if ( in_array( $product_id, $available_freegifts ) ) {
                        WC()->cart->add_to_cart($product_id, 1);
                        wp_send_json_success('OK');
                    }
                    else wp_send_json_error('404');
                }
            }
            else wp_send_json_error('404');
        }
        else wp_send_json_error('403');
    }

    static function check_if_freegift( $product_id ) {
        global $wpdb;
        $wp = $wpdb->prefix;

        $query_sql = $wpdb->prepare("SELECT p.ID from {$wp}posts AS p "
                . " LEFT JOIN `{$wp}postmeta` AS pm on p.`ID` = pm.`post_id`"
                . " LEFT JOIN `{$wp}postmeta` AS pm_stock on p.`ID` = pm_stock.`post_id`"
                . " WHERE pm.meta_key = '" . self::META_FIELD . "' AND pm.meta_value = 'yes' "    // select only freegift products
                . " AND pm_stock.meta_key = '_stock_status' AND pm_stock.meta_value = 'instock' "  // select only products in stock 
                . " AND p.post_type = 'product' AND p.post_status = 'publish' AND p.ID = %d ", $product_id);

        $result = $wpdb->get_row( $query_sql, ARRAY_A );

       
        return $result;
    }
    
    // fires on order being completed. 
    public function mark_freegifts_claimed( $order_id ) {
        $order = wc_get_order($order_id);
        $user_id = $order->get_user_id();
        $order_items = $order->get_items();
    
        foreach ( $order_items as $item ) {
            $product_id = $item['product_id'];
            if ( self::check_if_freegift( $product_id ) ) {
                self::claim_freegift( $user_id, $product_id );
            }
        }
    }
    
    // fires when a product is removed from cart 
    public function check_to_remove_all_freegifts_from_cart($cart_item_key, $cart ) {
                
        if ( ! self::check_if_deal_product_in_cart( $cart ) ) {

            $items = $cart->get_cart();    
            
            foreach( $items as $cart_item_key => $cart_item ) {
                $product_id = $cart_item['product_id'];

                if ( self::check_if_freegift( $product_id ) ) {
                    WC()->cart->remove_cart_item( $cart_item_key );
                    wc_add_notice('Free Gift of the Month has been removed from your cart since you don\'t have deal item in cart');
                }
            }
        }

    }
    
    // fires on order being processed. 
    public function remove_invalid_freegifts( $order_id ) {
        $user_id = get_current_user_id();
        
        if ( $user_id ) {

            $order = wc_get_order($order_id);

            if ( $order ) {
                $order_items = $order->get_items();

                foreach ( $order_items as $item_id => $item ) {
                    $product_id = $item['product_id'];
                    if ( self::check_if_freegift( $product_id ) ) {
                        if ( ! self::user_eligible_for_freegift( $user_id, $product_id ) ) {
                            wc_delete_order_item($item_id);
                            wc_add_notice('Free Gift of the Month has been removed from your order since you have already claimed it');
                        }
                    }
                }
            }
        }
    }
    
    // fires on user login. 
    public function remove_invalid_freegifts_from_cart( $user_login, $user ) {
        
        $user_id = $user->ID;
        
        if ( $user_id ) {

            $items = WC()->cart->get_cart();

            foreach( $items as $cart_item_key => $cart_item ) {
                $product_id = $cart_item['product_id'];
                
                if ( self::check_if_freegift( $product_id ) ) {
                    if ( ! self::user_eligible_for_freegift( $user_id, $product_id ) ) {
                        WC()->cart->remove_cart_item( $cart_item_key );
                        wc_add_notice('Free Gift of the Month has been removed from your cart since you have already claimed it');
                    }
                }
            }
        }
    }
    
    static function user_eligible_for_freegift( $user_id, $freegift_id ) {
        $user_freegifts = self::get_claimed_freegift_ids( $user_id );
        
        $is_claimed = isset($user_freegifts[$freegift_id]);
        $is_unclaimed = (isset($user_freegifts[$freegift_id]) && $user_freegifts[$freegift_id] == 0 );
        if ( ! $is_claimed || $is_unclaimed ) {
            return true;
        }
        return false;
    }
    
    // todo: should be done after successful checkout
    static function claim_freegift( $user_id, $product_id ) {
        $freegift_list = (array) get_user_meta($user_id, 'freegifts_list', true);
        $freegift_list[$product_id] = 1;
        update_user_meta($user_id, 'freegifts_list', $freegift_list);
    }

    static function unclaim_freegift( $user_id, $product_id ) {
        $freegift_list = (array) get_user_meta($user_id, 'freegifts_list', true);
        $freegift_list[$product_id] = 0;
        update_user_meta($user_id, 'freegifts_list', $freegift_list);
    }
    
    static function check_if_freegift_in_cart( $product_id ) {
        $items = WC()->cart->get_cart();
        
        foreach( $items as $cart_item ) {
            if ( $cart_item['product_id'] == $product_id ) {
                return true;
            }
        }

        return false;
    }
    
    static function check_if_deal_product_in_cart( $cart = false ) {
			
			return true;
			
        if ( ! is_a($cart, 'WC_Cart') ) {
            $cart = WC()->cart;
        }
        $items = $cart->get_cart();
        
        foreach( $items as $cart_item ) {
            if (get_post_meta($cart_item['product_id'], '_product_big_deal', true) == 'yes') {
                return true;
            }
        }
        
        return false;
    }

    static function get_claimed_freegift_ids( $user_id ) {
        $freegift_list = (array) get_user_meta($user_id, 'freegifts_list', true);
        return $freegift_list;
    }

    static function get_available_freegift_products( $user_id = 0 ) {
        $all_freegifts = self::get_all_freegift_product_ids();
        
        if ( $user_id ) {
            $user_freegifts = self::get_claimed_freegift_ids( $user_id );
        }
        else {
            $user_freegifts = array();
        }
        
        $available_freegifts = array();

        foreach ( $all_freegifts as $freegift_id ) {
            if ( ! self::check_if_freegift_in_cart($freegift_id) ) { // must be not in cart
                
                $is_claimed = isset($user_freegifts[$freegift_id]);
                $is_unclaimed = (isset($user_freegifts[$freegift_id]) && $user_freegifts[$freegift_id] == 0 );
                
                if ( ! $is_claimed || $is_unclaimed ) { 
                    $available_freegifts[$freegift_id] = get_product($freegift_id);
                }
            }
        }

        return $available_freegifts;
    }

   
}

$freegift_feature = new VST_FreeGifts();