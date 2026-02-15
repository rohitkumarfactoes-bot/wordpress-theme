<?php
/**
 * The header for our theme
 *
 * @package Gizmodotech
 * @since 1.0.0
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div id="page" class="site">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Skip to content', 'gizmodotech'); ?></a>

    <!-- Top Banner -->
    <div class="top-banner">
        <div class="container">
            <p>🚀 <?php esc_html_e('Explore the latest tech trends and gadget reviews', 'gizmodotech'); ?></p>
        </div>
    </div>

    <!-- Header -->
    <header id="masthead" class="site-header">
        <nav class="main-navigation">
            <div class="container">
                <div class="nav-wrapper">
                    
                    <!-- Logo -->
                    <div class="site-branding">
                        <?php
                        if (has_custom_logo()) {
                            the_custom_logo();
                        } else {
                            ?>
                            <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo-text">
                                <h1 class="site-title gradient-text">
                                    <?php bloginfo('name'); ?>
                                </h1>
                                <?php
                                $description = get_bloginfo('description', 'display');
                                if ($description || is_customize_preview()) :
                                ?>
                                    <p class="site-description"><?php echo $description; ?></p>
                                <?php endif; ?>
                            </a>
                            <?php
                        }
                        ?>
                    </div>

                    <!-- Desktop Navigation -->
                    <div class="main-menu hide-mobile">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'primary',
                            'menu_class'     => 'nav-menu',
                            'container'      => false,
                            'fallback_cb'    => 'gizmodotech_default_menu',
                        ));
                        ?>
                    </div>

                    <!-- Right Actions -->
                    <div class="header-actions">
                        <!-- Search Button -->
                        <button id="search-toggle" class="btn-icon" aria-label="<?php esc_attr_e('Toggle search', 'gizmodotech'); ?>">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                            </svg>
                        </button>

                        <!-- Dark Mode Toggle -->
                        <?php if (get_theme_mod('dark_mode_enabled', false)) : ?>
                        <button id="dark-mode-toggle" class="btn-icon" aria-label="<?php esc_attr_e('Toggle dark mode', 'gizmodotech'); ?>">
                            <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <svg class="moon-icon hidden" width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M21 12.79A9 9 0 1111.21 3 7 7 0 0021 12.79z"/>
                            </svg>
                        </button>
                        <?php endif; ?>

                        <!-- Mobile Menu Toggle -->
                        <button id="mobile-menu-toggle" class="btn-icon hide-desktop" aria-label="<?php esc_attr_e('Toggle menu', 'gizmodotech'); ?>">
                            <svg class="menu-icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="3" y1="12" x2="21" y2="12"></line>
                                <line x1="3" y1="6" x2="21" y2="6"></line>
                                <line x1="3" y1="18" x2="21" y2="18"></line>
                            </svg>
                            <svg class="close-icon hidden" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg>
                        </button>
                    </div>

                </div>
            </div>
        </nav>

        <!-- Search Overlay -->
        <div id="search-overlay" class="search-overlay hidden">
            <div class="container">
                <div class="search-container">
                    <?php get_search_form(); ?>
                    <button id="search-close" class="btn-icon">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <line x1="18" y1="6" x2="6" y2="18"></line>
                            <line x1="6" y1="6" x2="18" y2="18"></line>
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div id="mobile-menu" class="mobile-menu hidden">
            <div class="mobile-menu-content">
                <!-- Mobile Search -->
                <div class="mobile-search">
                    <?php get_search_form(); ?>
                </div>

                <!-- Mobile Navigation -->
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'menu_class'     => 'mobile-nav-menu',
                    'container'      => false,
                    'fallback_cb'    => 'gizmodotech_default_menu',
                ));
                ?>

                <!-- Social Links in Mobile Menu -->
                <?php if (gizmodotech_has_social_links()) : ?>
                <div class="mobile-social">
                    <?php gizmodotech_social_links(); ?>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </header><!-- #masthead -->
