<?php
/**
 * Subscription Product title
 * 
 * Only for subscription created within "WooCommerce Subscriptions" plugin
 *
 * @author    WooThemes
 * @package  WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>

<div class="summary product-hero">
	<div class="wrapper product-hero__wrap">
		<div class="product-hero__gallery">
			<div class="product-hero__gallery-image-wrap">
				<?php
				$image = wp_get_attachment_url( get_post_thumbnail_id( $productID ) );
				if($image) {
					?>
					<img src="<?php echo $image; ?>?featured" alt="<?php the_title(); ?>" class="product-hero__gallery-image"/>
					<?php
				}
				echo vst_get_tab_off('product-hero__gallery-image-off');
				?>
			</div>
			
		</div>
		<div class="product-hero__content">
			<h1 itemprop="name" class="product_title entry-title product-hero__title"><?php the_title(); ?></h1>
			
			<div class="product-hero__description" style="margin-top: 40px;">
				<?php

				$short_description = $product->get_short_description();

				if ( ! empty( $short_description ) ) {
					echo '<div class="product-short-description">' . wp_kses_post($short_description) . '</div>';
				}
				?>
			</div>
			<?php
			

			/**
			 * woocommerce_before_single_product hook
			 *
			 * @hooked wc_print_notices - 10
			 */
			do_action( 'woocommerce_before_single_product' );

			if ( $product->is_in_stock() && class_exists( 'WC_Subscriptions_Product' ) ) {
				do_action( 'woocommerce_before_add_to_cart_form' ); 
				
				
				$subscription_price = $product->get_price();

        // Get the subscription period
        $subscription_period = WC_Subscriptions_Product::get_period($product);

        // Get the subscription interval
        $subscription_interval = WC_Subscriptions_Product::get_interval($product);

        // Display the subscription price and period
		?>

        <div class="subscription-details" style=" font-size: 1.3em; color: white;">
			<p>Price: <strong><?php echo wc_price($subscription_price); ?></strong></p>
			<p>Every <?php echo $subscription_interval . ' ' . $subscription_period; ?></p>
        </div>
		
	
			  <form class="cart" method="post" enctype='multipart/form-data'>
				  <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
				<div class="button-wrap">
				<input type="hidden" name="add-to-cart" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
				<button type="submit" class="single_add_to_cart_button button alt add-to-cart">
					<?php
						echo '<span class="add-to-cart__text">' . __( 'Subscribe Now', 'vstbuzz' ) . '</span>';
					} ?>
				</button>
				  <?php
				  do_action( 'woocommerce_after_add_to_cart_button' );
				  ?>
				</div>
			  </form>
			
			  <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>
			
		</div>
	</div><!-- .wrapper -->
	
</div>
