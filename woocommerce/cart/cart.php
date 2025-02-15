<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.9.0
 */

defined( 'ABSPATH' ) || exit;

$points_value           = WC_Points_Rewards_Manager::get_users_points_value( get_current_user_id() );
$points_total           = WC_Points_Rewards_Manager::get_users_points( get_current_user_id() );
$available_points_value = $points_value;

?>
<header id="page-header">
  <div class="wrapper">
    <h1>Complete your purchase</h1>
  </div>
</header>

<div class="wrapper wrapper_cart">
	<?php echo vst_cart_progress( 'details' ); ?>
  <div class="notice-wrapper"><?php wc_print_notices(); ?></div>

	<?php do_action( 'woocommerce_before_cart' ); ?>

  <form class="woocommerce-cart-form" action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	  <?php do_action( 'woocommerce_before_cart_table' ); ?>

    <h3>Your Shopping Cart</h3>

    <ul class="shop_table cart">
      <li class="header"><span class="product">Product</span><span class="header-total">Price</span></li>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
		$total_savings = 0.00;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product                      = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id                    = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
			$meta_box_tech_note            = get_post_meta( $product_id, "meta-box-tech-note", true );
			$meta_box_tech_note_additional = get_post_meta( $product_id, "meta-box-tech-note-additional", true );
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				$css_class         = esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) );
				?>
              <li class="<?php echo $css_class ?>">
				  <?php
				  echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf( '<a href="%s" class="cart_item__remove remove" title="%s" aria-label="%s" data-product_id="%s" data-product_sku="%s"><img src="' . get_bloginfo( 'stylesheet_directory' ) . '/images/bin.svg" alt="' . __( 'Remove this item', 'woocommerce' ) . '" /></a>', esc_url( wc_get_cart_remove_url( $cart_item_key ) ), esc_html__( 'Remove this item', 'woocommerce' ), esc_html__( 'Remove this item', 'woocommerce' ),
					  esc_attr( $product_id ),
					  esc_attr( $_product->get_sku() ) ), $cart_item_key );
				  ?>
                <div class="cart_item__thumbnail">
					<?php
					//                    $thumbnail = '<img src="' . wp_get_attachment_url( get_post_thumbnail_id( $cart_item['product_id'] ) ) . '?featured" alt="Your product" />';
					$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );
					if ( ! $product_permalink ) {
						echo $thumbnail;
					} else {
						printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
					}
					?>
                </div>
                <div class="cart_item__name" data-title="<?php esc_attr_e( 'Product', 'woocommerce' ); ?>">
					<?php
					if ( ! $product_permalink ) {
						echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key ) . '&nbsp;' );
					} else {
						// If it's a bundled item, we don't want to link to it from the cart...
						if ( strpos( $css_class, 'bundled_table_item' ) !== false ) {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '%s', $_product->get_name() ), $cart_item, $cart_item_key ) );
						} else {
							echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', $_product->get_permalink(), $_product->get_name() ), $cart_item, $cart_item_key ) );
						}
					}

					if ( $meta_box_tech_note || $meta_box_tech_note_additional ) {
						echo "<span class='tech-note-text'>*</span>";
					}

					do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

					// Meta data
					echo wc_get_formatted_cart_item_data( $cart_item ); // PHPCS: XSS ok.

					// Backorder notification
					if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
						echo wp_kses_post( apply_filters( 'woocommerce_cart_item_backorder_notification', '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>', $product_id ) );
					}
					?>
                </div>
				  <?php
				  $product_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
				  $sym           = get_woocommerce_currency_symbol();
          $product_prices = vstbuzz_get_product_prices( $_product );
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
                    <?php if ( true || strpos( $css_class, 'bundle_table_item' ) === false ) { ?>
					<?php
					$cart_item_price         = '';
					$cart_item_price_comment = '';
					$cart_item_real_value    = '';
					$cart_item_you_save      = '';
					if ( ! vstbuzz_product_has_category( $product_id, 'the-vault' ) ) {
						$cart_item_price      = $product_price;
						$cart_item_real_value = $sym . number_format( $regular_price, 2 );
            if($save_price > 0) {
	            $cart_item_you_save = $sym . number_format( $save_price, 2 );
            }
					} else {
						if ( $available_points_value >= $regular_price ) {
							$points_to_spend         = $regular_price * 10;
							$cart_item_price         = "FREE*";
							$cart_item_price_comment .= 'Use <b>' . $points_to_spend . '</b> Reward Points at checkout. <b>Full price: ' . $product_price . '</b>';
							$available_points_value  = $available_points_value - $regular_price;
						} elseif ( $available_points_value > 0 ) {
							$points_to_spend         = min( $regular_price, $available_points_value ) * 10;
							$new_price               = $regular_price - $available_points_value;
							$cart_item_price         = $sym . number_format( $new_price, 2 ) . "*";
							$cart_item_price_comment = 'Use <b>' . $points_to_spend . '</b> Reward Points at checkout. <b>Full price: ' . $product_price . '</b>';
							$available_points_value  = $available_points_value - $regular_price;
						} else {
							$cart_item_price         = $sym . number_format( $regular_price, 2 ) . "*";
							$cart_item_price_comment = 'You don\'t have enough Reward Points to get a discount on this product.';
						}
						$available_points_value = $available_points_value > 0 ? $available_points_value : 0.0;
					}
					if ( ! empty( $cart_item_real_value ) ) {
						$cart_item_real_value = '<span>' . __( 'Real Value:', 'vstbuzz' ) . '</span>' . $cart_item_real_value;
					}
					if ( ! empty( $cart_item_you_save ) ) {
						$cart_item_you_save = '<span>' . __( 'You Save:', 'vstbuzz' ) . '</span>' . $cart_item_you_save;
					}
					?>
                  <div class="cart_item__total-savings">
                    <div class="cart_item__total-savings-real"><?php echo $cart_item_real_value; ?></div>
                    <div class="cart_item__total-savings-save"><?php echo $cart_item_you_save; ?></div>
                  </div>
                  <div class="cart_item__total-price">
					  <?php echo $cart_item_price; ?>
                  </div>
                  <?php } ?>
                </div>
				  <?php
				  if ( $cart_item_price_comment ) { ?>
                    <div class="cart_item__tech-note"><span class='cart_item__tech-note-star'>*</span>
						<?php
						echo $cart_item_price_comment;
						?>
                    </div>
					  <?php
				  }
				  if ( $meta_box_tech_note ) {
					  ?>
                    <div class="cart_item__tech-note">
						<?php
						$tech_note_text = $_product->get_title() . ' ' . '<span class=\'cart_item__tech-note-text tech-note__text\'>' . get_post_meta( $product_id, "meta-box-tech-note", true ) . '</span>';
						echo "<span class='cart_item__tech-note-star'>*</span> <strong>Please Note: </strong>" . $tech_note_text;
						?>
                    </div>
					  <?php
				  }

				  if ( $meta_box_tech_note_additional ) {
					  ?>
                    <div class="cart_item__tech-note">
						<?php
						echo "<span class='cart_item__tech-note-text tech-note__text'>$meta_box_tech_note_additional</span>";
						?>
                    </div>
					  <?php
				  }
				  ?>
              </li>
				<?php
			}
		} ?>
    </ul>


	  <?php do_action( 'woocommerce_cart_contents' ); ?>
	  <?php do_action( 'woocommerce_after_cart_contents' ); ?>
	  <?php do_action( 'woocommerce_after_cart_table' ); ?>
	  <?php do_action( 'woocommerce_cart_actions' ); ?>

	  <?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
    <div class="under-cart">
      <div class="under-cart__col under-cart__col_1">
		  <?php do_action( 'woocommerce_wc_points_rewards' ); ?>
        <a href="/" class="under-cart__button under-cart__button-continue">Continue shopping</a>
      </div>
      <div class="under-cart__col under-cart__col_2">
        <div class="under-cart__col-wrap">
			<?php
			$spent_points_value = $points_value - $available_points_value;
			$spent_points       = $spent_points_value * 10;
			?>
          <div class="order-total">Order Total: <strong><?php echo WC()->cart->get_cart_total(); ?></strong></div>

			<?php if ( $spent_points > 0 ) { ?>
              <div class="order-total-points">( <?php
				  $money_to_pay = WC()->cart->get_subtotal() - $spent_points / 10;
				  echo $sym . number_format( $money_to_pay, 2 );
				  echo " + $spent_points Reward Points";
				  ?> )
              </div>
			<?php } else {
				$money_to_pay = WC()->cart->get_subtotal();
			}

			$new_points_earned         = number_format( $money_to_pay / 2, 0 );
			$new_points_earned_message = "Complete your order and earn <strong>$new_points_earned</strong> Reward Points to use on products from The Vault";
			?>
          <script>
            jQuery('.wc_points_rewards_earn_points').html("<?php echo $new_points_earned_message; ?>");
          </script>
          <div class="total-savings">Total Savings: <?php echo $sym . number_format( $total_savings + $spent_points_value, 2 ); ?></div>
        </div>
        <a class="checkout-button under-cart__button under-cart__button-checkout" href="/checkout/">Proceed to Payment</a>
      </div>
    </div>
  </form>
	<?php
	// 30 deals will be returned by the function
	// and then we'll filter 8 live deals from it in the woocommerce/cart/cross-sells.php theme file
	woocommerce_cross_sell_display( 50, 1 );
	?>
	<?php do_action( 'woocommerce_after_cart' ); ?>
  <div class="clear"></div>
</div><!-- /.wrapper -->
