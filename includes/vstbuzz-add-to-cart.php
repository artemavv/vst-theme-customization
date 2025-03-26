<?php

/*-----------------------------------------------------------------------------------*/
/* Product Page: Show the add-to-cart/buy-now button */
/* Some of this code originally from add-to-cart/simple.php */
/*-----------------------------------------------------------------------------------*/

function vstbuzz_add_to_cart( $position, $middle_heading = "", $middle_description = "" ) {
	global $product;
	$productID = $product->get_ID();

	$sym            = get_woocommerce_currency_symbol();
	$product_prices = vstbuzz_get_product_prices( $product );
	/**
	 * @var number $sale_price
	 * @var number $regular_price
	 * @var number $save_price
	 */
	extract( $product_prices );

	$live_deal = vstbuzz_is_product_live( $productID );
	if ( ! $live_deal ) {
		// Don't show the deal expired button if this is the middle section...
		?>
      <span class="game-over">Deal Expired</span>
		<?php
	}

	if ( ! $live_deal ) {
		expired_subscribe();
	}

	// If the deal is live...
	if ( $live_deal ) {
		// Availability
		$availability      = $product->get_availability();
		$availability_html = empty( $availability['availability'] ) ? '' : '<p class="stock ' . esc_attr( $availability['class'] ) . '">' . esc_html( $availability['availability'] ) . '</p>';
		echo apply_filters( 'woocommerce_stock_html', $availability_html, $availability['availability'], $product );
		if ( $product->is_in_stock() ) {
			do_action( 'woocommerce_before_add_to_cart_form' ); ?>

          <form class="cart" method="post" enctype='multipart/form-data'>
			  <?php do_action( 'woocommerce_before_add_to_cart_button' );

			  if ( ! $product->is_sold_individually() ) {
				  woocommerce_quantity_input( array(
					  'min_value' => apply_filters( 'woocommerce_quantity_input_min', 1, $product ),
					  'max_value' => apply_filters( 'woocommerce_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
				  ) );
			  }
			  ?>
            <div class="button-wrap">
            <input type="hidden" name="add-to-cart" value="<?php echo esc_attr( get_the_ID() ); ?>"/>
            <button type="submit" class="single_add_to_cart_button button alt add-to-cart">
				<?php
				if ( vstbuzz_product_has_category( $prod_id, 'special-free' ) ) {
					echo '<span class="add-to-cart__text">' . __( 'Download Now', 'vstbuzz' ) . '</span>';
				} else if ( vstbuzz_product_has_category( $prod_id, 'competitions' ) ) {
					echo '<span class="add-to-cart__text add-to-cart__text_competitions">' . __( 'Enter for', 'vstbuzz' ) . ' <span class="price" data-original-price="' . $sale_price . '">' . $sym . $sale_price . '</span></span>';
				} else {
					echo '<span class="add-to-cart__text">' . __( 'Buy Now', 'vstbuzz' ) . ' <span class="price">' . $sym . $sale_price . ' </span></span>';
					echo '<span class="add-to-cart__comment">' . __( 'Instant Download', 'vstbuzz' ) . '</span>';
				} ?>
            </button>
            <?php if ( !vstbuzz_product_has_category( $prod_id, 'special-free' ) ) { /*?>
              <p class="product-hero__comments"><?php echo __('Excluding EU VAT', 'vstbuzz'); ?></p>
              <div class="product-currencies">
                <?php show_alt_currencies(); ?>
              </div>
            <?php */ } ?>
			  <?php
			  do_action( 'woocommerce_after_add_to_cart_button' );
			  ?>
            </div>
          </form>
			<?php do_action( 'woocommerce_after_add_to_cart_form' );
		}
	}
}
