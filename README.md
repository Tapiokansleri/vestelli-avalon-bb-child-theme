# Vestelli Avalon – Beaver Builder Child Theme

Avalon and Vestelli unified child theme for [Beaver Builder](https://www.wpbeaverbuilder.com/) (`bb-theme`).

## Features

- **Dual header design** – switch between Vestelli (two-tone) and Avalon Nordic (single-color) header via a theme setting
- **Mega menu** – auto-detects 3-level menus and renders a full-width dropdown panel
- **Custom Gutenberg blocks** – Hero, Hero Split, and Aineistot blocks
- **Beaver Builder modules** – reusable across both sites
- **WooCommerce support** – cart icon in header, product zoom control, quote request mode
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
2. Tag the commit: `git tag v0.02`
3. Push the tag: `git push origin v0.02`
4. Create a release on GitHub from the tag

## License

GPL-2.0-or-later
