<?php
/**
 * Hide parent theme from backend when child theme is active
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Hide parent theme from themes list in WordPress admin
 */
add_filter( 'wp_prepare_themes_for_js', function( $themes ) {
  $parent_theme = 'bb-theme';
  
  if ( isset( $themes[ $parent_theme ] ) ) {
    unset( $themes[ $parent_theme ] );
  }
  
  return $themes;
});
