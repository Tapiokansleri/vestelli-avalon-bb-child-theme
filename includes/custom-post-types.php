<?php
/**
 * Custom Post Types
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Register Portfolio custom post type (toggle via theme settings)
 */
add_action( 'init', function() {
  if ( get_option( 'va_enable_portfolio', '0' ) !== '1' ) {
    return;
  }

  $labels = array(
    'name'                  => _x( 'Portfolio', 'Post Type General Name', 'vestelli-avalon' ),
    'singular_name'         => _x( 'Portfolio Item', 'Post Type Singular Name', 'vestelli-avalon' ),
    'menu_name'             => __( 'Portfolio', 'vestelli-avalon' ),
    'name_admin_bar'        => __( 'Portfolio Item', 'vestelli-avalon' ),
    'archives'              => __( 'Portfolio Archives', 'vestelli-avalon' ),
    'attributes'            => __( 'Portfolio Item Attributes', 'vestelli-avalon' ),
    'parent_item_colon'     => __( 'Parent Portfolio Item:', 'vestelli-avalon' ),
    'all_items'             => __( 'All Portfolio Items', 'vestelli-avalon' ),
    'add_new_item'          => __( 'Add New Portfolio Item', 'vestelli-avalon' ),
    'add_new'               => __( 'Add New', 'vestelli-avalon' ),
    'new_item'              => __( 'New Portfolio Item', 'vestelli-avalon' ),
    'edit_item'             => __( 'Edit Portfolio Item', 'vestelli-avalon' ),
    'update_item'           => __( 'Update Portfolio Item', 'vestelli-avalon' ),
    'view_item'             => __( 'View Portfolio Item', 'vestelli-avalon' ),
    'view_items'            => __( 'View Portfolio Items', 'vestelli-avalon' ),
    'search_items'          => __( 'Search Portfolio Item', 'vestelli-avalon' ),
    'not_found'             => __( 'Not found', 'vestelli-avalon' ),
    'not_found_in_trash'    => __( 'Not found in Trash', 'vestelli-avalon' ),
    'featured_image'        => __( 'Featured Image', 'vestelli-avalon' ),
    'set_featured_image'    => __( 'Set featured image', 'vestelli-avalon' ),
    'remove_featured_image' => __( 'Remove featured image', 'vestelli-avalon' ),
    'use_featured_image'    => __( 'Use as featured image', 'vestelli-avalon' ),
    'insert_into_item'      => __( 'Insert into portfolio item', 'vestelli-avalon' ),
    'uploaded_to_this_item' => __( 'Uploaded to this portfolio item', 'vestelli-avalon' ),
    'items_list'            => __( 'Portfolio items list', 'vestelli-avalon' ),
    'items_list_navigation' => __( 'Portfolio items list navigation', 'vestelli-avalon' ),
    'filter_items_list'     => __( 'Filter portfolio items list', 'vestelli-avalon' ),
  );

  $args = array(
    'label'                 => __( 'Portfolio', 'vestelli-avalon' ),
    'description'           => __( 'Portfolio items', 'vestelli-avalon' ),
    'labels'                => $labels,
    'supports'              => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions' ),
    'taxonomies'            => array( 'project-type' ),
    'hierarchical'          => false,
    'public'                => true,
    'show_ui'               => true,
    'show_in_menu'          => true,
    'menu_position'         => 20,
    'menu_icon'             => 'dashicons-portfolio',
    'show_in_admin_bar'     => true,
    'show_in_nav_menus'     => true,
    'can_export'            => true,
    'has_archive'           => true,
    'exclude_from_search'   => false,
    'publicly_queryable'    => true,
    'capability_type'       => 'post',
    'show_in_rest'          => true,
    'rewrite'               => array( 'slug' => 'portfolio' ),
  );

  register_post_type( 'portfolio', $args );
} );

/**
 * Register Project Type taxonomy for Portfolio
 */
add_action( 'init', function() {
  if ( get_option( 'va_enable_portfolio', '0' ) !== '1' ) {
    return;
  }

  $labels = array(
    'name'                       => _x( 'Project Types', 'Taxonomy General Name', 'vestelli-avalon' ),
    'singular_name'              => _x( 'Project Type', 'Taxonomy Singular Name', 'vestelli-avalon' ),
    'menu_name'                  => __( 'Project Types', 'vestelli-avalon' ),
    'all_items'                  => __( 'All Project Types', 'vestelli-avalon' ),
    'parent_item'                => __( 'Parent Project Type', 'vestelli-avalon' ),
    'parent_item_colon'          => __( 'Parent Project Type:', 'vestelli-avalon' ),
    'new_item_name'              => __( 'New Project Type Name', 'vestelli-avalon' ),
    'add_new_item'               => __( 'Add New Project Type', 'vestelli-avalon' ),
    'edit_item'                  => __( 'Edit Project Type', 'vestelli-avalon' ),
    'update_item'                => __( 'Update Project Type', 'vestelli-avalon' ),
    'view_item'                  => __( 'View Project Type', 'vestelli-avalon' ),
    'separate_items_with_commas' => __( 'Separate project types with commas', 'vestelli-avalon' ),
    'add_or_remove_items'        => __( 'Add or remove project types', 'vestelli-avalon' ),
    'choose_from_most_used'      => __( 'Choose from the most used', 'vestelli-avalon' ),
    'popular_items'              => __( 'Popular Project Types', 'vestelli-avalon' ),
    'search_items'               => __( 'Search Project Types', 'vestelli-avalon' ),
    'not_found'                  => __( 'Not Found', 'vestelli-avalon' ),
    'no_terms'                   => __( 'No project types', 'vestelli-avalon' ),
    'items_list'                 => __( 'Project types list', 'vestelli-avalon' ),
    'items_list_navigation'      => __( 'Project types list navigation', 'vestelli-avalon' ),
  );

  $args = array(
    'labels'                     => $labels,
    'hierarchical'               => true,
    'public'                     => true,
    'show_ui'                    => true,
    'show_admin_column'          => true,
    'show_in_nav_menus'          => true,
    'show_tagcloud'              => false,
    'show_in_rest'               => true,
    'rewrite'                    => array( 'slug' => 'project-type' ),
  );

  register_taxonomy( 'project-type', array( 'portfolio' ), $args );
} );
