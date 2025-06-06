<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */

global $woo_options, $woocommerce;
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>"/>
  <title><?php woo_title( '' ); ?></title>
  <meta name="facebook-domain-verification" content="5xlmjhmk2k33pj9jjwv045vyrq4lfz"/>
  <?php /*<!-- Google Tag Manager -->
  <script>(function (w, d, s, l, i) {
      w[l] = w[l] || [];
      w[l].push({
        'gtm.start': new Date().getTime(), event: 'gtm.js'
      });
      var f = d.getElementsByTagName(s)[0], j = d.createElement(s), dl = l != 'dataLayer' ? '&l=' + l : '';
      j.async = true;
      j.src = 'https://www.googletagmanager.com/gtm.js?id=' + i + dl;
      f.parentNode.insertBefore(j, f);
    })(window, document, 'script', 'dataLayer', 'GTM-54FKSMN');</script>
  <!-- End Google Tag Manager -->*/?>
	<?php woo_meta(); ?>
  <link rel="pingback" href="<?php echo esc_url( get_bloginfo( 'pingback_url' ) ); ?>"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
	<?php
	wp_head();
	woo_head();
	?>
</head>
<body <?php body_class(); ?>>

<?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>
<?php /*
<!-- Google Tag Manager (noscript) -->
<noscript>
  <iframe src="https://www.googletagmanager.com/ns.html?id=GTM-54FKSMN"
          height="0" width="0" style="display:none;visibility:hidden"></iframe>
</noscript>
<!-- End Google Tag Manager (noscript) -->*/?>
<?php woo_top(); ?>

<div id="wrapper">

	<?php woo_header_before(); ?>

  <header id="header" class="col-full">
    <div class="wrapper">
	    <?php //woo_header_inside(); ?>
      <a id="logo"  href="https://vstbuzz.com/" title="Audio Software Deals">
        <picture class="logo">
          <img src="/wp-content/themes/vstbuzz/images/logo-vstbuzz-108x46.png" alt="VSTBuzz">
        </picture>
        <picture class="logo-white">
          <img src="/wp-content/themes/vstbuzz-v2/images/VSTBuzz1_white.png" alt="VSTBuzz">
        </picture>
      </a>
      <div class="header__deals header__deals_hidden">
		  <?php if ( ! is_page( array( 'cart', 'checkout' ) ) ) { ?>
            <button class="header__deals-title">Current deals</button>
            <div class="header__deals-content">
				<?php
				$homePageID = get_option( 'page_on_front' );
				if ( have_rows( 'content_blocks', $homePageID ) ) {

					$row_number = 0;
					while ( have_rows( 'content_blocks', $homePageID ) ) {
						the_row();
						$boxes = get_sub_field( 'deals' );
            if(is_array($boxes)) {

				      $row_number++;

							if ( $row_number > 1 ) {
								continue;
							}

	            $currencySymbol = get_woocommerce_currency_symbol();
						  foreach ( $boxes as $box ) {
//							$box_type = $box['box_type'];
//							if ( $box_type == 'Deal' ) {
								$deal_id    = $box['deal'];
								$instance   = new WC_product( $deal_id );
                $product_prices = vstbuzz_get_product_prices( $instance );
                /**
                 * @var number $sale_price
                 * @var number $regular_price
                 * @var number $save_price
                 */
                extract($product_prices);

                // fix empty price for the bundled item
								if ( $sale_price == 0 ) {
									$bundled_items = $wpdb->get_results( "select * from {$wpdb->prefix}woocommerce_bundled_items where bundle_id = $deal_id and menu_order = 0" );
									foreach ( $bundled_items as $bundled_item ) {
										$first_bundled_product = wc_get_product( $bundled_item->product_id );
										$sale_price            = $first_bundled_product->get_sale_price();
										if ( ! $sale_price ) {
											$sale_price = $first_bundled_product->get_attribute( 'sale_price' );
										}
										$save_price = $regular_price - $sale_price;
									}
								}

								$content_tab_off       = get_field( 'content_tab_off', $deal_id );
								$item_title            = get_field( 'content_tab_by', $deal_id );
								$sale_price_dates_from = get_post_meta( $deal_id, '_sale_price_dates_from', true );
								$sale_price_dates_to   = get_post_meta( $deal_id, '_sale_price_dates_to', true );
								$image                 = get_the_post_thumbnail_url( $deal_id, 'medium' );

								$backgroundImage = get_field( 'content_blocks', $deal_id )[0]['background_image'];
								$link            = get_permalink( $deal_id );
								$description     = wp_trim_words( get_field( 'content_blocks', $deal_id )[0]['description'], $num_words = 30, $more = '...' );
								$timerEnabled    = vstbuzz_product_has_category( $deal_id, 'Deals' );
//							} else {
//                // disable manually added deals
//                continue;
////								$now                   = time();
////								$backgroundImage       = $box['background_image'];
////								$deal_id               = rand( 1000, 9999 );
////								$link                  = $box['link'];
////								$description           = wp_trim_words( $box['flipside_text'], $num_words = 30, $more = '...' );
////								$image                 = $box['front_image'];
////								$item_title            = $box['front_text'];
////								$sale_price_dates_from = $now;
////								$timer_end_string      = $box['timer_end'];
////								$date                  = new DateTime( $timer_end_string, new DateTimeZone( 'Europe/Dublin' ) );
////								$timer_end             = $date->format( 'U' );
////								$sale_price_dates_to   = $timer_end;
////								$timerEnabled          = ! empty( $timer_end_string );
////								$sale_price            = '';
////								$regular_price         = '';
////								$save_price            = '';
////								$content_tab_off       = '';
//							}
							$you_save_num   = $regular_price - $sale_price;
							?>
                          <div class="header__deals-item" id="product-<?php echo $deal_id; ?>">
                            <div class="header__deals-item-image-wrap">
                              <a href="<?php echo $link; ?>" class="header__deals-item-link">
                                <img src="<?php echo $image; ?>" class="header__deals-item-image" alt="<?php echo $item_title; ?>">
								  <?php if ( ! empty( $content_tab_off ) ) { ?>
                                    <span class="header__deals-item-image-off"><?php echo $content_tab_off; ?></span>
								  <?php } ?>
                              </a>
                            </div>
                            <a href="<?php echo $link; ?>" class="header__deals-item-link header__deals-item-content">
                              <h4 class="header__deals-item-title"><?php echo $item_title; ?></h4>
                              <?php if($regular_price !== '') { ?>
                              <span class="header__deals-price">
                                <span class="header__deals-price-regular price"><?php echo $currencySymbol . $regular_price; ?></span>
				                          <?php if ( $you_save_num > 0 ) { ?>
                                    <span class="header__deals-price-sale price"><?php echo $currencySymbol . $sale_price; ?></span>
		                              <?php } ?>
                              </span>
                              <?php } ?>
								<?php if ( $timerEnabled ) { ?>
                                  <span class="timer"></span>
                                  <script type="text/javascript">
                                    timer("<?php echo $sale_price_dates_from;?>", "<?php echo $sale_price_dates_to;?>", "<?php echo $deal_id; ?>");
                                  </script>
								<?php } ?>
                            </a>
                          </div>
						<?php }
            }
					}
				}
				?>
            </div>

		  <?php } ?>
      </div>

      <ul class="header__nav">
		  <?php
		  $headerNav = [
			  'vault'   => [
				  'title' => get_the_title( 73794 ),
				  'link'  => get_the_permalink( 73794 ),
				  'icon'  => 'vault.svg',
			  ],
			  'search'  => [
				  'title' => __( 'Search', 'vstbuzz' ),
				  'link'  => get_search_link(),
				  'icon'  => 'search.svg',
			  ],
			  'account' => [
				  'title' => get_the_title( 158 ),
				  'link'  => get_the_permalink( 158 ),
				  'icon'  => 'profile.svg',
			  ],
			  'Cart'    => [
				  'title' => 'CART',
				  'link'  => esc_url( $woocommerce->cart->get_cart_url() ),
				  'icon'  => 'cart.svg',
			  ],
		  ];
		  foreach ( $headerNav as $itemID => $item ) {
			  ?>
            <li class="header__nav-item">
              <a class="header__nav-item-link header__nav-item-link_<?php echo $itemID; ?>" href="<?php echo $item['link']; ?>" title="<?php echo esc_attr( $item['title'] ); ?>">
                <img class="header__nav-item-icon" src="<?php echo get_stylesheet_directory_uri() . '/images/' . $item['icon']; ?>" alt="<?php echo esc_attr( $item['title'] ); ?>">
                  <?php
                    if ( $itemID === 'Cart') {
                      echo '<span class="header__nav-item-cart-count_js"></span>';
                    }
                  ?>
              </a>
            </li>
			  <?php
		  }
		  ?>
      </ul>
      <!-- <div class="faq-link"><a href="/faq">Problems with downloads or exctracting? Click here</a></div> -->
    </div><!-- /.wrapper -->
  </header><!-- /#header -->
    <div class="header-search">
        <div class="wrapper">
          <?php get_search_form(); ?>
        </div>
    </div>

	<?php woo_content_before();
	?>
