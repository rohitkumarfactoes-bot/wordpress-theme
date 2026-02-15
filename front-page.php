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
                <?php if (is_active_sidebar('homepage-widgets')) : ?>
                    <div class="homepage-widgets-area">
                        <?php dynamic_sidebar('homepage-widgets'); ?>
                    </div>
                <?php else : ?>
                    <div class="homepage-placeholder" style="text-align: center; padding: 4rem 0;">
                        <h2><?php esc_html_e('Welcome to Gizmodotech', 'gizmodotech'); ?></h2>
                        <p><?php esc_html_e('Please go to Appearance > Widgets and add widgets to the "Homepage Widgets" area.', 'gizmodotech'); ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();