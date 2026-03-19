<?php
/**
 * Stats Numbers Module
 * 
 * Näyttää tilastoluvut/numerot kortteina yhdellä rivillä
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class Stats_Numbers extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Vestelli lukuina', 'vestelli' ),
      'description'     => __( 'Näyttää lukuja ja tilastoja kortteina', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/stats-numbers/',
      'url'             => VESTELLI_MODULES_URL . '/stats-numbers/',
      'icon'            => 'button.svg',
      'editor_export'   => true,
      'enabled'         => true,
      'partial_refresh' => true,
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
FLBuilder::register_module( 'Stats_Numbers', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli' ),
        'fields' => array(
          'show_title' => array(
            'type'        => 'select',
            'label'       => __( 'Näytä otsikko', 'vestelli' ),
            'default'     => 'no',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli' ),
              'no'  => __( 'Ei', 'vestelli' ),
            ),
          ),
          'title_text' => array(
            'type'        => 'text',
            'label'       => __( 'Otsikon teksti', 'vestelli' ),
            'default'     => '',
          ),
        ),
      ),
      'stats' => array(
        'title'  => __( 'Tilastoluvut', 'vestelli' ),
        'fields' => array(
          'stats' => array(
            'type'         => 'form',
            'label'        => __( 'Tilastoluku', 'vestelli' ),
            'form'         => 'stat_form',
            'preview_text' => 'title',
            'multiple'     => true,
          ),
        ),
      ),
    ),
  ),
  'style' => array(
    'title'    => __( 'Tyyli', 'vestelli' ),
    'sections' => array(
      'colors' => array(
        'title'  => __( 'Värit', 'vestelli' ),
        'fields' => array(
          'stat_bg_color' => array(
            'type'       => 'color',
            'label'      => __( 'Kortin taustaväri', 'vestelli' ),
            'default'    => '',
            'show_reset' => true,
            'preview'    => array( 'type' => 'refresh' ),
          ),
          'stat_title_color' => array(
            'type'       => 'color',
            'label'      => __( 'Otsikon väri (numero)', 'vestelli' ),
            'default'    => '',
            'show_reset' => true,
            'preview'    => array( 'type' => 'refresh' ),
          ),
          'stat_subtitle_color' => array(
            'type'       => 'color',
            'label'      => __( 'Alaotsikon väri', 'vestelli' ),
            'default'    => '',
            'show_reset' => true,
            'preview'    => array( 'type' => 'refresh' ),
          ),
          'stat_desc_color' => array(
            'type'       => 'color',
            'label'      => __( 'Kuvauksen väri', 'vestelli' ),
            'default'    => '',
            'show_reset' => true,
            'preview'    => array( 'type' => 'refresh' ),
          ),
          'stat_icon_color' => array(
            'type'       => 'color',
            'label'      => __( 'Ikonin väri', 'vestelli' ),
            'default'    => '',
            'show_reset' => true,
            'preview'    => array( 'type' => 'refresh' ),
          ),
        ),
      ),
    ),
  ),
) );

/**
 * Stat form fields
 */
FLBuilder::register_settings_form( 'stat_form', array(
  'title' => __( 'Lisää tilastoluku', 'vestelli' ),
  'tabs'  => array(
    'general' => array(
      'title'    => __( 'Yleiset', 'vestelli' ),
      'sections' => array(
        'content' => array(
          'title'  => '',
          'fields' => array(
            'media_type' => array(
              'type'        => 'select',
              'label'       => __( 'Mediatyyppi', 'vestelli' ),
              'default'     => 'icon',
              'options'     => array(
                'icon'  => __( 'Ikoni', 'vestelli' ),
                'image' => __( 'Kuva', 'vestelli' ),
              ),
              'toggle'      => array(
                'icon'  => array(
                  'fields' => array( 'icon' ),
                ),
                'image' => array(
                  'fields' => array( 'image' ),
                ),
              ),
            ),
            'icon' => array(
              'type'        => 'icon',
              'label'       => __( 'Ikoni', 'vestelli' ),
              'default'     => '',
            ),
            'image' => array(
              'type'        => 'photo',
              'label'       => __( 'Kuva', 'vestelli' ),
              'default'     => '',
            ),
            'title' => array(
              'type'        => 'text',
              'label'       => __( 'Otsikko', 'vestelli' ),
              'default'     => '',
              'required'    => true,
              'help'        => __( 'Esimerkki: 100 %, +5000, 96 %', 'vestelli' ),
            ),
            'subtitle' => array(
              'type'        => 'text',
              'label'       => __( 'Alaotsikko', 'vestelli' ),
              'default'     => '',
              'help'        => __( 'Esimerkki: MYYTYJÄ TUOTTEITA', 'vestelli' ),
            ),
            'description' => array(
              'type'        => 'textarea',
              'label'       => __( 'Kuvaus', 'vestelli' ),
              'default'     => '',
              'rows'        => 3,
            ),
          ),
        ),
      ),
    ),
  ),
) );
