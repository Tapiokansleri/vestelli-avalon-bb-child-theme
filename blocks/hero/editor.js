/**
 * Hero Block Editor Script
 * 
 * @package Vestelli
 */

(function() {
  'use strict';
  
  // Wait for WordPress to be ready
  if ( typeof wp === 'undefined' || typeof wp.blocks === 'undefined' || typeof wp.blockEditor === 'undefined' ) {
    return;
  }
  
  var el = wp.element.createElement;
  var registerBlockType = wp.blocks.registerBlockType;
  var useBlockProps = wp.blockEditor.useBlockProps;
  var RichText = wp.blockEditor.RichText;
  var InspectorControls = wp.blockEditor.InspectorControls;
  var PanelBody = wp.components.PanelBody;
  var TextControl = wp.components.TextControl;
  var ToggleControl = wp.components.ToggleControl;
  
  registerBlockType('vestelli/hero', {
    edit: function(props) {
      var attributes = props.attributes || {};
      var setAttributes = props.setAttributes || function() {};
      
      var blockProps = useBlockProps({
        className: 'avalon-hero-block wp-block-vestelli-hero',
        style: {
          minHeight: attributes.height || '60vh',
          color: attributes.textColor || '#ffffff',
          position: 'relative',
          overflow: 'hidden',
        },
      });
      
      return el('div', { key: 'hero-wrapper' },
        el(InspectorControls, { key: 'inspector' },
          el(PanelBody, { key: 'settings', title: 'Hero Settings', initialOpen: true },
            el(TextControl, {
              key: 'title-control',
              label: 'Title',
              value: attributes.title || '',
              onChange: function(value) {
                setAttributes({ title: value });
              },
            }),
            el(TextControl, {
              key: 'description-control',
              label: 'Description',
              value: attributes.description || '',
              onChange: function(value) {
                setAttributes({ description: value });
              },
            })
          ),
          el(PanelBody, { key: 'button1-panel', title: 'Button 1', initialOpen: false },
            el(ToggleControl, {
              key: 'toggle-btn1',
              label: 'Show Button 1',
              checked: attributes.showButton1 !== false,
              onChange: function(value) {
                setAttributes({ showButton1: value });
              },
            }),
            attributes.showButton1 !== false ? el('div', { key: 'btn1-settings' },
              el(TextControl, {
                key: 'btn1-text',
                label: 'Button Text',
                value: attributes.button1Text || 'Pyydä tarjous',
                onChange: function(value) {
                  setAttributes({ button1Text: value });
                },
              }),
              el(TextControl, {
                key: 'btn1-url',
                label: 'Button URL',
                value: attributes.button1Url || '/pyyda-tarjous',
                onChange: function(value) {
                  setAttributes({ button1Url: value });
                },
              }),
              el(ToggleControl, {
                key: 'btn1-newtab',
                label: 'Open in New Tab',
                checked: attributes.button1OpenNewTab || false,
                onChange: function(value) {
                  setAttributes({ button1OpenNewTab: value });
                },
              })
            ) : null
          ),
          el(PanelBody, { key: 'button2-panel', title: 'Button 2', initialOpen: false },
            el(ToggleControl, {
              key: 'toggle-btn2',
              label: 'Show Button 2',
              checked: attributes.showButton2 || false,
              onChange: function(value) {
                setAttributes({ showButton2: value });
              },
            }),
            attributes.showButton2 ? el('div', { key: 'btn2-settings' },
              el(TextControl, {
                key: 'btn2-text',
                label: 'Button Text',
                value: attributes.button2Text || 'Lue lisää',
                onChange: function(value) {
                  setAttributes({ button2Text: value });
                },
              }),
              el(TextControl, {
                key: 'btn2-url',
                label: 'Button URL',
                value: attributes.button2Url || '#',
                onChange: function(value) {
                  setAttributes({ button2Url: value });
                },
              }),
              el(ToggleControl, {
                key: 'btn2-newtab',
                label: 'Open in New Tab',
                checked: attributes.button2OpenNewTab || false,
                onChange: function(value) {
                  setAttributes({ button2OpenNewTab: value });
                },
              })
            ) : null
          )
        ),
        el('div', blockProps,
          el('div', {
            key: 'background',
            className: 'avalon-hero-background',
            style: {
              position: 'absolute',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              backgroundImage: attributes.backgroundImageUrl ? "url('" + attributes.backgroundImageUrl + "')" : 'none',
              backgroundSize: 'cover',
              backgroundPosition: 'center',
              zIndex: 1,
            },
          }),
          el('div', {
            key: 'overlay',
            className: 'avalon-hero-overlay',
            style: {
              position: 'absolute',
              top: 0,
              left: 0,
              right: 0,
              bottom: 0,
              backgroundColor: 'rgba(0, 0, 0, ' + (attributes.overlayOpacity || 0.5) + ')',
              zIndex: 2,
            },
          }),
          el('div', {
            key: 'content',
            className: 'avalon-hero-content',
            style: {
              position: 'relative',
              zIndex: 3,
              maxWidth: '1650px',
              marginLeft: 'auto',
              marginRight: 'auto',
              minHeight: '100vh',
              display: 'flex',
              alignItems: 'flex-end',
              justifyContent: 'flex-start',
              padding: '0 2rem',
              paddingBottom: '60px',
            },
          },
            el('div', { key: 'inner', className: 'avalon-hero-inner', style: { width: '50%', textAlign: 'left' } },
              el(RichText, {
                key: 'title',
                tagName: 'h1',
                className: 'avalon-hero-title',
                value: attributes.title || '',
                onChange: function(value) {
                  setAttributes({ title: value });
                },
                placeholder: 'Enter hero title...',
                style: {
                  fontWeight: 'bold',
                  marginBottom: '20px',
                  color: attributes.textColor || '#ffffff',
                },
              }),
              el(RichText, {
                key: 'description',
                tagName: 'h3',
                className: 'avalon-hero-description',
                value: attributes.description || '',
                onChange: function(value) {
                  setAttributes({ description: value });
                },
                placeholder: 'Enter hero description...',
                allowedFormats: ['core/bold', 'core/italic', 'core/link'],
                style: {
                  margin: 0,
                  color: attributes.textColor || '#ffffff',
                },
              }),
              (attributes.showButton1 !== false || attributes.showButton2) ? el('div', {
                key: 'buttons',
                className: 'avalon-hero-buttons',
                style: {
                  display: 'flex',
                  gap: '16px',
                  flexWrap: 'wrap',
                  marginTop: '32px',
                  justifyContent: attributes.alignment === 'left' ? 'flex-start' : attributes.alignment === 'right' ? 'flex-end' : 'center',
                },
              },
                attributes.showButton1 !== false ? el('a', {
                  key: 'btn1',
                  href: attributes.button1Url || '/pyyda-tarjous',
                  className: 'avalon-hero-button avalon-hero-button-primary',
                  style: {
                    fontFamily: 'Roboto, sans-serif',
                    fontSize: '16px',
                    fontWeight: '600',
                    padding: '14px 32px',
                    borderRadius: '8px',
                    textDecoration: 'none',
                    display: 'inline-block',
                    backgroundColor: '#30CBD3',
                    color: '#ffffff',
                    border: '2px solid #30CBD3',
                    cursor: 'pointer',
                    whiteSpace: 'nowrap',
                  },
                }, attributes.button1Text || 'Pyydä tarjous') : null,
                attributes.showButton2 ? el('a', {
                  key: 'btn2',
                  href: attributes.button2Url || '#',
                  className: 'avalon-hero-button avalon-hero-button-secondary',
                  style: {
                    fontWeight: '600',
                    padding: '14px 32px',
                    borderRadius: '8px',
                    textDecoration: 'none',
                    display: 'inline-block',
                    backgroundColor: 'transparent',
                    color: attributes.textColor || '#ffffff',
                    border: '2px solid ' + (attributes.textColor || '#ffffff'),
                    cursor: 'pointer',
                    whiteSpace: 'nowrap',
                  },
                }, attributes.button2Text || 'Lue lisää') : null
              ) : null
            )
          )
        )
      );
    },
    save: function() {
      return null; // Dynamic block - rendered via PHP
    },
  });
})();
