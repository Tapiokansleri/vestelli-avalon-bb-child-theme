<?php
/**
 * Suunnittelija-aineistot Module
 *
 * Displays product download materials (PDF/DWG) in a table layout.
 * Uses BB repeater fields for sections and file items.
 *
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SuunnittelijaAineistot extends FLBuilderModule {

	public function __construct() {
		parent::__construct( array(
			'name'            => __( 'Suunnittelija-aineistot', 'vestelli' ),
			'description'     => __( 'Näyttää tuotteiden latausmateriaalit (PDF/DWG) taulukkomuodossa', 'vestelli' ),
			'category'        => __( 'Vestelli', 'vestelli' ),
			'dir'             => VESTELLI_MODULES . '/suunnittelija-aineistot/',
			'url'             => VESTELLI_MODULES_URL . '/suunnittelija-aineistot/',
			'icon'            => 'layout.svg',
			'editor_export'   => true,
			'enabled'         => true,
			'partial_refresh' => false,
		) );
	}

	public function enqueue_styles() {
		$this->add_css( 'sa-frontend', $this->url . 'css/frontend.css' );
	}
}

FLBuilder::register_module( 'SuunnittelijaAineistot', array(
	'general' => array(
		'title'    => __( 'Osiot', 'vestelli' ),
		'sections' => array(
			'main' => array(
				'title'  => '',
				'fields' => array(
					'material_sections' => array(
						'type'         => 'form',
						'label'        => __( 'Osio', 'vestelli' ),
						'form'         => 'sa_section_form',
						'preview_text' => 'heading',
						'multiple'     => true,
					),
				),
			),
		),
	),
) );

FLBuilder::register_settings_form( 'sa_section_form', array(
	'title' => __( 'Osio', 'vestelli' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'Yleiset', 'vestelli' ),
			'sections' => array(
				'info' => array(
					'title'  => __( 'Osion tiedot', 'vestelli' ),
					'fields' => array(
						'heading' => array(
							'type'    => 'text',
							'label'   => __( 'Otsikko', 'vestelli' ),
							'default' => '',
						),
						'text_content' => array(
							'type'    => 'textarea',
							'label'   => __( 'Teksti', 'vestelli' ),
							'default' => '',
							'rows'    => 3,
						),
					),
				),
				'file_list' => array(
					'title'  => __( 'Tiedostot', 'vestelli' ),
					'fields' => array(
						'material_files' => array(
							'type'         => 'form',
							'label'        => __( 'Tiedosto', 'vestelli' ),
							'form'         => 'sa_file_form',
							'preview_text' => 'file_name',
							'multiple'     => true,
						),
					),
				),
			),
		),
	),
) );

FLBuilder::register_settings_form( 'sa_file_form', array(
	'title' => __( 'Tiedosto', 'vestelli' ),
	'tabs'  => array(
		'general' => array(
			'title'    => __( 'Yleiset', 'vestelli' ),
			'sections' => array(
				'file_info' => array(
					'title'  => '',
					'fields' => array(
						'file_name' => array(
							'type'    => 'text',
							'label'   => __( 'Nimi', 'vestelli' ),
							'default' => '',
						),
						'pdf_link' => array(
							'type'        => 'text',
							'label'       => __( 'PDF-linkki', 'vestelli' ),
							'default'     => '',
							'placeholder' => 'https://',
						),
						'dwg_link' => array(
							'type'        => 'text',
							'label'       => __( 'DWG-linkki', 'vestelli' ),
							'default'     => '',
							'placeholder' => 'https://',
						),
					),
				),
			),
		),
	),
) );
