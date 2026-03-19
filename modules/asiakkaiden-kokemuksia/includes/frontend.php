<?php
/**
 * Frontend template for Asiakkaiden kokemuksia module
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Settings are passed from render method
if ( ! isset( $settings ) ) {
  $settings = isset( $module ) ? $module->settings : (object) array();
}

// Get testimonials
$testimonials = ! empty( $settings->testimonials ) ? $settings->testimonials : array();

// If no testimonials, show message
if ( empty( $testimonials ) ) {
  echo '<p class="avalon-testimonials-empty">' . __( 'Ei testimoniaaleja lisätty.', 'vestelli' ) . '</p>';
  return;
}

// Get settings
$show_title = ! empty( $settings->show_title ) && $settings->show_title === 'yes';
$title_text = ! empty( $settings->title_text ) ? $settings->title_text : __( 'Asiakkaiden kokemuksia', 'vestelli' );
$columns = ! empty( $settings->columns ) ? absint( $settings->columns ) : 3;

// Limit columns to valid range
if ( $columns < 1 ) {
  $columns = 1;
} elseif ( $columns > 4 ) {
  $columns = 4;
}

// Output testimonials
?>
<div class="avalon-testimonials" data-columns="<?php echo esc_attr( $columns ); ?>">
  <?php if ( $show_title ) : ?>
    <h2 class="avalon-testimonials-title"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>
  
  <div class="avalon-testimonials-grid">
    <?php foreach ( $testimonials as $testimonial ) : ?>
      <?php
      $name = ! empty( $testimonial->name ) ? esc_html( $testimonial->name ) : '';
      $position = ! empty( $testimonial->position ) ? esc_html( $testimonial->position ) : '';
      $company = ! empty( $testimonial->company ) ? esc_html( $testimonial->company ) : '';
      $testimonial_text = ! empty( $testimonial->testimonial ) ? wp_kses_post( $testimonial->testimonial ) : '';
      $rating = ! empty( $testimonial->rating ) ? absint( $testimonial->rating ) : 0;
      $photo = ! empty( $testimonial->photo ) ? $testimonial->photo : '';
      $photo_url = '';
      
      if ( $photo && is_numeric( $photo ) ) {
        $photo_url = wp_get_attachment_image_url( $photo, 'thumbnail' );
      } elseif ( is_string( $photo ) ) {
        $photo_url = $photo;
      }
      
      if ( empty( $name ) || empty( $testimonial_text ) ) {
        continue; // Skip incomplete testimonials
      }
      ?>
      <div class="avalon-testimonial-item">
        <?php if ( $rating > 0 ) : ?>
          <div class="avalon-testimonial-rating">
            <?php for ( $i = 1; $i <= 5; $i++ ) : ?>
              <span class="star <?php echo $i <= $rating ? 'filled' : ''; ?>">★</span>
            <?php endfor; ?>
          </div>
        <?php endif; ?>
        
        <div class="avalon-testimonial-content">
          <?php echo $testimonial_text; ?>
        </div>
        
        <div class="avalon-testimonial-author">
          <?php if ( $photo_url ) : ?>
            <div class="avalon-testimonial-photo">
              <img src="<?php echo esc_url( $photo_url ); ?>" alt="<?php echo esc_attr( $name ); ?>">
            </div>
          <?php endif; ?>
          <div class="avalon-testimonial-info">
            <div class="avalon-testimonial-name"><?php echo $name; ?></div>
            <?php if ( $position || $company ) : ?>
              <div class="avalon-testimonial-meta">
                <?php if ( $position ) : ?>
                  <span class="position"><?php echo $position; ?></span>
                <?php endif; ?>
                <?php if ( $position && $company ) : ?>
                  <span class="separator">, </span>
                <?php endif; ?>
                <?php if ( $company ) : ?>
                  <span class="company"><?php echo $company; ?></span>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
