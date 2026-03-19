<?php
/**
 * Frontend rendering for Tuotteen lisätiedot haitari module
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if we're on a product page
if ( ! function_exists( 'is_product' ) || ! is_product() ) {
  return;
}

// Check if ACF is active
if ( ! function_exists( 'get_field' ) ) {
  echo '<p>' . __( 'ACF plugin ei ole aktiivinen.', 'vestelli' ) . '</p>';
  return;
}

// Get the current product ID
global $product;
$product_id = get_the_ID();

// Get the ACF repeater field
$repeater_field = get_field( 'tuotteen_ominaisuudet', $product_id );

// Check if repeater has data
if ( ! $repeater_field || ! is_array( $repeater_field ) || empty( $repeater_field ) ) {
  return; // Don't display anything if no data
}

$accordion_id = ! empty( $module->settings->accordion_id ) ? esc_attr( $module->settings->accordion_id ) : 'tuotteen-lisatiedot-accordion';
$unique_id = $accordion_id . '-' . $product_id;

?>
<div class="tuotteen-lisatiedot-accordion" id="<?php echo esc_attr( $unique_id ); ?>" data-module-id="<?php echo esc_attr( $module->node ); ?>">
  <?php foreach ( $repeater_field as $index => $row ) : 
    $otsikko = isset( $row['otsikko'] ) ? $row['otsikko'] : '';
    $sisalto = isset( $row['sisalto'] ) ? $row['sisalto'] : '';
    
    if ( empty( $otsikko ) && empty( $sisalto ) ) {
      continue; // Skip empty rows
    }
    
    $item_id = $unique_id . '-item-' . $index;
  ?>
    <div class="accordion-item">
      <button class="accordion-header" aria-expanded="false" aria-controls="<?php echo esc_attr( $item_id ); ?>">
        <span class="accordion-title"><?php echo esc_html( $otsikko ); ?></span>
        <span class="accordion-icon">+</span>
      </button>
      <div class="accordion-content" id="<?php echo esc_attr( $item_id ); ?>" aria-hidden="true">
        <div class="accordion-inner">
          <?php echo wp_kses_post( $sisalto ); ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<?php
