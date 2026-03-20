<?php
/**
 * Shop Now and Back Button Module
 *
 * Product page buttons: Add to cart / Ask for quote + Back to category
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

class AvalonButton extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Shop now and back -button', 'vestelli-avalon' ),
      'description'     => __( 'Add to cart / quote request button + back to category button for product pages', 'vestelli-avalon' ),
      'category'        => __( 'Vestelli', 'vestelli-avalon' ),
      'dir'             => VESTELLI_MODULES . '/avalon-button/',
      'url'             => VESTELLI_MODULES_URL . '/avalon-button/',
      'icon'            => 'button.svg',
      'editor_export'   => true,
      'enabled'         => true,
      'partial_refresh' => false,
    ) );
  }

  public function render( $settings ) {
    $module = $this;
    include $this->dir . 'includes/frontend.php';
  }

  public function enqueue_styles() {
    $this->add_css( 'frontend', $this->url . 'css/frontend.css' );
  }
}

/**
 * Register the module
 */
FLBuilder::register_module( 'AvalonButton', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli-avalon' ),
    'sections' => array(
      'layout' => array(
        'title'  => __( 'Asettelu', 'vestelli-avalon' ),
        'fields' => array(
          'alignment' => array(
            'type'    => 'select',
            'label'   => __( 'Tasaus', 'vestelli-avalon' ),
            'default' => 'left',
            'options' => array(
              'left'   => __( 'Vasen', 'vestelli-avalon' ),
              'center' => __( 'Keskitetty', 'vestelli-avalon' ),
              'right'  => __( 'Oikea', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
      'shop_button' => array(
        'title'  => __( 'Osta / Tarjouspyyntö -painike', 'vestelli-avalon' ),
        'fields' => array(
          'shop_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti (normaali)', 'vestelli-avalon' ),
            'default' => 'Lisää ostoskoriin',
            'help'    => __( 'Näytetään kun tarjouspyyntötila EI ole päällä.', 'vestelli-avalon' ),
          ),
          'quote_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti (tarjouspyyntö)', 'vestelli-avalon' ),
            'default' => 'Pyydä tarjous',
            'help'    => __( 'Näytetään kun tarjouspyyntötila ON päällä. Jos tyhjä, käytetään teeman asetuksista.', 'vestelli-avalon' ),
          ),
          'shop_style' => array(
            'type'    => 'select',
            'label'   => __( 'Painikkeen tyyli', 'vestelli-avalon' ),
            'default' => 'default',
            'options' => array(
              'default'       => __( 'Tummansininen', 'vestelli-avalon' ),
              'light-blue'    => __( 'Vaaleansininen', 'vestelli-avalon' ),
              'bordered-blue' => __( 'Sininen reunus', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
      'back_button' => array(
        'title'  => __( 'Takaisin-painike', 'vestelli-avalon' ),
        'fields' => array(
          'show_back_button' => array(
            'type'    => 'select',
            'label'   => __( 'Näytä takaisin-painike', 'vestelli-avalon' ),
            'default' => 'yes',
            'options' => array(
              'yes' => __( 'Kyllä', 'vestelli-avalon' ),
              'no'  => __( 'Ei', 'vestelli-avalon' ),
            ),
            'toggle'  => array(
              'yes' => array(
                'fields' => array( 'back_label', 'back_style' ),
              ),
            ),
          ),
          'back_label' => array(
            'type'    => 'text',
            'label'   => __( 'Painikkeen teksti', 'vestelli-avalon' ),
            'default' => 'Takaisin tuoteryhmän tuotteisiin',
          ),
          'back_style' => array(
            'type'    => 'select',
            'label'   => __( 'Painikkeen tyyli', 'vestelli-avalon' ),
            'default' => 'light-blue',
            'options' => array(
              'default'       => __( 'Tummansininen', 'vestelli-avalon' ),
              'light-blue'    => __( 'Vaaleansininen', 'vestelli-avalon' ),
              'bordered-blue' => __( 'Sininen reunus', 'vestelli-avalon' ),
            ),
          ),
        ),
      ),
    ),
  ),
) );
