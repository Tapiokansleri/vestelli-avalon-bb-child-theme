<?php
/**
 * Vestelli Avalon Child Theme
 *
 * Unified child theme for Beaver Builder - supports both Vestelli and Avalon Nordic sites
 *
 * @package Vestelli_Avalon
 * @author Tapio Kauranen
 * @author URI https://kansleri.fi
 * @version 1.0
 */
if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Define theme directory path
 */
define( 'VA_INC', get_stylesheet_directory() . '/includes' );

/**
 * Load theme includes
 */
$includes = array(
  'enqueue.php',              // Enqueue scripts and styles
  'hide-parent-theme.php',    // Hide parent theme from backend
  'disable-comments.php',     // Disable comments site-wide
  'theme-settings.php',       // Theme settings page
  'beaver-builder-modules.php', // Register Beaver Builder modules
  'gutenberg-blocks.php',     // Register Gutenberg blocks
  'custom-post-types.php',    // Custom post types
  'custom-functions.php',     // Custom theme functions
  'shortcodes.php',           // Custom shortcodes
  'mobile-tabs-accordion.php', // Mobile tabs accordion functionality
);

foreach ( $includes as $file ) {
  $file_path = VA_INC . '/' . $file;
  if ( file_exists( $file_path ) ) {
    require_once $file_path;
  }
}

/**
 * Load child theme textdomain for translations.
 */
add_action( 'after_setup_theme', function() {
  load_child_theme_textdomain( 'vestelli-avalon', get_stylesheet_directory() . '/languages' );
} );

/**
 * Disable WPML "Edits you're about to make will be lost" modal in Beaver Builder UI.
 */
add_action( 'fl_builder_ui_enqueue_scripts', function() {
  remove_all_actions( 'wpml_maybe_display_modal_page_builder_warning' );
}, 1 );

add_action( 'admin_head', function() {
  ?>
  <style>
    .wpml-dialog-container,
    .wpml-dialog-translate-editor {
      display: none !important;
    }
  </style>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var observer = new MutationObserver(function() {
        var editBtn = document.querySelector('.wpml-dialog-container .edit-anyway');
        if (editBtn) editBtn.click();
      });
      observer.observe(document.body, { childList: true, subtree: true });
    });
  </script>
  <?php
} );
