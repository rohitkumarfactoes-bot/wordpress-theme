<?php
/**
 * The template for displaying single posts
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main single-post">
    <div class="container">
        
        <?php while (have_posts()) : the_post(); ?>
        
        <article id="post-<?php the_ID(); ?>" <?php post_class('post-single'); ?>>
            
            <!-- Post Header -->
            <header class="entry-header">
                <?php the_title('<h1 class="entry-title">', '</h1>'); ?>
                <?php gizmodotech_post_meta(); ?>
            </header>

            <!-- Featured Image -->
            <?php if (has_post_thumbnail()) : ?>
            <div class="featured-image-single">
                <?php the_post_thumbnail('gizmodotech-featured'); ?>
            </div>
            <?php endif; ?>

            <!-- Post Content -->
            <div class="entry-content">
                <?php the_content(); ?>
                
                <?php
                wp_link_pages(array(
                    'before' => '<div class="page-links">' . esc_html__('Pages:', 'gizmodotech'),
                    'after'  => '</div>',
                ));
                ?>
            </div>

            <!-- Post Footer (Tags, Share, etc.) -->
            <footer class="entry-footer">
                <?php
                if (has_tag()) {
                    echo '<div class="post-tags">';
                    the_tags('<span class="tags-label">' . esc_html__('Tags:', 'gizmodotech') . '</span> ', ', ');
                    echo '</div>';
                }
                ?>
            </footer>

        </article>

        <!-- Comments -->
        <?php
        if (comments_open() || get_comments_number()) {
            comments_template();
        }
        ?>
        
        <?php endwhile; ?>
        
    </div>
</main>

<?php get_footer(); ?>
