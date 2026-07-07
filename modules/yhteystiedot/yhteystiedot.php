<?php
/**
 * Yhteystiedot Module
 *
 * Listaa henkilökunnan ACF repeaterista (`henkilokunta`), ryhmittelee `osasto`-
 * kentän mukaan ja näyttää jokaisesta osastosta otsikkoboxin + ruudukon korteista.
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ACF `osasto`-kentän valintojen koodit ja käännökset.
 *
 * Pidetään PHP-rinnakkaisversiona, jotta moduuli pystyy renderöimään ilman
 * ACF:n field-objectin lataamista joka kierroksella.
 *
 * @return array<string, string>
 */
function vestelli_yhteystiedot_osasto_labels() {
	return array(
		'01_asiakaspalvelu' => __( 'Asiakaspalvelu', 'vestelli-avalon' ),
		'02_yritysmyynti'   => __( 'Yritysmyynti', 'vestelli-avalon' ),
		'03_projektimyynti' => __( 'Projektimyynti', 'vestelli-avalon' ),
		'03_wassis'         => __( 'Wassis-jätevesipalvelut', 'vestelli-avalon' ),
		'04_hallinto'       => __( 'Hallinto', 'vestelli-avalon' ),
	);
}

class VestelliYhteystiedot extends FLBuilderModule {

	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Yhteystiedot', 'vestelli-avalon' ),
			'description'     => __( 'Näyttää henkilökunnan ACF-kentistä, ryhmiteltynä osastoittain.', 'vestelli-avalon' ),
			'category'        => __( 'Vestelli', 'vestelli-avalon' ),
			'dir'             => VESTELLI_MODULES . '/yhteystiedot/',
			'url'             => VESTELLI_MODULES_URL . '/yhteystiedot/',
			'icon'            => 'icon.svg',
			'editor_export'   => true,
			'enabled'         => true,
			'partial_refresh' => false,
		) );
	}

	public function enqueue_styles() {
		$this->add_css( 'frontend', $this->url . 'css/frontend.css' );
	}
}

FLBuilder::register_module( 'VestelliYhteystiedot', array(
	'general' => array(
		'title'    => __( 'Yleiset', 'vestelli-avalon' ),
		'sections' => array(
			'source' => array(
				'title'  => __( 'Lähde', 'vestelli-avalon' ),
				'fields' => array(
					'source_post_id' => array(
						'type'        => 'text',
						'label'       => __( 'Sivun ID', 'vestelli-avalon' ),
						'default'     => '',
						'placeholder' => __( 'Tyhjä = nykyinen sivu', 'vestelli-avalon' ),
						'help'        => __( 'Mistä sivusta ACF-kenttä `henkilokunta` luetaan. Jätä tyhjäksi käyttääksesi nykyisen sivun kenttiä.', 'vestelli-avalon' ),
					),
					'field_name' => array(
						'type'        => 'text',
						'label'       => __( 'ACF-kentän nimi', 'vestelli-avalon' ),
						'default'     => 'henkilokunta',
						'help'        => __( 'Repeater-kentän nimi.', 'vestelli-avalon' ),
					),
				),
			),
			'layout' => array(
				'title'  => __( 'Asettelu', 'vestelli-avalon' ),
				'fields' => array(
					'columns' => array(
						'type'    => 'select',
						'label'   => __( 'Sarakkeet', 'vestelli-avalon' ),
						'default' => '4',
						'options' => array(
							'2' => __( '2 saraketta', 'vestelli-avalon' ),
							'3' => __( '3 saraketta', 'vestelli-avalon' ),
							'4' => __( '4 saraketta', 'vestelli-avalon' ),
						),
					),
					'show_section_titles' => array(
						'type'    => 'select',
						'label'   => __( 'Näytä osasto-otsikot', 'vestelli-avalon' ),
						'default' => 'yes',
						'options' => array(
							'yes' => __( 'Kyllä', 'vestelli-avalon' ),
							'no'  => __( 'Ei', 'vestelli-avalon' ),
						),
					),
					'photo_style' => array(
						'type'    => 'select',
						'label'   => __( 'Kuvan tyyli', 'vestelli-avalon' ),
						'default' => 'grayscale',
						'options' => array(
							'grayscale' => __( 'Mustavalkoinen', 'vestelli-avalon' ),
							'color'     => __( 'Värillinen', 'vestelli-avalon' ),
						),
					),
				),
			),
		),
	),
) );
