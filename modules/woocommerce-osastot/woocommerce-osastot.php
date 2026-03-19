<?php
/**
 * WooCommerce Osastot Module
 * 
 * Listaa kaikki WooCommercen osastot (ylätason kategoriat)
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class WooCommerceOsastot extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'WooCommerce Osastot', 'vestelli' ),
      'description'     => __( 'Listaa kaikki WooCommercen osastot (ylätason kategoriat)', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/woocommerce-osastot/',
      'url'             => VESTELLI_MODULES_URL . '/woocommerce-osastot/',
      'icon'            => 'button.svg',
      'editor_export'   => true,
      'enabled'         => true,
      'partial_refresh' => false,
    ) );
  }

  /**
   * Render frontend
   */
  public function render( $settings ) {
    $module = $this;
    $settings = $this->settings;
    include $this->dir . 'includes/frontend.php';
  }

  /**
   * Enqueue styles
   */
  public function enqueue_styles() {
    $this->add_css( 'frontend', $this->url . 'css/frontend.css' );
  }
}

/**
 * Register the module
 */
FLBuilder::register_module( 'WooCommerceOsastot', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli' ),
        'fields' => array(
          'show_title' => array(
            'type'        => 'select',
            'label'       => __( 'Näytä otsikko', 'vestelli' ),
            'default'     => 'yes',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli' ),
              'no'  => __( 'Ei', 'vestelli' ),
            ),
          ),
          'title_text' => array(
            'type'        => 'text',
            'label'       => __( 'Otsikon teksti', 'vestelli' ),
            'default'     => __( 'Osastot', 'vestelli' ),
            'help'        => __( 'Jätä tyhjäksi käyttääksesi oletusotsikkoa', 'vestelli' ),
          ),
          'columns' => array(
            'type'        => 'select',
            'label'       => __( 'Sarakkeet', 'vestelli' ),
            'default'     => '4',
            'options'     => array(
              '2' => __( '2 saraketta', 'vestelli' ),
              '3' => __( '3 saraketta', 'vestelli' ),
              '4' => __( '4 saraketta', 'vestelli' ),
            ),
          ),
        ),
      ),
    ),
  ),
) );
