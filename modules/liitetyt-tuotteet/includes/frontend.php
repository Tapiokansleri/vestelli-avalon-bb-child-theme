<?php
/**
 * Frontend template for Liitetyt tuotteet module
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
  return;
}

// Get current product
global $product;

// If not on product page, try to get product from post
if ( ! $product && is_singular( 'product' ) ) {
  $product = wc_get_product( get_the_ID() );
}

// If still no product, try to get from global post
if ( ! $product && isset( $GLOBALS['post'] ) ) {
  $product = wc_get_product( $GLOBALS['post']->ID );
}

// Settings are passed from render method
if ( ! isset( $settings ) ) {
  $settings = isset( $module ) ? $module->settings : (object) array();
}

// Get product type (upsell or cross_sell)
$product_type = ! empty( $settings->product_type ) ? $settings->product_type : 'cross_sell';

// Get product IDs based on type
$product_ids = array();

if ( $product && is_a( $product, 'WC_Product' ) ) {
  if ( $product_type === 'upsell' ) {
    $product_ids = $product->get_upsell_ids();
  } else {
    $product_ids = $product->get_cross_sell_ids();
  }
}

// If no products found, don't show the module
if ( empty( $product_ids ) ) {
  return;
}

// Get title settings
$show_title = ! empty( $settings->show_title ) && $settings->show_title === 'yes';
$title_text = ! empty( $settings->title_text ) ? $settings->title_text : '';

// Default titles
if ( empty( $title_text ) ) {
  if ( $product_type === 'upsell' ) {
    $title_text = __( 'Parempi tuote', 'vestelli' );
  } else {
    $title_text = __( 'Lisätuote', 'vestelli' );
  }
}

// Get products
$products = array();
foreach ( $product_ids as $product_id ) {
  $linked_product = wc_get_product( $product_id );
  if ( $linked_product && $linked_product->is_visible() ) {
    $products[] = $linked_product;
  }
}

// If no visible products found, don't show the module
if ( empty( $products ) ) {
  return;
}

// Calculate columns based on product count
$product_count = count( $products );
if ( $product_count === 2 ) {
  $columns = 2; // 50% 50%
} elseif ( $product_count === 3 ) {
  $columns = 3; // 33% each
} else {
  $columns = 4; // 25% each (4 or more products)
}

// Output products
?>
<div class="avalon-linked-products avalon-linked-products-<?php echo esc_attr( $product_type ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>">
  <?php if ( $show_title ) : ?>
    <h2 class="avalon-linked-products-title"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>
  
  <ul class="avalon-linked-products-list products columns-<?php echo esc_attr( $columns ); ?>">
    <?php foreach ( $products as $linked_product ) : ?>
      <?php
      $post_object = get_post( $linked_product->get_id() );
      setup_postdata( $GLOBALS['post'] =& $post_object );
      
      // Start output buffering to wrap WooCommerce template
      ob_start();
      wc_get_template_part( 'content', 'product' );
      $product_html = ob_get_clean();
      
      // First, extract h2 from inside the link tag if it's there, and move it outside
      // WooCommerce sometimes puts h2 inside the link, we need it outside for our layout
      $product_html = preg_replace(
        '/(<a[^>]*class="[^"]*woocommerce-LoopProduct-link[^"]*"[^>]*>.*?)(<h2[^>]*class="[^"]*woocommerce-loop-product__title[^"]*"[^>]*>.*?<\/h2>)(.*?<\/a>)/s',
        '$1$3</a>$2',
        $product_html
      );
      
      // Now wrap everything after the image link closes (h2, price, button) in a flex container
      // The regex captures: <li>...image link...</a> then everything else until </li>
      $product_html = preg_replace(
        '/(<li[^>]*>.*?<\/a>\s*)(.*?)(<\/li>)/s',
        '$1<div class="avalon-product-content-wrapper">$2</div>$3',
        $product_html
      );
      
      echo $product_html;
      ?>
    <?php endforeach; ?>
    <?php wp_reset_postdata(); ?>
  </ul>
</div>
