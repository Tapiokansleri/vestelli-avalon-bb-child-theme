<?php
/**
 * Avalon Button Module
 * 
 * Custom button with multiple styles and wave hover effect
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

class AvalonButton extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'Vestelli Button', 'vestelli' ),
      'description'     => __( 'Custom button with multiple styles and wave hover effect', 'vestelli' ),
      'category'        => __( 'Vestelli', 'vestelli' ),
      'dir'             => VESTELLI_MODULES . '/avalon-button/',
      'url'             => VESTELLI_MODULES_URL . '/avalon-button/',
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
 * Shortcode handler for Avalon Button
 * 
 * Usage: [avalon_button]
 * Optional attributes: text, url, style, alignment, open_new_tab, show_second_button, button2_text, button2_url, button2_style, button2_open_new_tab
 * 
 * @param array $atts Shortcode attributes
 * @return string Button HTML
 */
function avalon_button_shortcode( $atts ) {
  // Ensure constants are defined
  if ( ! defined( 'VESTELLI_MODULES' ) ) {
    define( 'VESTELLI_MODULES', get_stylesheet_directory() . '/modules' );
  }
  if ( ! defined( 'VESTELLI_MODULES_URL' ) ) {
    define( 'VESTELLI_MODULES_URL', get_stylesheet_directory_uri() . '/modules' );
  }
  
  // Parse shortcode attributes with defaults
  $atts = shortcode_atts( array(
    'text'              => 'Pyydä tarjous',
    'url'               => '/pyyda-tarjous',
    'style'             => 'default',
    'alignment'         => 'left',
    'open_new_tab'      => 'no',
    'show_second_button' => 'no',
    'button2_text'      => 'Painikkeen teksti',
    'button2_url'       => '#',
    'button2_style'     => 'default',
    'button2_open_new_tab' => 'no',
  ), $atts, 'avalon_button' );
  
  // Create settings object compatible with module
  $settings = (object) array(
    'alignment'         => $atts['alignment'],
    'show_second_button' => $atts['show_second_button'],
    'button_text'       => $atts['text'],
    'button_url'        => $atts['url'],
    'button_style'      => $atts['style'],
    'open_new_tab'      => $atts['open_new_tab'],
    'button2_text'      => $atts['button2_text'],
    'button2_url'       => $atts['button2_url'],
    'button2_style'     => $atts['button2_style'],
    'button2_open_new_tab' => $atts['button2_open_new_tab'],
  );
  
  // Enqueue styles
  $css_url = VESTELLI_MODULES_URL . '/avalon-button/css/frontend.css';
  if ( ! wp_style_is( 'avalon-button-frontend', 'enqueued' ) && ! wp_style_is( 'avalon-button-frontend', 'registered' ) ) {
    wp_enqueue_style( 'avalon-button-frontend', $css_url, array(), filemtime( VESTELLI_MODULES . '/avalon-button/css/frontend.css' ) );
  }
  
  // Capture output from frontend template
  ob_start();
  include VESTELLI_MODULES . '/avalon-button/includes/frontend.php';
  return ob_get_clean();
}

/**
 * Register shortcode
 */
add_shortcode( 'avalon_button', 'avalon_button_shortcode' );

/**
 * Register the module
 */
FLBuilder::register_module( 'AvalonButton', array(
  'general' => array(
    'title'    => __( 'General', 'vestelli' ),
    'sections' => array(
      'layout' => array(
        'title'  => __( 'Layout', 'vestelli' ),
        'fields' => array(
          'alignment' => array(
            'type'        => 'select',
            'label'       => __( 'Alignment', 'vestelli' ),
            'default'     => 'left',
            'options'     => array(
              'left'   => __( 'Left', 'vestelli' ),
              'center' => __( 'Center', 'vestelli' ),
              'right'  => __( 'Right', 'vestelli' ),
            ),
            'preview'     => array(
              'type'     => 'css',
              'selector' => '.avalon-button-wrapper',
              'property' => 'text-align',
            ),
          ),
          'show_second_button' => array(
            'type'        => 'select',
            'label'       => __( 'Show Second Button', 'vestelli' ),
            'default'     => 'no',
            'options'     => array(
              'no'  => __( 'No', 'vestelli' ),
              'yes' => __( 'Yes', 'vestelli' ),
            ),
            'toggle'      => array(
              'yes' => array(
                'sections' => array( 'second_button' ),
              ),
            ),
            'preview'     => array(
              'type' => 'none',
            ),
          ),
        ),
      ),
      'settings' => array(
        'title'  => __( 'First Button Settings', 'vestelli' ),
        'fields' => array(
          'button_text' => array(
            'type'        => 'text',
            'label'       => __( 'Button Text', 'vestelli' ),
            'default'     => 'Pyydä tarjous',
            'preview'     => array(
              'type'     => 'text',
              'selector' => '.avalon-button:first-child .avalon-button-text',
            ),
          ),
          'button_url' => array(
            'type'        => 'link',
            'label'       => __( 'Button URL', 'vestelli' ),
            'default'     => '/pyyda-tarjous',
            'preview'     => array(
              'type' => 'none',
            ),
          ),
          'open_new_tab' => array(
            'type'        => 'select',
            'label'       => __( 'Open in New Tab', 'vestelli' ),
            'default'     => 'no',
            'options'     => array(
              'no'  => __( 'No', 'vestelli' ),
              'yes' => __( 'Yes', 'vestelli' ),
            ),
            'preview'     => array(
              'type' => 'none',
            ),
          ),
          'button_style' => array(
            'type'        => 'select',
            'label'       => __( 'Button Style', 'vestelli' ),
            'default'     => 'default',
            'options'     => array(
              'default'       => __( 'Default (#002b56)', 'vestelli' ),
              'light-blue'    => __( 'Light Blue (#30CBD3)', 'vestelli' ),
              'bordered-blue' => __( 'Bordered Blue (#002b56 border)', 'vestelli' ),
              'white-border'  => __( 'White Border (#fff border)', 'vestelli' ),
            ),
            'preview'     => array(
              'type'     => 'css',
              'selector' => '.avalon-button:first-child',
              'property' => 'background-color',
            ),
          ),
        ),
      ),
      'second_button' => array(
        'title'  => __( 'Second Button Settings', 'vestelli' ),
        'fields' => array(
          'button2_text' => array(
            'type'        => 'text',
            'label'       => __( 'Button Text', 'vestelli' ),
            'default'     => 'Painikkeen teksti',
            'preview'     => array(
              'type'     => 'text',
              'selector' => '.avalon-button:last-child .avalon-button-text',
            ),
          ),
          'button2_url' => array(
            'type'        => 'link',
            'label'       => __( 'Button URL', 'vestelli' ),
            'default'     => '#',
            'preview'     => array(
              'type' => 'none',
            ),
          ),
          'button2_open_new_tab' => array(
            'type'        => 'select',
            'label'       => __( 'Open in New Tab', 'vestelli' ),
            'default'     => 'no',
            'options'     => array(
              'no'  => __( 'No', 'vestelli' ),
              'yes' => __( 'Yes', 'vestelli' ),
            ),
            'preview'     => array(
              'type' => 'none',
            ),
          ),
          'button2_style' => array(
            'type'        => 'select',
            'label'       => __( 'Button Style', 'vestelli' ),
            'default'     => 'default',
            'options'     => array(
              'default'       => __( 'Default (#002b56)', 'vestelli' ),
              'light-blue'    => __( 'Light Blue (#30CBD3)', 'vestelli' ),
              'bordered-blue' => __( 'Bordered Blue (#002b56 border)', 'vestelli' ),
              'white-border'  => __( 'White Border (#fff border)', 'vestelli' ),
            ),
            'preview'     => array(
              'type'     => 'css',
              'selector' => '.avalon-button:last-child',
              'property' => 'background-color',
            ),
          ),
        ),
      ),
    ),
  ),
) );
