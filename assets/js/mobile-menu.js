/**
 * Mobile Menu Toggle & Desktop Mega Menu
 *
 * @package Vestelli_Avalon
 */

(function() {
  'use strict';

  function getDirectChild(element, selector) {
    if (!element || !element.children || !element.children.length) {
      return null;
    }
    for (var i = 0; i < element.children.length; i += 1) {
      if (element.children[i].matches(selector)) {
        return element.children[i];
      }
    }
    return null;
  }

  function setupDesktopMegaMenu() {
    // Support both Vestelli and Avalon Nordic header selectors
    var desktopNav = document.querySelector('.vestelli-header-nav .vestelli-main-menu')
      || document.querySelector('.header-nav .main-menu');
    if (!desktopNav) {
      return;
    }

    var topItems = Array.prototype.slice.call(desktopNav.children);
    var HOVER_DELAY = 80;

    function ensureInnerContainer(submenu) {
      if (!submenu) {
        return;
      }
      if (submenu.querySelector(':scope > .kmm-submenu__inner')) {
        return;
      }
      var inner = document.createElement('div');
      inner.className = 'kmm-submenu__inner';
      var nodes = Array.prototype.slice.call(submenu.childNodes);
      nodes.forEach(function(node) {
        inner.appendChild(node);
      });
      submenu.appendChild(inner);
    }

    function closeAllMega() {
      topItems.forEach(function(item) {
        if (item.classList.contains('has-mega')) {
          item.classList.remove('is-open');
        }
      });
    }

    topItems.forEach(function(item) {
      var submenu = getDirectChild(item, '.sub-menu');
      if (!submenu || !item.classList.contains('menu-item-has-children')) {
        return;
      }

      if (!submenu.querySelector('.menu-item-has-children')) {
        return;
      }

      item.classList.add('has-mega');
      ensureInnerContainer(submenu);

      var trigger = getDirectChild(item, 'a');
      if (!trigger) {
        return;
      }

      var openTimer = null;
      var closeTimer = null;

      function openMega() {
        clearTimeout(closeTimer);
        closeTimer = null;
        closeAllMega();
        item.classList.add('is-open');
      }

      function closeMega() {
        clearTimeout(openTimer);
        openTimer = null;
        item.classList.remove('is-open');
      }

      trigger.addEventListener('mouseenter', function() {
        clearTimeout(closeTimer);
        openTimer = setTimeout(openMega, HOVER_DELAY);
      });

      trigger.addEventListener('mouseleave', function() {
        clearTimeout(openTimer);
        closeTimer = setTimeout(closeMega, 200);
      });

      submenu.addEventListener('mouseenter', function() {
        clearTimeout(closeTimer);
        clearTimeout(openTimer);
      });

      submenu.addEventListener('mouseleave', function() {
        closeTimer = setTimeout(closeMega, 200);
      });

      trigger.addEventListener('click', function(event) {
        var href = trigger.getAttribute('href');
        if (!href || href === '#' || href === 'javascript:void(0)') {
          event.preventDefault();
        }
      });
    });

    desktopNav.querySelectorAll('a.avalon-mega-no-link').forEach(function(link) {
      link.addEventListener('click', function(event) {
        event.preventDefault();
      });
    });
  }

  function setupMobileOverlayMenu() {
    var menuToggle = document.querySelector('.mobile-menu-toggle');
    var menuOverlay = document.querySelector('.mobile-menu-overlay');
    var body = document.body;

    if (!menuToggle || !menuOverlay) {
      return;
    }

    menuToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();

      if (menuToggle.classList.contains('active')) {
        closeMenu();
      } else {
        openMenu();
      }
    });

    menuOverlay.addEventListener('click', function(e) {
      if (e.target === menuOverlay) {
        closeMenu();
      }
    });

    var closeButton = menuOverlay.querySelector('.mobile-menu-close');
    if (closeButton) {
      closeButton.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        closeMenu();
      });
    }

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && menuToggle.classList.contains('active')) {
        closeMenu();
      }
    });

    var submenuToggles = menuOverlay.querySelectorAll('.mobile-submenu-toggle');
    submenuToggles.forEach(function(toggle) {
      toggle.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();

        var listItem = this.closest('li');
        var submenu = listItem ? getDirectChild(listItem, '.sub-menu') : null;

        if (!submenu) {
          return;
        }

        var isActive = submenu.classList.contains('active');
        submenu.classList.toggle('active', !isActive);
        this.setAttribute('aria-expanded', isActive ? 'false' : 'true');
      });
    });

    var menuLinks = menuOverlay.querySelectorAll('.mobile-menu-link, .mobile-main-menu .sub-menu a');
    menuLinks.forEach(function(link) {
      link.addEventListener('click', function() {
        setTimeout(function() {
          closeMenu();
        }, 100);
      });
    });

    function openMenu() {
      menuToggle.classList.add('active');
      menuToggle.setAttribute('aria-expanded', 'true');
      menuOverlay.classList.add('active');
      body.style.overflow = 'hidden';
    }

    function closeMenu() {
      menuToggle.classList.remove('active');
      menuToggle.setAttribute('aria-expanded', 'false');
      menuOverlay.classList.remove('active');
      body.style.overflow = '';

      menuOverlay.querySelectorAll('.sub-menu.active').forEach(function(submenu) {
        submenu.classList.remove('active');
      });

      menuOverlay.querySelectorAll('.mobile-submenu-toggle').forEach(function(button) {
        button.setAttribute('aria-expanded', 'false');
      });
    }

    window.addEventListener('resize', function() {
      if (window.innerWidth > 1200 && menuToggle.classList.contains('active')) {
        closeMenu();
      }
    });
  }

  // Desktop header search toggle (Avalon design)
  function setupDesktopSearch() {
    var headerSearch = document.querySelector('.header-search');
    var searchToggle = document.querySelector('.header-search-toggle');
    var searchDropdown = document.querySelector('.header-search-dropdown');
    var searchInput = document.querySelector('.header-search-input');

    if (!headerSearch || !searchToggle || !searchDropdown) {
      return;
    }

    function openSearch() {
      searchDropdown.classList.add('active');
      searchDropdown.setAttribute('aria-hidden', 'false');
      searchToggle.setAttribute('aria-expanded', 'true');
      if (searchInput) {
        searchInput.focus();
      }
    }

    function closeSearch() {
      searchDropdown.classList.remove('active');
      searchDropdown.setAttribute('aria-hidden', 'true');
      searchToggle.setAttribute('aria-expanded', 'false');
    }

    searchToggle.addEventListener('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      if (searchDropdown.classList.contains('active')) {
        closeSearch();
      } else {
        openSearch();
      }
    });

    document.addEventListener('click', function(e) {
      if (!headerSearch.contains(e.target)) {
        closeSearch();
      }
    });

    document.addEventListener('keydown', function(e) {
      if (e.key === 'Escape' && searchDropdown.classList.contains('active')) {
        closeSearch();
      }
    });
  }

  setupDesktopMegaMenu();
  setupMobileOverlayMenu();
  setupDesktopSearch();
})();
