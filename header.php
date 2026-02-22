<?php
/**
 * Gizmodotech Pro — Header
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

<div id="page" class="site-wrapper"> <div class="progress-bar" id="reading-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'gizmodotech-pro' ); ?></a>

<header class="site-header" id="masthead" role="banner">

	<div class="header-top">
		<div class="header-top__inner">
			<div class = "logo-wrap">
				
			
			<a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
				<?php if ( has_custom_logo() ) : the_custom_logo(); else : ?>
				<svg class="logo-icon" width="34" height="34" viewBox="0 0 40 40" fill="none" aria-hidden="true">
					<path d="M20 3L35 11.5V28.5L20 37L5 28.5V11.5L20 3Z" stroke="#2563EB" stroke-width="2.2" fill="none"/>
					<path d="M20 9L29 14.5V25.5L20 31L11 25.5V14.5L20 9Z" fill="#2563EB" opacity=".15"/>
					<path d="M14.5 17L20 13.8L25.5 17V23L20 26.2L14.5 23V17Z" fill="#2563EB"/>
				</svg>
				<span class="logo-text">
					<?php
					$n = get_bloginfo('name');
					if ( preg_match('/^(.+?)([A-Z][a-z]+)$/', $n, $m) ) {
						echo esc_html($m[1]) . '<em>' . esc_html($m[2]) . '</em>';
					} else {
						echo '<em>' . esc_html($n) . '</em>';
					}
					?>
				</span>
				<?php endif; ?>
			</a>
</div>
			<div class="header-actions">
				<button class="hdr-btn search-toggle" type="button" aria-label="<?php esc_attr_e('Search','gizmodotech-pro'); ?>" aria-expanded="false" aria-controls="search-overlay">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
				</button>

				<button class="hdr-btn dark-mode-toggle" id="dark-mode-toggle" type="button" aria-label="<?php esc_attr_e('Toggle dark mode','gizmodotech-pro'); ?>" aria-pressed="false">
					<svg class="icon-sun" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="5"/><line x1="12" y1="1" x2="12" y2="3"/><line x1="12" y1="21" x2="12" y2="23"/><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/><line x1="1" y1="12" x2="3" y2="12"/><line x1="21" y1="12" x2="23" y2="12"/><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/></svg>
					<svg class="icon-moon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/></svg>
				</button>

				<button class="hamburger" id="hamburger" type="button" aria-label="<?php esc_attr_e('Open menu','gizmodotech-pro'); ?>" aria-expanded="false" aria-controls="mobile-nav">
					<span></span><span></span><span></span>
				</button>
			</div>

		</div>
	</div>

	<div class="header-nav-strip">
		<div class="header-nav-strip__inner">
			<nav class="site-nav" id="primary-nav" aria-label="<?php esc_attr_e('Primary Navigation','gizmodotech-pro'); ?>">
				<?php
				wp_nav_menu([
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => 'nav-list',
					'menu_id'        => 'primary-menu',
					'depth'          => 2,
					'fallback_cb'    => false,
				]);
				?>
			</nav>
		</div>
	</div>

</header>

<div class="mobile-overlay" id="mobile-overlay" aria-hidden="true"></div>
<nav class="mobile-nav" id="mobile-nav" aria-hidden="true">
    </nav>

<main id="main" class="site-main" role="main">