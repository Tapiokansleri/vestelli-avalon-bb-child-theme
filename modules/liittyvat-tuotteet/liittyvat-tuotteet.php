<?php
/**
 * Liittyvät tuotteet Module
 * 
 * Näyttää samantyyppiset tuotteet samasta kategoriasta
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class LiittyvatTuotteet extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Liittyvät tuotteet', 'vestelli' ),
      'description'     => __( 'Näyttää samantyyppiset tuotteet samasta kategoriasta (max 4)', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/liittyvat-tuotteet/',
      'url'             => VESTELLI_MODULES_URL . '/liittyvat-tuotteet/',
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
FLBuilder::register_module( 'LiittyvatTuotteet', array(
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
            'default'     => __( 'Liittyvät tuotteet', 'vestelli' ),
            'help'        => __( 'Jätä tyhjäksi käyttääksesi oletusotsikkoa', 'vestelli' ),
          ),
        ),
      ),
    ),
  ),
) );
