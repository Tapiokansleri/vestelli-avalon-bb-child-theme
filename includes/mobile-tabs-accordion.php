<?php
/**
 * WooCommerce Tabs Accordion functionality
 * 
 * Converts WooCommerce Product Tabs to Accordion (all screen sizes)
 * This modifies the Yikes Custom WooCommerce Tabs plugin
 * to display as an accordion instead of tabs
 * 
 * @package Vestelli
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit; // Exit if accessed directly.
}

/**
 * Convert WooCommerce Product Tabs to Accordion
 */
add_action( 'wp_enqueue_scripts', 'vestelli_tabs_accordion', 20 );
function vestelli_tabs_accordion() {
  // Only load on single product pages (requires WooCommerce)
  if ( ! function_exists( 'is_product' ) || ! is_product() ) {
    return;
  }
  
  // Enqueue CSS for accordion (all screen sizes)
  wp_add_inline_style( 'woocommerce-general', '
    /* Hide tab navigation - always use accordion */
    .woocommerce-tabs .wc-tabs {
      display: none !important;
    }
    
    /* Accordion wrapper */
    .woocommerce-tabs .accordion-mobile-wrapper {
      display: block;
      width: 100%;
    }
    
    /* Accordion item - clean minimal design */
    .woocommerce-tabs .accordion-item {
      margin-bottom: 0;
      border: none;
      border-bottom: 1px solid #e5e5e5;
      border-radius: 0;
      overflow: visible;
      background: #fff;
      width: 100%;
      box-sizing: border-box;
    }
    
    .woocommerce-tabs .accordion-item:first-child {
      border-top: 1px solid #e5e5e5;
    }
    
    /* Accordion header - clean design */
    .woocommerce-tabs .accordion-header {
      padding: 18px 20px;
      background-color: transparent;
      font-weight: 400;
      font-size: 16px;
      color: #333;
      cursor: pointer;
      position: relative;
      user-select: none;
      transition: color 0.2s ease;
      min-height: 56px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }
    
    .woocommerce-tabs .accordion-header:hover {
      color: #000;
    }
    
    /* Accordion toggle icon */
    .woocommerce-tabs .accordion-icon {
      font-size: 18px;
      font-weight: 300;
      color: #666;
      transition: transform 0.3s ease, color 0.2s ease;
      line-height: 1;
      flex-shrink: 0;
      margin-left: 15px;
    }
    
    .woocommerce-tabs .accordion-header:hover .accordion-icon {
      color: #333;
    }
    
    /* Active accordion icon rotation */
    .woocommerce-tabs .accordion-item.active .accordion-icon {
      transform: rotate(45deg);
      color: #333;
    }
    
    /* Accordion content */
    .woocommerce-tabs .accordion-content {
      display: none;
      padding: 0 20px 24px 20px !important;
      margin: 0;
      width: 100%;
      box-sizing: border-box;
      overflow: visible;
      min-height: auto;
    }
    
    /* Show content when active */
    .woocommerce-tabs .accordion-item.active .accordion-content {
      display: block !important;
      visibility: visible;
      height: auto;
      max-height: none;
    }
    
    /* Hide original panel display */
    .woocommerce-tabs .woocommerce-Tabs-panel {
      display: none !important;
    }
    
    /* Hide .yikes-custom-woo-tab-title elements */
    .woocommerce-tabs .yikes-custom-woo-tab-title {
      display: none !important;
    }
  ' );
  
  // Enqueue JavaScript for accordion functionality
  wp_add_inline_script( 'jquery', '
    jQuery(document).ready(function($) {
      function initAccordion() {
        var $tabs = $(".woocommerce-tabs");
        
        if ($tabs.length && !$tabs.hasClass("accordion-initialized")) {
          $tabs.addClass("accordion-initialized");
          
          // Create accordion wrapper
          var $wrapper = $("<div class=\"accordion-mobile-wrapper\"></div>");
          
          // Process each tab
          $(".wc-tabs li").each(function() {
            var $tab = $(this);
            var $tabLink = $tab.find("a");
            var tabId = $tabLink.attr("href");
            var tabTitle = $tabLink.text();
            
            if (tabId && tabTitle) {
              var $panel = $(tabId);
              if ($panel.length) {
                // Create accordion item
                var $accordionItem = $("<div class=\"accordion-item\"></div>");
                var $accordionHeader = $("<div class=\"accordion-header\"><span class=\"accordion-title\">" + tabTitle + "</span><span class=\"accordion-icon\">+</span></div>");
                var $accordionContent = $("<div class=\"accordion-content\"></div>");
                
                // Move panel content to accordion
                $accordionContent.html($panel.html());
                
                // Remove .yikes-custom-woo-tab-title elements from content
                $accordionContent.find(".yikes-custom-woo-tab-title").remove();
                
                $accordionItem.append($accordionHeader).append($accordionContent);
                $wrapper.append($accordionItem);
              }
            }
          });
          
          // Insert accordion wrapper after tabs
          $tabs.append($wrapper);
          
          // All items start hidden (none active by default)
          
          // Accordion toggle functionality
          $wrapper.on("click", ".accordion-header", function(e) {
            e.preventDefault();
            var $item = $(this).closest(".accordion-item");
            var isActive = $item.hasClass("active");
            
            // Close all items
            $wrapper.find(".accordion-item").removeClass("active");
            
            // Toggle current item
            if (!isActive) {
              $item.addClass("active");
            }
          });
          
          // Prevent default tab link behavior
          $(".wc-tabs a").on("click", function(e) {
            e.preventDefault();
          });
        }
      }
      
      // Initialize on load
      initAccordion();
    });
  ', 'after' );
}
