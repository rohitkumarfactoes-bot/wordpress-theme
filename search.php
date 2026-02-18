<?php
/**
 * The template for displaying search results pages
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content">
    <div class="container">
        <div class="content-area">

            <header class="page-header">
                <h1 class="page-title">
                    <?php
                    printf(esc_html__('Search Results for: %s', 'gizmodotech'), '<span>' . get_search_query() . '</span>');
                    ?>
                </h1>
            </header>

            <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php
                    while (have_posts()) :
                        the_post();
                        get_template_part('template-parts/content', get_post_type());
                    endwhile;
                    ?>
                </div>
                <?php gizmodotech_pagination(); ?>
            <?php else : ?>
                <?php get_template_part('template-parts/content', 'none'); ?>
            <?php endif; ?>

        </div>
    </div>
</main>

<?php
get_footer();