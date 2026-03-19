<?php
/**
 * Frontend template for Liittyvät tuotteet module
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
  echo '<p>' . __( 'WooCommerce ei ole aktiivinen.', 'vestelli' ) . '</p>';
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

// If no product found, show message
if ( ! $product || ! is_a( $product, 'WC_Product' ) ) {
  echo '<p class="avalon-related-products-empty">' . __( 'Tuotetta ei löytynyt.', 'vestelli' ) . '</p>';
  return;
}

// Get current product ID
$current_product_id = $product->get_id();

// Get product category terms (not just IDs, we need parent info)
$category_terms = wp_get_post_terms( $current_product_id, 'product_cat', array( 'fields' => 'all' ) );

// If no categories found, show message
if ( empty( $category_terms ) || is_wp_error( $category_terms ) ) {
  echo '<p class="avalon-related-products-empty">' . __( 'Ei liittyviä tuotteita.', 'vestelli' ) . '</p>';
  return;
}

// Find the top-level category (osasto) - parent = 0
$top_level_category_id = null;
foreach ( $category_terms as $term ) {
  if ( $term->parent == 0 ) {
    $top_level_category_id = $term->term_id;
    break; // Use first top-level category found
  }
}

// If no top-level category found, try to get parent of first category
if ( ! $top_level_category_id && ! empty( $category_terms ) ) {
  $first_term = $category_terms[0];
  if ( $first_term->parent > 0 ) {
    // Get all ancestors and find the top one
    $ancestors = get_ancestors( $first_term->term_id, 'product_cat' );
    if ( ! empty( $ancestors ) ) {
      $top_level_category_id = end( $ancestors ); // Get the top ancestor
    }
  } else {
    $top_level_category_id = $first_term->term_id;
  }
}

// If still no top-level category, show message
if ( ! $top_level_category_id ) {
  echo '<p class="avalon-related-products-empty">' . __( 'Ei liittyviä tuotteita.', 'vestelli' ) . '</p>';
  return;
}

// Query related products from same top-level category (osasto)
// This includes all subcategories of the top-level category
$args = array(
  'post_type'      => 'product',
  'posts_per_page' => 4,
  'post__not_in'   => array( $current_product_id ),
  'post_status'    => 'publish',
  'orderby'        => 'rand', // Random order
  'tax_query'      => array(
    array(
      'taxonomy' => 'product_cat',
      'field'    => 'term_id',
      'terms'    => $top_level_category_id,
      'include_children' => true, // Include all subcategories
    ),
  ),
);

$related_query = new WP_Query( $args );

// Get products from query
$products = array();
if ( $related_query->have_posts() ) {
  while ( $related_query->have_posts() ) {
    $related_query->the_post();
    $related_product = wc_get_product( get_the_ID() );
    if ( $related_product && $related_product->is_visible() ) {
      $products[] = $related_product;
    }
  }
  wp_reset_postdata();
}

if ( empty( $products ) ) {
  echo '<p class="avalon-related-products-empty">' . __( 'Ei liittyviä tuotteita.', 'vestelli' ) . '</p>';
  return;
}

// Get title settings
$show_title = ! empty( $settings->show_title ) && $settings->show_title === 'yes';
$title_text = ! empty( $settings->title_text ) ? $settings->title_text : __( 'Liittyvät tuotteet', 'vestelli' );

// Calculate columns based on product count
$product_count = count( $products );
if ( $product_count === 2 ) {
  $columns = 2; // 50% 50%
} elseif ( $product_count === 3 ) {
  $columns = 3; // 33% each
} else {
  $columns = 4; // 25% each (4 products)
}

// Output products
?>
<div class="avalon-related-products" data-columns="<?php echo esc_attr( $columns ); ?>">
  <?php if ( $show_title ) : ?>
    <h2 class="avalon-related-products-title"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>
  
  <ul class="avalon-related-products-list products columns-<?php echo esc_attr( $columns ); ?>">
    <?php foreach ( $products as $related_product ) : ?>
      <?php
      $post_object = get_post( $related_product->get_id() );
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
