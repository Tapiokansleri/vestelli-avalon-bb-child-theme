<?php
/**
 * Dynamic header responsive CSS for Vestelli and Avalon designs.
 *
 * @package Vestelli_Avalon
 */

if ( ! defined( 'ABSPATH' ) ) {
  exit;
}

/**
 * Normalize a header mobile breakpoint value.
 */
function va_get_header_mobile_breakpoint_from_value( $value ) {
  $breakpoint = absint( $value );

  if ( ! $breakpoint ) {
    $breakpoint = 1200;
  }

  if ( $breakpoint < 480 ) {
    $breakpoint = 480;
  }

  if ( $breakpoint > 2400 ) {
    $breakpoint = 2400;
  }

  return $breakpoint;
}

/**
 * Get the shared header mobile breakpoint in pixels.
 */
function va_get_header_mobile_breakpoint() {
  $stored = get_option( 'va_header_mobile_breakpoint', '' );

  if ( $stored === '' || $stored === false ) {
    $stored = get_option( 'va_vestelli_mobile_breakpoint', 1200 );
  }

  return va_get_header_mobile_breakpoint_from_value( $stored );
}

/**
 * Build Vestelli header media-query CSS for the configured breakpoint.
 */
function va_get_vestelli_header_responsive_css( $bp = null ) {
  $bp          = null === $bp ? va_get_header_mobile_breakpoint() : va_get_header_mobile_breakpoint_from_value( $bp );
  $desktop_min = $bp + 1;

  return <<<CSS
@media (max-width: {$bp}px) {
  body.header-design-vestelli.page-template-tpl-sidebar .fl-page-content {
    padding-top: 102px;
  }

  .vestelli-header {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
  }

  .vestelli-header::before {
    width: 140px;
  }

  .vestelli-header::after {
    left: 140px;
  }

  .vestelli-header-container {
    width: 100%;
    max-width: 100%;
  }

  .vestelli-header-inner {
    min-height: 72px;
    align-items: center;
  }

  .vestelli-header-nav-section {
    display: none;
  }

  .vestelli-header-logo-section {
    flex: 1 1 auto;
    max-width: none;
    min-width: 0;
    justify-content: flex-start;
    padding: 10px 14px;
  }

  .vestelli-custom-logo {
    max-height: 40px;
  }

  .mobile-menu-toggle {
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-left: auto;
    margin-right: 14px;
    width: 44px;
    height: 44px;
    min-width: 44px;
    min-height: 44px;
    z-index: 10070;
    color: var(--va-brand-color);
    gap: 4px;
  }

  .vestelli-header .mobile-menu-toggle .hamburger-line {
    background-color: #fff;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.4);
  }

  .mobile-menu {
    width: min(92vw, 420px);
    right: min(-92vw, -420px);
  }
}

@media (min-width: {$desktop_min}px) and (max-width: 1560px) {
  .vestelli-header-nav-section {
    padding: 14px 20px 14px 16px;
  }

  .vestelli-header-main-row,
  .vestelli-header-extras {
    gap: 10px;
  }

  .vestelli-main-menu > li > a {
    padding: 10px 10px;
    font-size: 10px;
    letter-spacing: 0.5px;
  }

  .vestelli-cta-button {
    padding: 0 16px;
    font-size: 11px;
  }
}

@media (min-width: {$desktop_min}px) {
  .mobile-menu-toggle,
  .mobile-menu-overlay {
    display: none !important;
  }
}
CSS;
}

/**
 * Build Avalon header media-query CSS for the configured breakpoint.
 */
function va_get_avalon_header_responsive_css( $bp = null ) {
  $bp          = null === $bp ? va_get_header_mobile_breakpoint() : va_get_header_mobile_breakpoint_from_value( $bp );
  $desktop_min = $bp + 1;

  return <<<CSS
@media (max-width: {$bp}px) {
  .header-nav,
  .header-right {
    display: none;
  }

  .mobile-menu-toggle {
    display: flex;
  }

  .header-container {
    padding: 0 1.5rem;
  }
}

@media (min-width: {$desktop_min}px) {
  .mobile-menu-toggle,
  .mobile-menu-overlay {
    display: none !important;
  }
}

@media (min-width: {$desktop_min}px) and (max-width: 1500px) {
  .header-right {
    gap: 12px;
  }

  .header-search-toggle {
    width: 28px;
    height: 28px;
    border-radius: 6px;
  }

  .header-search-toggle svg {
    width: 14px;
    height: 14px;
  }

  .language-switcher select {
    font-size: 12px;
    padding: 4px 22px 4px 8px;
    background-position: right 6px center;
    background-size: 8px 6px;
  }
}
CSS;
}
