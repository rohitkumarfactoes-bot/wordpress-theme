<?php
/**
 * Gizmodotech Pro — Header
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

<div id="page" class="site-wrapper">
<?php if ( is_single() ) : ?>
    <div class="progress-bar" id="reading-progress" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" aria-label="<?php esc_attr_e( 'Reading progress', 'gizmodotech-pro' ); ?>"></div>
<?php endif; ?>
<a class="skip-link screen-reader-text" href="#main"><?php esc_html_e( 'Skip to content', 'gizmodotech-pro' ); ?></a>

<header class="site-header" id="masthead" role="banner">

    <div class="header-top">
        <div class="header-top__inner">
            <div class="logo-wrap">
                <?php if ( has_custom_logo() ) : ?>
                    <?php the_custom_logo(); ?>
                <?php else : ?>
                    <a class="site-logo" href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home">
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
                    </a>
                <?php endif; ?>
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
                wp_nav_menu( array(
                    'theme_location' => 'primary',
                    'container'      => false,
                    'menu_class'     => 'nav-list',
                    'menu_id'        => 'primary-menu',
                    'depth'          => 2,
                    'fallback_cb'    => false,
                ) );
                ?>
            </nav>
        </div>
    </div>
</header>

<div class="search-overlay" id="search-overlay" role="dialog" aria-modal="true" aria-label="Search" aria-hidden="true">
    <div class="search-overlay__box">
        <form role="search" method="get" class="search-overlay__form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
            <label class="screen-reader-text" for="search-overlay-input"><?php esc_html_e( 'Search for:', 'gizmodotech-pro' ); ?></label>
            <input type="search" id="search-overlay-input" class="search-overlay__input" name="s" placeholder="<?php esc_attr_e( 'Search phones, laptops, reviews…', 'gizmodotech-pro' ); ?>" value="<?php echo get_search_query(); ?>" autocomplete="off">
            <button type="button" class="search-overlay__close" aria-label="<?php esc_attr_e( 'Close Search', 'gizmodotech-pro' ); ?>">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </form>
        <p style="padding: 0 1.5rem 1rem; font-size: .8rem; color: var(--text-muted);">
            <?php esc_html_e( 'Press ↵ Enter to search · Esc to close', 'gizmodotech-pro' ); ?>
        </p>
    </div>
</div>

<div class="mobile-overlay" id="mobile-overlay" aria-hidden="true"></div>
<nav class="mobile-nav" id="mobile-nav" aria-hidden="true">
    <?php
    // Set up arguments for the mobile menu.
    $mobile_menu_args = array(
        'theme_location' => 'mobile',
        'container'      => false,
        'menu_class'     => 'mobile-nav-list',
        'fallback_cb'    => false,
    );

    // If no menu is assigned to the 'mobile' location, fall back to 'primary'.
    // This makes the theme more user-friendly.
    if ( ! has_nav_menu( 'mobile' ) ) {
        $mobile_menu_args['theme_location'] = 'primary';
    }

    wp_nav_menu( $mobile_menu_args );
    ?>
</nav>

<main id="main" class="site-main" role="main">