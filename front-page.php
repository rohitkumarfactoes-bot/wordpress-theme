<?php
/**
 * The template for displaying the homepage
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content">
    <div class="container">
        <div class="content-area">
            <div class="main-content">
                <?php
                // 1. Display Page Content (Blocks) if a static page is set
                if (have_posts() && is_page()) {
                    while (have_posts()) {
                        the_post();
                        the_content();
                    }
                }

                // 2. Display Homepage Widgets (Legacy or Block Widgets)
                if (is_active_sidebar('homepage-widgets')) : ?>
                    <div class="homepage-widgets-area">
                        <?php dynamic_sidebar('homepage-widgets'); ?>
                    </div>
                <?php elseif (!have_posts()) : ?>
                    // 3. Placeholder if nothing is configured
                    <div class="homepage-placeholder" style="text-align: center; padding: 4rem 0;">
                        <h2><?php esc_html_e('Welcome to Gizmodotech', 'gizmodotech'); ?></h2>
                        <p><?php esc_html_e('Set a static homepage in Settings > Reading and design it with Blocks, or add widgets to "Homepage Widgets".', 'gizmodotech'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();