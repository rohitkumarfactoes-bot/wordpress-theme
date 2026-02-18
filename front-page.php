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
            <?php
            // --- Featured Post Section ---
            $featured_args = array(
                'posts_per_page' => 1,
                'ignore_sticky_posts' => 1,
                'tag' => 'featured', // Example: You can tag a post as "featured"
            );
            $featured_query = new WP_Query($featured_args);

            if ($featured_query->have_posts()) :
                while ($featured_query->have_posts()) : $featured_query->the_post();
                    // You can create a new template part for a more dramatic featured post
                    get_template_part('template-parts/content', 'featured');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>

            <div class="main-content-grid">
                <div class="main-column">
                    <h2 class="section-title"><?php esc_html_e('Latest Updates', 'gizmodotech'); ?></h2>
                    <?php
                    // --- Latest Posts Section ---
                    if (have_posts()) : ?>
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

                <?php if (is_active_sidebar('sidebar-1')) : ?>
                    <aside class="sidebar">
                        <?php dynamic_sidebar('sidebar-1'); ?>
                    </aside>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php
get_footer();

/* Add this to style.css */
/*
.main-content-grid { display: grid; grid-template-columns: 1fr; gap: var(--spacing-2xl); }
@media (min-width: 1024px) {
    .main-content-grid { grid-template-columns: 1fr 320px; }
}
.section-title { margin-bottom: var(--spacing-lg); }
*/