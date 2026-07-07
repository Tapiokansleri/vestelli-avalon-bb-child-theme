/**
 * Hero Split Block Editor Script
 *
 * @package Vestelli
 */

(function () {
	'use strict';

	if (
		typeof wp === 'undefined' ||
		typeof wp.blocks === 'undefined' ||
		typeof wp.blockEditor === 'undefined'
	) {
		return;
	}

	var el = wp.element.createElement;
	var Fragment = wp.element.Fragment;
	var registerBlockType = wp.blocks.registerBlockType;
	var blockEditor = wp.blockEditor;
	var components = wp.components;

	var useBlockProps = blockEditor.useBlockProps;
	var useInnerBlocksProps = blockEditor.useInnerBlocksProps || (blockEditor.__experimentalUseInnerBlocksProps);
	var InnerBlocks = blockEditor.InnerBlocks;
	var InspectorControls = blockEditor.InspectorControls;
	var MediaUpload = blockEditor.MediaUpload;
	var MediaUploadCheck = blockEditor.MediaUploadCheck;

	var PanelBody = components.PanelBody;
	var Button = components.Button;
	var TextControl = components.TextControl;
	var RangeControl = components.RangeControl;
	var SelectControl = components.SelectControl;
	var ToggleControl = components.ToggleControl;
	var ColorPicker = components.ColorPicker;

	var TEMPLATE = [
		[
			'core/group',
			{
				className: 'va-hero-split__col va-hero-split__col--left',
				layout: { type: 'constrained' },
			},
			[
				['core/heading', { level: 6, content: 'Otsikko' }],
				['core/heading', { level: 2, content: 'Iso pääotsikko' }],
				['core/paragraph', { content: 'Lyhyt kuvausteksti tähän.' }],
			],
		],
		[
			'core/group',
			{
				className: 'va-hero-split__col va-hero-split__col--right',
				layout: { type: 'constrained' },
			},
			[
				['core/paragraph', { content: 'Oikean palstan sisältö.' }],
			],
		],
	];

	function hexToRgba(hex, opacity) {
		if (!hex) return 'rgba(0,0,0,' + opacity + ')';
		var h = hex.replace('#', '');
		if (h.length !== 6) return 'rgba(0,0,0,' + opacity + ')';
		var r = parseInt(h.substring(0, 2), 16);
		var g = parseInt(h.substring(2, 4), 16);
		var b = parseInt(h.substring(4, 6), 16);
		return 'rgba(' + r + ',' + g + ',' + b + ',' + opacity + ')';
	}

	registerBlockType('vestelli/hero-split', {
		edit: function (props) {
			var attributes = props.attributes || {};
			var setAttributes = props.setAttributes;

			var leftWidth = typeof attributes.leftWidth === 'number' ? attributes.leftWidth : 50;
			var rightWidth = 100 - leftWidth;
			var overlayOpacity = typeof attributes.overlayOpacity === 'number' ? attributes.overlayOpacity : 0.4;
			var overlayColor = attributes.overlayColor || '#000000';
			var textColor = attributes.textColor || '#ffffff';
			var minHeight = attributes.minHeight || '70vh';
			var verticalAlign = attributes.verticalAlign || 'center';
			var columnGap = attributes.columnGap || '48px';
			var stackOnMobile = attributes.stackOnMobile !== false;
			var bgUrl = attributes.backgroundImageUrl || '';

			var alignMap = { top: 'flex-start', center: 'center', bottom: 'flex-end' };

			var blockProps = useBlockProps({
				className: 'va-hero-split' + (bgUrl ? '' : ' va-hero-split--no-bg') + (stackOnMobile ? ' va-hero-split--stack-mobile' : ''),
				style: {
					position: 'relative',
					minHeight: minHeight,
					color: textColor,
					display: 'flex',
					alignItems: alignMap[verticalAlign] || 'center',
					padding: '80px 2rem',
					boxSizing: 'border-box',
					overflow: 'hidden',
					backgroundImage: bgUrl ? "url('" + bgUrl + "')" : 'none',
					backgroundSize: 'cover',
					backgroundPosition: 'center',
				},
			});

			var contentProps = useInnerBlocksProps(
				{
					className: 'va-hero-split__content',
					style: {
						position: 'relative',
						zIndex: 3,
						width: '100%',
						maxWidth: '1400px',
						margin: '0 auto',
						display: 'grid',
						gridTemplateColumns: leftWidth + '% ' + rightWidth + '%',
						gap: columnGap,
						alignItems: alignMap[verticalAlign] || 'center',
						boxSizing: 'border-box',
					},
				},
				{
					template: TEMPLATE,
					templateLock: 'all',
					renderAppender: false,
				}
			);

			var inspector = el(
				InspectorControls,
				{ key: 'inspector' },
				el(
					PanelBody,
					{ title: 'Tausta', initialOpen: true },
					el(MediaUploadCheck, {},
						el(MediaUpload, {
							onSelect: function (media) {
								setAttributes({
									backgroundImageId: media.id,
									backgroundImageUrl: media.url,
								});
							},
							allowedTypes: ['image'],
							value: attributes.backgroundImageId,
							render: function (obj) {
								return el(
									'div',
									{ style: { display: 'flex', flexDirection: 'column', gap: '8px', marginBottom: '12px' } },
									bgUrl ? el('img', { src: bgUrl, style: { maxWidth: '100%', height: 'auto', borderRadius: '4px' } }) : null,
									el(Button, { variant: 'secondary', onClick: obj.open }, bgUrl ? 'Vaihda kuva' : 'Valitse kuva'),
									bgUrl ? el(Button, {
										variant: 'link',
										isDestructive: true,
										onClick: function () { setAttributes({ backgroundImageId: 0, backgroundImageUrl: '' }); }
									}, 'Poista kuva') : null
								);
							},
						})
					),
					el('p', { style: { fontSize: '12px', marginTop: '4px', marginBottom: '12px' } }, 'Peittävän värin sävy:'),
					el(ColorPicker, {
						color: overlayColor,
						onChangeComplete: function (color) { setAttributes({ overlayColor: color.hex }); },
						disableAlpha: true,
					}),
					el(RangeControl, {
						label: 'Peittävän värin läpinäkyvyys',
						value: overlayOpacity,
						min: 0,
						max: 1,
						step: 0.05,
						onChange: function (v) { setAttributes({ overlayOpacity: typeof v === 'number' ? v : 0.4 }); },
					})
				),
				el(
					PanelBody,
					{ title: 'Asettelu', initialOpen: false },
					el(RangeControl, {
						label: 'Vasemman palstan leveys (%)',
						value: leftWidth,
						min: 20,
						max: 80,
						step: 5,
						onChange: function (v) { setAttributes({ leftWidth: typeof v === 'number' ? v : 50 }); },
					}),
					el(TextControl, {
						label: 'Palstojen väli (esim. 48px)',
						value: columnGap,
						onChange: function (v) { setAttributes({ columnGap: v }); },
					}),
					el(TextControl, {
						label: 'Minimikorkeus (esim. 70vh, 600px)',
						value: minHeight,
						onChange: function (v) { setAttributes({ minHeight: v }); },
					}),
					el(SelectControl, {
						label: 'Pystysuora kohdistus',
						value: verticalAlign,
						options: [
							{ label: 'Ylös', value: 'top' },
							{ label: 'Keskelle', value: 'center' },
							{ label: 'Alas', value: 'bottom' },
						],
						onChange: function (v) { setAttributes({ verticalAlign: v }); },
					}),
					el(ToggleControl, {
						label: 'Pinoa palstat mobiilissa',
						checked: stackOnMobile,
						onChange: function (v) { setAttributes({ stackOnMobile: !!v }); },
					})
				),
				el(
					PanelBody,
					{ title: 'Tekstin väri', initialOpen: false },
					el(ColorPicker, {
						color: textColor,
						onChangeComplete: function (color) { setAttributes({ textColor: color.hex }); },
						disableAlpha: true,
					})
				)
			);

			var overlayLayer = el('div', {
				key: 'overlay',
				className: 'va-hero-split__overlay',
				style: {
					position: 'absolute',
					inset: 0,
					backgroundColor: hexToRgba(overlayColor, overlayOpacity),
					zIndex: 2,
					pointerEvents: 'none',
				},
			});

			return el(
				Fragment,
				{},
				inspector,
				el(
					'div',
					blockProps,
					overlayLayer,
					el('div', contentProps)
				)
			);
		},
		save: function () {
			return el(InnerBlocks.Content);
		},
	});
})();
