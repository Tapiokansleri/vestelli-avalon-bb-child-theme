# Vestelli Avalon – Beaver Builder Child Theme

A unified WordPress child theme for [Beaver Builder](https://www.wpbeaverbuilder.com/) (`bb-theme`) that powers both the **Vestelli** and **Avalon Nordic** sites from a single codebase.

## Features

- **Dual header design** – switch between Vestelli (two-tone) and Avalon Nordic (single-color) header via a theme setting
- **Mega menu** – auto-detects 3-level menus and renders a full-width dropdown panel
- **Custom Gutenberg Hero block** – dynamic block with background image, overlay, buttons, and inline editing
- **8 Beaver Builder modules** – reusable across both sites
- **WooCommerce support** – cart icon in header, product zoom control
- **WPML integration** – language switcher in header, Beaver Builder modal fix
- **Portfolio CPT** – optional custom post type with Project Type taxonomy (toggle on/off)
- **Mobile menu** – full-screen overlay with sub-menu accordion
- **Transparent header** – page template for pages with transparent header background
- **Auto-updates** – checks GitHub Releases for new versions via the WordPress updater

## Requirements

- WordPress 6.0+
- Beaver Builder Theme (`bb-theme`) as parent theme
- PHP 7.4+

## Installation

1. Install and activate the Beaver Builder Theme
2. Download the [latest release](https://github.com/Tapiokansleri/vestelli-avalon-bb-child-theme/releases/latest)
3. Upload via **Appearance → Themes → Add New → Upload Theme**
4. Activate the theme

## Configuration

Go to **Appearance → Teeman asetukset** to configure:

- **Header Design** – Avalon or Vestelli
- **Logo, CTA button, cart, search, language switcher**
- **Social media URLs** (Vestelli design)
- **Header opacity** (Avalon transparent header)
- **Portfolio CPT** toggle
- **Custom header/footer scripts**

## Updates

The theme checks GitHub Releases automatically. When a new release is tagged, it will appear in **Dashboard → Updates** like any other theme.

To create a release:
1. Update the `Version` in `style.css`
2. Tag the commit: `git tag v1.1`
3. Push the tag: `git push origin v1.1`
4. Create a release on GitHub from the tag

## License

GPL-2.0-or-later
