<?php
/**
 * Register Gutenberg Blocks
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Register Hero block
 */
function vestelli_register_hero_block() {
  // Check if block editor is available
  if ( ! function_exists( 'register_block_type' ) ) {
    return;
  }

  $block_path = get_stylesheet_directory() . '/blocks/hero';
  
  // Check if block.json exists
  if ( ! file_exists( $block_path . '/block.json' ) ) {
    return;
  }

  // Register Hero block from block.json
  $block_type = register_block_type_from_metadata(
    $block_path,
    array(
      'render_callback' => 'vestelli_render_hero_block',
    )
  );
  
  // Debug: Log if block registration failed
  if ( ! $block_type ) {
    error_log( 'Vestelli Hero block registration failed' );
  }
}
add_action( 'init', 'vestelli_register_hero_block', 20 );

/**
 * Render callback for Hero block
 */
function vestelli_render_hero_block( $attributes, $content, $block ) {
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
  $alignment = isset( $attributes['alignment'] ) && ! empty( $attributes['alignment'] ) ? esc_attr( $attributes['alignment'] ) : 'left';
  $height = isset( $attributes['height'] ) && ! empty( $attributes['height'] ) ? esc_attr( $attributes['height'] ) : '60vh';
  
  // Button settings with safe defaults
  $show_button1 = isset( $attributes['showButton1'] ) ? (bool) $attributes['showButton1'] : true;
  $button1_text = isset( $attributes['button1Text'] ) && ! empty( $attributes['button1Text'] ) ? esc_html( $attributes['button1Text'] ) : 'Pyydä tarjous';
  $button1_url = isset( $attributes['button1Url'] ) && ! empty( $attributes['button1Url'] ) ? esc_url( $attributes['button1Url'] ) : '/pyyda-tarjous';
  $button1_open_new_tab = isset( $attributes['button1OpenNewTab'] ) ? (bool) $attributes['button1OpenNewTab'] : false;
  
  $show_button2 = isset( $attributes['showButton2'] ) ? (bool) $attributes['showButton2'] : false;
  $button2_text = isset( $attributes['button2Text'] ) && ! empty( $attributes['button2Text'] ) ? esc_html( $attributes['button2Text'] ) : 'Lue lisää';
  $button2_url = isset( $attributes['button2Url'] ) && ! empty( $attributes['button2Url'] ) ? esc_url( $attributes['button2Url'] ) : '#';
  $button2_open_new_tab = isset( $attributes['button2OpenNewTab'] ) ? (bool) $attributes['button2OpenNewTab'] : false;
  
  // Convert hex color to rgba for overlay
  $overlay_rgba = va_hex_to_rgba( $overlay_color, $overlay_opacity );
  
  // Build wrapper attributes
  $wrapper_class = 'avalon-hero-block wp-block-vestelli-hero';
  $wrapper_style = 'min-height: ' . esc_attr( $height ) . '; color: ' . esc_attr( $text_color ) . ';';
  
  // Use get_block_wrapper_attributes if available
  if ( function_exists( 'get_block_wrapper_attributes' ) ) {
    try {
      $wrapper_attributes = get_block_wrapper_attributes( array(
        'class' => $wrapper_class,
        'style' => $wrapper_style,
      ) );
    } catch ( Exception $e ) {
      $wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '" style="' . esc_attr( $wrapper_style ) . '"';
    }
  } else {
    $wrapper_attributes = 'class="' . esc_attr( $wrapper_class ) . '" style="' . esc_attr( $wrapper_style ) . '"';
  }
  
  // Build output
  $output = '<div ' . $wrapper_attributes . '>';
  
  // Background image - use default if empty
  $default_background_url = '';
  $final_background_url = ! empty( $background_image_url ) ? $background_image_url : $default_background_url;
  $output .= '<div class="avalon-hero-background" style="background-image: url(\'' . esc_url( $final_background_url ) . '\');"></div>';
  
  // Overlay
  $output .= '<div class="avalon-hero-overlay" style="background-color: ' . esc_attr( $overlay_rgba ) . ';"></div>';
  
  // Content
  $output .= '<div class="avalon-hero-content avalon-hero-align-' . esc_attr( $alignment ) . '">';
  $output .= '<div class="avalon-hero-inner">';
  
  // Title - h1 bolded
  if ( ! empty( $title ) ) {
    $output .= '<h1 class="avalon-hero-title">' . wp_kses_post( $title ) . '</h1>';
  }
  
  // Description - h3
  if ( ! empty( $description ) ) {
    $output .= '<h3 class="avalon-hero-description">' . wp_kses_post( $description ) . '</h3>';
  }
  
  // Buttons
  if ( $show_button1 || $show_button2 ) {
    $output .= '<div class="avalon-hero-buttons">';
    
    if ( $show_button1 ) {
      $target = $button1_open_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
      $output .= '<a href="' . esc_url( $button1_url ) . '" class="avalon-hero-button avalon-hero-button-primary"' . $target . '>';
      $output .= esc_html( $button1_text );
      $output .= '</a>';
    }
    
    if ( $show_button2 ) {
      $target = $button2_open_new_tab ? ' target="_blank" rel="noopener noreferrer"' : '';
      $output .= '<a href="' . esc_url( $button2_url ) . '" class="avalon-hero-button avalon-hero-button-secondary"' . $target . '>';
      $output .= esc_html( $button2_text );
      $output .= '</a>';
    }
    
    $output .= '</div>';
  }
  
  $output .= '</div>'; // .avalon-hero-inner
  $output .= '</div>'; // .avalon-hero-content
  $output .= '</div>'; // .avalon-hero-block
  
  return $output;
}

/**
 * Register block category
 */
function avalon_register_block_category( $categories, $post ) {
  return array_merge(
    array(
      array(
        'slug'  => 'vestelli',
        'title' => __( 'Vestelli', 'vestelli' ),
        'icon'  => null,
      ),
    ),
    $categories
  );
}
add_filter( 'block_categories_all', 'avalon_register_block_category', 10, 2 );

/**
 * Enqueue block editor assets manually (fallback if block.json doesn't handle it)
 */
function avalon_enqueue_hero_block_editor_assets() {
  $block_path = get_stylesheet_directory() . '/blocks/hero';
  $block_url = get_stylesheet_directory_uri() . '/blocks/hero';
  
  // Enqueue editor script
  $editor_js = $block_path . '/editor.js';
  if ( file_exists( $editor_js ) ) {
    wp_enqueue_script(
      'avalon-hero-block-editor',
      $block_url . '/editor.js',
      array( 'wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n', 'wp-block-editor', 'wp-data' ),
      filemtime( $editor_js ),
      true
    );
  }
  
  // Enqueue editor styles
  $editor_css = $block_path . '/editor.css';
  if ( file_exists( $editor_css ) ) {
    wp_enqueue_style(
      'avalon-hero-block-editor',
      $block_url . '/editor.css',
      array(),
      filemtime( $editor_css )
    );
  }
}
add_action( 'enqueue_block_editor_assets', 'avalon_enqueue_hero_block_editor_assets' );
