<?php
/**
 * Frontend template for Avalon Button
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Get layout settings
$alignment = ! empty( $settings->alignment ) ? esc_attr( $settings->alignment ) : 'left';
$show_second_button = isset( $settings->show_second_button ) && $settings->show_second_button === 'yes';

// First button settings
$button_text = ! empty( $settings->button_text ) ? esc_html( $settings->button_text ) : 'Pyydä tarjous';
$button_url = ! empty( $settings->button_url ) ? esc_url( $settings->button_url ) : '/pyyda-tarjous';
$open_new_tab = isset( $settings->open_new_tab ) && $settings->open_new_tab === 'yes' ? '_blank' : '_self';
$button_style = ! empty( $settings->button_style ) ? esc_attr( $settings->button_style ) : 'default';

// Second button settings
$button2_text = ! empty( $settings->button2_text ) ? esc_html( $settings->button2_text ) : 'Painikkeen teksti';
$button2_url = ! empty( $settings->button2_url ) ? esc_url( $settings->button2_url ) : '#';
$button2_open_new_tab = isset( $settings->button2_open_new_tab ) && $settings->button2_open_new_tab === 'yes' ? '_blank' : '_self';
$button2_style = ! empty( $settings->button2_style ) ? esc_attr( $settings->button2_style ) : 'default';

// Build wrapper classes
$wrapper_classes = array( 'avalon-button-wrapper', 'avalon-button-align-' . $alignment );
if ( $show_second_button ) {
  $wrapper_classes[] = 'avalon-button-has-two';
}
$wrapper_class = implode( ' ', $wrapper_classes );

// Build first button classes
$button_classes = array( 'avalon-button', 'avalon-button-' . $button_style );
$button_class = implode( ' ', $button_classes );

// Build second button classes
$button2_classes = array( 'avalon-button', 'avalon-button-' . $button2_style );
$button2_class = implode( ' ', $button2_classes );
?>

<div class="<?php echo $wrapper_class; ?>">
  <a href="<?php echo $button_url; ?>" 
     target="<?php echo $open_new_tab; ?>"
     class="<?php echo $button_class; ?>"
     rel="<?php echo $open_new_tab === '_blank' ? 'noopener noreferrer' : ''; ?>">
    <span class="avalon-button-text"><?php echo $button_text; ?></span>
  </a>
  
  <?php if ( $show_second_button ) : ?>
    <a href="<?php echo $button2_url; ?>" 
       target="<?php echo $button2_open_new_tab; ?>"
       class="<?php echo $button2_class; ?>"
       rel="<?php echo $button2_open_new_tab === '_blank' ? 'noopener noreferrer' : ''; ?>">
      <span class="avalon-button-text"><?php echo $button2_text; ?></span>
    </a>
  <?php endif; ?>
</div>
