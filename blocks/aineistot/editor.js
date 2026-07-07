/**
 * Suunnittelija-aineistot Block Editor Script
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
	var useState = wp.element.useState;
	var useRef = wp.element.useRef;
	var registerBlockType = wp.blocks.registerBlockType;
	var useBlockProps = wp.blockEditor.useBlockProps;
	var InspectorControls = wp.blockEditor.InspectorControls;
	var PanelBody = wp.components.PanelBody;
	var TextControl = wp.components.TextControl;
	var TextareaControl = wp.components.TextareaControl;
	var Button = wp.components.Button;
	var Notice = wp.components.Notice;

	var DOWNLOAD_ICON_SVG = '<svg class="sa-download-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M12 16l-5-5h3V4h4v7h3l-5 5zm-7 2h14v2H5v-2z"/></svg>';

	function renderPreview(sections) {
		var sectionEls = [];
		for (var i = 0; i < sections.length; i++) {
			var s = sections[i] || {};
			var heading = s.heading || '';
			var text = s.text || '';
			var files = Array.isArray(s.files) ? s.files : [];

			if (heading === '' && files.length === 0) {
				continue;
			}

			var children = [];
			if (heading !== '') {
				children.push(el('h3', { key: 'h', className: 'sa-product-title' }, heading));
			}
			if (text !== '') {
				children.push(el('div', {
					key: 't',
					className: 'sa-product-text',
					dangerouslySetInnerHTML: { __html: text.replace(/\n/g, '<br />') },
				}));
			}

			var rows = [];
			rows.push(el('div', { key: 'header', className: 'sa-files-header' },
				el('span', { key: 'n', className: 'sa-file-name-col' }),
				el('span', { key: 'p', className: 'sa-file-icon-col' }, 'PDF'),
				el('span', { key: 'd', className: 'sa-file-icon-col' }, 'DWG')
			));

			for (var j = 0; j < files.length; j++) {
				var f = files[j] || {};
				var name = f.name || '';
				if (name === '') continue;
				var pdfUrl = f.pdfUrl || '';
				var dwgUrl = f.dwgUrl || '';

				rows.push(el('div', { key: 'r-' + j, className: 'sa-files-row' },
					el('span', { key: 'n', className: 'sa-file-name-col' }, name),
					el('span', { key: 'p', className: 'sa-file-icon-col' },
						pdfUrl
							? el('a', {
								href: pdfUrl,
								target: '_blank',
								rel: 'noopener',
								title: name + ' (PDF)',
								onClick: function (e) { e.preventDefault(); },
								dangerouslySetInnerHTML: { __html: DOWNLOAD_ICON_SVG },
							})
							: null
					),
					el('span', { key: 'd', className: 'sa-file-icon-col' },
						dwgUrl
							? el('a', {
								href: dwgUrl,
								target: '_blank',
								rel: 'noopener',
								title: name + ' (DWG)',
								onClick: function (e) { e.preventDefault(); },
								dangerouslySetInnerHTML: { __html: DOWNLOAD_ICON_SVG },
							})
							: null
					)
				));
			}

			if (files.length > 0) {
				children.push(el('div', { key: 'tbl', className: 'sa-files-table' }, rows));
			}

			sectionEls.push(el('div', { key: 's-' + i, className: 'sa-product-section' }, children));
		}

		return el('div', { className: 'sa-aineistot-wrap' }, sectionEls);
	}

	function normalizeSections(input) {
		if (!Array.isArray(input)) return null;
		var out = [];
		for (var i = 0; i < input.length; i++) {
			var s = input[i];
			if (!s || typeof s !== 'object') return null;
			var srcFiles = Array.isArray(s.files) ? s.files : [];
			var files = [];
			for (var j = 0; j < srcFiles.length; j++) {
				var f = srcFiles[j];
				if (!f || typeof f !== 'object') return null;
				files.push({
					name: typeof f.name === 'string' ? f.name : '',
					pdfUrl: typeof f.pdfUrl === 'string' ? f.pdfUrl : '',
					dwgUrl: typeof f.dwgUrl === 'string' ? f.dwgUrl : '',
				});
			}
			out.push({
				heading: typeof s.heading === 'string' ? s.heading : '',
				text: typeof s.text === 'string' ? s.text : '',
				files: files,
			});
		}
		return out;
	}

	function blankSection() {
		return { heading: '', text: '', files: [] };
	}

	function blankFile() {
		return { name: '', pdfUrl: '', dwgUrl: '' };
	}

	function cloneSections(sections) {
		var copy = [];
		for (var i = 0; i < sections.length; i++) {
			var s = sections[i] || {};
			var files = [];
			var srcFiles = Array.isArray(s.files) ? s.files : [];
			for (var j = 0; j < srcFiles.length; j++) {
				var f = srcFiles[j] || {};
				files.push({
					name: f.name || '',
					pdfUrl: f.pdfUrl || '',
					dwgUrl: f.dwgUrl || '',
				});
			}
			copy.push({
				heading: s.heading || '',
				text: s.text || '',
				files: files,
			});
		}
		return copy;
	}

	function arrayMove(arr, from, to) {
		if (to < 0 || to >= arr.length) {
			return arr;
		}
		var item = arr[from];
		arr.splice(from, 1);
		arr.splice(to, 0, item);
		return arr;
	}

	registerBlockType('vestelli/aineistot', {
		edit: function (props) {
			var attributes = props.attributes || {};
			var setAttributes = props.setAttributes || function () {};
			var sections = Array.isArray(attributes.sections) ? attributes.sections : [];

			var importState = useState('');
			var importText = importState[0];
			var setImportText = importState[1];

			var noticeState = useState(null);
			var notice = noticeState[0];
			var setNotice = noticeState[1];

			var fileInputRef = useRef(null);

			function commit(next) {
				setAttributes({ sections: next });
			}

			function addSection() {
				var next = cloneSections(sections);
				next.push(blankSection());
				commit(next);
			}

			function removeSection(idx) {
				var next = cloneSections(sections);
				next.splice(idx, 1);
				commit(next);
			}

			function moveSection(idx, dir) {
				var next = cloneSections(sections);
				commit(arrayMove(next, idx, idx + dir));
			}

			function updateSection(idx, patch) {
				var next = cloneSections(sections);
				if (!next[idx]) return;
				for (var k in patch) {
					if (Object.prototype.hasOwnProperty.call(patch, k)) {
						next[idx][k] = patch[k];
					}
				}
				commit(next);
			}

			function addFile(sIdx) {
				var next = cloneSections(sections);
				if (!next[sIdx]) return;
				next[sIdx].files.push(blankFile());
				commit(next);
			}

			function removeFile(sIdx, fIdx) {
				var next = cloneSections(sections);
				if (!next[sIdx]) return;
				next[sIdx].files.splice(fIdx, 1);
				commit(next);
			}

			function moveFile(sIdx, fIdx, dir) {
				var next = cloneSections(sections);
				if (!next[sIdx]) return;
				arrayMove(next[sIdx].files, fIdx, fIdx + dir);
				commit(next);
			}

			function updateFile(sIdx, fIdx, patch) {
				var next = cloneSections(sections);
				if (!next[sIdx] || !next[sIdx].files[fIdx]) return;
				for (var k in patch) {
					if (Object.prototype.hasOwnProperty.call(patch, k)) {
						next[sIdx].files[fIdx][k] = patch[k];
					}
				}
				commit(next);
			}

			function renderFileRow(section, sIdx, file, fIdx) {
				var rowKey = 'file-' + sIdx + '-' + fIdx;
				return el(
					'div',
					{ key: rowKey, className: 'va-aineistot-file-row' },
					el('div', { className: 'va-aineistot-file-row__title' },
						el('strong', null, 'Tiedosto ' + (fIdx + 1))
					),
					el(TextControl, {
						label: 'Nimi',
						value: file.name || '',
						onChange: function (value) { updateFile(sIdx, fIdx, { name: value }); },
					}),
					el(TextControl, {
						label: 'PDF-linkki',
						value: file.pdfUrl || '',
						placeholder: 'https://',
						onChange: function (value) { updateFile(sIdx, fIdx, { pdfUrl: value }); },
					}),
					el(TextControl, {
						label: 'DWG-linkki',
						value: file.dwgUrl || '',
						placeholder: 'https://',
						onChange: function (value) { updateFile(sIdx, fIdx, { dwgUrl: value }); },
					}),
					el('div', { className: 'va-aineistot-row-actions' },
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							disabled: fIdx === 0,
							onClick: function () { moveFile(sIdx, fIdx, -1); },
						}, '↑'),
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							disabled: fIdx === section.files.length - 1,
							onClick: function () { moveFile(sIdx, fIdx, 1); },
						}, '↓'),
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							isDestructive: true,
							onClick: function () { removeFile(sIdx, fIdx); },
						}, 'Poista')
					)
				);
			}

			function renderSectionPanel(section, sIdx) {
				var heading = section.heading || '';
				var label = heading !== '' ? heading : 'Osio ' + (sIdx + 1);
				var files = Array.isArray(section.files) ? section.files : [];

				var fileChildren = [];
				for (var f = 0; f < files.length; f++) {
					fileChildren.push(renderFileRow(section, sIdx, files[f], f));
				}

				return el(
					PanelBody,
					{ key: 'section-' + sIdx, title: label, initialOpen: false },
					el(TextControl, {
						label: 'Otsikko',
						value: heading,
						onChange: function (value) { updateSection(sIdx, { heading: value }); },
					}),
					el(TextareaControl, {
						label: 'Teksti',
						value: section.text || '',
						rows: 3,
						onChange: function (value) { updateSection(sIdx, { text: value }); },
					}),
					el('div', { className: 'va-aineistot-section-actions' },
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							disabled: sIdx === 0,
							onClick: function () { moveSection(sIdx, -1); },
						}, '↑ Siirrä ylös'),
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							disabled: sIdx === sections.length - 1,
							onClick: function () { moveSection(sIdx, 1); },
						}, '↓ Siirrä alas'),
						el(Button, {
							variant: 'tertiary',
							size: 'small',
							isDestructive: true,
							onClick: function () { removeSection(sIdx); },
						}, 'Poista osio')
					),
					el('hr', { className: 'va-aineistot-divider' }),
					el('div', { className: 'va-aineistot-files-label' },
						el('strong', null, 'Tiedostot')
					),
					fileChildren.length
						? fileChildren
						: el('p', { className: 'va-aineistot-empty' }, 'Ei tiedostoja vielä.'),
					el(Button, {
						variant: 'secondary',
						onClick: function () { addFile(sIdx); },
					}, 'Lisää tiedosto')
				);
			}

			var sectionPanels = [];
			for (var i = 0; i < sections.length; i++) {
				sectionPanels.push(renderSectionPanel(sections[i], i));
			}

			var exportJson = JSON.stringify(sections, null, 2);

			function copyExportJson() {
				if (navigator.clipboard && navigator.clipboard.writeText) {
					navigator.clipboard.writeText(exportJson).then(
						function () { setNotice({ status: 'success', message: 'Kopioitu leikepöydälle.' }); },
						function () { setNotice({ status: 'error', message: 'Kopiointi epäonnistui.' }); }
					);
				} else {
					setNotice({ status: 'error', message: 'Selain ei tue leikepöydän käyttöä. Kopioi teksti manuaalisesti.' });
				}
			}

			function downloadExportJson() {
				try {
					var blob = new Blob([exportJson], { type: 'application/json' });
					var url = URL.createObjectURL(blob);
					var a = document.createElement('a');
					a.href = url;
					a.download = 'suunnittelija-aineistot.json';
					document.body.appendChild(a);
					a.click();
					document.body.removeChild(a);
					URL.revokeObjectURL(url);
					setNotice({ status: 'success', message: 'JSON-tiedosto ladattu.' });
				} catch (e) {
					setNotice({ status: 'error', message: 'Lataus epäonnistui: ' + e.message });
				}
			}

			function applyImportText(text) {
				if (!text || !text.replace(/\s/g, '')) {
					setNotice({ status: 'error', message: 'Tekstikenttä on tyhjä.' });
					return;
				}
				var parsed;
				try {
					parsed = JSON.parse(text);
				} catch (e) {
					setNotice({ status: 'error', message: 'JSON-jäsennys epäonnistui: ' + e.message });
					return;
				}
				var normalized = normalizeSections(parsed);
				if (!normalized) {
					setNotice({ status: 'error', message: 'JSON ei vastaa odotettua muotoa (taulukko osioita {heading, text, files: [...]}).' });
					return;
				}
				commit(normalized);
				setImportText('');
				setNotice({
					status: 'success',
					message: 'Tuotu ' + normalized.length + ' osio(t)a.',
				});
			}

			function pickImportFile() {
				if (fileInputRef.current) {
					fileInputRef.current.click();
				}
			}

			function handleImportFile(event) {
				var file = event.target.files && event.target.files[0];
				if (!file) return;
				var reader = new FileReader();
				reader.onload = function (e) {
					var text = typeof e.target.result === 'string' ? e.target.result : '';
					setImportText(text);
					applyImportText(text);
				};
				reader.onerror = function () {
					setNotice({ status: 'error', message: 'Tiedoston luku epäonnistui.' });
				};
				reader.readAsText(file);
				event.target.value = '';
			}

			var importExportPanel = el(
				PanelBody,
				{ key: 'import-export', title: 'Tuonti / vienti (JSON)', initialOpen: false },
				notice
					? el(Notice, {
						status: notice.status,
						isDismissible: true,
						onRemove: function () { setNotice(null); },
					}, notice.message)
					: null,
				el('div', { className: 'va-aineistot-ie-section' },
					el('strong', null, 'Vienti'),
					el(TextareaControl, {
						label: 'Nykyiset tiedot JSON-muodossa',
						value: exportJson,
						readOnly: true,
						rows: 8,
						onChange: function () {},
					}),
					el('div', { className: 'va-aineistot-row-actions' },
						el(Button, {
							variant: 'secondary',
							onClick: copyExportJson,
						}, 'Kopioi leikepöydälle'),
						el(Button, {
							variant: 'secondary',
							onClick: downloadExportJson,
						}, 'Lataa .json-tiedostona')
					)
				),
				el('hr', { className: 'va-aineistot-divider' }),
				el('div', { className: 'va-aineistot-ie-section' },
					el('strong', null, 'Tuonti'),
					el('p', { className: 'va-aineistot-ie-help' },
						'Tuonti korvaa lohkon nykyiset osiot annetulla JSON-tiedolla.'
					),
					el(TextareaControl, {
						label: 'Liitä JSON tähän',
						value: importText,
						rows: 8,
						placeholder: '[\n  {\n    "heading": "Tuote",\n    "text": "Kuvaus",\n    "files": [\n      { "name": "Mittakuva", "pdfUrl": "https://...", "dwgUrl": "https://..." }\n    ]\n  }\n]',
						onChange: function (value) { setImportText(value); },
					}),
					el('div', { className: 'va-aineistot-row-actions' },
						el(Button, {
							variant: 'primary',
							onClick: function () { applyImportText(importText); },
						}, 'Korvaa tekstistä'),
						el(Button, {
							variant: 'secondary',
							onClick: pickImportFile,
						}, 'Tuo tiedostosta…')
					),
					el('input', {
						ref: fileInputRef,
						type: 'file',
						accept: 'application/json,.json',
						style: { display: 'none' },
						onChange: handleImportFile,
					})
				)
			);

			var blockProps = useBlockProps({
				className: 'wp-block-vestelli-aineistot',
			});

			var canvas;
			if (sections.length === 0) {
				canvas = el(
					'div',
					{ className: 'wp-block-vestelli-aineistot__placeholder' },
					el('p', null, 'Suunnittelija-aineistot'),
					el('p', { className: 'wp-block-vestelli-aineistot__hint' },
						'Lisää osioita ja tiedostoja oikeasta sivupalkista.'
					),
					el(Button, {
						variant: 'primary',
						onClick: addSection,
					}, 'Lisää ensimmäinen osio')
				);
			} else {
				canvas = renderPreview(sections);
			}

			return el(
				Fragment,
				null,
				el(
					InspectorControls,
					null,
					el(
						PanelBody,
						{ title: 'Osiot', initialOpen: true },
						sectionPanels.length
							? null
							: el('p', { className: 'va-aineistot-empty' }, 'Ei osioita vielä.'),
						el(Button, {
							variant: 'primary',
							onClick: addSection,
						}, 'Lisää osio')
					),
					sectionPanels,
					importExportPanel
				),
				el('div', blockProps, canvas)
			);
		},

		save: function () {
			return null;
		},
	});
})();
