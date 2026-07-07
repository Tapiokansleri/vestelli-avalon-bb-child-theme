<?php
/**
 * Gravity Forms Module
 * 
 * Näyttää Gravity Forms -lomakkeen
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class GravityFormsModule extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Lisää Gravity Forms', 'vestelli' ),
      'description'     => __( 'Näyttää Gravity Forms -lomakkeen', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/gravity-forms/',
      'url'             => VESTELLI_MODULES_URL . '/gravity-forms/',
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
 * Get available Gravity Forms
 */
function avalon_get_gravity_forms() {
  $forms = array();
  
  if ( class_exists( 'GFAPI' ) ) {
    $all_forms = GFAPI::get_forms();
    foreach ( $all_forms as $form ) {
      $forms[ $form['id'] ] = $form['title'];
    }
  }
  
  return $forms;
}

/**
 * Register the module
 */
FLBuilder::register_module( 'GravityFormsModule', array(
  'general' => array(
    'title'    => __( 'Yleiset', 'vestelli' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli' ),
        'fields' => array(
          'form_id' => array(
            'type'        => 'select',
            'label'       => __( 'Valitse lomake', 'vestelli' ),
            'default'     => '',
            'options'     => avalon_get_gravity_forms(),
            'help'        => __( 'Valitse näytettävä Gravity Forms -lomake', 'vestelli' ),
          ),
          'title' => array(
            'type'        => 'select',
            'label'       => __( 'Näytä otsikko', 'vestelli' ),
            'default'     => 'yes',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli' ),
              'no'  => __( 'Ei', 'vestelli' ),
            ),
            'help'        => __( 'Näytetäänkö lomakkeen otsikko', 'vestelli' ),
          ),
          'description' => array(
            'type'        => 'select',
            'label'       => __( 'Näytä kuvaus', 'vestelli' ),
            'default'     => 'yes',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli' ),
              'no'  => __( 'Ei', 'vestelli' ),
            ),
            'help'        => __( 'Näytetäänkö lomakkeen kuvaus', 'vestelli' ),
          ),
          'ajax' => array(
            'type'        => 'select',
            'label'       => __( 'Käytä AJAXia', 'vestelli' ),
            'default'     => 'yes',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli' ),
              'no'  => __( 'Ei', 'vestelli' ),
            ),
            'help'        => __( 'Lähetetäänkö lomake AJAXilla ilman sivun uudelleenlatausta', 'vestelli' ),
          ),
        ),
      ),
    ),
  ),
) );
