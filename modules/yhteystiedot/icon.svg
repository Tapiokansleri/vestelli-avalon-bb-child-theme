<?php
/**
 * Liitetyt tuotteet Module
 * 
 * Näyttää WooCommercen liitetyt tuotteet (Up-Sell ja Cross-Sell)
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class LiitetytTuotteet extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Liitetyt tuotteet', 'vestelli' ),
      'description'     => __( 'Näyttää WooCommercen liitetyt tuotteet (Up-Sell ja Cross-Sell)', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/liitetyt-tuotteet/',
      'url'             => VESTELLI_MODULES_URL . '/liitetyt-tuotteet/',
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
FLBuilder::register_module( 'LiitetytTuotteet', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli' ),
        'fields' => array(
          'product_type' => array(
            'type'        => 'select',
            'label'       => __( 'Tuotetyyppi', 'vestelli' ),
            'default'     => 'cross_sell',
            'options'     => array(
              'upsell'     => __( 'Up-Sell (Parempi tuote)', 'vestelli' ),
              'cross_sell' => __( 'Cross-Sell (Lisätuote)', 'vestelli' ),
            ),
            'help'        => __( 'Valitse näytettävä tuotetyyppi. Moduuli hakee automaattisesti nykyisen tuotteen liitetyt tuotteet.', 'vestelli' ),
          ),
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
            'default'     => '',
            'help'        => __( 'Jätä tyhjäksi käyttääksesi oletusotsikkoa', 'vestelli' ),
          ),
        ),
      ),
    ),
  ),
) );
