<?php
/**
 * Gizmodotech Pro — Header
 * Modern sticky navbar: logo left, dark mode + search right.
 *
 * @package gizmodotech-pro
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?> data-theme="light">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<!-- Reading Progress Bar -->
<?php if ( is_single() ) : ?>
<div class="progress-bar" role="progressbar" aria-label="<?php esc_attr_e( 'Reading progress', 'gizmodotech-pro' ); ?>" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
<?php endif; ?>

<!-- Skip Link -->
<a class="skip-link" href="#main"><?php esc_html_e( 'Skip to content', 'gizmodotech-pro' ); ?></a>

<!-- ============================================================
     SITE HEADER
     ============================================================ -->
<header class="site-header" id="masthead" role="banner">
	<div class="site-header__inner">

		<!-- Logo -->
		<a class="site-header__logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home" aria-label="<?php bloginfo( 'name' ); ?> — <?php esc_html_e( 'Home', 'gizmodotech-pro' ); ?>">
			<?php
			if ( has_custom_logo() ) {
				the_custom_logo();
			} else {
				$site_name  = get_bloginfo( 'name' );
				$name_parts = explode( ' ', trim( $site_name ), 2 );
				if ( count( $name_parts ) === 2 ) {
					echo esc_html( $name_parts[0] ) . '<span>' . esc_html( $name_parts[1] ) . '</span>';
				} else {
					echo '<span>' . esc_html( $site_name ) . '</span>';
				}
			}
			?>
		</a>

		<!-- Primary Navigation -->
		<nav class="site-nav" id="primary-nav" aria-label="<?php esc_attr_e( 'Primary Navigation', 'gizmodotech-pro' ); ?>">
			<?php
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( [
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'site-nav__list',
					'menu_id'        => 'primary-menu',
					'link_before'    => '',
					'link_after'     => '',
					'item_spacing'   => 'discard',
					'depth'          => 2,
					'walker'         => class_exists( 'Gizmo_Nav_Walker' ) ? new Gizmo_Nav_Walker() : null,
					'fallback_cb'    => false,
				] );
			} else {
				/* Show placeholder links in editor / when no menu is set */
				$quick_links = [
					__( 'Smartphones', 'gizmodotech-pro' ) => '#',
					__( 'Laptops', 'gizmodotech-pro' )     => '#',
					__( 'Reviews', 'gizmodotech-pro' )      => '#',
					__( 'Deals', 'gizmodotech-pro' )        => '#',
					__( 'How-To', 'gizmodotech-pro' )       => '#',
				];
				echo '<ul class="site-nav__list">';
				foreach ( $quick_links as $label => $href ) {
					printf( '<li class="site-nav__item"><a href="%s" class="site-nav__link">%s</a></li>', esc_url( $href ), esc_html( $label ) );
				}
				echo '</ul>';
			}
			?>
		</nav>

		<!-- Header Actions -->
		<div class="site-header__actions">

			<!-- Search Toggle -->
			<button class="search-toggle" type="button"
			        aria-label="<?php esc_attr_e( 'Open Search', 'gizmodotech-pro' ); ?>"
			        aria-expanded="false"
			        aria-controls="search-overlay">
				<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24"
				     fill="none" stroke="currentColor" stroke-width="2.2"
				     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="11" cy="11" r="8"/>
					<path d="m21 21-4.35-4.35"/>
				</svg>
			</button>

			<!-- Dark Mode Toggle -->
			<button class="dark-mode-toggle" type="button"
			        id="dark-mode-toggle"
			        aria-label="<?php esc_attr_e( 'Toggle Dark Mode', 'gizmodotech-pro' ); ?>"
			        aria-pressed="false">
				<!-- Sun icon (light mode) -->
				<svg class="icon-sun" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
				     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<circle cx="12" cy="12" r="5"/>
					<line x1="12" y1="1"  x2="12" y2="3"/>
					<line x1="12" y1="21" x2="12" y2="23"/>
					<line x1="4.22" y1="4.22"  x2="5.64" y2="5.64"/>
					<line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
					<line x1="1" y1="12" x2="3"  y2="12"/>
					<line x1="21" y1="12" x2="23" y2="12"/>
					<line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
					<line x1="18.36" y1="5.64"  x2="19.78" y2="4.22"/>
				</svg>
				<!-- Moon icon (dark mode) -->
				<svg class="icon-moon" xmlns="http://www.w3.org/2000/svg" width="18" height="18"
				     viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
				     stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
				</svg>
			</button>

			<!-- Hamburger (mobile) -->
			<button class="hamburger" type="button" id="hamburger"
			        aria-label="<?php esc_attr_e( 'Open Menu', 'gizmodotech-pro' ); ?>"
			        aria-expanded="false"
			        aria-controls="mobile-nav">
				<span></span>
				<span></span>
				<span></span>
			</button>

		</div><!-- /.site-header__actions -->

	</div><!-- /.site-header__inner -->
</header><!-- /#masthead -->

<!-- ============================================================
     MOBILE NAV
     ============================================================ -->
<nav class="mobile-nav" id="mobile-nav" aria-label="<?php esc_attr_e( 'Mobile Navigation', 'gizmodotech-pro' ); ?>" aria-hidden="true">
	<?php
	wp_nav_menu( [
		'theme_location' => 'primary',
		'container'      => false,
		'menu_class'     => 'mobile-nav__list',
		'link_class'     => 'mobile-nav__link',
		'fallback_cb'    => false,
		'depth'          => 1,
	] );
	?>
</nav>

<!-- ============================================================
     SEARCH OVERLAY
     ============================================================ -->
<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'Search', 'gizmodotech-pro' ); ?>" aria-hidden="true">
	<div class="search-overlay__box">
		<form role="search" method="get" class="search-overlay__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="screen-reader-text" for="search-overlay-input"><?php esc_html_e( 'Search for:', 'gizmodotech-pro' ); ?></label>
			<input
				type="search"
				id="search-overlay-input"
				class="search-overlay__input"
				name="s"
				placeholder="<?php esc_attr_e( 'Search phones, laptops, reviews…', 'gizmodotech-pro' ); ?>"
				value="<?php echo esc_attr( get_search_query() ); ?>"
				autocomplete="off"
			>
			<button type="button" class="search-overlay__close" aria-label="<?php esc_attr_e( 'Close Search', 'gizmodotech-pro' ); ?>">
				<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
				     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
					<line x1="18" y1="6" x2="6" y2="18"/>
					<line x1="6" y1="6" x2="18" y2="18"/>
				</svg>
			</button>
		</form>
		<p style="padding: 0 1.5rem 1rem; font-size: .8rem; color: var(--text-muted);">
			<?php esc_html_e( 'Press ↵ Enter to search · Esc to close', 'gizmodotech-pro' ); ?>
		</p>
	</div>
</div>

<!-- ============================================================
     PAGE WRAPPER
     ============================================================ -->
<div class="site-wrapper">
	<main id="main" class="site-main" role="main">
