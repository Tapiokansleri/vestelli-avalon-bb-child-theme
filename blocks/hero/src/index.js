/**
 * Hero Block Editor Script
 * 
 * @package Vestelli
 */

import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls, PanelColorSettings } from '@wordpress/block-editor';
import { PanelBody, RangeControl, SelectControl, ToggleControl, TextControl, Button } from '@wordpress/components';
import { __ } from '@wordpress/i18n';
import './editor.css';
import '../style.css';

registerBlockType('vestelli/hero', {
	edit: ({ attributes, setAttributes }) => {
		const {
			title,
			description,
			backgroundImageUrl,
			overlayOpacity,
			overlayColor,
			textColor,
			alignment,
			height,
			showButton1,
			button1Text,
			button1Url,
			button1OpenNewTab,
			showButton2,
			button2Text,
			button2Url,
			button2OpenNewTab,
		} = attributes;

		const blockProps = useBlockProps({
			className: 'avalon-hero-block',
			style: {
				minHeight: height,
				color: textColor,
			},
		});

		const onSelectImage = (media) => {
			setAttributes({
				backgroundImageUrl: media.url,
				backgroundImage: {
					id: media.id,
					url: media.url,
				},
			});
		};

		const onRemoveImage = () => {
			setAttributes({
				backgroundImageUrl: '',
				backgroundImage: null,
			});
		};

		// Convert hex to rgba for overlay
		const hexToRgba = (hex, opacity) => {
			const r = parseInt(hex.slice(1, 3), 16);
			const g = parseInt(hex.slice(3, 5), 16);
			const b = parseInt(hex.slice(5, 7), 16);
			return `rgba(${r}, ${g}, ${b}, ${opacity})`;
		};

		return (
			<>
				<InspectorControls>
					<PanelBody title={__('Background Settings', 'vestelli')} initialOpen={true}>
						<MediaUploadCheck>
							<MediaUpload
								onSelect={onSelectImage}
								allowedTypes={['image']}
								value={backgroundImageUrl}
								render={({ open }) => (
									<div>
										{backgroundImageUrl ? (
											<div>
												<img src={backgroundImageUrl} alt="" style={{ width: '100%', marginBottom: '10px' }} />
												<Button onClick={onRemoveImage} isDestructive>
													{__('Remove Image', 'vestelli')}
												</Button>
											</div>
										) : (
											<Button onClick={open}>
												{__('Select Background Image', 'vestelli')}
											</Button>
										)}
									</div>
								)}
							/>
						</MediaUploadCheck>
						<RangeControl
							label={__('Overlay Opacity', 'vestelli')}
							value={overlayOpacity}
							onChange={(value) => setAttributes({ overlayOpacity: value })}
							min={0}
							max={1}
							step={0.1}
						/>
						<PanelColorSettings
							title={__('Colors', 'vestelli')}
							colorSettings={[
								{
									value: overlayColor,
									onChange: (value) => setAttributes({ overlayColor: value }),
									label: __('Overlay Color', 'vestelli'),
								},
								{
									value: textColor,
									onChange: (value) => setAttributes({ textColor: value }),
									label: __('Text Color', 'vestelli'),
								},
							]}
						/>
						<TextControl
							label={__('Height', 'vestelli')}
							value={height}
							onChange={(value) => setAttributes({ height: value })}
							help={__('e.g., 600px, 50vh', 'vestelli')}
						/>
						<SelectControl
							label={__('Text Alignment', 'vestelli')}
							value={alignment}
							options={[
								{ label: __('Left', 'vestelli'), value: 'left' },
								{ label: __('Center', 'vestelli'), value: 'center' },
								{ label: __('Right', 'vestelli'), value: 'right' },
							]}
							onChange={(value) => setAttributes({ alignment: value })}
						/>
					</PanelBody>
					<PanelBody title={__('Button 1', 'vestelli')} initialOpen={false}>
						<ToggleControl
							label={__('Show Button 1', 'vestelli')}
							checked={showButton1}
							onChange={(value) => setAttributes({ showButton1: value })}
						/>
						{showButton1 && (
							<>
								<TextControl
									label={__('Button Text', 'vestelli')}
									value={button1Text}
									onChange={(value) => setAttributes({ button1Text: value })}
								/>
								<TextControl
									label={__('Button URL', 'vestelli')}
									value={button1Url}
									onChange={(value) => setAttributes({ button1Url: value })}
								/>
								<ToggleControl
									label={__('Open in New Tab', 'vestelli')}
									checked={button1OpenNewTab}
									onChange={(value) => setAttributes({ button1OpenNewTab: value })}
								/>
							</>
						)}
					</PanelBody>
					<PanelBody title={__('Button 2', 'vestelli')} initialOpen={false}>
						<ToggleControl
							label={__('Show Button 2', 'vestelli')}
							checked={showButton2}
							onChange={(value) => setAttributes({ showButton2: value })}
						/>
						{showButton2 && (
							<>
								<TextControl
									label={__('Button Text', 'vestelli')}
									value={button2Text}
									onChange={(value) => setAttributes({ button2Text: value })}
								/>
								<TextControl
									label={__('Button URL', 'vestelli')}
									value={button2Url}
									onChange={(value) => setAttributes({ button2Url: value })}
								/>
								<ToggleControl
									label={__('Open in New Tab', 'vestelli')}
									checked={button2OpenNewTab}
									onChange={(value) => setAttributes({ button2OpenNewTab: value })}
								/>
							</>
						)}
					</PanelBody>
				</InspectorControls>
				<div {...blockProps}>
					{backgroundImageUrl && (
						<div
							className="avalon-hero-background"
							style={{ backgroundImage: `url('${backgroundImageUrl}')` }}
						/>
					)}
					<div
						className="avalon-hero-overlay"
						style={{ backgroundColor: hexToRgba(overlayColor, overlayOpacity) }}
					/>
					<div className={`avalon-hero-content avalon-hero-align-${alignment}`}>
						<div className="avalon-hero-inner">
							<RichText
								tagName="h1"
								className="avalon-hero-title"
								value={title}
								onChange={(value) => setAttributes({ title: value })}
								placeholder={__('Enter hero title...', 'vestelli')}
							/>
							<RichText
								tagName="div"
								className="avalon-hero-description"
								value={description}
								onChange={(value) => setAttributes({ description: value })}
								placeholder={__('Enter hero description...', 'vestelli')}
								multiline="p"
							/>
							{(showButton1 || showButton2) && (
								<div className="avalon-hero-buttons">
									{showButton1 && (
										<a href={button1Url} className="avalon-hero-button avalon-hero-button-primary">
											{button1Text}
										</a>
									)}
									{showButton2 && (
										<a href={button2Url} className="avalon-hero-button avalon-hero-button-secondary">
											{button2Text}
										</a>
									)}
								</div>
							)}
						</div>
					</div>
				</div>
			</>
		);
	},
	save: () => {
		// Dynamic block - rendered via PHP
		return null;
	},
});
