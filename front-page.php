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

            // Fallback: If no post has 'featured' tag, get the latest post
            if ( ! $featured_query->have_posts() ) {
                $featured_args = array(
                    'posts_per_page' => 1,
                );
                $featured_query = new WP_Query($featured_args);
            }

            if ($featured_query->have_posts()) :
                while ($featured_query->have_posts()) : $featured_query->the_post();
                    // You can create a new template part for a more dramatic featured post
                    get_template_part('template-parts/content', 'featured');
                endwhile;
                wp_reset_postdata();
            endif;
            ?>

            <div class="content-area">
                <div class="main-content">
                    <section class="latest-posts-section">
                        <h2 class="section-title"><span><?php esc_html_e('Latest Updates', 'gizmodotech'); ?></span></h2>
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
                    </section>
                </div>

                <?php if (is_active_sidebar('sidebar-1')) : // This ensures the sidebar is part of the main grid ?>
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