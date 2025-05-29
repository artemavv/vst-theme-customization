<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;
global $position_bundle;
global $currencyr;
$position_bundle = "none";
$use_cache       = false;
$refresh_cache   = false;

$deal_id   = get_the_ID();
$live_deal = vstbuzz_is_product_live( $deal_id );

if ( $use_cache ) {
	$cache_dir = WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id";
	if ( ! file_exists( $cache_dir ) ) {
		mkdir( $cache_dir, 0755, true );
	}

	$caching_key     = "vst_cache_deal_part-id-$deal_id-live-$live_deal";
	$cache_timestamp = get_option( $caching_key );

	if ( ! $cache_timestamp ) {
		$refresh_cache = true;
		update_option( $caching_key, current_time( 'timestamp' ), false );
	}
}

$points_value = WC_Points_Rewards_Manager::get_users_points_value( get_current_user_id() );
//echo "POINTS VALUE: $points_value";
$points_total = WC_Points_Rewards_Manager::get_users_points( get_current_user_id() );

//$user_cart =  WC()->cart->get_cart();

// holds checks for all products in cart to see if they're in our category
$category_checks = array();
$small_cart      = array();

// check each cart item for our category
$user_cart = WC()->cart->get_cart();
foreach ( $user_cart as $cart_item_key => $cart_item ) {

	$_product       = $cart_item['data'];
	$product_in_cat = has_term( 'the-vault', 'product_cat', $_product->id );

	$pr           = array( 'product_id' => $_product->id, 'can_spend_points' => $product_in_cat, 'price' => $_product->get_price() );
	$small_cart[] = $pr;

}

$user_data = array(
	'user_id'      => get_current_user_id(),
	'points_total' => $points_total,
	'points_value' => $points_value,
	'cart'         => $small_cart
);
woocommerce_template_single_title();

$product_basic  = get_basic_product_info( $deal_id );
$exchange_rates = $currencyr->get_rates();
?>
<script>
  var rewards_data_product = <?php echo wp_json_encode( $product_basic ); ?>;
  var rewards_data_user = <?php echo wp_json_encode( $user_data ); ?>;
  var exchange_rates = <?php echo wp_json_encode( $exchange_rates ); ?>;

  function mask_price_with_reward_points() {
    var user_points_value = parseFloat(rewards_data_user.points_value);
    var user_id = rewards_data_user.user_id;
    var product_price = parseFloat(rewards_data_product.price);
    var can_spend = rewards_data_product.can_spend_points;
    var cart = rewards_data_user.cart;
    var reward_spent = 0.0;
    var current_product_rewards_value = 0.0;
    for (var i = 0; i < cart.length; i++) {
      var cart_item = cart[i];
      if (cart_item.can_spend_points) {
        cart_item.reserved_points_value = Math.min(cart_item.price, (user_points_value - reward_spent));
        cart_item.reserved_points_value = cart_item.reserved_points_value > 0 ? cart_item.reserved_points_value : 0;
        if (rewards_data_product.product_id == cart_item.product_id) {
          current_product_rewards_value = cart_item.reserved_points_value;
        }
        reward_spent += parseFloat(cart_item.price);
        // console.log(cart_item);
      }
    }
    if (user_id > 0 && can_spend) {
      var available_points = user_points_value - reward_spent;
      available_points = available_points >= 0 ? available_points : 0;
      var points_available_for_current_product = Math.max(current_product_rewards_value, available_points);
      // console.log("available_points ", available_points);
      if (points_available_for_current_product >= product_price) {
        jQuery('.price').html('FREE*');
        jQuery('.alt-currencies').hide();
        var additional_explanation = "* Use " + product_price * 10 + " Reward Points on" + " checkout. Regular price €" + product_price + ".";
        //Hide Earn Reward Points message
        jQuery('.wc-points-rewards-product-message').hide();
      } else if (points_available_for_current_product == 0) {
        if (user_points_value == 0) {
          var additional_explanation = "* You don't have enough Reward Points to get a discount on this product. Collect them after every completed purchase.";
        } else {
          var additional_explanation = "* You don't have enough Reward Points to get a discount on this product. All of your Reward Points are 'reserved' by the products already in the cart.";
        }
      } else {
        // Calculate lowest possible price with points
        var lowest_price = product_price - points_available_for_current_product;
        jQuery('.price').html("€" + lowest_price.toFixed(2) + '*');
        var additional_explanation = "* Use " + points_available_for_current_product * 10 + " Reward Points at checkout. Regular price €" + product_price + ".";
        //Update currency conversion
        // "34 USD<span> || </span>26 GBP"
        var alt_currencies_text = Math.round(exchange_rates['USD'] * lowest_price) + ' USD<span> || </span>' + Math.round(exchange_rates['GBP'] * lowest_price) + ' GBP';
        jQuery('.alt-currencies .num').html(alt_currencies_text);
        //Update Earn rewards program message.
        var earnPointsText = "Purchase this product now and earn <strong>" + (lowest_price / 2).toFixed() + "</strong> Reward Points!";
        jQuery('.wc-points-rewards-product-message').html(earnPointsText);
      }
      jQuery('.rewards-explanation').html(additional_explanation);
    }
  }
</script>

<?php

if ( post_password_required() ) {
	echo get_the_password_form();
	return;
}
?>

<article id="product-<?php the_ID(); ?>" <?php wc_product_class( '', $product ); ?>>
	<?php
	$show_all_parts = true;
	// check if the flexible content field has rows of data
	if ( have_rows( 'content_blocks' ) ):
		$block_global_counter = 0;
		$block_counter        = array(
			'intro'         => 0,
			'full_width'    => 0,
			'2_cols'        => 0,
			'buy_now_strip' => 0,
			'recap'         => 0,
			'testimonials'  => 0
		);
		$product_countdown = 1;
		// loop through the rows of data
	  reset_rows();
		while ( have_rows( 'content_blocks' ) ) : the_row(); ?>
			<?php if ( get_row_layout() == 'intro' ): ?>
				<?php
				$block_counter['intro'] ++;
				$block_global_counter ++;
				/**
				 * woocommerce_before_single_product_summary hook
				 *
				 * @hooked woocommerce_show_product_sale_flash - 10
				 * @hooked woocommerce_show_product_images - 20
				 */
				// do_action( 'woocommerce_before_single_product_summary' );

				// Get the top section
				if ( $product->product_type == "bundle" ) {
					$position_bundle = "top";
				}
				//do_action( 'woocommerce_single_product_summary' );

        comments_template();

        elseif ( get_row_layout() == 'full_width' ):
				$block_counter['full_width'] ++;
				$block_global_counter ++;
				if ( $show_all_parts ) {
					deal_full_width();
				} else {
					$part_name = 'full_width';
					deal_part_placeholder( $part_name, $block_counter[ $part_name ], $block_global_counter );

					if ( $use_cache && $refresh_cache ) {
						ob_start();
						deal_full_width();
						$cached_content = ob_get_clean();
						file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_" . $block_counter[ $part_name ] . "_0", $cached_content );
					}
				} elseif ( get_row_layout() == '2_cols' ):
				$block_counter['2_cols'] ++;
				$block_global_counter ++;
				if ( $show_all_parts ) {
					deal_two_cols();
				} else {
					$part_name = '2_cols';
					deal_part_placeholder( $part_name, $block_counter[ $part_name ], $block_global_counter );
					if ( $use_cache && $refresh_cache ) {
						ob_start();
						deal_two_cols();
						$cached_content = ob_get_clean();
						file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_" . $block_counter[ $part_name ] . "_0", $cached_content );
					}
				} elseif ( get_row_layout() == 'buy_now_strip' ):
			  $block_counter['buy_now_strip'] ++;
			  $block_global_counter ++;
			  $product_countdown ++;
			  if ( $show_all_parts ) {
          if ( is_product() && has_term( 'competitions', 'product_cat' ) ) {
            // buy now deal design for raffle competitions
            deal_buy_now_strip_competition($product_countdown);
          } else {
            // default design buy now deal
            deal_buy_now_strip($product_countdown);
          }
			  } else {
				  $part_name = 'buy_now_strip';
				  deal_part_placeholder( $part_name, $block_counter[ $part_name ], $block_global_counter, $product_countdown );
				  if ( $use_cache && $refresh_cache ) {
					  ob_start();
					  deal_buy_now_strip( $product_countdown );
					  $cached_content = ob_get_clean();
					  file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_" . $block_counter[ $part_name ] . '_' . $product_countdown, $cached_content );
				  }
			  } elseif ( get_row_layout() == 'recap' ):
				$block_counter['recap'] ++;
				$block_global_counter ++;
				if ( $show_all_parts ) {
					deal_recap();
				} else {
					$part_name = 'recap';
					deal_part_placeholder( $part_name, $block_counter[ $part_name ], $block_global_counter );
					if ( $use_cache && $refresh_cache ) {
						ob_start();
						deal_recap();
						$cached_content = ob_get_clean();
						file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_" . $block_counter[ $part_name ] . "_0", $cached_content );
					}
				} elseif ( get_row_layout() == 'testimonials' ):
				$block_counter['testimonials'] ++;
				$block_global_counter ++;
				if ( $show_all_parts ) {
					deal_testimonials();
				} else {
					$part_name = 'testimonials';
					deal_part_placeholder( $part_name, $block_counter[ $part_name ], $block_global_counter );

					if ( $use_cache && $refresh_cache ) {
						ob_start();
						deal_testimonials();
						$cached_content = ob_get_clean();
						file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_" . $block_counter[ $part_name ] . "_0", $cached_content );
					}
				}
        endif;
      endwhile;
	endif;

// To enable, first uncomment include_once( 'includes/vstbuzz-get-coupon.php' ); in functions.php
// echo vstbuzz_get_coupon($live_deal);

	if ( !$show_all_parts ) {
		$part_name = 'facebook_comments';
		deal_part_placeholder( $part_name, 1, $block_global_counter );

		if ( $use_cache && $refresh_cache ) {
			ob_start();
			$cached_content = ob_get_clean();
			file_put_contents( WP_CONTENT_DIR . "/vst_cache/deal_part/$deal_id/$part_name" . "_1_0", $cached_content );
		}
	}
	?>

	<?php do_action( 'woocommerce_after_single_product_summary' ); ?>

  <meta itemprop="url" content="<?php the_permalink(); ?>"/>


</article><!-- #product-<?php the_ID(); ?> -->

<?php do_action( 'woocommerce_after_single_product' ); ?>

<script type="text/javascript">

  var ajax_url = '<?php echo admin_url( 'admin-ajax.php' ); ?>';
  var current_post_id = '<?php echo get_the_ID(); ?>';
  var use_cache = '<?php echo $use_cache; ?>';


  /*
  Custom function to check if element is visible
   */
  jQuery.fn.isInViewport = function () {
    var elementTop = jQuery(this).offset().top;
    var elementBottom = elementTop + jQuery(this).outerHeight();

    var viewportTop = jQuery(window).scrollTop();
    var viewportBottom = viewportTop + jQuery(window).height();

    return elementBottom > viewportTop && elementTop < viewportBottom;

  };


  function get_deal_part(part_name, part_number, product_countdown) {
    //var url = ajax_url + '?action=get_deal_part&part_id='+part_id;
    var selector = '#' + part_name + '_' + part_number;
    jQuery(selector).addClass('ajax-loader');

    if (use_cache) {
      var ts = (new Date()).getTime()
      var url = "/wp-content/vst_cache/deal_part/" + current_post_id + "/" + part_name + "_" + part_number + "_" + product_countdown + "?" + ts;
      var data = {};
    } else {
      var url = ajax_url;
      var data = {
        action: 'get_deal_part', part_name: part_name, part_number: part_number, product_id: current_post_id, product_countdown: product_countdown <?php if ( isset( $_GET['testmode'] ) ) {
			  echo ", testmode:true";
		  } ?>};
    }
    jQuery.ajax({
      url: url, data: data, type: 'GET', success: function (data) {

        //console.log(selector);
        jQuery(selector).html(data);
        jQuery(selector).removeClass('deal_placeholder');
        jQuery(selector).data('status', 'done');
        rerun_lightbox(jQuery);
        //console.log("product countdown ",product_countdown );
        if (product_countdown > 1) {
          create_flip_counter('#product_countdown_shortcode' + product_countdown);
        }
        jQuery('.myselect').trigger('change'); // Recalculate conversion rates
        // Hide expired coundown
        var dealExpired = jQuery('.game-over').length > 0;

        if (dealExpired) {
          jQuery('.small-countdown .product_countdown').hide();
          jQuery('.small-countdown h6').hide();
        }
        //Facebook comment stlying
        jQuery('.fb-comments-wrapper').css('max-width', '1200px');
        jQuery('.fb-comments-wrapper').css('margin', 'auto');
        jQuery('.facebook_comments').css('background', '#F1F1F1');

        jQuery(selector).removeClass('ajax-loader');

        mask_price_with_reward_points();
        currencySelectorAction();
        replaceAllCurrencies();
      }
    });
  }


  jQuery(window).on('resize scroll load', function () {
    jQuery('.deal_placeholder').each(function () {
      var id = jQuery(this).attr('id');
      if (jQuery(this).isInViewport()) {

        var global_part_number = jQuery(this).data('part_global_number');
        var status = jQuery(this).data('status');
        var deal_name = jQuery(this).data('deal_name');
        var deal_number = jQuery(this).data('deal_number');

        if (status == 'empty') {
          console.log('Should load data for : ' + id);
          jQuery(this).data('status', 'in-progress');

          var product_countdown = 0;
          if (deal_name == 'buy_now_strip') {
            product_countdown = jQuery(this).data('product_countdown');
            console.log("product_countdown " + product_countdown);
          }
          get_deal_part(deal_name, deal_number, product_countdown);
        }
        //console.log("Visible: " + id + "  " +global_part_number + ' status: ' + status);

      } else {
        //console.log("Not visible: " + id);
      }
    });
  });


  function check_input(rid, value) {

    var selectbox = jQuery('#' + rid);
    var parent = selectbox.closest('.bundle_form ');

    if (value != '') {
      if (!parent.find('input[name=' + value + ']').is(':checked')) {
        parent.find('input[name=' + value + ']').trigger("click");
      }
      var els = parent.find('input[name!=' + value + ']');
      jQuery.each(els, function (index, el) {
        if (jQuery(el).is(':checked')) {
          jQuery(el).trigger("click");
        }
      })
    } else {
      var els = parent.find('input.bundled_product_checkbox');
      jQuery.each(els, function (index, el) {
        if (jQuery(el).is(':checked')) {
          jQuery(el).trigger("click");
        }
      })
    }
  }

  jQuery(document).ready(function () {
    var els_sb = jQuery('.myselect');
    jQuery.each(els_sb, function (index, el) {
      var selectbox = jQuery(el);
      var val = selectbox.val();
      if (val != '') {
        var parent = selectbox.closest('.bundle_form ');
        if (!parent.find('input[name=' + val + ']').is(':checked')) {
          parent.find('input[name=' + val + ']').trigger("click");
        }
      }
    })
  })


</script>


<script>
  //Run after loading part of the page to create lightbox on images
  function rerun_lightbox(a) {
    a('a[href*=".jpg"],a[href*=".jpeg"],a[href*=".jpe"],a[href*=".jfif"],a[href*=".gif"],a[href*=".png"],a[href*=".tif"],a[href*=".tiff"],a[href*=".avi"],a[href*=".mov"],a[href*=".mpg"],a[href*=".mpeg"],a[href*=".mp4"],a[href*=".webm"],a[href*=".ogg"],a[href*=".ogv"],a[href*=".3gp"],a[href*=".m4v"],a[href*=".swf"],[rel="ilightbox"]').not('[rel^="ilightbox["]').each(function () {
      var b = a(this), c = { path: 'horizontal', infinite: 1, fullAlone: 0, show: { title: 0 }, caption: { start: 0 }, social: { start: 0 } };
      (b.parents('.ilightbox_gallery').length || b.parents('.tiled-gallery').length || b.parents('.ngg-galleryoverview').length) || b.iLightBox(c)
    });
    var b = [], d = { path: 'horizontal', infinite: 1, fullAlone: 0, show: { title: 0 }, caption: { start: 0 }, social: { start: 0 } };
    a('[rel^="ilightbox["]').each(function () {
      a.inArray(a(this).attr("rel"), b) === -1 && b.push(a(this).attr("rel"))
    });
    a.each(b, function (b, c) {
      a('[rel="' + c + '"]').iLightBox(d)
    });
    a('a[href*="youtu.be/"],a[href*="youtube.com/watch"],a[href*="vimeo.com"],a[href*="metacafe.com/watch"],a[href*="dailymotion.com/video"],a[href*="hulu.com/watch"]').not('[rel*="ilightbox"]').each(function () {
      var b = a(this), c = { path: 'horizontal', infinite: 1, smartRecognition: 1, fullAlone: 0, show: { title: 0 }, caption: { start: 0 }, social: { start: 0 } };
      (b.parents('.ilightbox_gallery').length || b.parents('.tiled-gallery').length || b.parents('.ngg-galleryoverview').length) || b.iLightBox(c)
    });
  };

  /** Create flip counter, depends on first big counter for time */
  function create_flip_counter(selector) {
    console.log("Creating flip counter ", selector);
    var dates_to = jQuery('#product_countdown_shortcode').data('dates_to');
    var now = Math.floor(Date.now() / 1000);

    var clock = jQuery(selector).FlipClock(dates_to - now, {
      clockFace: 'DailyCounter', countdown: true
    });

  }


</script>


<style>
  .deal_placeholder {
    height: 200px;
  }
</style>

<script>
  mask_price_with_reward_points();
</script>
