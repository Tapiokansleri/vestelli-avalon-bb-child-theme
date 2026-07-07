/**
 * Frontend JavaScript for Tuotteen lisätiedot haitari module
 * 
 * @package Vestelli
 */

(function($) {
  $(document).ready(function() {
    // Handle all accordion modules on the page
    $('.tuotteen-lisatiedot-accordion').each(function() {
      var accordion = $(this);
      
      accordion.on('click', '.accordion-header', function(e) {
        e.preventDefault();
        
        var $item = $(this).closest('.accordion-item');
        var $content = $item.find('.accordion-content');
        var $icon = $(this).find('.accordion-icon');
        var isOpen = $item.hasClass('open');
        
        // Close all items in this accordion
        accordion.find('.accordion-item').removeClass('open');
        accordion.find('.accordion-header').attr('aria-expanded', 'false');
        accordion.find('.accordion-content').attr('aria-hidden', 'true');
        
        // Open clicked item if it was closed
        if (!isOpen) {
          $item.addClass('open');
          $(this).attr('aria-expanded', 'true');
          $content.attr('aria-hidden', 'false');
        }
      });
    });
  });
})(jQuery);
