/**
 * Header Scroll Behavior
 * Hide header on scroll down, show on scroll up
 * 
 * @package Vestelli
 */

(function() {
  'use strict';
  
  var lastScrollTop = 0;
  var scrollThreshold = 10; // Minimum scroll distance to trigger hide/show
  var header = document.getElementById('vestelli-header');
  
  if (!header) {
    return;
  }
  
  var ticking = false;
  
  function updateHeader() {
    var scrollTop = window.pageYOffset || document.documentElement.scrollTop;
    var scrollDifference = Math.abs(scrollTop - lastScrollTop);
    
    // Only update if scrolled enough
    if (scrollDifference < scrollThreshold) {
      ticking = false;
      return;
    }
    
    if (scrollTop > lastScrollTop && scrollTop > 100) {
      // Scrolling down - hide header
      header.classList.add('header-hidden');
      header.classList.remove('header-visible');
    } else {
      // Scrolling up - show header
      header.classList.remove('header-hidden');
      header.classList.add('header-visible');
    }
    
    lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    ticking = false;
  }
  
  function onScroll() {
    if (!ticking) {
      window.requestAnimationFrame(updateHeader);
      ticking = true;
    }
  }
  
  // Listen to scroll events
  window.addEventListener('scroll', onScroll, { passive: true });
  
  // Show header at top of page
  if (window.pageYOffset === 0) {
    header.classList.add('header-visible');
  }
})();
