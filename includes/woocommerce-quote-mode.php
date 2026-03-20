<?php
/**
 * WooCommerce Quote Mode
 *
 * Converts WooCommerce into a quote request system:
 * - Hides all prices (optional)
 * - Replaces "Add to cart" button text
 * - Checkout sends email quote request without payment
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Check if quote mode is active.
 */
function va_is_quote_mode() {
  return get_option( 'va_quote_mode', '0' ) === '1';
}

/**
 * Check if prices should be hidden.
 */
function va_hide_prices() {
  return va_is_quote_mode() && get_option( 'va_hide_prices', '0' ) === '1';
}

/**
 * Get the quote button text with WPML support.
 */
function va_get_quote_button_text() {
  $text = get_option( 'va_quote_button_text', 'Pyydä tarjous' );
  if ( function_exists( 'icl_t' ) ) {
    $text = icl_t( 'vestelli-avalon', 'Quote button text', $text );
  }
  return $text;
}

// Bail early if WooCommerce is not active.
if ( ! class_exists( 'WooCommerce' ) ) {
  return;
}

// ─── WPML String Registration ───────────────────────────────────────────────

add_action( 'admin_init', function() {
  if ( ! va_is_quote_mode() || ! function_exists( 'icl_register_string' ) ) {
    return;
  }
  $button_text = get_option( 'va_quote_button_text', 'Pyydä tarjous' );
  icl_register_string( 'vestelli-avalon', 'Quote button text', $button_text );
  icl_register_string( 'vestelli-avalon', 'Quote checkout button', 'Lähetä tarjouspyyntö' );
  icl_register_string( 'vestelli-avalon', 'Quote proceed to checkout', 'Jatka tarjouspyyntöön' );
} );

// ─── A. Hide Prices ─────────────────────────────────────────────────────────

add_filter( 'woocommerce_get_price_html', function( $price ) {
  if ( va_hide_prices() && ! is_admin() ) {
    return '';
  }
  return $price;
}, 9999 );

add_filter( 'woocommerce_variable_price_html', function( $price ) {
  if ( va_hide_prices() && ! is_admin() ) {
    return '';
  }
  return $price;
}, 9999 );

add_filter( 'woocommerce_grouped_price_html', function( $price ) {
  if ( va_hide_prices() && ! is_admin() ) {
    return '';
  }
  return $price;
}, 9999 );

add_filter( 'woocommerce_cart_item_price', function( $price ) {
  if ( va_hide_prices() ) {
    return '';
  }
  return $price;
}, 9999 );

add_filter( 'woocommerce_cart_item_subtotal', function( $subtotal ) {
  if ( va_hide_prices() ) {
    return '';
  }
  return $subtotal;
}, 9999 );

add_filter( 'woocommerce_cart_totals_order_total_html', function( $total ) {
  if ( va_hide_prices() ) {
    return '';
  }
  return $total;
}, 9999 );

add_filter( 'woocommerce_widget_cart_item_quantity', function( $quantity ) {
  if ( va_hide_prices() ) {
    // Strip price, keep just the quantity.
    return preg_replace( '/<span class="quantity">.*<\/span>/s', '', $quantity );
  }
  return $quantity;
}, 9999 );

add_filter( 'woocommerce_cart_subtotal', function( $subtotal ) {
  if ( va_hide_prices() ) {
    return '';
  }
  return $subtotal;
}, 9999 );

add_filter( 'woocommerce_order_formatted_line_subtotal', function( $subtotal ) {
  if ( va_hide_prices() ) {
    return '';
  }
  return $subtotal;
}, 9999 );

add_filter( 'woocommerce_get_order_item_totals', function( $rows ) {
  if ( va_hide_prices() ) {
    unset( $rows['cart_subtotal'], $rows['order_total'] );
  }
  return $rows;
}, 9999 );

// CSS fallback for cached pages + hide price columns and totals.
add_action( 'wp_head', function() {
  if ( ! va_hide_prices() ) {
    return;
  }
  ?>
  <style>
    /* Product prices everywhere */
    .woocommerce .price,
    .woocommerce .amount,
    .woocommerce-Price-amount,
    .widget .price,
    .widget .amount,
    .woocommerce-variation-price { display: none !important; }

    /* Cart table: hide price & subtotal columns (header + cells) */
    .woocommerce-cart-form table.shop_table th.product-price,
    .woocommerce-cart-form table.shop_table td.product-price,
    .woocommerce-cart-form table.shop_table th.product-subtotal,
    .woocommerce-cart-form table.shop_table td.product-subtotal { display: none !important; }

    /* Cart totals: hide price rows but keep the checkout button */
    .cart_totals .shop_table { display: none !important; }
    .cart_totals h2 { display: none !important; }

    /* Checkout order review: hide price & total columns */
    .woocommerce-checkout-review-order-table th:last-child,
    .woocommerce-checkout-review-order-table td:last-child,
    .woocommerce-checkout-review-order-table tfoot { display: none !important; }

    /* Order received / thank-you page */
    .woocommerce-order-overview__total,
    .woocommerce-table--order-details tfoot,
    .woocommerce-table--order-details th:last-child,
    .woocommerce-table--order-details td:last-child { display: none !important; }
  </style>
  <?php
}, 99 );

// Checkout page layout & form styling in quote mode.
add_action( 'wp_head', function() {
  if ( ! va_is_quote_mode() ) {
    return;
  }
  ?>
  <style>
    /* ── Checkout layout ── */

    /* Align col-2 (Lisätiedot / Tilauksen kommentit) to the top */
    .woocommerce-checkout .col2-set {
      display: flex;
      flex-wrap: wrap;
      align-items: flex-start;
    }
    .woocommerce-checkout .col2-set .col-1,
    .woocommerce-checkout .col2-set .col-2 {
      flex: 1 1 45%;
      min-width: 300px;
    }
    .woocommerce-checkout .col2-set .col-1 {
      padding-right: 30px;
    }

    /* ── Unified form field styling ── */
    .woocommerce-checkout .form-row input.input-text,
    .woocommerce-checkout .form-row textarea,
    .woocommerce-checkout .form-row select,
    .woocommerce-checkout .select2-container .select2-selection--single {
      font-size: 15px !important;
      padding: 10px 12px !important;
      border: 1px solid #ccc !important;
      border-radius: 6px !important;
      background: #fff !important;
      width: 100% !important;
      box-sizing: border-box !important;
      font-family: inherit !important;
      line-height: 1.4 !important;
      -webkit-appearance: none;
      -moz-appearance: none;
      appearance: none;
    }
    /* Native select arrow */
    .woocommerce-checkout .form-row select {
      background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='8'%3E%3Cpath d='M1 1l5 5 5-5' stroke='%23333' stroke-width='1.5' fill='none'/%3E%3C/svg%3E") !important;
      background-repeat: no-repeat !important;
      background-position: right 12px center !important;
      padding-right: 36px !important;
    }
    /* Select2 dropdown override */
    .woocommerce-checkout .select2-container .select2-selection--single {
      height: 42px !important;
      min-height: 42px !important;
      max-height: 42px !important;
      display: flex !important;
      align-items: center !important;
      border: 1px solid #ccc !important;
      border-radius: 6px !important;
      background: #fff !important;
      padding: 0 !important;
    }
    .woocommerce-checkout .select2-container .select2-selection__rendered {
      padding: 0 12px !important;
      line-height: 42px !important;
      font-size: 15px !important;
      color: #333 !important;
    }
    .woocommerce-checkout .select2-container .select2-selection__arrow {
      height: 42px !important;
      right: 8px !important;
    }
    /* Textarea match */
    .woocommerce-checkout #order_comments,
    .woocommerce-checkout .form-row textarea {
      font-size: 15px !important;
      padding: 10px 12px !important;
      border: 1px solid #ccc !important;
      border-radius: 6px !important;
      background: #fff !important;
      font-family: inherit !important;
      line-height: 1.4 !important;
    }

    /* Consistent label styling */
    .woocommerce-checkout .form-row label {
      font-size: 14px;
      font-weight: 500;
      margin-bottom: 4px;
      display: block;
      color: #333;
    }

    /* Consistent spacing between fields */
    .woocommerce-checkout .form-row {
      margin-bottom: 16px;
    }

    /* Order notes textarea */
    .woocommerce-checkout #order_comments {
      min-height: 120px;
      resize: vertical;
    }

    /* Section headings */
    .woocommerce-checkout h3 {
      font-size: 20px;
      font-weight: 600;
      margin: 0 0 20px;
      color: #002b56;
    }

    /* ── Order review table cleanup ── */
    .woocommerce-checkout-review-order-table {
      border-collapse: collapse;
      width: 100%;
      margin-bottom: 20px;
    }
    .woocommerce-checkout-review-order-table th,
    .woocommerce-checkout-review-order-table td {
      padding: 10px 12px;
      font-size: 15px;
      border-bottom: 1px solid #eee;
    }

    /* Submit button */
    .woocommerce-checkout #place_order {
      font-size: 16px;
      font-weight: 600;
      padding: 14px 32px;
      border-radius: 8px;
      background-color: #002b56;
      color: #fff;
      border: none;
      cursor: pointer;
      width: 100%;
      margin-top: 10px;
    }
    .woocommerce-checkout #place_order:hover {
      background-color: #003d7a;
    }

    /* ── Responsive ── */
    @media (max-width: 768px) {
      .woocommerce-checkout .col2-set .col-1,
      .woocommerce-checkout .col2-set .col-2 {
        flex: 1 1 100%;
      }
    }
  </style>
  <?php
}, 100 );

// Allow products without a price to be purchasable in quote mode.
add_filter( 'woocommerce_is_purchasable', function( $purchasable, $product ) {
  if ( va_is_quote_mode() && ! $purchasable && $product->get_price() === '' ) {
    return true;
  }
  return $purchasable;
}, 9999, 2 );

// ─── B. Replace Button Text ─────────────────────────────────────────────────

add_filter( 'woocommerce_product_add_to_cart_text', function( $text ) {
  if ( va_is_quote_mode() ) {
    return va_get_quote_button_text();
  }
  return $text;
}, 9999 );

add_filter( 'woocommerce_product_single_add_to_cart_text', function( $text ) {
  if ( va_is_quote_mode() ) {
    return va_get_quote_button_text();
  }
  return $text;
}, 9999 );

// ─── C. Quote Checkout ──────────────────────────────────────────────────────

// Set cart total to 0 so no payment is needed.
add_filter( 'woocommerce_calculated_total', function( $total ) {
  if ( va_is_quote_mode() ) {
    return 0;
  }
  return $total;
}, 9999 );

// Remove all payment gateways.
add_filter( 'woocommerce_available_payment_gateways', function( $gateways ) {
  if ( va_is_quote_mode() ) {
    return array();
  }
  return $gateways;
}, 9999 );

// Change "Place order" button text.
add_filter( 'woocommerce_order_button_text', function( $text ) {
  if ( va_is_quote_mode() ) {
    $translated = 'Lähetä tarjouspyyntö';
    if ( function_exists( 'icl_t' ) ) {
      $translated = icl_t( 'vestelli-avalon', 'Quote checkout button', $translated );
    }
    return $translated;
  }
  return $text;
} );

// Disable coupons in quote mode.
add_filter( 'woocommerce_coupons_enabled', function( $enabled ) {
  if ( va_is_quote_mode() ) {
    return false;
  }
  return $enabled;
} );

// Change "Proceed to checkout" text.
add_filter( 'gettext', function( $translated, $text, $domain ) {
  if ( $domain !== 'woocommerce' || ! va_is_quote_mode() ) {
    return $translated;
  }
  if ( $text === 'Proceed to checkout' ) {
    $quote_text = 'Jatka tarjouspyyntöön';
    if ( function_exists( 'icl_t' ) ) {
      $quote_text = icl_t( 'vestelli-avalon', 'Quote proceed to checkout', $quote_text );
    }
    return $quote_text;
  }
  return $translated;
}, 10, 3 );

// Send quote email and set order status after checkout.
add_action( 'woocommerce_checkout_order_processed', function( $order_id ) {
  if ( ! va_is_quote_mode() ) {
    return;
  }

  $order = wc_get_order( $order_id );
  if ( ! $order ) {
    return;
  }

  // Set order status to on-hold.
  $order->update_status( 'on-hold', __( 'Tarjouspyyntö vastaanotettu.', 'vestelli-avalon' ) );

  // Build and send quote email.
  $to = get_option( 'va_quote_email', get_option( 'admin_email' ) );
  $site_name = get_bloginfo( 'name' );
  $subject = sprintf( 'Uusi tarjouspyyntö - %s (#%s)', $site_name, $order->get_order_number() );

  $billing_name    = $order->get_billing_first_name() . ' ' . $order->get_billing_last_name();
  $billing_company = $order->get_billing_company();
  $billing_email   = $order->get_billing_email();
  $billing_phone   = $order->get_billing_phone();
  $billing_address = $order->get_formatted_billing_address();
  $order_notes     = $order->get_customer_note();

  // Build items list.
  $items_html = '<table style="width:100%;border-collapse:collapse;margin:15px 0;">';
  $items_html .= '<tr style="background:#f5f5f5;"><th style="padding:8px;text-align:left;border:1px solid #ddd;">' . __( 'Tuote', 'vestelli-avalon' ) . '</th>';
  $items_html .= '<th style="padding:8px;text-align:center;border:1px solid #ddd;">' . __( 'Määrä', 'vestelli-avalon' ) . '</th>';
  $items_html .= '<th style="padding:8px;text-align:left;border:1px solid #ddd;">' . __( 'SKU', 'vestelli-avalon' ) . '</th></tr>';

  foreach ( $order->get_items() as $item ) {
    $product = $item->get_product();
    $sku = $product ? $product->get_sku() : '';
    $items_html .= '<tr>';
    $items_html .= '<td style="padding:8px;border:1px solid #ddd;">' . esc_html( $item->get_name() ) . '</td>';
    $items_html .= '<td style="padding:8px;text-align:center;border:1px solid #ddd;">' . esc_html( $item->get_quantity() ) . '</td>';
    $items_html .= '<td style="padding:8px;border:1px solid #ddd;">' . esc_html( $sku ) . '</td>';
    $items_html .= '</tr>';
  }
  $items_html .= '</table>';

  $admin_url = admin_url( 'post.php?post=' . $order_id . '&action=edit' );

  $body = '<html><body style="font-family:Arial,sans-serif;color:#333;">';
  $body .= '<h2>' . sprintf( __( 'Uusi tarjouspyyntö #%s', 'vestelli-avalon' ), $order->get_order_number() ) . '</h2>';
  $body .= '<h3>' . __( 'Asiakkaan tiedot', 'vestelli-avalon' ) . '</h3>';
  $body .= '<p><strong>' . __( 'Nimi', 'vestelli-avalon' ) . ':</strong> ' . esc_html( $billing_name ) . '</p>';
  if ( $billing_company ) {
    $body .= '<p><strong>' . __( 'Yritys', 'vestelli-avalon' ) . ':</strong> ' . esc_html( $billing_company ) . '</p>';
  }
  $body .= '<p><strong>' . __( 'Sähköposti', 'vestelli-avalon' ) . ':</strong> ' . esc_html( $billing_email ) . '</p>';
  if ( $billing_phone ) {
    $body .= '<p><strong>' . __( 'Puhelin', 'vestelli-avalon' ) . ':</strong> ' . esc_html( $billing_phone ) . '</p>';
  }
  if ( $billing_address ) {
    $body .= '<p><strong>' . __( 'Osoite', 'vestelli-avalon' ) . ':</strong><br>' . $billing_address . '</p>';
  }

  $body .= '<h3>' . __( 'Tuotteet', 'vestelli-avalon' ) . '</h3>';
  $body .= $items_html;

  if ( $order_notes ) {
    $body .= '<h3>' . __( 'Lisätiedot', 'vestelli-avalon' ) . '</h3>';
    $body .= '<p>' . nl2br( esc_html( $order_notes ) ) . '</p>';
  }

  $body .= '<p><a href="' . esc_url( $admin_url ) . '">' . __( 'Näytä tilaus hallintapaneelissa', 'vestelli-avalon' ) . '</a></p>';
  $body .= '</body></html>';

  $headers = array(
    'Content-Type: text/html; charset=UTF-8',
    'Reply-To: ' . $billing_name . ' <' . $billing_email . '>',
  );

  wp_mail( $to, $subject, $body, $headers );
}, 10, 1 );

// Custom thank-you page message.
add_filter( 'woocommerce_thankyou_order_received_text', function( $text ) {
  if ( va_is_quote_mode() ) {
    return __( 'Kiitos tarjouspyynnöstäsi! Olemme vastaanottaneet pyyntösi ja otamme sinuun yhteyttä mahdollisimman pian.', 'vestelli-avalon' );
  }
  return $text;
} );
