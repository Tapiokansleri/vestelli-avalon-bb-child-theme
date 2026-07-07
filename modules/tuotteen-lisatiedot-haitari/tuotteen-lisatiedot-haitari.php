<?php
/**
 * Tuotteen lisätiedot haitari Module
 * 
 * Displays ACF repeater field as an accordion on WooCommerce product pages
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class TuotteenLisatiedotHaitari extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Tuotteen lisätiedot haitari', 'vestelli' ),
      'description'     => __( 'Näyttää ACF repeater kentän accordionina WooCommerce tuotesivulla', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/tuotteen-lisatiedot-haitari/',
      'url'             => VESTELLI_MODULES_URL . '/tuotteen-lisatiedot-haitari/',
      'icon'            => 'list.svg',
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
    include $this->dir . 'includes/frontend.php';
  }

  /**
   * Enqueue scripts
   */
  public function enqueue_scripts() {
    $this->add_js( 'jquery' );
    $this->add_js( 'frontend', $this->url . 'js/frontend.js' );
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
FLBuilder::register_module( 'TuotteenLisatiedotHaitari', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli' ),
        'fields' => array(
          'accordion_id' => array(
            'type'        => 'text',
            'label'       => __( 'Accordion ID', 'vestelli' ),
            'default'     => 'tuotteen-lisatiedot-accordion',
            'help'        => __( 'Yksilöllinen ID accordionille', 'vestelli' ),
            'preview'     => array(
              'type' => 'none',
            ),
          ),
        ),
      ),
    ),
  ),
) );
