<?php
/**
 * Frontend template for Gravity Forms module
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if Gravity Forms is active
if ( ! class_exists( 'GFAPI' ) ) {
  echo '<p class="avalon-gravity-forms-error">' . __( 'Gravity Forms ei ole aktiivinen.', 'vestelli' ) . '</p>';
  return;
}

// Settings are passed from render method
if ( ! isset( $settings ) ) {
  $settings = isset( $module ) ? $module->settings : (object) array();
}

// Get form ID
$form_id = ! empty( $settings->form_id ) ? absint( $settings->form_id ) : 0;

// If no form selected, show message
if ( empty( $form_id ) ) {
  echo '<p class="avalon-gravity-forms-error">' . __( 'Valitse lomake moduulin asetuksista.', 'vestelli' ) . '</p>';
  return;
}

// Check if form exists
$form = GFAPI::get_form( $form_id );
if ( ! $form || is_wp_error( $form ) ) {
  echo '<p class="avalon-gravity-forms-error">' . __( 'Lomaketta ei löytynyt.', 'vestelli' ) . '</p>';
  return;
}

// Get settings
$show_title = ! empty( $settings->title ) && $settings->title === 'yes';
$show_description = ! empty( $settings->description ) && $settings->description === 'yes';
$ajax = ! empty( $settings->ajax ) && $settings->ajax === 'yes';

// Output form
?>
<div class="avalon-gravity-forms-wrapper">
  <?php
  // Display Gravity Form
  // gravity_form() echoes by default, so we use it directly
  gravity_form(
    $form_id,
    $show_title,      // Display title
    $show_description, // Display description
    false,            // Display inactive
    null,             // Field values
    $ajax,            // AJAX
    '',               // Tab index
    true              // Echo (default)
  );
  ?>
</div>
