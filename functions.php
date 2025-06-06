<?php

include_once( 'includes/helpers.php' );
include_once( 'functions/sidebars.php' );
include_once( 'functions/highwinds.php' );
include_once( 'functions/custom_firewall.php' );
include_once( 'functions/caching.php' );
include_once( 'functions/facebook-comments.php' );
include_once( 'functions/vstbuzz-optin-form.php' );
include_once( 'functions/freebies-cpt.php' );
include_once( 'woocommerce/deal-page/deal-page-parts.php' );
include_once( 'cron/user-accunt-check.php' );
include_once( 'functions/affiliates-banner.php' );
include_once( 'functions/Blogdeals_Widget.php' );
include_once( 'functions/remind-me.php' );
include_once( 'functions/paypal.php' );
include_once( 'cron/affiliate-ads-configuration.php' );
include_once( 'cron/partially.php' );
include_once( 'cron/remind-me-job.php' );
include_once( 'functions/product_software_taxonomy.php' );
include_once( 'includes/new-deals.php' );
include_once( 'includes/vstbuzz-add-to-cart.php' );
include_once( 'includes/expired-subscribe.php' );
include_once( 'includes/vstbuzz-show-prices.php' );
include_once( 'includes/vstbuzz-upsell-product-details.php' );
include_once( 'includes/vstbuzz-countdown.php' );
include_once( 'includes/vstbuzz-cart-progress.php' );
include_once( 'includes/vstbuzz-products-vault.php' );
include_once( 'includes/vstbuzz-get-product-prices.php' );
include_once( 'includes/vstbuzz-woo-redirections.php' );
//include_once( 'includes/vstbuzz-get-coupon.php' );
include_once( 'includes/rank-math-schema.php' );
include_once( 'includes/booster-buy-once-remove-sell.php');
include_once( 'includes/vstbuzz-competitions.php');
include_once( 'functions/free-gift-of-the-month.php' );
include_once( 'includes/shortcodes.php');
include_once( 'functions/sync-deals-with-apd.php' );



// Load theme stylesheets and scripts
function theme_enqueue_styles_scripts() {
	wp_dequeue_style( 'theme-stylesheet' );
	wp_deregister_style( 'theme-stylesheet' ); // Unload the child stylesheet that WP auto loads so we can version control

	wp_deregister_style( 'woocommerce-photo-reviews-style' );
	wp_deregister_style( 'woocommerce-photo-reviews-style-inline' );
//    wp_deregister_script( 'woocommerce-photo-reviews-script' );

	wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
	wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ), '20210201' );


	/* old styles -- left for backwards compatibility */
	wp_deregister_style( 'child-style' );
	wp_register_style( 'child-style', get_theme_file_uri( 'style.css' ),
		[], filemtime( get_theme_file_path( 'style.css' ) ) );
	wp_enqueue_style( 'child-style' );
	wp_register_style( 'style-2018', get_theme_file_uri( 'style-2018.css' ),
		[], filemtime( get_theme_file_path( 'style-2018.css' ) ) );
	wp_enqueue_style( 'style-2018' );

	/*-----------------------------------------------------------------------------------*/
	/* Add JS required for live price update on bundles pages
  /*-----------------------------------------------------------------------------------*/
//	if ( is_product() ) {
//		wp_enqueue_script( 'bundles', get_theme_file_uri( 'js/bundles.js' ), [ 'jquery' ], filemtime( get_theme_file_path( 'js/bundles.js' ) ), true );
//	}

  /* CSS and JS for contact page */
	if ( is_page( 'contact' ) ) {
		wp_enqueue_script( 'chosen.jquery.min', get_stylesheet_directory_uri() . '/js/chosen.jquery.min.js', [], filemtime( get_theme_file_path( 'js/chosen.jquery.min.js' ) ), true );
		wp_enqueue_style( 'chosen', get_stylesheet_directory_uri() . '/css/chosen.css', [], filemtime( get_theme_file_path( 'css/chosen.css' ) ) );
		wp_enqueue_style( 'contact.css', get_stylesheet_directory_uri() . '/css/contact.css', [], filemtime( get_theme_file_path( 'css/contact.css' ) ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Queue custom styles and JS for Freebies page */
	/* This is to get the styled select box in there, using existing Woocommerce code */
	/*-----------------------------------------------------------------------------------*/

	if ( is_page( 'freebies' ) ) {
	  wp_enqueue_script( 'chosen.jquery.min', get_stylesheet_directory_uri() . '/js/chosen.jquery.min.js', [], filemtime( get_theme_file_path( 'js/chosen.jquery.min.js' ) ), true );
	  wp_enqueue_style( 'chosen', get_stylesheet_directory_uri() . '/css/chosen.css', [], filemtime( get_theme_file_path( 'css/chosen.css' ) ) );
	}

	/*-----------------------------------------------------------------------------------*/
	/* Queue custom styles and JS for FAQ page */
	/* This is to get the show/hide toggle bit working */
	/*-----------------------------------------------------------------------------------*/

	if ( is_page( 'faq' ) ) {
		wp_enqueue_script( 'faq-1', get_stylesheet_directory_uri() . '/js/faq.js', array(), '1.0', true );
	}


	/*-----------------------------------------------------------------------------------*/
	/* Queue jQuery UI for the promotion page */
	/* Used for pricing slider */
	/*-----------------------------------------------------------------------------------*/

	if ( is_page( 81902 ) ) {
      // Enqueue jQuery UI
      wp_enqueue_script('jquery-ui-core');
      wp_enqueue_script('jquery-ui-slider');

      // Enqueue jQuery UI CSS
      wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
  }

  /* custom styles for Competitions pages */
	if (
    (is_product() && has_term( 'competitions', 'product_cat' ))
    || is_page( 'competitions' )
  ) {
	  wp_enqueue_style( 'competitions', get_stylesheet_directory_uri() . '/css/competitions.css', [], filemtime( get_theme_file_path( 'css/competitions.css' ) ) );
    add_filter('body_class', function ($classes) {
      $classes[] = 'competitions'; return $classes;
    });
  }

  /* Custom styles for new landing page */
  if ( is_page( 552236 ) ) {
      wp_enqueue_style( 'page-552236', get_stylesheet_directory_uri() . '/css/page-552236.css', [], filemtime( get_theme_file_path( 'css/page-552236.css' ) ) );
  }

	/* https://dbrekalo.github.io/simpleLightbox/ */
	wp_register_style( 'simpleLightbox', get_theme_file_uri( 'css/simpleLightbox.min.css' ),
		[], filemtime( get_theme_file_path( 'css/simpleLightbox.min.css' ) ) );
	wp_enqueue_style( 'simpleLightbox' );
	wp_register_script( 'simpleLightbox', get_theme_file_uri( 'js/simpleLightbox.min.js' ),
		[ 'jquery' ], filemtime( get_theme_file_path( 'js/simpleLightbox.min.js' ) ) );
	wp_enqueue_script( 'simpleLightbox' );

	/* simple timer */
	wp_register_script( 'timer', get_theme_file_uri( 'js/timer.js' ),
		[ 'jquery' ], filemtime( get_theme_file_path( 'js/timer.js' ) ) );
	wp_enqueue_script( 'timer' );

	/* more complicated timer: https://github.com/PButcher/flipdown */
	wp_register_style( 'flipdown', get_theme_file_uri( 'css/flipdown.min.css' ),
		[], filemtime( get_theme_file_path( 'css/flipdown.min.css' ) ) );
	wp_enqueue_style( 'flipdown' );
	wp_register_script( 'flipdown', get_theme_file_uri( 'js/flipdown.min.js' ),
		[ 'jquery' ], filemtime( get_theme_file_path( 'js/flipdown.min.js' ) ) );
	wp_enqueue_script( 'flipdown' );

	/* https://owlcarousel2.github.io/OwlCarousel2/ */
	wp_register_style( 'owl.carousel', get_theme_file_uri( 'css/owl.carousel.min.css' ),
		[], filemtime( get_theme_file_path( 'css/owl.carousel.min.css' ) ) );
	wp_enqueue_style( 'owl.carousel' );
	wp_register_style( 'owl.theme.default', get_theme_file_uri( 'css/owl.theme.default.min.css' ),
		[], filemtime( get_theme_file_path( 'css/owl.theme.default.min.css' ) ) );
	wp_enqueue_style( 'owl.theme.default' );
	wp_register_script( 'owl.carousel', get_theme_file_uri( 'js/owl.carousel.min.js' ),
		[ 'jquery' ], filemtime( get_theme_file_path( 'js/owl.carousel.min.js' ) ) );
	wp_enqueue_script( 'owl.carousel' );

	/* currency conversion */
	wp_register_script( 'vstbuzz_currency_convert', get_theme_file_uri( 'js/vstbuzz_currency_convert.js' ),
		[ 'jquery' ], filemtime( get_theme_file_path( 'js/vstbuzz_currency_convert.js' ) ) );
	wp_enqueue_script( 'vstbuzz_currency_convert' );

	wp_register_style( 'vstbuzz-v2', get_theme_file_uri( 'css/v2.css' ),
		[], filemtime( get_theme_file_path( 'css/v2.css' ) ) );
	wp_enqueue_style( 'vstbuzz-v2' );

	wp_register_style( 'custom', get_theme_file_uri( 'css/custom.css' ),
		[ 'parent-style' ], filemtime( get_theme_file_path( 'css/custom.css' ) ) );
	wp_enqueue_style( 'custom' );

	wp_register_script( 'customjs', get_theme_file_uri( 'js/custom.js' ),
		[], filemtime( get_theme_file_path( 'js/custom.js' ) ) );
	wp_enqueue_script( 'customjs' );
	wp_localize_script( 'customjs', 'vst',
		array(
			'ajaxurl' => admin_url('admin-ajax.php')
		)
	);
}

add_action( 'wp_enqueue_scripts', 'theme_enqueue_styles_scripts', 80 );


/*-----------------------------------------------------------------------------------*/
/* Remove admin bar for non-admins
/*-----------------------------------------------------------------------------------*/

add_action('after_setup_theme', 'remove_admin_bar');
function remove_admin_bar() {
	if (!current_user_can('administrator') && !is_admin()) {
		show_admin_bar(false);
	}
}


/*-----------------------------------------------------------------------------------*/
/* Set the display name from whatever the user enters for first name
/* This is so it shows up nice in comments and atop the My Account section
/* using this in review.php and my-account.php
/*-----------------------------------------------------------------------------------*/

function vstbuzz_user_first_name( $user_id, $default_name ) {
	$user_info = get_userdata( $user_id );
	if ( $user_info != null ) {
		$first_name = $user_info->first_name;
		if ( $first_name != '' ) {
			return $first_name;
		} else {
			return $default_name;
		};
	} else {
		return $default_name;
	};
}


/*-----------------------------------------------------------------------------------*/
/* Featured Slider: Hook Into Content */
/* Copied from hustle/includes/theme-functions.php */
/* Had to modify so it shows up on the custom front page (i.e. not is_home) /*
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_featured_slider_loader_vstbuzz' ) ) {
	function woo_featured_slider_loader_vstbuzz() {
		$settings = woo_get_dynamic_values( array( 'featured' => 'true' ) );

		if ( ( $settings['featured'] == 'true' ) ) {
			get_template_part( 'includes/featured', 'slider' );
		}
	} // End woo_featured_slider_loader()
}

/*-----------------------------------------------------------------------------------*/
/* Kill "Additional Notes" field on the checkout */
/* From here: http://stackoverflow.com/questions/22483312/woocommerce-removing-additional-information-name-on-checkout-page */
/*-----------------------------------------------------------------------------------*/
add_filter( 'woocommerce_enable_order_notes_field', '__return_false' );


/*-----------------------------------------------------------------------------------*/
/* Truncate nicely */
/* Via: http://www.ambrosite.com/blog/truncate-long-titles-to-the-nearest-whole-word-using-php-strrpos */
/*-----------------------------------------------------------------------------------*/

function myTruncate( $string, $limit ) {
	$string = substr( $string, 0, strrpos( substr( $string, 0, $limit ), ' ' ) ) . '...';

	return $string;
}

/*-----------------------------------------------------------------------------------*/
/* Add JS required for tabs to the footer of the "My Account" page */
/*-----------------------------------------------------------------------------------*/

function my_account_tabs_js() {
	if ( is_page( 'my-account' ) ) {
		echo "<script type='text/javascript'> /* <![CDATA[ */ var wc_single_product_params = {'i18n_required_rating_text':'Please select a rating','review_rating_required':'no'}; /* ]]> */ </script>";
		echo "<script type='text/javascript' src='" . plugins_url( 'woocommerce/assets/js/frontend/single-product.min.js?ver=2.2.8' ) . "'></script>";
		echo "<script type='text/javascript' src='" . get_stylesheet_directory_uri() . "/js/scroller.js'></script>";
	}
}

add_action( 'wp_footer', 'my_account_tabs_js', 100 );

/*-----------------------------------------------------------------------------------*/
/* Jump to cart if user tries to add the same product to cart twice */
/* Needed this so user wouldn't be put back on the product page with an error message */
/*-----------------------------------------------------------------------------------*/

function handle_duplicate_product() {
	global $woocommerce;
	// Check for errors
	$all_notices = WC()->session->get( 'wc_notices', [] );
	$errors      = isset( $all_notices['error'] ) ? $all_notices['error'] : false;
	// So if there's an error...
	if ( $errors ) {
		foreach ( $errors as $error ) {
			// If the error informs user that they've already added that product to the cart
			if ( isset( $error['notice'] ) && stristr( $error['notice'], 'You cannot add another' ) ) {
		    wc_clear_notices();
				wp_redirect( $woocommerce->cart->get_cart_url() );
				exit();
			}
			if ( isset( $error['notice'] ) && stristr( $error['notice'], 'has been removed from your cart' ) ) {
				// Clear the errors/notices so they won't affect the next page
				wc_clear_notices();
			}
		}
	}
}
// TODO: uncomment
//add_action( 'template_redirect', 'handle_duplicate_product' );
//add_action( 'woocommerce_checkout_update_order_review', 'handle_duplicate_product' );


/*-----------------------------------------------------------------------------------*/
/* Jump to parent product if this is a bundled product, don't want people accessing it directly */
/* Product must have parent_product_url attribute set for this to work */
/*-----------------------------------------------------------------------------------*/

function handle_bundled_product() {
	// Check first if it's a product page
	if ( is_product() ) {
		global $product;
		if ( ! is_object( $product ) ) {
			$product = wc_get_product( get_the_ID() );
		}
		$parent_product_url = $product->get_attribute( 'parent_product_url' );
		if ( $parent_product_url != null ) {
			wp_redirect( $parent_product_url );
			exit();
		}
	}
}

add_action( 'template_redirect', 'handle_bundled_product' );


/*-----------------------------------------------------------------------------------*/
/* Clear the user's cart for expired products every time it's loaded
/* This ensures they can't come back after a deal has expired and still buy it
/* http://www.sitepoint.com/woocommerce-actions-and-filters-manipulate-cart/
/*-----------------------------------------------------------------------------------*/

add_action( 'wp', 'vstbuzz_clear_cart_of_expired_products' );
function vstbuzz_clear_cart_of_expired_products() {

	// Check first if it's the cart or checkout page
	if ( is_cart() || is_checkout() ) {

		// Cycle through each product in the cart
		foreach ( WC()->cart->cart_contents as $prod_in_cart ) {

			// Get the Variation or Product ID
			$prod_id   = ( isset( $prod_in_cart['variation_id'] ) && $prod_in_cart['variation_id'] != 0 ) ? $prod_in_cart['variation_id'] : $prod_in_cart['product_id'];
			$live_deal = vstbuzz_is_product_live( $prod_id ) || vstbuzz_is_product_freegift( $prod_id ) ;

      $product = wc_get_product( $prod_id );
      $is_subscription = is_object( $product ) ? $product->is_type( 'subscription' ) : false;
      
			// If the product is no longer available for purchase...
			if ( $live_deal == 0 && ! $is_subscription ) {
				$prod_unique_id = WC()->cart->generate_cart_id( $prod_id );
				// Remove it from the cart by un-setting it
				unset( WC()->cart->cart_contents[ $prod_unique_id ] );
			}
		}
	}
}


function vstbuzz_is_product_live( $prod_id, $skip_on_sale_check = false ) {
	$product = wc_get_product( $prod_id );

	$todays_date = current_time( 'timestamp', true );

	$sale_end_date = get_post_meta( $prod_id, '_sale_price_dates_to', true );

	if ( vstbuzz_product_has_category( $prod_id, 'store' ) || vstbuzz_product_has_category( $prod_id, 'special-free' ) || vstbuzz_product_has_category( $prod_id, 'the-vault' ) ) {
		$never_expires = 1;
	} else {
		$never_expires = 0;
	}

	// Figure out if this product is available for purchase or not...
	if ( $never_expires == 1 || ( ( ! $skip_on_sale_check && $product->is_on_sale() ) && ( $todays_date < $sale_end_date ) ) ) {
		$live_deal = 1;
	} else {
		$live_deal = 0;
	}

	return $live_deal;
}


function vstbuzz_is_product_freegift( $product_id ) {

	$is_freegift = VST_FreeGifts::check_if_freegift( $product_id );
	return $is_freegift;

}


function vstbuzz_product_has_category( $product_id, $category ) {
	if ( has_term( $category, 'product_cat', $product_id ) ) {
		return true;
	} else {
		return false;
	}
}

/**
 * Matching "purchasable" flag with our own definition of live deal. Jilt check this to check if product can be purchased.
 *
 * @param $purchasable
 * @param $product
 *
 * @return bool|int
 */
function vstbuzz_update_purchasable_status( $purchasable, $product ) {
	// If not purchasable, no need to do additional checks
	if ( ! $purchasable ) {
		return $purchasable;
	}

	$purchasable = vstbuzz_is_product_live( $product->get_id() );
	// Product is purchasable by our definition, no need for additional checks
	if ( $purchasable ) {
		return $purchasable;
	}

	// If product is not purchasable, it's possible that it's a bundled product, we have to check parent product aka. bundle
	// bundled products, example https://dev2.vstbuzz.com/deals/50-off-dronar-scupltor-bundle-gothic-instruments/
	global $wpdb;

	$sql     = "select bundle_id from {$wpdb->prefix}woocommerce_bundled_items where product_id = " . (int)$product->get_id();
	$bundles = $wpdb->get_results( $sql );

	$is_parent_live = false;
	foreach ( $bundles as $bundle ) {
		if ( $is_parent_live ) {
			break;
		} // If product is in more than one bundle, only one parent has to be live
		$is_bundle_live = vstbuzz_is_product_live( $bundle->bundle_id, true );
		$is_parent_live = $is_parent_live || $is_bundle_live;
	}
	$purchasable = $is_parent_live;

	return $purchasable;
}

add_filter( 'woocommerce_variation_is_purchasable', 'vstbuzz_update_purchasable_status', 10, 2 );
add_filter( 'woocommerce_is_purchasable', 'vstbuzz_update_purchasable_status', 10, 2 );

/*-----------------------------------------------------------------------------------*/
/* Add dynamic login/logout link to main menu  */
/* Code from here: http://xparkmedia.com/blog/add-login-logout-link-menu/ */
/*-----------------------------------------------------------------------------------*/

function add_login_out_item_to_menu( $items, $args ) {

	//change theme location with your theme location name
	if ( is_admin() || $args->theme_location != 'primary-menu' ) {
		return $items;
	}

	$redirect = ( is_home() ) ? false : get_permalink();
	if ( is_user_logged_in() ) {
		$link = '<a href="/my-account/">My Account</a>';
	} else {
		$link = '<a href="/my-account/" class="simplemodal-login">Login</a>';
	}

	return $items .= '<li id="log-in-out-link" class="menu-item menu-type-link">' . $link . '</li>';
}

add_filter( 'wp_nav_menu_items', 'add_login_out_item_to_menu', 50, 2 );


/*-----------------------------------------------------------------------------------*/
/* Add cart link to main menu if user has items in there  */
/* https://sridharkatakam.com/adding-cart-icon-number-items-total-cost-nav-menu-using-woocommerce/
/*-----------------------------------------------------------------------------------*/

function add_cart_item_to_menu( $menu, $args ) {

	// Check if WooCommerce is active and add a new item to a menu assigned to Primary Navigation Menu location
	if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || 'primary-menu' !== $args->theme_location ) {
		return $menu;
	}

	ob_start();
	global $woocommerce;
	$cart_contents_count = $woocommerce->cart->cart_contents_count;
	if ( $cart_contents_count > 0 ) {
		$menu_item = '<li id="cart-link"><a class="wcmenucart-contents" href="/cart/" title="View your shopping cart">Cart (<span>' . $cart_contents_count . '</span>)</a></li>';
	}
	echo $menu_item;
	$social = ob_get_clean();

	return $menu . $social;

}

add_filter( 'wp_nav_menu_items', 'add_cart_item_to_menu', 60, 2 );




/*-----------------------------------------------------------------------------------*/
/* Allow users to log in with their email address */
/* http://itechtuts.com/login-wordpress-email-address/ */
/* https://codex.wordpress.org/Plugin_API/Action_Reference/wp_authenticate
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_authenticate', 'email_address_login' );
function email_address_login( &$username ) {
	$user = get_user_by( 'email', $username );
	if ( ! empty( $user->user_login ) ) {
		$username = $user->user_login;
	}
}


/*-----------------------------------------------------------------------------------*/
/* Update VAT number for new users at checkout, and for everyone at My Account */
/*-----------------------------------------------------------------------------------*/

function update_vat_number( $user_id ) {
	$vat_number = $_POST['account_vat_number'];
	// First make sure we have the correct POST data...
	if ( isset( $vat_number ) ) {
		update_user_meta( $user_id, 'vat_number', $vat_number );
	}

}

add_action( 'profile_update', 'update_vat_number', 10, 1 );


/*-----------------------------------------------------------------------------------*/
/* Update VAT number for existing users at checkout */
/*-----------------------------------------------------------------------------------*/

function update_vat_number_existing_checkout() {

	$user_id = wp_get_current_user()->ID;

	$vat_number = $_POST['vat_number'];

	// First make sure we have the correct POST data...
	if ( isset( $vat_number ) ) {
		update_user_meta( $user_id, 'vat_number', $vat_number );
	}

}

add_action( 'woocommerce_checkout_process', 'update_vat_number_existing_checkout' );

/*-----------------------------------------------------------------------------------*/
/* After a new account is created at checkout and the user is redirected to the
/* login page, show a message telling them what to do next.
/*-----------------------------------------------------------------------------------*/

function after_checkout_notice() {

	global $woocommerce;

	$referrer     = $_SERVER['HTTP_REFERER'];
	$checkout_url = $woocommerce->cart->get_checkout_url();

	// We check if the referring URL is the checkout or PayPal.
	if ( $referrer == $checkout_url || strpos( $referrer, 'paypal' ) !== false ) { ?>

      <ul class="woocommerce-message">
        <li>Thank you for your order. You must login to download your files. If you just created an account, first check your email for an activation link.</li>
      </ul>

		<?php
		// Also add in this for the Improvely plugin so people who land on the login page will be tracked properly.
		do_action( 'woocommerce_thankyou', $order->id );
		?>

		<?php // And add in this tracking snippet too ?>

      <!-- Facebook Conversion Code for Bought VSTBuzz deal -->
      <script>(function () {
          var _fbq = window._fbq || (window._fbq = []);
          if (!_fbq.loaded) {
            var fbds = document.createElement('script');
            fbds.async = true;
            fbds.src = '//connect.facebook.net/en_US/fbds.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(fbds, s);
            _fbq.loaded = true;
          }
        })();
        window._fbq = window._fbq || [];
        window._fbq.push(['track', '6028428754239', { 'value': '0.00', 'currency': 'USD' }]);
      </script>
      <noscript><img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6028428754239&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1"/></noscript>

	<?php }
}

add_action( 'woocommerce_before_customer_login_form', 'after_checkout_notice' );


/*-----------------------------------------------------------------------------------*/
/* After a user registers from the login screen, show tracking code.
/* This function is called from form-login.php
/*-----------------------------------------------------------------------------------*/

function after_registration( $notices ) {
	global $post;

	$referrer    = $_SERVER['HTTP_REFERER'];
	$current_url = get_permalink( $post->ID );

	// If we have a success message, and we were redirected from the same page (i.e. the user must have registered from the login page)...
	if ( array_key_exists( 'success', $notices ) && ( $current_url == $referrer ) ) {

		// Then add this code
		echo "<!-- Facebook Conversion Code for Created VSTBuzz account -->
			<script>(function() {
			  var _fbq = window._fbq || (window._fbq = []);
			  if (!_fbq.loaded) {
				var fbds = document.createElement('script');
				fbds.async = true;
				fbds.src = '//connect.facebook.net/en_US/fbds.js';
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(fbds, s);
				_fbq.loaded = true;
			  }
			})();
			window._fbq = window._fbq || [];
			window._fbq.push(['track', '6028428943039', {'value':'0.00','currency':'USD'}]);
			</script>
			<noscript><img height='1' width='1' alt='' style='display:none' src='https://www.facebook.com/tr?ev=6028428943039&amp;cd[value]=0.00&amp;cd[currency]=USD&amp;noscript=1' /></noscript>";

	}

}


/*-----------------------------------------------------------------------------------*/
/* This is for the My Account page, for the Sponsor A Friend plugin.
/* We want the success message to appear above the panels, not inside.
/* Function is called from my-account.php, above wc_print_notices()
/* There's also CSS in style.css to hide the success message outputted by the plugin.
/*-----------------------------------------------------------------------------------*/

function sponsor_friend_confirmation() {
	if ( is_page( 'my-account' ) ) {
		if ( isset( $_GET['wsaf-status'] ) && $_GET['wsaf-status'] == 'success' ) {
			$successMessage = sprintf( __( 'Congratulations, a discount code has been sent to your friend!', 'woocommerce' ) );
			wc_add_notice( $successMessage, 'success' );
		}
	}
}


/*-----------------------------------------------------------------------------------*/
/* Reorder the checkout fields
/* https://stackoverflow.com/questions/45200360/reorder-woocommerce-checkout-fields
/*-----------------------------------------------------------------------------------*/

add_filter( "woocommerce_checkout_fields", "vst_custom_override_checkout_fields" );
function vst_custom_override_checkout_fields( $fields ) {
	$fields['billing']['billing_first_name']['priority'] = 11;
	$fields['billing']['billing_country']['priority']    = 21;
	$fields['billing']['billing_last_name']['priority']  = 31;
	$fields['billing']['billing_postcode']['priority']   = 41;
	$fields['billing']['billing_postcode']['required']   = false;
	$fields['billing']['billing_email']['priority']      = 51;
	$fields['billing']['billing_address_1']['priority']  = 61;
	$fields['billing']['billing_address_2']['priority']  = 71;

	$fields['billing']['billing_first_name']['custom_attributes'] = ['tabindex' => 1];
	$fields['billing']['billing_last_name']['custom_attributes'] = ['tabindex' => 2];
	$fields['billing']['billing_email']['custom_attributes'] = ['tabindex' => 3];
	$fields['billing']['billing_country']['custom_attributes'] = ['tabindex' => 4];
	$fields['billing']['billing_postcode']['custom_attributes'] = ['tabindex' => 5];
	$fields['billing']['billing_address_1']['custom_attributes'] = ['tabindex' => 6];
	$fields['billing']['billing_address_2']['custom_attributes'] = ['tabindex' => 7];

	$fields['billing']['billing_address_2']['label_class'] = '';
	$fields['billing']['billing_address_2']['label'] = 'Street Address 2';

	unset(
		$fields['billing']['billing_company'],
		$fields['billing']['billing_state'],
		$fields['billing']['billing_phone'],
		$fields['billing']['billing_city']
	);

	return $fields;
}


/*-----------------------------------------------------------------------------------*/
/* Reduce required password strength for new accounts
/* Ref: https://wordpress.org/support/topic/woocommerce-password-strength-meter-too-high#post-8364114
/*-----------------------------------------------------------------------------------*/

add_filter( 'wc_password_strength_meter_params', 'mr_strength_meter_custom_strings' );
function mr_strength_meter_custom_strings( $data ) {
	if ( ! is_array( $data ) ) {
		return $data;
	}
	$data_new = array(
		'min_password_strength' => apply_filters( 'woocommerce_min_password_strength', 0 ),
		'i18n_password_error'   => esc_attr__( '<span class="mr-red">Please enter a stronger password.</span>', 'woocommerce' ),
		'i18n_password_hint'    => esc_attr__( '', 'woocommerce' )
	);

	return array_merge( $data, $data_new );
}


/*-----------------------------------------------------------------------------------*/
/* Don't show products with cat "Never Expires (Store) and The Vault" on the /deals/ archive page
/* http://docs.woothemes.com/document/exclude-a-category-from-the-shop-page/
/*-----------------------------------------------------------------------------------*/

add_action( 'woocommerce_product_query', 'custom_pre_get_posts_query' );
function custom_pre_get_posts_query( $q ) {
	if ( is_archive() && ! is_admin() ) {
		$q->set( 'tax_query', array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => array( 'store', 'never-expires', 'the-vault' ),
				'operator' => 'NOT IN'
			)
		) );
	}
}

function add_tab_header_meta_box() {
	add_meta_box(
		'show_tab_header_option', // $id
		'Show Tab Header', // $title
		'show_tab_header_meta_box', // $callback
		'product', // $page
		'normal', // $context
		'high' ); // $priority
}

add_action( 'add_meta_boxes', 'add_tab_header_meta_box' );
global $products_meta_fields;
$prefix = 'custom_';
// Field Array
$products_meta_fields = array(
	array(
		'label'   => 'Select Show Tab',
		'desc'    => 'Select show tab in header site.',
		'id'      => $prefix . 'tab_header',
		'type'    => 'select',
		'options' => array(
			'one'   => array(
				'label' => 'No Show',
				'value' => 'no-show'
			),
			'two'   => array(
				'label' => 'Latest Deal',
				'value' => 'Bite'
			),
			'three' => array(
				'label' => 'Expiring Soon',
				'value' => 'Mega'
			)
		)
	)
);
// The Callback
function show_tab_header_meta_box() {
	global $products_meta_fields, $post;

	// Use nonce for verification
	echo '<input type="hidden" name="tab_header_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ( $products_meta_fields as $field ) {
		// get value of this field if it exists for this post
		$meta = get_post_meta( $post->ID, $field['id'], true );
		// begin a table row with
		echo '<tr>
	                <th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
	                <td>';
		switch ( $field['type'] ) {
			case 'select':
				echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
				foreach ( $field['options'] as $option ) {
					echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
				}
				echo '</select><br /><span class="description">' . $field['desc'] . '</span>';
				break;
		} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}

// Save the Data
function save_tab_header_meta( $post_id ) {

	global $products_meta_fields;

	// verify nonce
	if ( ! wp_verify_nonce( $_POST['tab_header_meta_box_nonce'], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	// check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) ) {
			return $post_id;
		}
	} elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// loop through fields and save the data
	foreach ( $products_meta_fields as $field ) {
		$old = get_post_meta( $post_id, $field['id'], true );
		$new = $_POST[ $field['id'] ];
		if ( $new && $new != $old ) {
			update_post_meta( $post_id, $field['id'], $new );
		} elseif ( '' == $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	} // end foreach
}

add_action( 'save_post', 'save_tab_header_meta' );

/**
 * Custom navigation
 */
function vst_corenavi( $max_num_pages ) {
	$big = 999999999;

	$args = array(
		'base'         => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'       => '?page=%#%',
		'total'        => $max_num_pages,
		'current'      => max( 1, get_query_var( 'paged' ) ),
		'show_all'     => false,
		'end_size'     => 1,
		'mid_size'     => 2,
		'prev_next'    => true,
		'prev_text'    => __( 'Previous', 'crb' ),
		'next_text'    => __( 'Next', 'crb' ),
		'type'         => 'list',
		'add_args'     => false,
		'add_fragment' => ''
	);

	echo paginate_links( $args );
}

/* Additional Gravity Forms functions */

add_filter( 'gform_field_css_class', 'theme_custom_gforms_classes', 10, 3 );
function theme_custom_gforms_classes( $classes, $field, $form ) {
	$classes .= ' gfield-' . $field['type'];

	return $classes;
}

# Render a Gravity Form
function theme_render_form( $id, $display_title = false, $display_description = false, $ajax = false, $tabindex = 1 ) {
	if ( ! function_exists( 'gravity_form' ) || empty( $id ) ) {
		return;
	}

	gravity_form( $id, $display_title, $display_description, $display_inactive = false, $field_values = null, $ajax, $tabindex );
}

// define the woocommerce_gateway_icon callback
add_filter( 'woocommerce_gateway_icon', 'theme_woocommerce_gateway_icon', 10, 2 );
function theme_woocommerce_gateway_icon( $icon, $id ) {

	if ( $id != 'Stripe' ) {
		return $icon;
	}

	$path = get_bloginfo( 'stylesheet_directory' );

	$icons = array(
		'icon-visa'    => 'Icon Visa',
		'icon-ms-card' => 'Icon Master Card',
		'icon-amx'     => 'Icon American Express',
	);

	foreach ( $icons as $img => $alt ) {
		$icon .= '<img src="' . $path . '/images/' . $img . '.png" alt="' . $alt . '" />';
	}

	// make filter magic happen here...
	return $icon;
}


/**
 * Extension to contact form 7 plugin
 * Adds custom tags to subject and body before sending email.
 * Additional tags:
 * [ref-code]
 * [member-firstname]
 * [member-lastname]
 * [member-email]
 */


add_filter( 'wpcf7_verify_nonce', '__return_true' );
add_action( 'wpcf7_before_send_mail', 'vstbuzz_wpcf7_update_email_body', 10 );
function vstbuzz_wpcf7_update_email_body( $contact_data ) {
	$submission     = WPCF7_Submission::get_instance();
	$meta_val       = get_user_meta( get_current_user_id(), 'gens_referral_id', true );
	$ref_link       = home_url() . '?raf=' . get_user_meta( get_current_user_id(), 'gens_referral_id', true );
	$current_user   = wp_get_current_user();
	$current_userID = $contact_data;
	if ( $submission ) {
		$mail = $contact_form->prop( 'mail' );
		//print_r($contact_form);
		//replace custom tags in subject
		$mail['subject'] = str_replace( "[ref-code]", $ref_link, $mail['subject'] );
		$mail['subject'] = str_replace( "[member-firstname]", $current_user->user_nicename, $mail['subject'] );
		$mail['subject'] = str_replace( "[member-lastname]", '', $mail['subject'] );
		$mail['subject'] = str_replace( "[member-email]", $current_user->user_email, $mail['subject'] );

		//replace custom tags in body
		$mail['body'] = str_replace( "[ref-code]", $ref_link, $mail['body'] );
		$mail['body'] = str_replace( "[member-firstname]", $current_user->user_nicename, $mail['body'] );
		$mail['body'] = str_replace( "[member-lastname]", '', $mail['body'] );
		$mail['body'] = str_replace( "[member-email]", $current_user->user_email, $mail['body'] );
		$contact_form->set_properties( array( 'mail' => $mail ) );
	}
}

/**
 * Show or call to generate new referal ID
 *
 * @return string
 * @since    1.0.0
 */
function vstbuzz_ref_get_referral_id() {
	$user_id = get_current_user_id();
	if ( ! $user_id ) {
		return false;
	}
	$referralID = get_user_meta( $user_id, "gens_referral_id", true );
	if ( $referralID && $referralID != "" ) {
		return $referralID;
	} else {
		do {
			$referralID = vstbuzz_ref_generate_referral_id();
		} while ( vstbuzz_ref_exists_ref_id( $referralID ) );
		update_user_meta( $user_id, 'gens_referral_id', $referralID );

		return $referralID;
	}

}


/**
 * Check if ID already exists
 *
 * @return string
 * @since    1.0.0
 */
function vstbuzz_ref_exists_ref_id( $referralID ) {

	$args = array( 'meta_key' => "gens_referral_id", 'meta_value' => $referralID );
	if ( get_users( $args ) ) {
		return true;
	} else {
		return false;
	}

}


/**
 * Generate a new Referral ID
 *
 * @return string
 * @since    1.0.0
 */
function vstbuzz_ref_generate_referral_id( $randomString = "ref" ) {

	$characters = "0123456789";
	for ( $i = 0; $i < 7; $i ++ ) {
		$randomString .= $characters[ rand( 0, strlen( $characters ) - 1 ) ];
	}

	return $randomString;
}

/*------------------------------------------------------------------------------------------*/
/* Add a "tech note" meta box for each product, which will be displayed at cart and checkout
/*------------------------------------------------------------------------------------------*/

function custom_meta_box_markup( $object ) {
	wp_nonce_field( basename( __FILE__ ), "meta-box-nonce" );
	?>
  <div>
    <input name="meta-box-tech-note" type="text" style="width: 100%; margin-bottom: 10px" value="<?php echo get_post_meta( $object->ID, "meta-box-tech-note", true ); ?>"/>
  </div>
  <div>
    <input name="meta-box-tech-note-additional" type="text" style="width: 100%" value="<?php echo get_post_meta( $object->ID, "meta-box-tech-note-additional", true ); ?>"/>
  </div>
	<?php
}

function add_custom_meta_box() {
	add_meta_box( "demo-meta-box", "Tech Note", "custom_meta_box_markup", "product", "side", "low", null );
}

add_action( "add_meta_boxes", "add_custom_meta_box" );

function save_custom_meta_box( $post_id, $post, $update ) {
	if ( ! isset( $_POST["meta-box-nonce"] ) || ! wp_verify_nonce( $_POST["meta-box-nonce"], basename( __FILE__ ) ) ) {
		return $post_id;
	}
	if ( ! current_user_can( "edit_post", $post_id ) ) {
		return $post_id;
	}
	if ( defined( "DOING_AUTOSAVE" ) && DOING_AUTOSAVE ) {
		return $post_id;
	}
	$slug = "product";
	if ( $slug != $post->post_type ) {
		return $post_id;
	}
	$meta_box_tech_note_value = "";
	if ( isset( $_POST["meta-box-tech-note"] ) ) {
		$meta_box_tech_note_value = $_POST["meta-box-tech-note"];
	}
	update_post_meta( $post_id, "meta-box-tech-note", $meta_box_tech_note_value );

	$meta_box_tech_additional_note_value = "";
	if ( isset( $_POST["meta-box-tech-note-additional"] ) ) {
		$meta_box_tech_additional_note_value = $_POST["meta-box-tech-note-additional"];
	}
	update_post_meta( $post_id, "meta-box-tech-note-additional", $meta_box_tech_additional_note_value );
}

add_action( "save_post", "save_custom_meta_box", 10, 3 );


/*------------------------------------------------------------------------------------------*/
/* Remove related products from bottom of deal pages
/* via https://docs.woocommerce.com/document/remove-related-posts-output/
/*------------------------------------------------------------------------------------------*/

remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );


/*------------------------------------------------------------------------------------------*/
/* Add functionality to allow admin's to change serial numbers
/* Note that we also had to make modifications to the License Manager plugin to get this working
/*------------------------------------------------------------------------------------------*/

add_action( 'woocommerce_after_order_itemmeta', 'order_meta_customized_display', 10, 3 );

function order_meta_customized_display( $item_id, $item, $product ) {
	$serial = get_metadata( 'order_item', $item_id, 'Serial Number(s)' );
	echo "<small>*To change or add Serial Number change order status to Pending payment. Also, delete used serial number from list of available codes.  <br>Meta key: <strong>Serial Number(s)</strong></small>";

}


//-----------------------------------------------------------------------------------------------------------------------
//Delay adding to Mailchimp list.
//Default behavior is to add user after registration but we want to cancel this and add user to list
//only after user confirms email address

//This code depends on plugins 'Autochimp' and 'WooCommerce Email Verification Pro'

//add_action('user_register','AC_OnRegisterUser', 501) -> Remove this action
//Function in 'Autochimp'
remove_action( 'user_register', 'AC_OnRegisterUser', 501 );
remove_action( 'profile_update', 'AC_OnUpdateUser', 501, 2 );


/**
 * Function for saving additional data on user registration
 *
 * @param $user_id
 */
function vstbuzz_registration_save( $user_id ) {

	if ( isset( $_POST['subscription'] ) ) {
		$subscription_accepted = $_POST['subscription'];
		update_user_meta( $user_id, 'subscription_accepted_on_registration', $subscription_accepted );
	}

	if ( isset( $_POST['_wp_http_referer'] ) ) {
		$referer = $_POST['_wp_http_referer'];
		update_user_meta( $user_id, 'user_registration_source_page', $referer );
	}

}

add_action( 'user_register', 'vstbuzz_registration_save', 10, 1 );



//End Modifications for Mailchimp
//------------------------------------------------------------------------------------------------------------------


// Change Serial Number(s) label. We can't just change metadata because it's used as a key in database
// so we change generated html. This is used by invoice plugin to show license code in invoice
function filter_woocommerce_display_item_meta( $html, $item, $args ) {

	if ( strpos( $html, 'Serial Number(s)' ) !== false ) {
		$html = str_replace( "Serial Number(s)", "Code", $html );
	}

	return $html;
}

;
add_filter( 'woocommerce_display_item_meta', 'filter_woocommerce_display_item_meta', 99, 3 );


if ( ! function_exists( 'vst_str_contains' ) ) {
	function vst_str_contains( $needle, $haystack ) {
		return strpos( $haystack, $needle ) !== false;
	}
}

add_image_size( 'vstbuzz-header', 300, 0 );


/**
 * Detect search engine crawlers
 * @return bool
 */
function bot_detected() {
	return (
		isset( $_SERVER['HTTP_USER_AGENT'] )
		&& preg_match( '/bot|google|crawl|slurp|spider|facebookexternalhit|ia_archiver|mediapartners/i', $_SERVER['HTTP_USER_AGENT'] )
	);
}


/**
 * Get basic product information used for rewards program
 */
function get_basic_product_info( $product_id ) {
	$_product      = wc_get_product( $product_id );
	$product_basic = array(
		'product_id'       => $product_id,
		'slug'             => $_product->get_slug(),
		'price'            => $_product->get_price(),
		'can_spend_points' => has_term( 'the-vault', 'product_cat', $product_id )

	);

	return $product_basic;
}

// Registering sidebar to blog layout

if ( function_exists( 'register_sidebar' ) ) {
	register_sidebar( array(
		'name'          => 'Blog Sidebar',
		'id'            => 'blog-sidebar',
		'description'   => 'Appears as the sidebar on the custom blog page',
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widgettitle">',
		'after_title'   => '</h3>',
	) );
}


/**
 * Remove display name from required fields list in Woocommerce on my profile page
 */
add_filter( 'woocommerce_save_account_details_required_fields', 'wc_save_account_details_required_fields' );
function wc_save_account_details_required_fields( $required_fields ) {
	unset( $required_fields['account_display_name'] );

	return $required_fields;
}


/**
 * Fetching one or more latest products
 *
 * @param string $type 'latest' or 'random', default is 'latest'
 * @param int $count number of products to return or choose from for random parameter default 2
 * @param string $category - category, default 'deals'
 *
 * @return array|bool
 */
function vst_get_latest_deal( $type = 'latest', $count = 2, $category = 'deals' ) {

	if ( ! is_array( $category ) && is_string( $category ) ) {
		$category_str = $category;
		$category     = array();
		$category[]   = $category_str;
	}

	$args  = array(
		'post_type'      => 'product',
		'stock'          => 1,
		'posts_per_page' => $count,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $category,
				'operator' => 'IN'
			),
		),
		'orderby'        => 'date',
		'order'          => 'DESC'
	);
	$query = new WP_Query( $args );
	$deals = $query->get_posts();


	if ( $type == 'latest' ) {
		return $deals;
	} else if ( $type == 'random' ) {

		if ( count( $deals ) > 1 ) {
			$index       = rand( 0, count( $deals ) - 1 );
			$random_deal = $deals[ $index ];

			return $random_deal;
		}
	}

	return false;

}

/**
 * Return product category name. Our products always have only one category but this function support multiple categories too.
 *
 * @param $product_id
 *
 * @return string
 */
function vst_get_product_cat_name( $product_id ) {

	$names      = array();
	$categories = wp_get_post_terms( $product_id, 'product_cat' );
	foreach ( $categories as $category ) {
		$names[] = $category->name;
	}

	return implode( ', ', $names );
}


/**
 * Adds 'Payment method' column header to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $columns
 *
 * @return string[] $new_columns
 */
function vstbuzz_add_payment_method_column_header( $columns ) {

	$new_columns = array();

	foreach ( $columns as $column_name => $column_info ) {

		$new_columns[ $column_name ] = $column_info;

		if ( 'order_total' === $column_name ) {
			$new_columns['payment_method'] = __( 'Payment Method', 'vstbuzz' );
		}
	}

	return $new_columns;
}

add_filter( 'manage_edit-shop_order_columns', 'vstbuzz_add_payment_method_column_header', 20 );


/**
 * Adds 'Payment Method' column content to 'Orders' page immediately after 'Total' column.
 *
 * @param string[] $column name of column being displayed
 */
function vstbuzz_add_payment_method_column_content( $column ) {
	global $post;

	if ( 'payment_method' === $column ) {

		$order = wc_get_order( $post->ID );


		echo "{$order->get_payment_method_title()}";
	}
}

add_action( 'manage_shop_order_posts_custom_column', 'vstbuzz_add_payment_method_column_content' );


/** Orders caching */
add_action( 'woocommerce_new_order', 'insert_into_order_cache', 1, 1 );
function insert_into_order_cache( $order_id ) {
	global $wpdb;
	$order = new WC_Order( $order_id );

	$wpdb->insert( 'orders_cache', array(
		'order_id'      => $order_id,
		'customer_id'   => $order->get_customer_id(),
		'billing_email' => $order->get_billing_email(),
		'order_date'    => $order->get_date_created()
	) );
}

add_action( 'updated_post_meta', 'vst_update_orders_cache_on_meta_change', 10, 4 );
function vst_update_orders_cache_on_meta_change( $meta_id, $post_id, $meta_key, $meta_value ) {
	global $wpdb;
	// Watch these meta keys and store them in orders_cache
	$meta_map = array( '_customer_user' => 'customer_id', '_billing_email' => 'billing_email' );
	if ( in_array( $meta_key, array_keys( $meta_map ) ) ) {
		$order = wc_get_order( $post_id );
		if ( $order ) {
			$wpdb->update( 'orders_cache',
				array( $meta_map[ $meta_key ] => $meta_value ),
				array( 'order_id' => $post_id )
			);
		}
	}
}


/* BitPay */
add_action( 'woocommerce_order_status_processing', 'vst_bitpay_processing' );
function vst_bitpay_processing( $order_id ) {

	$order          = new WC_Order( $order_id );
	$payment_method = $order->get_payment_method();
	if ( 'bitpay_checkout_gateway' == $payment_method ) {
		$order->update_status( 'completed', 'VST: Status changed from processing to completed automatically after receiving a message from BitPay' );
	}


}

/** Remove product reviews from default location */
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {
	unset( $tabs['reviews'] );  // Removes the reviews tab

	return $tabs;
}


/* Remove defualt WooCommerce rating hook */
remove_action( 'comment_post', array( 'WC_Comments', 'add_comment_rating', 1 ) ); //remove static class hook
add_action( 'comment_post', 'vstbuzz_comment_post_action', 1 );

function vstbuzz_comment_post_action( $comment_id ) {

	if ( isset( $_POST['rating'], $_POST['comment_post_ID'] ) && 'product' === get_post_type( absint( $_POST['comment_post_ID'] ) ) ) { // WPCS: input var ok, CSRF ok.
		if ( ! $_POST['rating'] || $_POST['rating'] > 10 || $_POST['rating'] < 0 ) { // WPCS: input var ok, CSRF ok, sanitization ok.
			return;
		}
		add_comment_meta( $comment_id, 'rating', intval( $_POST['rating'] ), true ); // WPCS: input var ok, CSRF ok.

		$post_id = isset( $_POST['comment_post_ID'] ) ? absint( $_POST['comment_post_ID'] ) : 0; // WPCS: input var ok, CSRF ok.
		if ( $post_id ) {
			WC_Comments::clear_transients( $post_id );
		}
	}

}


function get_request_parameter( $key, $default = '' ) {
	// If not request set
	if ( ! isset( $_REQUEST[ $key ] ) || empty( $_REQUEST[ $key ] ) ) {
		return $default;
	}

	// Set so process it
	return strip_tags( (string) wp_unslash( $_REQUEST[ $key ] ) );
}


// define the posts_request callback
function filter_posts_request( $array ) {
	$url = 'https://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	if ( strpos( $url, 'edit.php' ) !== false ) {

		$customerId = get_request_parameter( '_customer_user' );
		$postType   = get_request_parameter( 'post_type' );
		if ( ! empty( $postType ) && $postType == 'shop_order' ) {
			echo "POST TYPE" . $postType;
			if ( ! empty( $customerId ) ) {
				echo "customer id" . $customerId;
				echo 'EDIT PAGE';
				$sqlToReplace = "SELECT SQL_CALC_FOUND_ROWS  wp_posts.* FROM wp_posts  INNER JOIN wp_postmeta ON ( wp_posts.ID = wp_postmeta.post_id ) WHERE 1=1  AND ( 
  ( wp_postmeta.meta_key = '_customer_user' AND wp_postmeta.meta_value = '$customerId' )
) AND wp_posts.post_type = 'shop_order' AND ((wp_posts.post_status = 'wc-pending' OR wp_posts.post_status = 'wc-processing' OR wp_posts.post_status = 'wc-on-hold' OR wp_posts.post_status = 'wc-completed' OR wp_posts.post_status = 'wc-cancelled' OR wp_posts.post_status = 'wc-refunded' OR wp_posts.post_status = 'wc-failed')) GROUP BY wp_posts.ID ORDER BY wp_posts.post_date DESC LIMIT 0, 20";
				if ( $array == $sqlToReplace ) {
					echo "WE SHOULD REPLACE THIS SQL WITH OPTIMAL";
					$array = "select SQL_CALC_FOUND_ROWS p.* from orders_cache oc inner join wp_posts p on p.ID=oc.order_id where oc.customer_id = $customerId and p.post_status in ('wc-pending', 'wc-processing', 'wc-on-hold', 'wc-completed','wc-cancelled', 'wc-refunded','wc-failed')  order by    p.post_date DESC LIMIT 0,
        20";
				}
			}
		}
	}

	return $array;
}

// add the filter
add_filter( 'posts_request', 'filter_posts_request', 10, 1 );

//COUPONS FIX
add_filter( 'woocommerce_coupon_generator_coupon_meta_data', function ( $meta, $coupon_id ) {
	$meta['usage_count'] = 0;

	return $meta;
}, 10, 2 );


//User query optimization -woocommerce ajax customer search
add_filter( 'woocommerce_customer_pre_search_customers', function ( $var1, $term, $limit = '' ) {

	if ( simpleCheckEmail( $term ) ) {

		$query = new WP_User_Query(
			array(
				'search'         => esc_attr( $term ),
				'search_columns' => array( 'user_email' ),
				'fields'         => 'ID',
			) );

		$results = wp_parse_id_list( (array) $query->get_results() );
		if ( $limit && count( $results ) > $limit ) {
			$results = array_slice( $results, 0, $limit );
		}

		return $results;
	}

	return false;

}, 10, 3 );


function simpleCheckEmail( $email ) {
	$find1 = strpos( $email, '@' );
	$find2 = strpos( $email, '.' );

	return ( $find1 !== false && $find2 !== false && $find2 > $find1 );
}

/**
 * TODO: remove after 15 may (together with cusrev plugin)
 * The problem: previous comment version (for a some reason) operated reviews with rating 1-10
 * while WooCommerce allows products rating 1-5
 * This filter increases all the rating from the ivole_order x2 (2* -> 4*, 5* -> 10*)
 */
add_filter( 'comment_post', 'pulse_fix_rating_stars', 999, 3 );
function pulse_fix_rating_stars( $comment_id, $commentdata_comment_approved, $commentdata ) {
	$rating  = get_comment_meta( $comment_id, 'rating', true );
	$rating  = (int) $rating;
	$isIvole = "" !== get_comment_meta( $comment_id, 'ivole_order', true );
	if ( $isIvole && $rating <= 5 ) {
		$newRating = $rating * 2;
		update_comment_meta( $comment_id, 'rating', $newRating );
	}
}


/**
 * Custom function to check if there're products on sale in the cart, before sending the notification
 *
 * @param $cart_id
 *
 * @return bool
 */
add_filter( 'addify_acr_send_cart_notification', 'vst_check_if_has_products_on_sale', 10, 3 );
function vst_check_if_has_products_on_sale( $result, $template_id, $cart_id ) {
	// override default $result
	$result = false;
	$cart   = get_post( $cart_id );
	if ( ! is_a( $cart, 'WP_Post' ) ) {
		return false;
	}
	$cart_contents = json_decode( $cart->post_content, true );
	foreach ( $cart_contents as $item ) {
		$product_id = isset( $item['product_id'] ) ? (int) $item['product_id'] : 0;
		$product    = wc_get_product( $product_id );
		if (
			$product->is_on_sale() ||
			has_term( 38, 'product_cat', $product )
		) {
			$result = true;
			break;
		}
	}

	return $result;
}


/* custom options */
function woo_options_add( $options ) {
	$options[] = array(
		'name' => __( 'Youtube link', 'woothemes' ),
		'desc' => __( '', 'woothemes' ),
		'id'   => 'woo_contact_youtube',
		'std'  => '',
		'type' => 'text'
	);
	$options[] = array(
		'name' => __( 'Facebook link', 'woothemes' ),
		'desc' => __( '', 'woothemes' ),
		'id'   => 'woo_contact_facebook',
		'std'  => '',
		'type' => 'text'
	);
	$options[] = array(
		'name' => __( 'Instagram link', 'woothemes' ),
		'desc' => __( '', 'woothemes' ),
		'id'   => 'woo_contact_instagram',
		'std'  => '',
		'type' => 'text'
	);

	return $options;
}

/* remove points message from the product page */
add_filter( 'wc_points_rewards_single_product_message', 'remove_rewards_earn_points_message_single', 15, 2 );
function remove_rewards_earn_points_message_single( $message, $data ) {
	if ( is_product() ) {
		return '';
	}

	return $message;
}

/*
 * Move points and rewards message like 'Complete your order and earn 39 Reward Points for a discount on a future purchase' below the cart
 */
function move_points_rewards_below_cart() {
	if ( isset( $GLOBALS['wc_points_rewards'] ) && property_exists( $GLOBALS['wc_points_rewards'], 'cart' ) ) {
		remove_action( 'woocommerce_before_cart', array( $GLOBALS['wc_points_rewards']->cart, 'render_earn_points_message' ), 15 );
		add_action( 'woocommerce_wc_points_rewards', array( $GLOBALS['wc_points_rewards']->cart, 'render_earn_points_message' ), 15 );
	}
}

add_action( 'init', 'move_points_rewards_below_cart' );

function vst_get_tech_note( $productID ) {
	$meta_box_tech_note = get_post_meta( $cart_item['product_id'], "meta-box-tech-note", true );
	if ( $meta_box_tech_note ) {
		$tech_note .= '<dd>' . $_product->get_title() . '<strong> ' . get_post_meta( $cart_item['product_id'], "meta-box-tech-note", true ) . '</strong></dd>';
		$tech_note_count ++;
	}
	$meta_box_tech_note_additional = get_post_meta( $cart_item['product_id'], "meta-box-tech-note-additional", true );
	if ( $meta_box_tech_note_additional ) {
		$tech_note .= '<dd>' . $_product->get_title() . '<strong> ' . get_post_meta( $cart_item['product_id'], "meta-box-tech-note-additional", true ) . '</strong></dd>';
		$tech_note_count ++;
	}
}

function vst_pagination($the_query) {
	$big = 999999999; // need an unlikely integer

	$pagination = paginate_links( array(
		'base'    => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
		'format'  => '?paged=%#%',
		'current' => max( 1, get_query_var( 'paged' ) ),
		'total'   => $the_query->max_num_pages,
    'prev_text'    => __('&lt; Previous'),
    'next_text'    => __('Next &gt;'),
	) );
	if(!empty(trim(strip_tags($pagination)))) {
		return '<nav class="pagination woo-pagination">' . $pagination . '</nav>';
	}
}

function vst_get_tab_off($class = '') {
	global $product;
	$content_tab_off = get_field( 'content_tab_off', $product->get_id() );
	if(empty($content_tab_off)) {
	  $product_prices = vstbuzz_get_product_prices( $product );
	  /**
	   * @var number $sale_price
	   * @var number $regular_price
	   * @var number $save_price
	   */
	  extract($product_prices);
    if($sale_price !== "") {
	    $off_percent = 100 - round( ( $sale_price * 100 ) / $regular_price );
	    if ( $off_percent > 0 ) {
		    $content_tab_off = $off_percent . '% ' . __( 'off', 'vstbuzz' );
	    }
    }
	}
	if(!empty($content_tab_off)) {
		return '<div class="' . $class . '">' . $content_tab_off . '</div>';
	}
  return '';
}

// add 'New audio plugin deals drop in' section to the https://vstbuzz.com/deals/ page
add_action( 'woocommerce_after_main_content', 'vst_show_new_deals_section', 20 );
function vst_show_new_deals_section() {
  if ( !(is_product() && has_term( 'competitions', 'product_cat' ) )) {
    echo get_new_deals();
  }
}

// remove the Order Again button as we're not allowing to by more than one product
remove_action( 'woocommerce_order_details_after_order_table', 'woocommerce_order_again_button' );


/**
 * Search orders only in the e-mail
 */
function custom_woocommerce_shop_order_search_fields( $search_fields ) {
  /*
   default $search_fields are:
  [
    0 => '_billing_address_index',
    1 => '_shipping_address_index',
    2 => '_billing_last_name',
    3 => '_billing_email'
  ]
   */
	return ['_billing_last_name', '_billing_email'];
}
add_filter( 'woocommerce_shop_order_search_fields', 'custom_woocommerce_shop_order_search_fields' );


/*
 * Check user cart items
 * */
function cart_count_retriever() {
	global $wpdb;
	echo WC()->cart->get_cart_contents_count();
	wp_die();
}
add_action('wp_ajax_cart_count_retriever', 'cart_count_retriever');
add_action('wp_ajax_nopriv_cart_count_retriever', 'cart_count_retriever');

/* we have custom radio for the Klaviyo subscription consent */
function vst_remove_klaviyo_filter() {
// Add the checkbox field
	remove_filter( 'woocommerce_checkout_fields', 'kl_checkbox_custom_checkout_field', 11 );
}
add_action( 'init', 'vst_remove_klaviyo_filter', 20 ); // The priority 20 ensures it runs after the original.

/* Subscribe user during registration if they agree */
if(
  isset($_REQUEST['klaviyo_subscribe'])
  && isset($_REQUEST['kl_newsletter_checkbox'])
  && (int)$_REQUEST['kl_newsletter_checkbox'] === 1
  && function_exists('kl_add_to_list')
) {
  $_POST['billing_email']   = $_REQUEST['email'];
	$_POST['billing_phone']   = '';
	$_POST['billing_country'] = '';
  kl_add_to_list();
}

/* go right to checkout when added to cart */
add_filter('woocommerce_add_to_cart_redirect', 'cw_redirect_add_to_cart');
function cw_redirect_add_to_cart($url) {
	global $woocommerce;

	// LO.V.E Piano -> product ID
	$target_product_id = 106339;

	// Get the last added item's product ID
	$recent_cart = $woocommerce->cart->get_cart();
	$last_added_item = end($recent_cart);
	$last_added_product_id = (int)$last_added_item['product_id'];

	// Check if the last added product's ID matches the target ID or if it belongs to the competitions / raffle
	if($last_added_product_id === $target_product_id || has_term('competitions', 'product_cat', $last_added_product_id)) {
		$url = $woocommerce->cart->get_checkout_url();
	} else {
		$url = $woocommerce->cart->get_cart_url();
	}

	return $url;
}
add_filter('woocommerce_checkout_fields', 'custom_override_checkout_fields');

/* LO.V.E Piano -> remove all fields from checkout if only one product is in the cart */
function custom_override_checkout_fields($fields) {
	global $woocommerce;

	$target_product_id = 106339;  // Replace with your specific product ID
	$only_target_product = true;
	$cart_item_count = 0;

	foreach ($woocommerce->cart->get_cart() as $cart_item_key => $values) {
		$_product = $values['data'];
		$cart_item_count += $values['quantity'];

		if ($_product->get_id() != $target_product_id) {
			$only_target_product = false;
			break;
		}
	}

	if ($only_target_product && $cart_item_count === 1) {
//		unset($fields['billing']['billing_first_name']);
//		unset($fields['billing']['billing_last_name']);

	  // Add a custom class to the fields instead of unsetting them
	  $fields['billing']['billing_first_name']['class'][] = 'd-none';
	  $fields['billing']['billing_last_name']['class'][] = 'd-none';
    // Set the fields as not required
    $fields['billing']['billing_first_name']['required'] = false;
    $fields['billing']['billing_last_name']['required'] = false;

	  unset($fields['billing']['billing_company']);
		unset($fields['billing']['billing_address_1']);
		unset($fields['billing']['billing_address_2']);
		unset($fields['billing']['billing_city']);
		unset($fields['billing']['billing_postcode']);
		unset($fields['billing']['billing_country']);
		unset($fields['billing']['billing_state']);
		unset($fields['billing']['billing_phone']);

		unset($fields['shipping']);

		add_filter('woocommerce_cart_needs_payment', '__return_false');
	}

	return $fields;
}


/* Fix pdf infoice <p> tags */
add_filter( 'wpi_item_description_data', 'replace_encoded_p_tags', 10, 3 );
function replace_encoded_p_tags( $description, $item_id, $item ) {
	// Replace encoded <p> and </p> tags with their HTML equivalents
	$description = str_replace('&lt;p&gt;', '<p>', $description);
	$description = str_replace('&lt;/p&gt;', '</p>', $description);

	return $description;
}


/* Add page url to form submission */
function add_current_page_url_to_form_data($form_data) {
	// Check if the SERVER variable has the information
	if (isset($_SERVER['HTTP_REFERER'])) {
		$form_data['page'] = $_SERVER['HTTP_REFERER'];
	}
	return $form_data;
}
add_filter('WPFormsDB_before_save_data', 'add_current_page_url_to_form_data');


/**
 *
 * Fires after all field validation and formatting data.
 *
 * @link  https://wpforms.com/developers/wpforms_process_filter/
 *
 * @param  array  $fields     Sanitized entry field values/properties.
 * @param  array  $entry      Original $_POST global.
 * @param  array  $form_data  Form data and settings.
 *
 * @return array
 */

function vstbuzz_wpf_dev_process_filter( $fields, $entry, $form_data ) {
	$form_id = 530359; // contact form ID

	// Bail early if form ID does not match
	if ( (int)$form_data[ 'id' ] !== $form_id ) {
		return $fields;
	}
	foreach ( $fields as $field ) {
		// "payment-single" was used as just a html field, so we don't need it
		if ( $field[ 'type' ] === 'payment-single' ) {
			unset($fields[$field[ 'id' ]]);
		}
	}
	return $fields;

}
add_filter( 'wpforms_process_filter', 'vstbuzz_wpf_dev_process_filter', 10, 3 );


/* Add custom class to the competitions page */
//add_filter('body_class', function ($classes) {
//  if (
//    (is_product() && has_term('competitions', 'product_cat'))
//    || is_page('competitions')
//  ) {
//    $classes[] = 'competitions';
//  }
//  return $classes;
//});


//if(isset($_COOKIE['vstdev']) && (int)$_COOKIE['vstdev'] === 1) {
  add_action('vstbuzz_add_captcha', function() {
      if(function_exists('hcaptcha_verify_post')) {
        echo '<div class="hcap_wc_checkout_captcha" style="display: none;">';
        echo do_shortcode('[hcaptcha]');
        echo '</div>';
        ?>
      <script type="text/javascript">
          jQuery(function($) {
              function toggleCaptchaVisibility() {
                  const paymentMethodValue = $('input[name="payment_method"]:checked').val();
                  const isStripe = paymentMethodValue ? paymentMethodValue.indexOf('stripe_cc') !== -1 : false;
                  const isCartTotalZero = <?php echo WC()->cart->total == 0 ? 'true' : 'false'; ?>;
                  const showCaptcha = isStripe || isCartTotalZero;
                  $('.hcap_wc_checkout_captcha').toggle(showCaptcha);
              }

              $(document).on('change', 'input[name="payment_method"]', toggleCaptchaVisibility);
              $(document).ready(toggleCaptchaVisibility);
          });
      </script>
  <?php
      }
  });

  add_action('woocommerce_checkout_process', function() {
    if (WC()->cart->total == 0 || (isset($_POST['payment_method']) && stripos($_POST['payment_method'], 'stripe_cc') !== false)) {
      if(function_exists('hcaptcha_verify_post')) {
        $result = hcaptcha_verify_post();
        if (null !== $result) {
          wc_add_notice(__('Please verify that you are not a robot.', 'domain'), 'error');
        }
      }
    }
  });
//  function my_hcap_delay_api( $delay ) {
//    return 0;
//  }
//  add_filter( 'hcap_delay_api', 'my_hcap_delay_api' );
//}

//if (isset($_GET['vstdev'])) {
//  if ($_GET['vstdev'] === '1') {
//    // Set the cookie 'vstdev' to '1', with an expiration time of 1 hour from now
//    setcookie('vstdev', '1', time() + 3600, "/");
//  } elseif ($_GET['vstdev'] === '0') {
//    // Remove the cookie by setting its expiration time to the past
//    setcookie('vstdev', '', time() - 3600, "/");
//  }
//}

/* Used to display products on the promotion page */
  function get_products_by_params($params) {
    $show_future = isset($params['show_future']);
    $category = $params['category'] ?? 'deals';//array('store', 'never-expires');
    $product_tag = $params['product_tag'] ?? '';
    $sort_by = $params['sort_by'] ?? false;
    $min_price = filter_var(     $params['min_price'], FILTER_VALIDATE_BOOLEAN) ?? false;
    $max_price = filter_var(     $params['max_price'], FILTER_VALIDATE_BOOLEAN) ?? false;
    $show_prices = filter_var(     $params['show_prices'], FILTER_VALIDATE_BOOLEAN) ?? true;
    $paged = $params['paged'] ?? 1;
    $per_page = $params['per_page'] ?? 30;
    $search_query = $params['search_query'] ?? '';
    $search_query = trim($search_query);

//    if(isset($_COOKIE['kontest'])) {
//      echo '<pre>';
//      var_dump($params);
//    }

    ob_start();
    ?>
    <?php
    // Enable admins to see the page before deals are published
    if ($show_future && current_user_can('administrator')) {
      $post_statuses = array('publish', 'future');
    } else {
      $post_statuses = array('publish');
    }

    $args = array(
      'post_type' => 'product',
      'post_status' => $post_statuses,
      'posts_per_page' => $per_page,
      'paged' => $paged,
      'tax_query' => array(
        array(
          'taxonomy' => 'product_cat',
          'field' => 'slug',
          'terms' => $category
        ),
        array(
          'taxonomy' => 'product_tag',
          'field' => 'term_id',
          'terms' => $product_tag
        ),
      ),
      'meta_query' => array()
    );


    if (!empty($search_query)) {
      $args['s'] = $search_query;
    }

    if (!empty($sort_by)) {
      switch ($sort_by) {
//        case 'title-asc':
//          $args['orderby'] = 'title';
//          $args['order'] = 'ASC';
//          break;
//        case 'title-desc':
//          $args['orderby'] = 'title';
//          $args['order'] = 'DESC';
//          break;
      /* Products starts with like "85% off" so ordering by title equals ordering by discount */
        case 'discount-asc':
          $args['orderby'] = 'title';
          $args['order'] = 'ASC';
          break;
        case 'discount-desc':
          $args['orderby'] = 'title';
          $args['order'] = 'DESC';
          break;
        case 'price-asc':
          $args['meta_key'] = '_price';
          $args['orderby'] = 'meta_value_num';
          $args['order'] = 'ASC';
          break;
        case 'price-desc':
          $args['meta_key'] = '_price';
          $args['orderby'] = 'meta_value_num';
          $args['order'] = 'DESC';
          break;
//        case 'discount-asc':
//          $args['meta_key'] = '_discount';
//          $args['orderby'] = 'meta_value_num';
//          $args['order'] = 'ASC';
//          break;
//        case 'discount-desc':
//          $args['meta_key'] = '_discount';
//          $args['orderby'] = 'meta_value_num';
//          $args['order'] = 'DESC';
//          break;
        case 'newest':
          $args['orderby'] = 'date';
          $args['order'] = 'DESC';
          break;
        case 'random':
          $args['orderby'] = 'rand';
          break;
      }
    }

    if ($min_price && $max_price) {
      $args['meta_query'][] = array(
        'key' => '_price',
        'value' => array($min_price, $max_price),
        'type' => 'numeric',
        'compare' => 'BETWEEN'
      );
    } else if($min_price) {
      $args['meta_query'][] = array(
        'key' => '_price',
        'value' => $min_price,
        'type' => 'numeric',
        'compare' => '>='
      );
    } else if($max_price) {
      $args['meta_query'][] = array(
        'key' => '_price',
        'value' => $max_price,
        'type' => 'numeric',
        'compare' => '<='
      );
    }
//    if(isset($_COOKIE['kontest'])) {
//      echo '<pre>';
//      var_dump($params, $args);
//    }

    $transient_key = 'products_' . md5(serialize($args));

    $products = get_transient($transient_key);

    if ($products === false) {
      $the_query = new WP_Query($args);

      ?>
      <?php
      $products = array();

      if ($the_query->have_posts()) {
        while ($the_query->have_posts()) {
          $the_query->the_post();

          // Check if the flexible content field has rows of data
          if (have_rows('content_blocks')) {
            // Loop through the rows of data
            while (have_rows('content_blocks')) {
              the_row();

              if (get_row_layout() == 'intro') {
                $prices = '';
                if ($show_prices) {
                  ob_start();
                  vstbuzz_show_prices('promo', false);
                  $prices = ob_get_contents();
                  ob_end_clean();
                }

                $title = trim(get_the_title());
                $cleanTitle = preg_replace('/\d+% off/', '', $title);
                $cleanTitle = trim(str_ireplace(array('&#8220;', '&#8221;', '“', '”', '"', '\''), '', $cleanTitle));
                $content_tab_off = get_field('content_tab_off');
                $content_tab_off = '&ndash; ' . trim(str_ireplace('off', '', $content_tab_off));

                $products[$cleanTitle] = array(
                  'title' => get_the_title(),
                  'link' => get_permalink(),
                  'image' => wp_get_attachment_url(get_post_thumbnail_id(get_the_ID())),
                  'prices' => $prices,
                  'description' => wp_trim_words(get_sub_field('description'), 40),
                  'off' => $content_tab_off
                );
              }
            }
          }

          wp_reset_postdata();
        }
        set_transient($transient_key, $products, HOUR_IN_SECONDS);
      }
    }


    if ($sort_by === 'title-asc') {
      ksort($products);
    } elseif ($sort_by === 'title-desc') {
      krsort($products);
    }
//    if(isset($_COOKIE['kontest'])) {
//      VAR_DUMP($products);
//    }
    ?>

    <?php if (!empty($products)) { ?>
      <?php foreach ($products as $product) { ?>
        <div class="the-vault-item the-vault-item_promotion">
          <a href="<?php echo $product['link']; ?>" class="the-vault-item__off">
            <span class="the-vault-item__off-text">
            <?php echo $product['off']; ?>
            </span>
          </a>
          <a href="<?php echo $product['link']; ?>" class="the-vault-item__link">
            <img src="<?php echo $product['image']; ?>?featured" alt="<?php echo $product['title']; ?>" class="the-vault-item__link-image"/>
          </a>
          <a href="<?php echo $product['link']; ?>" class="the-vault-product-title"><?php echo $product['title']; ?></a>
          <?php if ($show_prices) { ?>
            <div class="the-vault__prices">
              <?php echo $product['prices']; ?>
            </div>
          <?php } ?>
         <?php /* <div class="the-vault-item__description">
            <?php echo $product['description']; ?>
          </div>*/?>
          <a class='the-vault-cta button' href='<?php echo $product['link']; ?>'>More info</a>
        </div>
      <?php } ?>
    <?php } else { ?>
      <p><?php _e('Sorry, no products matched your criteria.'); ?></p>
    <?php } ?>

    <nav class="pagination woo-pagination">
      <?php
      $big = 999999999; // need an unlikely integer

      echo paginate_links(array(
        'base' => str_replace($big, '%#%', esc_url(get_pagenum_link($big))),
        'format' => '?paged=%#%',
        'current' => max(1, get_query_var('paged')),
        'total' => $the_query->max_num_pages
      ));
      ?>
    </nav>
<?php
    $result = ob_get_contents();
    ob_end_clean();
    return $result;
  }

function get_filtered_products() {
  $params = array(
    'show_future' => isset($_POST['show_future']),
    'category' => $_POST['category'] ?? 'deals',
    'product_tag' => $_POST['product_tag'] ?? '',
    'sort_by' => $_POST['sort_by'] ?? false,
    'min_price' => $_POST['min_price'] ?? false,
    'max_price' => $_POST['max_price'] ?? false,
    'show_prices' => $_POST['show_prices'] ?? true,
    'paged' => $_POST['paged'] ?? 1,
    'search_query' => $_POST['search_query'] ?? '',
    'per_page' => $_POST['per_page'] ?? ''
  );

  $html = get_products_by_params($params);
  echo $html;
  wp_die(); // this is required to terminate immediately and return a proper response
}
add_action('wp_ajax_get_filtered_products', 'get_filtered_products');
add_action('wp_ajax_nopriv_get_filtered_products', 'get_filtered_products');

add_action('get_header', function ($name) {
    if ($name === 'dark') {
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_style(
                'header-dark-style',
                get_theme_file_uri('/css/header-dark.css'),
                [],
                filemtime(get_theme_file_path('/css/header-dark.css'))
            );
        });
    }
});
