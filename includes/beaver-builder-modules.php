<?php
/**
 * Register Beaver Builder Custom Modules
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

// Check if Beaver Builder is active
if ( ! class_exists( 'FLBuilder' ) ) {
  return;
}

/**
 * Define module directory paths
 */
define( 'VESTELLI_MODULES', get_stylesheet_directory() . '/modules' );
define( 'VESTELLI_MODULES_URL', get_stylesheet_directory_uri() . '/modules' );

/**
 * Register custom modules
 */
add_action( 'init', function() {
  if ( class_exists( 'FLBuilder' ) ) {
    // Register Tuotteen lisätiedot haitari module
    require_once VESTELLI_MODULES . '/tuotteen-lisatiedot-haitari/tuotteen-lisatiedot-haitari.php';
    // Register Vestelli Button module
    require_once VESTELLI_MODULES . '/avalon-button/avalon-button.php';
    // Register Liitetyt tuotteet module
    require_once VESTELLI_MODULES . '/liitetyt-tuotteet/liitetyt-tuotteet.php';
    // Register Liittyvät tuotteet module
    require_once VESTELLI_MODULES . '/liittyvat-tuotteet/liittyvat-tuotteet.php';
    // Register Gravity Forms module
    require_once VESTELLI_MODULES . '/gravity-forms/gravity-forms.php';
    // Register Asiakkaiden kokemuksia module
    require_once VESTELLI_MODULES . '/asiakkaiden-kokemuksia/asiakkaiden-kokemuksia.php';
    // Register WooCommerce Osastot module
    require_once VESTELLI_MODULES . '/woocommerce-osastot/woocommerce-osastot.php';
    // Register Vestelli lukuina module
    require_once VESTELLI_MODULES . '/stats-numbers/stats-numbers.php';
  }
}, 1);
