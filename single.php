<?php
/**
 * The template for displaying single posts
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
                while (have_posts()) :
                    the_post();
                    
                    // Display standard post meta (Category, Date)
                    gizmodotech_post_meta();

                    get_template_part('template-parts/content', 'single');

                    // Social Share Buttons
                    gizmodotech_the_social_share_buttons();

                    // Modern Post Navigation
                    gizmodotech_the_post_navigation();

                    // Comments Toggle
                    if ( comments_open() || get_comments_number() ) :
                    ?>
                        <button id="toggle-comments-button" class="button">
                            <?php esc_html_e( 'Leave a Comment', 'gizmodotech' ); ?>
                        </button>
                        <div id="comments-wrapper" class="comments-wrapper comments-hidden">
                            <?php comments_template(); ?>
                        </div>
                    <?php
                    endif;
                    
                    // Related Posts
                    get_template_part('template-parts/related-posts');

                endwhile;
                ?>
            </div>

            <?php if (is_active_sidebar('sidebar-1')) : ?>
                <aside class="sidebar">
                    <?php dynamic_sidebar('sidebar-1'); ?>
                </aside>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
get_footer();
