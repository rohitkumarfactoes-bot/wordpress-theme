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
        <div class="content-area">
            <div class="main-content">
                <section class="error-404 not-found">
                    <header class="page-header">
                        <h1 class="page-title"><?php esc_html_e('Oops! That page can&rsquo;t be found.', 'gizmodotech'); ?></h1>
                    </header>

                    <div class="page-content">
                        <p><?php esc_html_e('It looks like nothing was found at this location. Maybe try a search?', 'gizmodotech'); ?></p>

                        <?php get_search_form(); ?>

                        <div class="error-404-widgets" style="margin-top: 2rem;">
                            <div class="widget-area" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 2rem;">
                                <div class="widget">
                                    <h2 class="widget-title"><?php esc_html_e('Recent Posts', 'gizmodotech'); ?></h2>
                                    <ul>
                                        <?php
                                        wp_list_pages(array(
                                            'title_li' => '',
                                            'number'   => 5,
                                        ));
                                        ?>
                                    </ul>
                                </div>

                                <div class="widget">
                                    <h2 class="widget-title"><?php esc_html_e('Categories', 'gizmodotech'); ?></h2>
                                    <ul>
                                        <?php
                                        wp_list_categories(array(
                                            'orderby'    => 'count',
                                            'order'      => 'DESC',
                                            'show_count' => 1,
                                            'title_li'   => '',
                                            'number'     => 10,
                                        ));
                                        ?>
                                    </ul>
                                </div>

                                <div class="widget">
                                    <h2 class="widget-title"><?php esc_html_e('Archives', 'gizmodotech'); ?></h2>
                                    <ul>
                                        <?php
                                        wp_get_archives(array(
                                            'type'  => 'monthly',
                                            'limit' => 12,
                                        ));
                                        ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();
