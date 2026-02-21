<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content">
    <div class="container">
        <section class="error-404 not-found error-404-content">
            <header class="page-header mb-8">
                <h1 class="page-title error-code">404</h1>
                <h2 class="error-title"><?php esc_html_e('Page Not Found', 'gizmodotech'); ?></h2>
                <p class="error-subtitle error-text"><?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'gizmodotech'); ?></p>
            </header>

            <div class="page-content max-w-lg mx-auto">
                <p class="mb-6 text-text-light"><?php esc_html_e('Try searching for what you need:', 'gizmodotech'); ?></p>
                <?php get_search_form(); ?>
                <div class="mt-8">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="button">
                        <?php esc_html_e('Back to Homepage', 'gizmodotech'); ?>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();