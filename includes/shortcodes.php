<?php
/**
 * Custom Shortcodes
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Shortcode: Get image by ID from media library
 * 
 * Usage: [avalon_image id="123"]
 * 
 * @param array $atts Shortcode attributes
 * @return string Image HTML or empty string
 */
function avalon_image_shortcode( $atts ) {
  // Parse shortcode attributes
  $atts = shortcode_atts( array(
    'id'        => 0,
    'size'      => 'full',
    'class'     => '',
    'alt'       => '',
    'title'     => '',
    'lightbox'  => 'true',
  ), $atts, 'avalon_image' );
  
  // Get image ID
  $image_id = intval( $atts['id'] );
  
  // Validate image ID
  if ( ! $image_id || $image_id <= 0 ) {
    return '';
  }
  
  // Check if attachment exists
  $image = get_post( $image_id );
  if ( ! $image || $image->post_type !== 'attachment' ) {
    return '';
  }
  
  // Check if it's an image
  if ( ! wp_attachment_is_image( $image_id ) ) {
    return '';
  }
  
  // Get thumbnail image URL (for display)
  $thumbnail_url = wp_get_attachment_image_url( $image_id, $atts['size'] );
  if ( ! $thumbnail_url ) {
    return '';
  }
  
  // Get full size image URL (for lightbox)
  $full_image_url = wp_get_attachment_image_url( $image_id, 'full' );
  
  // Get alt text (use provided alt, or get from attachment, or use title)
  $alt_text = ! empty( $atts['alt'] ) ? $atts['alt'] : get_post_meta( $image_id, '_wp_attachment_image_alt', true );
  if ( empty( $alt_text ) ) {
    $alt_text = ! empty( $atts['title'] ) ? $atts['title'] : get_the_title( $image_id );
  }
  
  // Get title attribute
  $title_attr = ! empty( $atts['title'] ) ? $atts['title'] : get_the_title( $image_id );
  
  // Build class attribute
  $classes = array( 'avalon-image-shortcode' );
  if ( ! empty( $atts['class'] ) ) {
    $custom_classes = explode( ' ', $atts['class'] );
    foreach ( $custom_classes as $custom_class ) {
      $custom_class = sanitize_html_class( trim( $custom_class ) );
      if ( ! empty( $custom_class ) ) {
        $classes[] = $custom_class;
      }
    }
  }
  $class_attr = implode( ' ', $classes );
  
  // Enqueue Fancybox assets
  static $fancybox_enqueued = false;
  if ( ! $fancybox_enqueued ) {
    wp_enqueue_style( 
      'fancybox', 
      'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css',
      array(),
      '5.0'
    );
    wp_enqueue_script( 
      'fancybox', 
      'https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js',
      array(),
      '5.0',
      true
    );
    
    // Initialize Fancybox
    wp_add_inline_script( 'fancybox', '
      document.addEventListener("DOMContentLoaded", function() {
        if (typeof Fancybox !== "undefined") {
          Fancybox.bind("[data-fancybox]", {
            Toolbar: {
              display: {
                left: ["infobar"],
                middle: [],
                right: ["close"],
              },
            },
          });
        }
      });
    ' );
    
    $fancybox_enqueued = true;
  }
  
  // Build image HTML
  $image_html = '<div class="' . esc_attr( $class_attr ) . '" style="margin-top: 20px; margin-bottom: 20px;">';
  
  // Check if lightbox should be enabled
  $use_lightbox = ( $atts['lightbox'] === 'true' || $atts['lightbox'] === true || $atts['lightbox'] === '1' || $atts['lightbox'] === 1 );
  
  if ( $use_lightbox && $full_image_url ) {
    // Wrap image in link for lightbox
    $image_html .= sprintf(
      '<a href="%s" data-fancybox="gallery" data-caption="%s">',
      esc_url( $full_image_url ),
      esc_attr( $title_attr )
    );
  }
  
  $image_html .= sprintf(
    '<img src="%s" alt="%s" title="%s" />',
    esc_url( $thumbnail_url ),
    esc_attr( $alt_text ),
    esc_attr( $title_attr )
  );
  
  if ( $use_lightbox && $full_image_url ) {
    $image_html .= '</a>';
  }
  
  $image_html .= '</div>';
  
  return $image_html;
}
add_shortcode( 'avalon_image', 'avalon_image_shortcode' );

/**
 * Shortcode: Display Font Awesome icon
 * 
 * Usage: [avalon_icon icon="fa fa-star"]
 *        [avalon_icon icon="fas fa-check" size="2x" color="#012b55"]
 *        [avalon_icon icon="fa-solid fa-heart" size="lg" class="my-icon"]
 * 
 * @param array $atts Shortcode attributes
 * @return string Icon HTML
 */
function avalon_icon_shortcode( $atts ) {
  // Parse shortcode attributes
  $atts = shortcode_atts( array(
    'icon'      => '',
    'size'      => '',
    'color'     => '',
    'class'     => '',
    'style'     => '',
  ), $atts, 'avalon_icon' );
  
  // Get icon class (required)
  $icon_class = trim( $atts['icon'] );
  
  // Validate icon class
  if ( empty( $icon_class ) ) {
    return '';
  }
  
  // Sanitize icon class (allow only valid characters for CSS classes)
  $icon_class = sanitize_html_class( $icon_class );
  
  // Build classes array
  $classes = array( $icon_class );
  
  // Add size class if provided
  if ( ! empty( $atts['size'] ) ) {
    $size_class = sanitize_html_class( $atts['size'] );
    // Common Font Awesome size classes
    $valid_sizes = array( 'xs', 'sm', 'lg', '2x', '3x', '4x', '5x', '6x', '7x', '8x', '9x', '10x' );
    if ( in_array( $size_class, $valid_sizes, true ) ) {
      $classes[] = 'fa-' . $size_class;
    }
  }
  
  // Add custom class if provided
  if ( ! empty( $atts['class'] ) ) {
    $custom_classes = explode( ' ', $atts['class'] );
    foreach ( $custom_classes as $custom_class ) {
      $custom_class = sanitize_html_class( trim( $custom_class ) );
      if ( ! empty( $custom_class ) ) {
        $classes[] = $custom_class;
      }
    }
  }
  
  // Build style attribute
  $styles = array();
  
  // Add color if provided
  if ( ! empty( $atts['color'] ) ) {
    $color = sanitize_hex_color( $atts['color'] );
    if ( $color ) {
      $styles[] = 'color: ' . $color;
    }
  }
  
  // Add custom styles if provided
  if ( ! empty( $atts['style'] ) ) {
    $styles[] = esc_attr( $atts['style'] );
  }
  
  // Build class attribute
  $class_attr = implode( ' ', $classes );
  
  // Build style attribute
  $style_attr = ! empty( $styles ) ? ' style="' . implode( '; ', $styles ) . '"' : '';
  
  // Build icon HTML
  $icon_html = sprintf(
    '<i class="%s"%s></i>',
    esc_attr( $class_attr ),
    $style_attr
  );
  
  return $icon_html;
}
add_shortcode( 'avalon_icon', 'avalon_icon_shortcode' );
