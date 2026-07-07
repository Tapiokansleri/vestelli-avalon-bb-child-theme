<?php
/**
 * Frontend template for WooCommerce Osastot module
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if WooCommerce is active
if ( ! class_exists( 'WooCommerce' ) ) {
  echo '<p>' . esc_html__( 'WooCommerce ei ole aktiivinen.', 'vestelli-avalon' ) . '</p>';
  return;
}

// Settings are passed from render method
if ( ! isset( $settings ) ) {
  $settings = isset( $module ) ? $module->settings : (object) array();
}

// Get settings with defaults
$show_title = isset( $settings->show_title ) ? $settings->show_title : 'yes';
$title_text = isset( $settings->title_text ) && ! empty( $settings->title_text ) ? $settings->title_text : __( 'Osastot', 'vestelli-avalon' );
$columns = isset( $settings->columns ) ? intval( $settings->columns ) : 4;
if ( ! in_array( $columns, array( 2, 3, 4 ), true ) ) {
  $columns = 4;
}
$category_source = isset( $settings->category_source ) ? $settings->category_source : 'all';
$order_by = isset( $settings->order_by ) ? $settings->order_by : 'name';
$order = isset( $settings->order ) && strtoupper( $settings->order ) === 'DESC' ? 'DESC' : 'ASC';
$empty_selected_behavior = isset( $settings->empty_selected_behavior ) ? $settings->empty_selected_behavior : 'all';

$allowed_order_by = array(
  'name'    => 'name',
  'slug'    => 'slug',
  'id'      => 'term_id',
  'count'   => 'count',
  'include' => 'include',
);
$order_by = isset( $allowed_order_by[ $order_by ] ) ? $allowed_order_by[ $order_by ] : 'name';

// Parse selected categories from draggable form rows (preferred UI).
$selected_categories = array();
if ( ! empty( $settings->selected_category_rows ) && is_array( $settings->selected_category_rows ) ) {
  foreach ( $settings->selected_category_rows as $row ) {
    if ( is_object( $row ) && isset( $row->category_id ) ) {
      $selected_categories[] = $row->category_id;
    }
  }
}

// Backward compatibility: support old multi-select setting if present.
if ( empty( $selected_categories ) && isset( $settings->selected_categories ) ) {
  if ( is_array( $settings->selected_categories ) ) {
    $selected_categories = $settings->selected_categories;
  } elseif ( is_string( $settings->selected_categories ) && $settings->selected_categories !== '' ) {
    $selected_categories = explode( ',', $settings->selected_categories );
  }
}
$selected_categories = array_values( array_unique( array_filter( array_map( 'absint', $selected_categories ) ) ) );

// Map selected category IDs to current language terms when WPML is active.
if ( ! empty( $selected_categories ) && has_filter( 'wpml_object_id' ) ) {
  $selected_categories = array_values(
    array_unique(
      array_filter(
        array_map(
          function( $term_id ) {
            $translated_id = apply_filters( 'wpml_object_id', (int) $term_id, 'product_cat', true );
            return $translated_id ? (int) $translated_id : (int) $term_id;
          },
          $selected_categories
        )
      )
    )
  );
}

// Make custom heading setting translatable via WPML String Translation.
if ( ! empty( $title_text ) && has_filter( 'wpml_translate_single_string' ) ) {
  $string_key = 'woocommerce-osastot-title-' . get_the_ID();
  do_action( 'wpml_register_single_string', 'vestelli-avalon', $string_key, $title_text );
  $title_text = apply_filters( 'wpml_translate_single_string', $title_text, 'vestelli-avalon', $string_key );
}

// Build category query
$term_query_args = array(
  'taxonomy'   => 'product_cat',
  'hide_empty' => true,
  'parent'     => 0,
  'orderby'    => $order_by,
  'order'      => $order,
);

// Limit to selected categories when requested
if ( $category_source === 'selected' ) {
  if ( ! empty( $selected_categories ) ) {
    $term_query_args['include'] = $selected_categories;

    // Preserve the chosen order when explicitly requested
    if ( $order_by === 'include' ) {
      $term_query_args['orderby'] = 'include';
      unset( $term_query_args['order'] );
    }
  } elseif ( $empty_selected_behavior === 'empty' ) {
    echo '<p class="avalon-osastot-empty">' . esc_html__( 'Valittuja osastoja ei löytynyt.', 'vestelli-avalon' ) . '</p>';
    return;
  }
}

// "include" order only makes sense with include list
if ( $term_query_args['orderby'] === 'include' && empty( $term_query_args['include'] ) ) {
  $term_query_args['orderby'] = 'name';
  $term_query_args['order'] = 'ASC';
}

$categories = get_terms( $term_query_args );

// Filter to current WPML language and recount published products only
if ( ! empty( $categories ) && ! is_wp_error( $categories ) ) {
  $filtered = array();
  foreach ( $categories as $cat ) {
    // WPML: skip terms that belong to a different language
    if ( has_filter( 'wpml_object_id' ) ) {
      $translated_id = apply_filters( 'wpml_object_id', $cat->term_id, 'product_cat', false );
      if ( ! $translated_id || (int) $translated_id !== (int) $cat->term_id ) {
        continue;
      }
    }
    // Recount only published products in this category
    $published_count = new WP_Query( array(
      'post_type'      => 'product',
      'post_status'    => 'publish',
      'posts_per_page' => -1,
      'fields'         => 'ids',
      'no_found_rows'  => true,
      'tax_query'      => array( array(
        'taxonomy' => 'product_cat',
        'field'    => 'term_id',
        'terms'    => $cat->term_id,
      ) ),
      'suppress_filters' => false,
    ) );
    $cat->count = $published_count->post_count;
    if ( $cat->count > 0 ) {
      $filtered[] = $cat;
    }
  }
  $categories = $filtered;
}

// If no categories found, show message
if ( empty( $categories ) || is_wp_error( $categories ) ) {
  echo '<p class="avalon-osastot-empty">' . esc_html__( 'Osastoja ei löytynyt.', 'vestelli-avalon' ) . '</p>';
  return;
}

// Calculate column width percentage
$column_width = 100 / $columns;
?>

<div class="avalon-osastot-wrapper">
  <?php if ( $show_title === 'yes' && ! empty( $title_text ) ) : ?>
    <h2 class="avalon-osastot-title"><?php echo esc_html( $title_text ); ?></h2>
  <?php endif; ?>

  <div class="avalon-osastot-grid avalon-osastot-columns-<?php echo esc_attr( $columns ); ?>" style="grid-template-columns: repeat(<?php echo esc_attr( $columns ); ?>, 1fr);">
    <?php foreach ( $categories as $category ) :
      $category_link = get_term_link( $category );
      $thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
      $image_url = '';

      if ( $thumbnail_id ) {
        $image_url = wp_get_attachment_image_url( $thumbnail_id, 'medium' );
      }

      // Fallback to default image setting, then WooCommerce placeholder
      if ( ! $image_url ) {
        $default_image = isset( $settings->default_image_src ) ? $settings->default_image_src : '';
        if ( $default_image ) {
          $image_url = $default_image;
        } else {
          $image_url = wc_placeholder_img_src( 'medium' );
        }
      }

      if ( is_wp_error( $category_link ) ) {
        continue;
      }
      ?>
      <div class="avalon-osasto-item">
        <a href="<?php echo esc_url( $category_link ); ?>" class="avalon-osasto-link">
          <?php if ( $image_url ) : ?>
            <div class="avalon-osasto-image">
              <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $category->name ); ?>" />
            </div>
          <?php endif; ?>
          <div class="avalon-osasto-content">
            <h3 class="avalon-osasto-name"><?php echo esc_html( $category->name ); ?></h3>
            <?php if ( $category->count > 0 ) : ?>
              <span class="avalon-osasto-count"><?php echo esc_html( $category->count ); ?> <?php echo _n( 'tuote', 'tuotetta', $category->count, 'vestelli-avalon' ); ?></span>
            <?php endif; ?>
          </div>
        </a>
      </div>
    <?php endforeach; ?>
  </div>
</div>
