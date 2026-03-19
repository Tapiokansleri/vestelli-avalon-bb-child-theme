<?php
/**
 * Custom Menu Walker
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class VA_Menu_Walker extends Walker_Nav_Menu {

  /**
   * Start the element output.
   */
  public function start_lvl( &$output, $depth = 0, $args = null ) {
    $indent = str_repeat( "\t", $depth );
    // Add depth class for styling different menu levels
    $depth_class = $depth > 0 ? ' sub-sub-menu' : '';
    $output .= "\n$indent<ul class=\"sub-menu{$depth_class}\">\n";
  }

  /**
   * End the element output.
   */
  public function end_lvl( &$output, $depth = 0, $args = null ) {
    $indent = str_repeat( "\t", $depth );
    $output .= "$indent</ul>\n";
  }

  /**
   * Start the element output.
   */
  public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    $has_children = in_array( 'menu-item-has-children', $classes );
    $is_placeholder_link = isset( $item->url ) && ( $item->url === '#' || $item->url === '' );

    if ( $is_placeholder_link ) {
      $classes[] = 'avalon-mega-no-link';
    }

    $class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
    $class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

    $id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args );
    $id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

    $output .= $indent . '<li' . $id . $class_names .'>';

    $attributes = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
    if ( ! empty( $item->url ) ) {
      $normalized_url = $item->url;
      if ( $is_placeholder_link ) {
        $normalized_url = 'javascript:void(0)';
      }
      $attributes .= ' href="' . esc_attr( $normalized_url ) . '"';
    }

    $item_output = isset( $args->before ) ? $args->before : '';
    
    // Check if we're in mobile menu
    $is_mobile_menu = false;
    if ( isset( $args->menu_class ) ) {
      $menu_class = is_string( $args->menu_class ) ? $args->menu_class : '';
      if ( strpos( $menu_class, 'mobile-main-menu' ) !== false ) {
        $is_mobile_menu = true;
      }
    }
    
    if ( $is_mobile_menu && $has_children ) {
      // Mobile: Separate link and toggle button
      $item_output .= '<div class="mobile-menu-item-wrapper">';
      $link_classes = 'mobile-menu-link';
      if ( $is_placeholder_link ) {
        $link_classes .= ' avalon-mega-no-link';
      }
      $item_output .= '<a' . $attributes .' class="' . esc_attr( $link_classes ) . '">';
      $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
      $item_output .= '</a>';
      $item_output .= '<button class="mobile-submenu-toggle" aria-label="Toggle submenu" aria-expanded="false"><span class="dropdown-icon">▼</span></button>';
      $item_output .= '</div>';
    } else {
      // Desktop: Normal link with icon
      $desktop_link_classes = $is_placeholder_link ? ' class="avalon-mega-no-link"' : '';
      $item_output .= '<a' . $attributes . $desktop_link_classes . '>';
      $item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . apply_filters( 'the_title', $item->title, $item->ID ) . ( isset( $args->link_after ) ? $args->link_after : '' );
      
      // Add chevron icon if has children (only for top-level items, depth === 0)
      if ( $has_children && $depth === 0 ) {
        $item_output .= '<span class="dropdown-chevron"><svg width="12" height="8" viewBox="0 0 12 8" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M1 1L6 6L11 1" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg></span>';
      }
      
      $item_output .= '</a>';
    }
    
    $item_output .= isset( $args->after ) ? $args->after : '';

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

  /**
   * End the element output.
   */
  public function end_el( &$output, $item, $depth = 0, $args = null ) {
    $output .= "</li>\n";
  }
}
