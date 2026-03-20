<?php
/**
 * WooCommerce Osastot Module
 *
 * Listaa kaikki WooCommercen osastot (ylätason kategoriat)
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Get top-level WooCommerce categories for module settings.
 *
 * @return array
 */
function avalon_woocommerce_osastot_options() {
  $options = array();

  // Primary path: WP taxonomy API (when product_cat is registered).
  if ( taxonomy_exists( 'product_cat' ) ) {
    $terms = get_terms( array(
      'taxonomy'   => 'product_cat',
      'hide_empty' => false,
      'parent'     => 0,
      'orderby'    => 'name',
      'order'      => 'ASC',
    ) );

    if ( ! empty( $terms ) && ! is_wp_error( $terms ) ) {
      foreach ( $terms as $term ) {
        $options[ strval( $term->term_id ) ] = sprintf(
          '%s (%d)',
          $term->name,
          intval( $term->count )
        );
      }
    }
  }

  // Fallback path: direct DB query for early execution timing (BB settings load).
  if ( empty( $options ) ) {
    global $wpdb;

    $rows = $wpdb->get_results(
      "
      SELECT t.term_id, t.name, tt.count
      FROM {$wpdb->terms} t
      INNER JOIN {$wpdb->term_taxonomy} tt ON t.term_id = tt.term_id
      WHERE tt.taxonomy = 'product_cat'
        AND tt.parent = 0
      ORDER BY t.name ASC
      "
    );

    if ( ! empty( $rows ) ) {
      foreach ( $rows as $row ) {
        $options[ strval( intval( $row->term_id ) ) ] = sprintf(
          '%s (%d)',
          $row->name,
          intval( $row->count )
        );
      }
    }
  }

  return $options;
}

class WooCommerceOsastot extends FLBuilderModule {

  public function __construct() {
    parent::__construct( array(
      'name'            => __( 'WooCommerce Osastot', 'vestelli-avalon' ),
      'description'     => __( 'Listaa kaikki WooCommercen osastot (ylätason kategoriat)', 'vestelli-avalon' ),
      'category'        => __( 'Vestelli', 'vestelli-avalon' ),
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
    'title'    => __( 'Yleiset', 'vestelli-avalon' ),
    'sections' => array(
      'settings' => array(
        'title'  => __( 'Asetukset', 'vestelli-avalon' ),
        'fields' => array(
          'show_title' => array(
            'type'        => 'select',
            'label'       => __( 'Näytä otsikko', 'vestelli-avalon' ),
            'default'     => 'yes',
            'options'     => array(
              'yes' => __( 'Kyllä', 'vestelli-avalon' ),
              'no'  => __( 'Ei', 'vestelli-avalon' ),
            ),
          ),
          'title_text' => array(
            'type'        => 'text',
            'label'       => __( 'Otsikon teksti', 'vestelli-avalon' ),
            'default'     => __( 'Osastot', 'vestelli-avalon' ),
            'help'        => __( 'Jätä tyhjäksi käyttääksesi oletusotsikkoa', 'vestelli-avalon' ),
          ),
          'columns' => array(
            'type'        => 'select',
            'label'       => __( 'Sarakkeet', 'vestelli-avalon' ),
            'default'     => '4',
            'options'     => array(
              '2' => __( '2 saraketta', 'vestelli-avalon' ),
              '3' => __( '3 saraketta', 'vestelli-avalon' ),
              '4' => __( '4 saraketta', 'vestelli-avalon' ),
            ),
          ),
          'category_source' => array(
            'type'        => 'select',
            'label'       => __( 'Näytettävät osastot', 'vestelli-avalon' ),
            'default'     => 'all',
            'options'     => array(
              'all'      => __( 'Kaikki osastot', 'vestelli-avalon' ),
              'selected' => __( 'Valitut osastot', 'vestelli-avalon' ),
            ),
            'toggle'      => array(
              'selected' => array(
                'fields' => array( 'selected_category_rows', 'empty_selected_behavior' ),
              ),
            ),
          ),
          'selected_category_rows' => array(
            'type'         => 'form',
            'label'        => __( 'Valitut osastot', 'vestelli-avalon' ),
            'form'         => 'woocommerce_osasto_select_form',
            'preview_text' => 'category_id',
            'multiple'     => true,
            'help'         => __( 'Lisää osastot yksi kerrallaan ja järjestä ne vetämällä.', 'vestelli-avalon' ),
          ),
          'empty_selected_behavior' => array(
            'type'        => 'select',
            'label'       => __( 'Jos valittuja osastoja ei ole', 'vestelli-avalon' ),
            'default'     => 'all',
            'options'     => array(
              'all'   => __( 'Näytä kaikki osastot', 'vestelli-avalon' ),
              'empty' => __( 'Näytä tyhjä tila', 'vestelli-avalon' ),
            ),
          ),
          'order_by' => array(
            'type'        => 'select',
            'label'       => __( 'Järjestä osastot', 'vestelli-avalon' ),
            'default'     => 'include',
            'options'     => array(
              'name'    => __( 'Nimi', 'vestelli-avalon' ),
              'slug'    => __( 'Slug', 'vestelli-avalon' ),
              'id'      => __( 'ID', 'vestelli-avalon' ),
              'count'   => __( 'Tuotemäärä', 'vestelli-avalon' ),
              'include' => __( 'Valittujen osastojen järjestys', 'vestelli-avalon' ),
            ),
          ),
          'order' => array(
            'type'        => 'select',
            'label'       => __( 'Järjestyssuunta', 'vestelli-avalon' ),
            'default'     => 'ASC',
            'options'     => array(
              'ASC'  => __( 'Nouseva (ASC)', 'vestelli-avalon' ),
              'DESC' => __( 'Laskeva (DESC)', 'vestelli-avalon' ),
            ),
          ),
          'default_image' => array(
            'type'        => 'photo',
            'label'       => __( 'Oletuskuva', 'vestelli-avalon' ),
            'show_remove' => true,
            'help'        => __( 'Näytetään jos osastolla ei ole omaa kuvaa. Jos ei valittu, käytetään WooCommercen oletuskuvaa.', 'vestelli-avalon' ),
          ),
        ),
      ),
    ),
  ),
) );

FLBuilder::register_settings_form( 'woocommerce_osasto_select_form', array(
  'title' => __( 'Valitse osasto', 'vestelli-avalon' ),
  'tabs'  => array(
    'general' => array(
      'title'    => __( 'Yleiset', 'vestelli-avalon' ),
      'sections' => array(
        'settings' => array(
          'title'  => '',
          'fields' => array(
            'category_id' => array(
              'type'    => 'select',
              'label'   => __( 'Osasto', 'vestelli-avalon' ),
              'default' => '',
              'options' => array( '' => __( 'Valitse osasto', 'vestelli-avalon' ) ) + avalon_woocommerce_osastot_options(),
            ),
          ),
        ),
      ),
    ),
  ),
) );
