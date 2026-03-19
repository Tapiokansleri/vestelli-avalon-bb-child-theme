<?php
/**
 * Hero Block Render Template
 * 
 * @package Vestelli
 * 
 * @var array    $attributes Block attributes.
 * @var string   $content    Block default content.
 * @var WP_Block $block      Block instance.
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Ensure attributes is an array
if ( ! is_array( $attributes ) ) {
  $attributes = array();
}

// Get attributes with safe defaults
$title = isset( $attributes['title'] ) && ! empty( $attributes['title'] ) ? $attributes['title'] : '';
$description = isset( $attributes['description'] ) && ! empty( $attributes['description'] ) ? $attributes['description'] : '';
$background_image_url = isset( $attributes['backgroundImageUrl'] ) && ! empty( $attributes['backgroundImageUrl'] ) ? esc_url( $attributes['backgroundImageUrl'] ) : '';
$overlay_opacity = isset( $attributes['overlayOpacity'] ) && is_numeric( $attributes['overlayOpacity'] ) ? floatval( $attributes['overlayOpacity'] ) : 0.5;
$overlay_color = isset( $attributes['overlayColor'] ) && ! empty( $attributes['overlayColor'] ) ? esc_attr( $attributes['overlayColor'] ) : '#000000';
$text_color = isset( $attributes['textColor'] ) && ! empty( $attributes['textColor'] ) ? esc_attr( $attributes['textColor'] ) : '#ffffff';
$alignment = isset( $attributes['alignment'] ) && ! empty( $attributes['alignment'] ) ? esc_attr( $attributes['alignment'] ) : 'center';
$height = isset( $attributes['height'] ) && ! empty( $attributes['height'] ) ? esc_attr( $attributes['height'] ) : '600px';

// Button settings with safe defaults
$show_button1 = isset( $attributes['showButton1'] ) ? (bool) $attributes['showButton1'] : true;
$button1_text = isset( $attributes['button1Text'] ) && ! empty( $attributes['button1Text'] ) ? esc_html( $attributes['button1Text'] ) : 'Pyydä tarjous';
$button1_url = isset( $attributes['button1Url'] ) && ! empty( $attributes['button1Url'] ) ? esc_url( $attributes['button1Url'] ) : '/pyyda-tarjous';
$button1_open_new_tab = isset( $attributes['button1OpenNewTab'] ) ? (bool) $attributes['button1OpenNewTab'] : false;

$show_button2 = isset( $attributes['showButton2'] ) ? (bool) $attributes['showButton2'] : false;
$button2_text = isset( $attributes['button2Text'] ) && ! empty( $attributes['button2Text'] ) ? esc_html( $attributes['button2Text'] ) : 'Lue lisää';
$button2_url = isset( $attributes['button2Url'] ) && ! empty( $attributes['button2Url'] ) ? esc_url( $attributes['button2Url'] ) : '#';
$button2_open_new_tab = isset( $attributes['button2OpenNewTab'] ) ? (bool) $attributes['button2OpenNewTab'] : false;

// Convert hex color to rgba for overlay - safe function
if ( ! function_exists( 'avalon_hex_to_rgba' ) ) {
  function avalon_hex_to_rgba( $hex, $opacity ) {
    if ( empty( $hex ) || ! is_string( $hex ) ) {
      $hex = '#000000';
    }
    $hex = str_replace( '#', '', $hex );
    if ( strlen( $hex ) !== 6 ) {
      $hex = '000000';
    }
    $r = absint( hexdec( substr( $hex, 0, 2 ) ) );
    $g = absint( hexdec( substr( $hex, 2, 2 ) ) );
    $b = absint( hexdec( substr( $hex, 4, 2 ) ) );
    $opacity = floatval( $opacity );
    if ( $opacity < 0 ) {
      $opacity = 0;
    }
    if ( $opacity > 1 ) {
      $opacity = 1;
    }
    return "rgba($r, $g, $b, $opacity)";
  }
}

$overlay_rgba = avalon_hex_to_rgba( $overlay_color, $overlay_opacity );

// Build wrapper attributes safely
$wrapper_class = 'avalon-hero-block wp-block-vestelli-hero';
$wrapper_style = 'min-height: ' . esc_attr( $height ) . '; color: ' . esc_attr( $text_color ) . ';';

// Use get_block_wrapper_attributes if available, otherwise build manually
if ( function_exists( 'get_block_wrapper_attributes' ) ) {
  try {
    $wrapper_attributes = get_block_wrapper_attributes( array(
      'class' => $wrapper_class,
      'style' => $wrapper_style,
    ) );
  } catch ( Exception $e ) {
    // Fallback if function throws error
    $wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '" style="' . esc_attr( $wrapper_style ) . '"';
  }
} else {
  // Fallback if function doesn't exist
  $wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '" style="' . esc_attr( $wrapper_style ) . '"';
}
?>

<div <?php echo $wrapper_attributes; ?>>
  <?php if ( ! empty( $background_image_url ) ) : ?>
    <div class="avalon-hero-background" style="background-image: url('<?php echo esc_url( $background_image_url ); ?>');"></div>
  <?php endif; ?>
  <div class="avalon-hero-overlay" style="background-color: <?php echo esc_attr( $overlay_rgba ); ?>;"></div>
  <div class="avalon-hero-content avalon-hero-align-<?php echo esc_attr( $alignment ); ?>">
    <div class="avalon-hero-inner">
      <?php if ( ! empty( $title ) ) : ?>
        <h1 class="avalon-hero-title"><?php echo wp_kses_post( $title ); ?></h1>
      <?php endif; ?>
      
      <?php if ( ! empty( $description ) ) : ?>
        <div class="avalon-hero-description">
          <?php echo wp_kses_post( wpautop( $description ) ); ?>
        </div>
      <?php endif; ?>
      
      <?php if ( $show_button1 || $show_button2 ) : ?>
        <div class="avalon-hero-buttons">
          <?php if ( $show_button1 ) : ?>
            <a href="<?php echo esc_url( $button1_url ); ?>" 
               class="avalon-hero-button avalon-hero-button-primary"
               <?php echo $button1_open_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
              <?php echo esc_html( $button1_text ); ?>
            </a>
          <?php endif; ?>
          
          <?php if ( $show_button2 ) : ?>
            <a href="<?php echo esc_url( $button2_url ); ?>" 
               class="avalon-hero-button avalon-hero-button-secondary"
               <?php echo $button2_open_new_tab ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
              <?php echo esc_html( $button2_text ); ?>
            </a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>
