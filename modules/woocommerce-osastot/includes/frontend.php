<?php
/**
 * Frontend template for WooCommerce Osastot module
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

// Settings are passed from render method
if ( ! isset( $settings ) ) {
  $settings = isset( $module ) ? $module->settings : (object) array();
}

// Get settings with defaults
$show_title = isset( $settings->show_title ) ? $settings->show_title : 'yes';
$title_text = isset( $settings->title_text ) && ! empty( $settings->title_text ) ? $settings->title_text : __( 'Osastot', 'vestelli' );
$columns = isset( $settings->columns ) ? intval( $settings->columns ) : 4;

// Get top-level product categories (parent = 0)
$categories = get_terms( array(
  'taxonomy'   => 'product_cat',
  'hide_empty' => true,
  'parent'     => 0,
  'orderby'    => 'name',
  'order'      => 'ASC',
) );

// If no categories found, show message
if ( empty( $categories ) || is_wp_error( $categories ) ) {
  echo '<p class="avalon-osastot-empty">' . __( 'Osastoja ei löytynyt.', 'vestelli' ) . '</p>';
  return;
}

// Calculate column width percentage
$column_width = 100 / $columns;
?>

<div class="avalon-osastot-wrapper">
  <?php if ( $show_title === 'yes' && ! empty( $title_text ) ) : ?>
    <h2 class="avalon-osastot-title"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>
  
  <div class="avalon-osastot-grid avalon-osastot-columns-<?php echo esc_attr( $columns ); ?>" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
    <?php foreach ( $categories as $category ) : 
      $category_link = get_term_link( $category );
      $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
      $image_url = '';
      
      if ( $thumbnail_id ) {
        $image_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );
      }
      
      // Fallback to placeholder if no image
      if ( ! $image_url ) {
        $image_url = wc_placeholder_img_src( 'medium' );
      }
      
      if ( is_wp_error( $category_link ) ) {
        continue;
      }
      ?>
      <div class="avalon-osasto-item">
        <a href="<?php echo esc_url( $category_link ); ?>" class="avalon-osasto-link">
          <?php if ( $image_url ) : ?>
            <div class="avalon-osasto-image">
              <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" />
            </div>
          <?php endif; ?>
          <div class="avalon-osasto-content">
            <h3 class="avalon-osasto-name"><?php echo esc_html( $category->name ); ?></h3>
            <?php if ( $category->count > 0 ) : ?>
              <span class="avalon-osasto-count"><?php echo esc_html( $category->count ); ?> <?php echo _n( 'tuote', 'tuotetta', $category->count, 'vestelli' ); ?></span>
            <?php endif; ?>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
