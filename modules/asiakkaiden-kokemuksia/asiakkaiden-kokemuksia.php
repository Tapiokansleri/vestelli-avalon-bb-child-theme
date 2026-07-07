<?php
/**
 * Asiakkaiden kokemuksia Module
 * 
 * Näyttää asiakkaiden kokemuksia/testimoniaaleja
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class AsiakkaidenKokemuksia extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Avalon asiakkaiden kokemuksia', 'vestelli' ),
      'description'     => __( 'Näyttää asiakkaiden kokemuksia ja testimoniaaleja', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/asiakkaiden-kokemuksia/',
      'url'             => VESTELLI_MODULES_URL . '/asiakkaiden-kokemuksia/',
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
FLBuilder::register_module( 'AsiakkaidenKokemuksia', array(
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
            'default'     => __( 'Asiakkaiden kokemuksia', 'vestelli' ),
          ),
          'columns' => array(
            'type'        => 'unit',
            'label'       => __( 'Sarakkeet', 'vestelli' ),
            'default'     => '3',
            'units'       => array( 'columns' ),
            'slider'      => true,
            'min'         => 1,
            'max'         => 4,
            'help'        => __( 'Montako testimoniaalia näytetään per rivi', 'vestelli' ),
          ),
        ),
      ),
      'testimonials' => array(
        'title'  => __( 'Testimoniaalit', 'vestelli' ),
        'fields' => array(
          'testimonials' => array(
            'type'         => 'form',
            'label'        => __( 'Testimoniaali', 'vestelli' ),
            'form'         => 'testimonial_form',
            'preview_text' => 'name',
            'multiple'     => true,
          ),
        ),
      ),
    ),
  ),
) );

/**
 * Testimonial form fields
 */
FLBuilder::register_settings_form( 'testimonial_form', array(
  'title' => __( 'Lisää testimoniaali', 'vestelli' ),
  'tabs'  => array(
    'general' => array(
      'title'    => __( 'Yleiset', 'vestelli' ),
      'sections' => array(
        'content' => array(
          'title'  => '',
          'fields' => array(
            'name' => array(
              'type'        => 'text',
              'label'       => __( 'Nimi', 'vestelli' ),
              'default'     => '',
              'required'    => true,
            ),
            'position' => array(
              'type'        => 'text',
              'label'       => __( 'Asema / Titteli', 'vestelli' ),
              'default'     => '',
            ),
            'company' => array(
              'type'        => 'text',
              'label'       => __( 'Yritys', 'vestelli' ),
              'default'     => '',
            ),
            'testimonial' => array(
              'type'        => 'textarea',
              'label'       => __( 'Kokemus / Arvostelu', 'vestelli' ),
              'default'     => '',
              'required'    => true,
              'rows'        => 5,
            ),
            'rating' => array(
              'type'        => 'unit',
              'label'       => __( 'Arvosana (1-5)', 'vestelli' ),
              'default'     => '5',
              'units'       => array( 'stars' ),
              'slider'      => true,
              'min'         => 1,
              'max'         => 5,
            ),
            'photo' => array(
              'type'        => 'photo',
              'label'       => __( 'Kuva', 'vestelli' ),
              'show_remove' => true,
            ),
          ),
        ),
      ),
    ),
  ),
) );
