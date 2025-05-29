<?php
/**
 * Single Product title
 *
 * @author    WooThemes
 * @package  WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly
global $product;
$productID = $product->get_ID();

$currencySymbol = get_woocommerce_currency_symbol();

$product_prices = vstbuzz_get_product_prices( $product );
/**
 * @var number $sale_price
 * @var number $regular_price
 * @var number $save_price
 */
extract( $product_prices );

// fix empty price for the bundled item
if($sale_price === '0') {
	$bundle_sale_price = probably_get_bundled_sale_price($productID, $sale_price);
	if ( $bundle_sale_price ) {
		$sale_price = $bundle_sale_price;
		$save_price = $regular_price - $sale_price;
	}
}

$save_price = round($save_price, 2);

if($sale_price === 'free'){
	$sale_price = 0;
}
$you_save_percent = round( ( $save_price / $regular_price ) * 100 ) . '%';
$points = round(WC_Points_Rewards_Product::get_points_earned_for_product_purchase($product));
$live_deal = vstbuzz_is_product_live( $productID );

if ( is_product() && has_term( 'competitions', 'product_cat' ) ) {
  include ('title-competitions.php');
} else {
  include ('title-common.php');
}