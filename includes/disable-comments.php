<?php
/**
 * Disable Comments Site-wide
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Remove comment support from all post types
 */
add_action( 'admin_init', function() {
  $post_types = get_post_types();
  foreach ( $post_types as $post_type ) {
    if ( post_type_supports( $post_type, 'comments' ) ) {
      remove_post_type_support( $post_type, 'comments' );
      remove_post_type_support( $post_type, 'trackbacks' );
    }
  }
});

/**
 * Close comments on the front-end
 */
add_filter( 'comments_open', '__return_false', 20, 2 );
add_filter( 'pings_open', '__return_false', 20, 2 );

/**
 * Hide existing comments
 */
add_filter( 'comments_array', '__return_empty_array', 10, 2 );

/**
 * Remove comments page in menu
 */
add_action( 'admin_menu', function() {
  remove_menu_page( 'edit-comments.php' );
});

/**
 * Remove comments links from admin bar
 */
add_action( 'init', function() {
  if ( is_admin_bar_showing() ) {
    remove_action( 'admin_bar_menu', 'wp_admin_bar_comments_menu', 60 );
  }
});

/**
 * Remove comments metabox from dashboard
 */
add_action( 'admin_init', function() {
  remove_meta_box( 'dashboard_recent_comments', 'dashboard', 'normal' );
});

/**
 * Remove comments metabox from post edit screen
 */
add_action( 'admin_init', function() {
  remove_meta_box( 'commentstatusdiv', 'post', 'normal' );
  remove_meta_box( 'commentsdiv', 'post', 'normal' );
  remove_meta_box( 'trackbacksdiv', 'post', 'normal' );
  remove_meta_box( 'commentstatusdiv', 'page', 'normal' );
  remove_meta_box( 'commentsdiv', 'page', 'normal' );
});

/**
 * Remove comments column from posts list
 */
add_filter( 'manage_posts_columns', function( $columns ) {
  unset( $columns['comments'] );
  return $columns;
});

/**
 * Remove comments column from pages list
 */
add_filter( 'manage_pages_columns', function( $columns ) {
  unset( $columns['comments'] );
  return $columns;
});

/**
 * Disable comment feeds
 */
add_filter( 'feed_links_show_comments_feed', '__return_false' );

/**
 * Redirect comment pages to homepage
 */
add_action( 'template_redirect', function() {
  if ( is_comment_feed() ) {
    wp_redirect( home_url() );
    exit;
  }
});
