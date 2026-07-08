(function ($) {
  'use strict';

  var config = window.vaThemeSettings || {};

  function getHeaderType() {
    return $('#va_header_type').val() || config.headerType || 'custom';
  }

  function getHeaderDesign() {
    return $('#va_header_design').val() || config.headerDesign || 'avalon';
  }

  function isQuoteMode() {
    return $('#va_quote_mode').is(':checked');
  }

  function isThemerActive() {
    return !!config.themerHeaderActive;
  }

  function setRowVisible($row, visible) {
    if (!$row || !$row.length) {
      return;
    }
    $row.toggle(visible);
  }

  function toggleField(fieldId, visible) {
    var $wrapper = $('#va_field_' + fieldId);
    if (!$wrapper.length) {
      return;
    }
    setRowVisible($wrapper.closest('tr'), visible);
  }

  function toggleGroup(groupName, visible) {
    $('[data-va-group="' + groupName + '"]').each(function () {
      var $target = $(this).is('tr') ? $(this) : $(this).closest('tr');
      setRowVisible($target, visible);
    });
  }

  function initTabs() {
    var $tabs = $('.va-settings-nav-tab');
    var $panels = $('.va-settings-tab-panel');

    $tabs.on('click', function (event) {
      event.preventDefault();
      var tab = $(this).data('va-tab');

      $tabs.removeClass('nav-tab-active');
      $(this).addClass('nav-tab-active');

      $panels.removeClass('is-active');
      $('#va-tab-panel-' + tab).addClass('is-active');
    });
  }

  function updateConditionalFields() {
    var headerType = getHeaderType();
    var headerDesign = getHeaderDesign();
    var themerActive = isThemerActive();
    var customHeader = !themerActive && headerType === 'custom';
    var beaverHeader = !themerActive && headerType === 'beaver-builder';

    toggleGroup('themer-notice', themerActive);
    toggleGroup('header-general', !themerActive);
    toggleGroup('beaver-header', beaverHeader);
    toggleGroup('custom-header', customHeader);
    toggleGroup('header-responsive', customHeader);
    toggleGroup('avalon-opacity', customHeader && headerDesign === 'avalon');

    toggleField('va_hide_prices', isQuoteMode());
    toggleField('va_quote_button_text', isQuoteMode());
    toggleField('va_quote_email', isQuoteMode());
  }

  $(document).ready(function () {
    initTabs();
    updateConditionalFields();

    $('#va_header_type, #va_header_design, #va_quote_mode').on('change', updateConditionalFields);
  });
})(jQuery);
