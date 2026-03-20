<?php
/**
 * Custom theme functions
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Migrate old options to va_* prefix (one-time)
 *
 * Detects whether the site was previously running Avalon Nordic or Vestelli
 * and maps all old options to the unified va_* naming.
 */
add_action( 'after_setup_theme', function() {
  if ( get_option( 'va_options_migrated', false ) ) {
    return;
  }

  // Detect which old theme was in use
  $has_vestelli = get_option( 'vestelli_logo', '__unset' ) !== '__unset'
    || get_option( 'vestelli_cta_text', '__unset' ) !== '__unset';
  $has_avalon = get_option( 'avalon_nordic_logo', '__unset' ) !== '__unset'
    || get_option( 'avalon_nordic_cta_text', '__unset' ) !== '__unset';

  // Vestelli options → va_* (superset)
  $vestelli_map = array(
    'vestelli_logo'                 => 'va_logo',
    'vestelli_cta_text'             => 'va_cta_text',
    'vestelli_cta_link'             => 'va_cta_link',
    'vestelli_header_type'          => 'va_header_type',
    'vestelli_bb_header_template'   => 'va_bb_header_template',
    'vestelli_header_opacity'       => 'va_header_opacity',
    'vestelli_social_facebook'      => 'va_social_facebook',
    'vestelli_social_instagram'     => 'va_social_instagram',
    'vestelli_social_linkedin'      => 'va_social_linkedin',
    'vestelli_social_youtube'       => 'va_social_youtube',
    'vestelli_show_social_icons'    => 'va_show_social_icons',
    'vestelli_show_search'          => 'va_show_search',
    'vestelli_show_language_switcher' => 'va_show_language_switcher',
    'vestelli_show_cta'             => 'va_show_cta',
    'vestelli_show_cart'            => 'va_show_cart',
    'vestelli_custom_scripts'       => 'va_custom_scripts',
  );

  // Avalon Nordic options → va_*
  $avalon_map = array(
    'avalon_nordic_logo'               => 'va_logo',
    'avalon_nordic_cta_text'           => 'va_cta_text',
    'avalon_nordic_cta_link'           => 'va_cta_link',
    'avalon_nordic_header_type'        => 'va_header_type',
    'avalon_nordic_bb_header_template' => 'va_bb_header_template',
    'avalon_nordic_header_opacity'     => 'va_header_opacity',
    'avalon_nordic_hide_cart'          => 'va_hide_cart',
  );

  if ( $has_vestelli ) {
    foreach ( $vestelli_map as $old => $new ) {
      $old_val = get_option( $old, '__unset' );
      if ( $old_val !== '__unset' && get_option( $new, '__unset' ) === '__unset' ) {
        update_option( $new, $old_val );
      }
    }
    // Set header design based on source theme
    if ( get_option( 'va_header_design', '__unset' ) === '__unset' ) {
      update_option( 'va_header_design', 'vestelli' );
    }
  } elseif ( $has_avalon ) {
    foreach ( $avalon_map as $old => $new ) {
      $old_val = get_option( $old, '__unset' );
      if ( $old_val !== '__unset' && get_option( $new, '__unset' ) === '__unset' ) {
        update_option( $new, $old_val );
      }
    }
    // Avalon didn't have these toggles - set sensible defaults
    if ( get_option( 'va_show_search', '__unset' ) === '__unset' ) {
      update_option( 'va_show_search', '1' );
    }
    if ( get_option( 'va_show_language_switcher', '__unset' ) === '__unset' ) {
      update_option( 'va_show_language_switcher', '1' );
    }
    if ( get_option( 'va_show_cta', '__unset' ) === '__unset' ) {
      update_option( 'va_show_cta', '1' );
    }
    if ( get_option( 'va_show_cart', '__unset' ) === '__unset' ) {
      // Avalon used va_hide_cart (inverted logic)
      $hide = get_option( 'va_hide_cart', '0' );
      update_option( 'va_show_cart', $hide ? '0' : '1' );
    }
    if ( get_option( 'va_header_design', '__unset' ) === '__unset' ) {
      update_option( 'va_header_design', 'avalon' );
    }
    // Enable portfolio by default on Avalon sites
    if ( get_option( 'va_enable_portfolio', '__unset' ) === '__unset' ) {
      update_option( 'va_enable_portfolio', '1' );
    }
  }

  update_option( 'va_options_migrated', true );
}, 1 );

/**
 * Register navigation menus
 */
add_action( 'after_setup_theme', function() {
  register_nav_menus( array(
    'primary' => __( 'Primary Menu', 'vestelli-avalon' ),
  ) );

  add_theme_support( 'custom-logo', array(
    'height'      => 120,
    'width'       => 320,
    'flex-height' => true,
    'flex-width'  => true,
    'header-text' => array( 'site-title', 'site-description' ),
  ) );

  // Load menu walker
  require_once get_stylesheet_directory() . '/includes/menu-walker.php';
} );

/**
 * Add body classes for page templates and header design
 */
add_filter( 'body_class', function( $classes ) {
  // Add header design class
  $design = get_option( 'va_header_design', 'avalon' );
  $classes[] = 'header-design-' . sanitize_html_class( $design );

  // Quote mode body classes
  if ( function_exists( 'va_is_quote_mode' ) && va_is_quote_mode() ) {
    $classes[] = 'va-quote-mode';
    if ( function_exists( 'va_hide_prices' ) && va_hide_prices() ) {
      $classes[] = 'va-hide-prices';
    }
  }

  // Transparent header support (Avalon design)
  if ( is_page() && ! is_front_page() ) {
    $page_template = get_page_template_slug();
    if ( $page_template === 'page-transparent-header.php' ) {
      $classes[] = 'has-transparent-header';
    } elseif ( empty( $page_template ) || $page_template === 'default' ) {
      $classes[] = 'has-default-page-template';
    }
  }

  return $classes;
} );

/**
 * Convert hex color to rgba string.
 */
function va_hex_to_rgba( $hex, $opacity ) {
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
  $opacity = max( 0.0, min( 1.0, floatval( $opacity ) ) );
  return "rgba($r, $g, $b, $opacity)";
}

/**
 * Output brand CSS custom properties.
 */
add_action( 'wp_head', function() {
  $brand = get_option( 'va_brand_color', '#012b55' );
  if ( empty( $brand ) ) {
    $brand = '#012b55';
  }
  $accent = get_option( 'va_accent_color', '#30CBD3' );
  if ( empty( $accent ) ) {
    $accent = '#30CBD3';
  }
  $radius = absint( get_option( 'va_button_radius', '10' ) );

  // Helper: hex → r,g,b integers.
  $to_rgb = function ( $hex ) {
    $hex = ltrim( $hex, '#' );
    return array(
      hexdec( substr( $hex, 0, 2 ) ),
      hexdec( substr( $hex, 2, 2 ) ),
      hexdec( substr( $hex, 4, 2 ) ),
    );
  };

  // Brand hover: ~15% lighter.
  list( $br, $bg, $bb ) = $to_rgb( $brand );
  $brand_hover = sprintf( '#%02x%02x%02x', min( 255, $br + 25 ), min( 255, $bg + 25 ), min( 255, $bb + 25 ) );

  // Accent hover: ~10% darker.
  list( $ar, $ag, $ab ) = $to_rgb( $accent );
  $accent_hover = sprintf( '#%02x%02x%02x', max( 0, $ar - 15 ), max( 0, $ag - 15 ), max( 0, $ab - 15 ) );

  printf(
    "<style>:root{--va-brand-color:%s;--va-brand-color-hover:%s;--va-brand-rgb:%d,%d,%d;--va-accent-color:%s;--va-accent-color-hover:%s;--va-accent-rgb:%d,%d,%d;--va-button-radius:%dpx;}</style>\n",
    $brand, $brand_hover, $br, $bg, $bb,
    $accent, $accent_hover, $ar, $ag, $ab,
    $radius
  );
}, 5 );

/**
 * Disable WooCommerce zoom via JavaScript params (but keep gallery functionality)
 */
add_filter( 'woocommerce_single_product_params', function( $params ) {
  if ( isset( $params['zoom_enabled'] ) ) {
    $params['zoom_enabled'] = '0';
  }
  return $params;
}, 10, 1 );
