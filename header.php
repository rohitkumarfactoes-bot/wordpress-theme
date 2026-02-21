<?php
/**
 * The header for our theme
 *
 * @package Gizmodotech
 */
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<div class="site-wrapper">
    <header class="site-header" id="masthead">
        <div class="container">
            <div class="header-inner">
                <div class="site-branding">
                    <?php
                    if (has_custom_logo()) {
                        the_custom_logo();
                    } else {
                        ?>
                        <h1 class="site-title">
                            <a href="<?php echo esc_url(home_url('/')); ?>" rel="home">
                                <?php bloginfo('name'); ?>
                            </a>
                        </h1>
                        <?php
                        $description = get_bloginfo('description', 'display');
                        if ($description || is_customize_preview()) :
                            ?>
                            <p class="site-description"><?php echo $description; ?></p>
                        <?php endif;
                    }
                    ?>
                </div>

                <div class="header-actions">
                    <button class="search-toggle" aria-label="<?php esc_attr_e('Search', 'gizmodotech'); ?>">
                        <i data-feather="search"></i>
                    </button>

                    <?php if (get_theme_mod('gizmodotech_enable_dark_mode', true)) : ?>
                        <button class="dark-mode-toggle" id="dark-mode-toggle" aria-label="<?php esc_attr_e('Toggle Dark Mode', 'gizmodotech'); ?>">
                            <i data-feather="sun" class="sun-icon"></i>
                            <i data-feather="moon" class="moon-icon"></i>
                        </button>
                    <?php endif; ?>

                    <button class="mobile-menu-toggle" aria-label="<?php esc_attr_e('Toggle Menu', 'gizmodotech'); ?>">
                        <i data-feather="menu"></i>
                    </button>
                </div>
            </div>

            <div class="header-navigation-bar">
                <nav class="main-navigation" id="site-navigation">
                    <?php
                    wp_nav_menu(array(
                        'theme_location' => 'primary',
                        'menu_id'        => 'primary-menu',
                        'menu_class'     => 'primary-menu',
                        'container'      => false,
                        'fallback_cb'    => false,
                    ));
                    ?>
                </nav>
            </div>
        </div>
    </header>

    <div class="search-overlay" id="search-overlay">
        <div class="search-overlay-inner">
            <button class="search-close" aria-label="<?php esc_attr_e('Close Search', 'gizmodotech'); ?>">
                <i data-feather="x"></i>
            </button>
            <?php get_search_form(); ?>
        </div>
    </div>
