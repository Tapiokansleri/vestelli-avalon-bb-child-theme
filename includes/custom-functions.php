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
 * and maps all old options to the va_* naming.
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
 * Render configured social icons for custom headers.
 */
function va_render_header_social_icons( $wrapper_class = 'header-social-icons' ) {
  $social_facebook  = get_option( 'va_social_facebook', '' );
  $social_instagram = get_option( 'va_social_instagram', '' );
  $social_linkedin  = get_option( 'va_social_linkedin', '' );
  $social_youtube   = get_option( 'va_social_youtube', '' );

  if ( ! $social_facebook && ! $social_instagram && ! $social_linkedin && ! $social_youtube ) {
    return;
  }
  ?>
  <div class="<?php echo esc_attr( $wrapper_class ); ?>">
    <?php if ( $social_facebook ) : ?>
      <a href="<?php echo esc_url( $social_facebook ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Facebook"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg></a>
    <?php endif; ?>
    <?php if ( $social_instagram ) : ?>
      <a href="<?php echo esc_url( $social_instagram ); ?>" target="_blank" rel="noopener noreferrer" aria-label="Instagram"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
    <?php endif; ?>
    <?php if ( $social_linkedin ) : ?>
      <a href="<?php echo esc_url( $social_linkedin ); ?>" target="_blank" rel="noopener noreferrer" aria-label="LinkedIn"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg></a>
    <?php endif; ?>
    <?php if ( $social_youtube ) : ?>
      <a href="<?php echo esc_url( $social_youtube ); ?>" target="_blank" rel="noopener noreferrer" aria-label="YouTube"><svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg></a>
    <?php endif; ?>
  </div>
  <?php
}

/**
 * Check whether the primary menu has any top-level items with submenus.
 */
function va_primary_menu_has_dropdowns( $theme_location = 'primary' ) {
  $locations = get_nav_menu_locations();
  if ( empty( $locations[ $theme_location ] ) ) {
    return false;
  }

  $items = wp_get_nav_menu_items( $locations[ $theme_location ] );
  if ( empty( $items ) || ! is_array( $items ) ) {
    return false;
  }

  $parents_with_children = array();
  foreach ( $items as $item ) {
    if ( ! empty( $item->menu_item_parent ) ) {
      $parents_with_children[ (int) $item->menu_item_parent ] = true;
    }
  }

  foreach ( $items as $item ) {
    if ( 0 === (int) $item->menu_item_parent && isset( $parents_with_children[ $item->ID ] ) ) {
      return true;
    }
  }

  return false;
}

/**
 * Add a modifier class when the Avalon desktop menu has no dropdown items.
 */
add_filter(
  'wp_nav_menu_args',
  function( $args ) {
    if ( empty( $args['theme_location'] ) || 'primary' !== $args['theme_location'] ) {
      return $args;
    }

    if ( empty( $args['menu_class'] ) || 'main-menu' !== $args['menu_class'] ) {
      return $args;
    }

    if ( ! va_primary_menu_has_dropdowns() ) {
      $args['menu_class'] .= ' main-menu--no-dropdowns';
    }

    return $args;
  }
);

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
