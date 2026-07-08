<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<?php do_action( 'fl_head_open' ); ?>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<?php echo apply_filters( 'fl_theme_viewport', "<meta name='viewport' content='width=device-width, initial-scale=1.0' />\n" ); ?>
<?php echo apply_filters( 'fl_theme_xua_compatible', "<meta http-equiv='X-UA-Compatible' content='IE=edge' />\n" ); ?>
<link rel="profile" href="https://gmpg.org/xfn/11" />
<?php
wp_head();
FLTheme::head();
?>
</head>
<body <?php body_class(); ?><?php FLTheme::print_schema( ' itemscope="itemscope" itemtype="https://schema.org/WebPage"' ); ?>>
<?php
// Check for BB Themer header BEFORE rendering header_code
// Priority 1: Check if BB Themer header is configured
$themer_header_exists = false;

// Method 1: Check if Themer header layout function exists and returns a value
if ( function_exists( 'fl_theme_builder_get_header_layout' ) ) {
	$themer_header_id = fl_theme_builder_get_header_layout();
	$themer_header_exists = ! empty( $themer_header_id ) && is_numeric( $themer_header_id );
}

// Method 2: Check if Themer has published header layouts
if ( ! $themer_header_exists && post_type_exists( 'fl-theme-layout' ) ) {
	$themer_headers = get_posts( array(
		'post_type'      => 'fl-theme-layout',
		'posts_per_page' => 1,
		'post_status'    => 'publish',
		'meta_query'     => array(
			array(
				'key'   => '_fl_theme_layout_type',
				'value' => 'header',
			),
		),
	) );
	$themer_header_exists = ! empty( $themer_headers );
}

FLTheme::header_code();
do_action( 'fl_body_open' );
?>
<div class="fl-page">
	<?php
	do_action( 'fl_page_open' );
	?>

	<?php
	if ( $themer_header_exists ) {
		// BB Themer header is set - use it (highest priority)
		FLTheme::fixed_header();
		do_action( 'fl_before_top_bar' );
		FLTheme::top_bar();
		do_action( 'fl_after_top_bar' );
		do_action( 'fl_before_header' );
		FLTheme::header_layout();
		do_action( 'fl_after_header' );
	} else {
		// Priority 2: Check user's header type preference
		$header_type = get_option( 'va_header_type', 'custom' );

		if ( $header_type === 'beaver-builder' && class_exists( 'FLBuilder' ) ) {
			// Use Beaver Builder header template
			$template_id = get_option( 'va_bb_header_template', '' );
			if ( ! empty( $template_id ) && get_post_status( $template_id ) === 'publish' ) {
				FLBuilder::render_content_by_id( $template_id );
			} else {
				$header_type = 'custom';
			}
		}

		if ( $header_type === 'custom' || ( $header_type === 'beaver-builder' && empty( $template_id ) ) ) :
			// Get shared settings
			$header_design = get_option( 'va_header_design', 'avalon' );
			$cta_text = get_option( 'va_cta_text', 'Pyydä tarjous' );
			$cta_link = get_option( 'va_cta_link', '#' );
			if ( empty( $cta_link ) || $cta_link === '#' ) {
				$cta_link = get_permalink( get_page_by_path( 'yhteys' ) ) ?: '#';
			}
			$show_cta = ( get_option( 'va_show_cta', '1' ) === '1' );
			$show_cart = ( get_option( 'va_show_cart', '1' ) === '1' );
			$show_language_switcher = ( get_option( 'va_show_language_switcher', '1' ) === '1' );
			$show_search = ( get_option( 'va_show_search', '1' ) === '1' );
			$show_social_icons = ( get_option( 'va_show_social_icons', '1' ) === '1' );
			$logo_url = get_option( 'va_logo' );

			if ( $header_design === 'vestelli' ) :
				// ─── VESTELLI HEADER: Two-tone (white left, dark blue right) ───
		?>
		<header id="vestelli-header" class="vestelli-header">
		<div class="vestelli-header-container">
			<div class="vestelli-header-inner">
				<!-- Left: Logo area (solid white) -->
				<div class="vestelli-header-logo-section">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="vestelli-logo-link">
						<?php if ( $logo_url ) : ?>
							<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="vestelli-custom-logo" />
						<?php else : ?>
							<span class="vestelli-logo-text"><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
						<?php endif; ?>
					</a>
				</div>

				<!-- Right: Navigation + extras on same row -->
				<div class="vestelli-header-nav-section">
					<div class="vestelli-header-main-row">
						<nav class="vestelli-header-nav">
							<?php
							wp_nav_menu( array(
								'theme_location' => 'primary',
								'container'      => false,
								'menu_class'     => 'vestelli-main-menu',
								'fallback_cb'    => false,
								'depth'          => 3,
								'walker'         => new VA_Menu_Walker(),
							) );
							?>
						</nav>
						<div class="vestelli-header-extras">
						<?php
						if ( $show_social_icons ) {
							va_render_header_social_icons( 'vestelli-social-icons' );
						}
						?>
						<?php if ( $show_search ) : ?>
							<button type="button" class="vestelli-search-toggle" aria-label="<?php esc_attr_e( 'Open search', 'vestelli-avalon' ); ?>" aria-controls="vestelli-search-overlay" aria-expanded="false">
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
									<circle cx="11" cy="11" r="8"></circle>
									<path d="m21 21-4.35-4.35"></path>
								</svg>
							</button>
						<?php endif; ?>
						<?php
						if ( $show_language_switcher && ( function_exists( 'icl_get_languages' ) || function_exists( 'apply_filters' ) ) ) {
							$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
							if ( ! empty( $languages ) && count( $languages ) > 1 ) {
								$current_language = apply_filters( 'wpml_current_language', NULL );
						?>
								<div class="vestelli-language-switcher">
									<select id="wpml-language-switcher" class="vestelli-lang-select" onchange="if(this.value) window.location.href=this.value;">
										<?php foreach ( $languages as $code => $language ) :
											$url = apply_filters( 'wpml_permalink', $language['url'], $code );
											if ( ! $url ) {
												$url = $language['url'];
											}
										?>
											<option value="<?php echo esc_url( $url ); ?>" <?php selected( $current_language, $code ); ?>>
												<?php echo esc_html( strtoupper( $code ) ); ?>
											</option>
										<?php endforeach; ?>
									</select>
								</div>
						<?php
							}
						}
						?>
						<?php if ( $show_cta ) : ?>
							<a href="<?php echo esc_url( $cta_link ); ?>" class="vestelli-cta-button">
								<span><?php echo esc_html( $cta_text ); ?></span>
							</a>
						<?php endif; ?>
						<?php if ( $show_cart && class_exists( 'WooCommerce' ) ) : ?>
							<div class="vestelli-header-cart">
								<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="vestelli-cart-link">
									<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
										<path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V17C17 18.1 17.9 19 19 19C20.1 19 21 18.1 21 17V13M9 19.5C9.8 19.5 10.5 20.2 10.5 21C10.5 21.8 9.8 22.5 9 22.5C8.2 22.5 7.5 21.8 7.5 21C7.5 20.2 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21C21.5 21.8 20.8 22.5 20 22.5C19.2 22.5 18.5 21.8 18.5 21C18.5 20.2 19.2 19.5 20 19.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
									</svg>
									<?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
										<span class="vestelli-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
									<?php endif; ?>
								</a>
							</div>
						<?php endif; ?>
						</div>
					</div>
					<?php if ( $show_search ) : ?>
						<div id="vestelli-search-overlay" class="vestelli-search-overlay" aria-hidden="true">
							<div class="vestelli-search-overlay-backdrop"></div>
							<div class="vestelli-search-overlay-panel" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'vestelli-avalon' ); ?>">
								<button type="button" class="vestelli-search-overlay-close" aria-label="<?php esc_attr_e( 'Close search', 'vestelli-avalon' ); ?>">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
										<path d="M18 6L6 18M6 6l12 12"></path>
									</svg>
								</button>
								<form role="search" method="get" class="vestelli-header-search-overlay-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
									<input type="search" placeholder="<?php esc_attr_e( 'Hae...', 'vestelli-avalon' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" name="s" />
									<button type="submit"><?php esc_html_e( 'Hae', 'vestelli-avalon' ); ?></button>
								</form>
							</div>
						</div>
					<?php endif; ?>
				</div>

			<!-- Hamburger Menu Button (Mobile) -->
			<button class="mobile-menu-toggle vestelli-mobile-toggle" aria-label="<?php _e( 'Toggle menu', 'vestelli-avalon' ); ?>" aria-expanded="false">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>
		</div>
		</div>

		<?php else : // $header_design === 'avalon'
			// ─── AVALON HEADER: Single-color dark blue, fixed ───
			$header_classes = 'avalon-nordic-header';
			$header_style = '';
			$page_template = get_page_template_slug();
			$is_transparent_header = ( $page_template === 'page-transparent-header.php' );
			if ( $is_transparent_header ) {
				$header_classes .= ' is-transparent-header';
				$opacity = get_option( 'va_header_opacity', '80' );
				$opacity = max( 0, min( 100, intval( $opacity ) ) );
				$opacity_decimal = $opacity / 100;
				$header_style = ' style="background: rgba(var(--va-brand-rgb), ' . esc_attr( $opacity_decimal ) . ');"';
			}
		?>
		<header id="avalon-nordic-header" class="<?php echo esc_attr( $header_classes ); ?>"<?php echo $header_style; ?>>
		<div class="header-container">
			<!-- Left: Logo -->
			<div class="header-logo">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="logo-link">
					<?php if ( $logo_url ) : ?>
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php bloginfo( 'name' ); ?>" class="custom-logo" />
					<?php else : ?>
						<span class="logo-text"><?php bloginfo( 'name' ); ?></span>
					<?php endif; ?>
				</a>
			</div>

			<!-- Center: Navigation Menu (Desktop) -->
			<nav class="header-nav">
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'main-menu',
					'fallback_cb'    => false,
					'depth'          => 3,
					'walker'         => new VA_Menu_Walker(),
				) );
				?>
			</nav>

			<!-- Right: Language, Search, CTA Button, Cart (Desktop) -->
			<div class="header-right">
				<?php
				if ( $show_social_icons ) {
					va_render_header_social_icons( 'header-social-icons' );
				}

				if ( $show_language_switcher && ( function_exists( 'icl_get_languages' ) || function_exists( 'apply_filters' ) ) ) {
					$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
					if ( ! empty( $languages ) && count( $languages ) > 1 ) {
						$current_language = apply_filters( 'wpml_current_language', NULL );
				?>
						<div class="language-switcher">
							<select id="wpml-language-switcher" onchange="if(this.value) window.location.href=this.value;">
								<?php foreach ( $languages as $code => $language ) :
									$url = apply_filters( 'wpml_permalink', $language['url'], $code );
									if ( ! $url ) {
										$url = $language['url'];
									}
								?>
									<option value="<?php echo esc_url( $url ); ?>" <?php selected( $current_language, $code ); ?>>
										<?php echo esc_html( $language['native_name'] ); ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
				<?php
					}
				}
				?>

				<?php if ( $show_search ) : ?>
				<!-- Search Toggle -->
				<div class="header-search">
					<button class="header-search-toggle" aria-label="<?php esc_attr_e( 'Avaa haku', 'vestelli-avalon' ); ?>" aria-expanded="false" aria-controls="header-search-dropdown">
						<svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
							<circle cx="11" cy="11" r="7" stroke="white" stroke-width="2"/>
							<path d="M20 20L16.65 16.65" stroke="white" stroke-width="2" stroke-linecap="round"/>
						</svg>
					</button>
					<div id="header-search-dropdown" class="header-search-dropdown" aria-hidden="true">
						<form role="search" method="get" class="header-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
							<label for="header-search-input" class="screen-reader-text"><?php esc_html_e( 'Hae', 'vestelli-avalon' ); ?></label>
							<input id="header-search-input" type="search" name="s" class="header-search-input" placeholder="<?php esc_attr_e( 'Hae...', 'vestelli-avalon' ); ?>" />
							<button type="submit" class="header-search-submit"><?php esc_html_e( 'Hae', 'vestelli-avalon' ); ?></button>
						</form>
					</div>
				</div>
				<?php endif; ?>

				<?php if ( $show_cta ) : ?>
					<a href="<?php echo esc_url( $cta_link ); ?>" class="cta-button">
						<?php echo esc_html( $cta_text ); ?>
					</a>
				<?php endif; ?>

				<?php if ( $show_cart && class_exists( 'WooCommerce' ) ) : ?>
					<div class="header-cart">
						<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="cart-link">
							<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
								<path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V17C17 18.1 17.9 19 19 19C20.1 19 21 18.1 21 17V13M9 19.5C9.8 19.5 10.5 20.2 10.5 21C10.5 21.8 9.8 22.5 9 22.5C8.2 22.5 7.5 21.8 7.5 21C7.5 20.2 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21C21.5 21.8 20.8 22.5 20 22.5C19.2 22.5 18.5 21.8 18.5 21C18.5 20.2 19.2 19.5 20 19.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
							</svg>
							<?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
								<span class="cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
							<?php endif; ?>
						</a>
					</div>
				<?php endif; ?>
			</div>

			<!-- Hamburger Menu Button (Mobile) -->
			<button class="mobile-menu-toggle" aria-label="<?php _e( 'Toggle menu', 'vestelli-avalon' ); ?>" aria-expanded="false">
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
				<span class="hamburger-line"></span>
			</button>
		</div>
		<?php endif; // End header design conditional ?>

		<!-- Mobile Menu (shared between both designs) -->
		<div class="mobile-menu-overlay">
			<nav class="mobile-menu">
				<div class="mobile-menu-header">
					<button class="mobile-menu-close" aria-label="<?php _e( 'Close menu', 'vestelli-avalon' ); ?>">
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
							<path d="M18 6L6 18M6 6L18 18" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
						</svg>
					</button>
				</div>
				<?php
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'mobile-main-menu',
					'fallback_cb'    => false,
					'depth'          => 3,
					'walker'         => new VA_Menu_Walker(),
				) );
				?>

				<div class="mobile-menu-footer">
					<?php
					if ( $show_language_switcher && ( function_exists( 'icl_get_languages' ) || function_exists( 'apply_filters' ) ) ) {
						$languages = apply_filters( 'wpml_active_languages', NULL, array( 'skip_missing' => 0 ) );
						if ( ! empty( $languages ) && count( $languages ) > 1 ) {
							$current_language = apply_filters( 'wpml_current_language', NULL );
					?>
							<div class="mobile-language-switcher">
								<?php foreach ( $languages as $code => $language ) :
									$url = apply_filters( 'wpml_permalink', $language['url'], $code );
									if ( ! $url ) {
										$url = $language['url'];
									}
									$is_current = ( $current_language === $code );
								?>
									<a href="<?php echo esc_url( $url ); ?>" class="<?php echo $is_current ? 'current-lang' : ''; ?>">
										<?php echo esc_html( strtoupper( $code ) ); ?>
									</a>
								<?php endforeach; ?>
							</div>
					<?php
						}
					}
					?>

					<?php if ( $show_cta ) : ?>
						<a href="<?php echo esc_url( $cta_link ); ?>" class="mobile-cta-button">
							<?php echo esc_html( $cta_text ); ?>
						</a>
					<?php endif; ?>

					<?php if ( $show_cart && class_exists( 'WooCommerce' ) ) : ?>
						<div class="mobile-header-cart">
							<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="mobile-cart-link">
								<svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
									<path d="M3 3H5L5.4 5M7 13H17L21 5H5.4M7 13L5.4 5M7 13L4.7 15.3C4.3 15.7 4.6 16.5 5.1 16.5H17M17 13V17C17 18.1 17.9 19 19 19C20.1 19 21 18.1 21 17V13M9 19.5C9.8 19.5 10.5 20.2 10.5 21C10.5 21.8 9.8 22.5 9 22.5C8.2 22.5 7.5 21.8 7.5 21C7.5 20.2 8.2 19.5 9 19.5ZM20 19.5C20.8 19.5 21.5 20.2 21.5 21C21.5 21.8 20.8 22.5 20 22.5C19.2 22.5 18.5 21.8 18.5 21C18.5 20.2 19.2 19.5 20 19.5Z" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
								</svg>
								<?php if ( WC()->cart->get_cart_contents_count() > 0 ) : ?>
									<span class="mobile-cart-count"><?php echo WC()->cart->get_cart_contents_count(); ?></span>
								<?php endif; ?>
							</a>
						</div>
					<?php endif; ?>
				</div>
			</nav>
		</div>
		</header>
		<?php if ( $header_design === 'vestelli' && $show_search ) : ?>
			<script>
			(function() {
				var toggleBtn = document.querySelector('.vestelli-search-toggle');
				var overlay = document.getElementById('vestelli-search-overlay');
				if (!toggleBtn || !overlay) {
					return;
				}
				var closeBtn = overlay.querySelector('.vestelli-search-overlay-close');
				var backdrop = overlay.querySelector('.vestelli-search-overlay-backdrop');
				var searchInput = overlay.querySelector('input[type="search"]');

				function openSearch() {
					overlay.classList.add('is-open');
					overlay.setAttribute('aria-hidden', 'false');
					toggleBtn.setAttribute('aria-expanded', 'true');
					if (searchInput) {
						searchInput.focus();
					}
				}

				function closeSearch() {
					overlay.classList.remove('is-open');
					overlay.setAttribute('aria-hidden', 'true');
					toggleBtn.setAttribute('aria-expanded', 'false');
				}

				toggleBtn.addEventListener('click', function() {
					if (overlay.classList.contains('is-open')) {
						closeSearch();
					} else {
						openSearch();
					}
				});

				if (closeBtn) {
					closeBtn.addEventListener('click', closeSearch);
				}
				if (backdrop) {
					backdrop.addEventListener('click', closeSearch);
				}
				document.addEventListener('keydown', function(event) {
					if (event.key === 'Escape' && overlay.classList.contains('is-open')) {
						closeSearch();
					}
				});
			})();
			</script>
		<?php endif; ?>
		<?php
		endif; // End custom header
	} // End else (when Themer header not set)
	?>

	<?php
	do_action( 'fl_before_content' );
	?>
	<div id="fl-main-content" class="fl-page-content" itemprop="mainContentOfPage" role="main">
		<?php do_action( 'fl_content_open' ); ?>
