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
        <section class="error-404 not-found text-center py-20 lg:py-32">
            <header class="page-header mb-8">
                <h1 class="page-title text-8xl font-bold text-primary mb-4">404</h1>
                <h2 class="text-3xl font-heading font-bold text-text mb-4"><?php esc_html_e('Page Not Found', 'gizmodotech'); ?></h2>
                <p class="error-subtitle text-lg text-text-light max-w-md mx-auto"><?php esc_html_e('The page you are looking for might have been removed, had its name changed, or is temporarily unavailable.', 'gizmodotech'); ?></p>
            </header>

            <div class="page-content max-w-lg mx-auto">
                <p class="mb-6 text-text-light"><?php esc_html_e('Try searching for what you need:', 'gizmodotech'); ?></p>
                <?php get_search_form(); ?>
                <div class="mt-8">
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="inline-block px-6 py-3 bg-primary text-white font-semibold rounded-lg hover:bg-primary-dark transition-colors">
                        <?php esc_html_e('Back to Homepage', 'gizmodotech'); ?>
                    </a>
                </div>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();