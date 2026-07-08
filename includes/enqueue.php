<?php
/**
 * Enqueue scripts and styles
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Enqueue child theme style.css file
 */
add_action( 'wp_enqueue_scripts', function() {
  $design = get_option( 'va_header_design', 'avalon' );

  // Google Fonts - design-specific
  // Avalon Nordic: Roboto. Vestelli: Oxygen + Cormorant Garamond.
  $style_deps = array( 'fl-automator-skin' );
  $fonts_url = ( $design === 'vestelli' )
    ? 'https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;1,400&family=Oxygen:wght@300;400;700&display=swap'
    : 'https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap';
  wp_enqueue_style(
    'va-google-fonts',
    $fonts_url,
    array(),
    null
  );
  $style_deps[] = 'va-google-fonts';

  wp_enqueue_style(
    'child-style',
    get_stylesheet_uri(),
    $style_deps,
    wp_get_theme()->get( 'Version' )
  );

  // Enqueue custom CSS
  $custom_css_path = get_stylesheet_directory() . '/assets/css/custom.css';
  if ( file_exists( $custom_css_path ) ) {
    wp_enqueue_style(
      'va-custom',
      get_stylesheet_directory_uri() . '/assets/css/custom.css',
      array(),
      filemtime( $custom_css_path )
    );
  }

  // Enqueue header CSS - conditionally based on header design
  $header_file = ( $design === 'vestelli' ) ? 'header-vestelli.css' : 'header-avalon.css';
  $header_css_path = get_stylesheet_directory() . '/assets/css/' . $header_file;
  wp_enqueue_style(
    'va-header',
    get_stylesheet_directory_uri() . '/assets/css/' . $header_file,
    array( 'child-style' ),
    file_exists( $header_css_path ) ? filemtime( $header_css_path ) : wp_get_theme()->get( 'Version' )
  );

  $responsive_css = ( $design === 'vestelli' )
    ? va_get_vestelli_header_responsive_css()
    : va_get_avalon_header_responsive_css();

  wp_add_inline_style( 'va-header', $responsive_css );

  // Enqueue header scroll JavaScript
  wp_enqueue_script(
    'va-header-scroll',
    get_stylesheet_directory_uri() . '/assets/js/header-scroll.js',
    array(),
    wp_get_theme()->get( 'Version' ),
    true
  );

  // Enqueue mobile menu JavaScript
  wp_enqueue_script(
    'va-mobile-menu',
    get_stylesheet_directory_uri() . '/assets/js/mobile-menu.js',
    array(),
    wp_get_theme()->get( 'Version' ),
    true
  );

  wp_localize_script(
    'va-mobile-menu',
    'vaMobileMenu',
    array(
      'headerBreakpoint' => va_get_header_mobile_breakpoint(),
    )
  );

  // Disable WooCommerce product image zoom via JavaScript (but keep gallery functionality)
  if ( class_exists( 'WooCommerce' ) && is_product() ) {
    wp_add_inline_script( 'wc-single-product', '
      jQuery(document).ready(function($) {
        if (typeof wc_single_product_params !== "undefined") {
          wc_single_product_params.zoom_enabled = "0";
        }
        function destroyZoom() {
          if (typeof $.fn.zoom !== "undefined") {
            $(".woocommerce-product-gallery__image img").each(function() {
              var $img = $(this);
              if ($img.data("zoom")) {
                try {
                  $img.trigger("zoom.destroy");
                } catch(e) {}
              }
            });
          }
        }
        setTimeout(destroyZoom, 200);
        $(".woocommerce-product-gallery").on("flexslider.after", destroyZoom);
      });
    ', 'after' );
  }
} );
