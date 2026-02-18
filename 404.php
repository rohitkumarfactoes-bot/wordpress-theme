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
        <section class="error-404 not-found">
            <header class="page-header">
                <h1 class="page-title"><?php esc_html_e('404', 'gizmodotech'); ?></h1>
                <p class="error-subtitle"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'gizmodotech'); ?></p>
            </header>

            <div class="page-content">
                <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'gizmodotech'); ?></p>
                <?php get_search_form(); ?>
            </div>
        </section>
    </div>
</main>

<?php
get_footer();