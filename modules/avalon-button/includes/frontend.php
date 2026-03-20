<?php
/**
 * Frontend template for Shop now and back button
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

// Get current product
global $post;
$product = null;
if ( function_exists( 'wc_get_product' ) && $post ) {
  $product = wc_get_product( $post->ID );
}

// Layout
$alignment = ! empty( $settings->alignment ) ? esc_attr( $settings->alignment ) : 'left';

// Shop / Quote button
$is_quote_mode = function_exists( 'va_is_quote_mode' ) && va_is_quote_mode();

if ( $is_quote_mode ) {
  $shop_text = ! empty( $settings->quote_label ) ? $settings->quote_label : '';
  if ( empty( $shop_text ) && function_exists( 'va_get_quote_button_text' ) ) {
    $shop_text = va_get_quote_button_text();
  }
  if ( empty( $shop_text ) ) {
    $shop_text = 'Pyydä tarjous';
  }
} else {
  $shop_text = ! empty( $settings->shop_label ) ? $settings->shop_label : 'Lisää ostoskoriin';
}

$shop_style = ! empty( $settings->shop_style ) ? esc_attr( $settings->shop_style ) : 'default';

// Build add-to-cart URL
$shop_url = '#';
if ( $product ) {
  $shop_url = $product->add_to_cart_url();
}

// Back button
$show_back = isset( $settings->show_back_button ) ? $settings->show_back_button === 'yes' : true;
$back_text = ! empty( $settings->back_label ) ? $settings->back_label : 'Takaisin tuoteryhmän tuotteisiin';
$back_style = ! empty( $settings->back_style ) ? esc_attr( $settings->back_style ) : 'light-blue';

// Get the product's primary category URL
$back_url = '';
if ( $product && $show_back ) {
  $terms = get_the_terms( $post->ID, 'product_cat' );
  if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
    // Prefer the deepest (most specific) category, skip "Uncategorized"
    $best_term = null;
    foreach ( $terms as $term ) {
      if ( $term->slug === 'uncategorized' || $term->slug === 'ei-kategoriaa' ) {
        continue;
      }
      if ( ! $best_term || $term->parent > 0 ) {
        $best_term = $term;
      }
    }
    if ( ! $best_term && ! empty( $terms ) ) {
      $best_term = $terms[0];
    }
    if ( $best_term ) {
      $back_url = get_term_link( $best_term );
      if ( is_wp_error( $back_url ) ) {
        $back_url = '';
      }
    }
  }
  // Fallback to shop page
  if ( empty( $back_url ) ) {
    $back_url = get_permalink( wc_get_page_id( 'shop' ) );
  }
}

// Wrapper classes
$wrapper_classes = array( 'avalon-button-wrapper', 'avalon-button-align-' . $alignment );
if ( $show_back ) {
  $wrapper_classes[] = 'avalon-button-has-two';
}
?>

<div class="<?php echo esc_attr( implode( ' ', $wrapper_classes ) ); ?>">
  <?php
    $can_add = $product && $product->is_type( 'simple' ) && $product->is_in_stock()
               && ( $product->is_purchasable() || $is_quote_mode );
  ?>
  <?php if ( $can_add ) :
    // In quote mode without price, build the add-to-cart URL manually
    $add_url = $is_quote_mode && ! $product->is_purchasable()
      ? add_query_arg( 'add-to-cart', $product->get_id(), wc_get_cart_url() )
      : $shop_url;
  ?>
    <a href="<?php echo esc_url( $add_url ); ?>"
       data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
       data-quantity="1"
       class="avalon-button avalon-button-<?php echo $shop_style; ?> add_to_cart_button<?php echo $product->is_purchasable() ? ' ajax_add_to_cart' : ''; ?>"
       aria-label="<?php echo esc_attr( $shop_text ); ?>">
      <span class="avalon-button-text"><?php echo esc_html( $shop_text ); ?></span>
    </a>
  <?php elseif ( $product ) : ?>
    <a href="<?php echo esc_url( $product->get_permalink() ); ?>"
       class="avalon-button avalon-button-<?php echo $shop_style; ?>">
      <span class="avalon-button-text"><?php echo esc_html( $shop_text ); ?></span>
    </a>
  <?php else : ?>
    <span class="avalon-button avalon-button-<?php echo $shop_style; ?>">
      <span class="avalon-button-text"><?php echo esc_html( $shop_text ); ?></span>
    </span>
  <?php endif; ?>

  <?php if ( $show_back && ! empty( $back_url ) ) : ?>
    <a href="<?php echo esc_url( $back_url ); ?>"
       class="avalon-button avalon-button-<?php echo $back_style; ?>">
      <span class="avalon-button-text"><?php echo esc_html( $back_text ); ?></span>
    </a>
  <?php endif; ?>
</div>
