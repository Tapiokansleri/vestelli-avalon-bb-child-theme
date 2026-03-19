<?php
/**
 * Frontend template for Stats Numbers module
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

// Get stats
$stats = ! empty( $settings->stats ) ? $settings->stats : array();

// If no stats, show message
if ( empty( $stats ) ) {
  echo '<p class="avalon-stats-empty">' . __( 'Ei tilastolukuja lisätty.', 'vestelli' ) . '</p>';
  return;
}

// Get settings
$show_title = ! empty( $settings->show_title ) && $settings->show_title === 'yes';
$title_text = ! empty( $settings->title_text ) ? $settings->title_text : '';

// Get color settings - use new field names with defaults
$background_color = ! empty( $settings->stat_bg_color ) ? $settings->stat_bg_color : 'ffffff';
$title_color = ! empty( $settings->stat_title_color ) ? $settings->stat_title_color : '012b55';
$subtitle_color = ! empty( $settings->stat_subtitle_color ) ? $settings->stat_subtitle_color : '012b55';
$description_color = ! empty( $settings->stat_desc_color ) ? $settings->stat_desc_color : '333333';
$icon_color = ! empty( $settings->stat_icon_color ) ? $settings->stat_icon_color : '012b55';

// Add # prefix if not already present (BB stores colors without #)
if ( ! empty( $background_color ) && strpos( $background_color, '#' ) !== 0 ) {
  $background_color = '#' . $background_color;
}
if ( ! empty( $title_color ) && strpos( $title_color, '#' ) !== 0 ) {
  $title_color = '#' . $title_color;
}
if ( ! empty( $subtitle_color ) && strpos( $subtitle_color, '#' ) !== 0 ) {
  $subtitle_color = '#' . $subtitle_color;
}
if ( ! empty( $description_color ) && strpos( $description_color, '#' ) !== 0 ) {
  $description_color = '#' . $description_color;
}
if ( ! empty( $icon_color ) && strpos( $icon_color, '#' ) !== 0 ) {
  $icon_color = '#' . $icon_color;
}

// Output stats
?>
<div class="avalon-stats-wrapper" data-module-id="<?php echo esc_attr( $module->node ); ?>">
  <?php if ( $show_title && ! empty( $title_text ) ) : ?>
    <h2 class="avalon-stats-title" style="color: <?php echo esc_attr( $title_color ); ?>;"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>
  
  <div class="avalon-stats-container">
    <?php foreach ( $stats as $stat ) : ?>
      <?php
      $media_type = ! empty( $stat->media_type ) ? $stat->media_type : 'icon';
      $icon = ! empty( $stat->icon ) ? $stat->icon : '';
      $image = ! empty( $stat->image ) ? $stat->image : '';
      $title = ! empty( $stat->title ) ? esc_html( $stat->title ) : '';
      $subtitle = ! empty( $stat->subtitle ) ? esc_html( $stat->subtitle ) : '';
      $description = ! empty( $stat->description ) ? wp_kses_post( $stat->description ) : '';
      
      if ( empty( $title ) ) {
        continue; // Skip incomplete stats
      }
      ?>
      <div class="avalon-stat-card-wrap">
        <div class="avalon-stat-item" style="background-color: <?php echo esc_attr( $background_color ); ?> !important;">
          <?php if ( $media_type === 'icon' && $icon ) : ?>
            <div class="avalon-stat-media avalon-stat-icon" style="color: <?php echo esc_attr( $icon_color ); ?> !important;">
              <i class="<?php echo esc_attr( $icon ); ?>"></i>
            </div>
          <?php elseif ( $media_type === 'image' && $image ) : ?>
            <div class="avalon-stat-media avalon-stat-image">
              <?php
              $image_url = is_numeric( $image ) ? wp_get_attachment_image_url( $image, 'large' ) : $image;
              if ( $image_url ) :
              ?>
                <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $title ); ?>">
              <?php endif; ?>
            </div>
          <?php endif; ?>
          
          <div class="avalon-stat-content">
            <div class="avalon-stat-title" style="color: <?php echo esc_attr( $title_color ); ?> !important;"><?php echo $title; ?></div>
            <?php if ( $subtitle ) : ?>
              <div class="avalon-stat-subtitle" style="color: <?php echo esc_attr( $subtitle_color ); ?> !important;"><?php echo $subtitle; ?></div>
            <?php endif; ?>
            <?php if ( $description ) : ?>
              <div class="avalon-stat-description" style="color: <?php echo esc_attr( $description_color ); ?> !important;"><?php echo $description; ?></div>
            <?php endif; ?>
          </div>
          <div class="avalon-stat-wave">
            <img src="<?php echo esc_url( content_url( '/uploads/2026/03/element_wave_03-scaled.png' ) ); ?>" alt="" loading="lazy">
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
