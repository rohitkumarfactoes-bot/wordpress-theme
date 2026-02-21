<?php
/**
 * The template for displaying single posts
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content site-content--single">
    <div class="container">
        <div class="content-area has-sidebar">
            <div class="main-content single-main">
                <?php
                while (have_posts()) :
                    the_post();
                    
                    // --- Added: Reading Time & Meta ---
                    ?>
                    <div class="post-meta-header flex items-center gap-4 text-sm text-text-light mb-6">
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <?php echo esc_html(gizmodotech_get_reading_time()); ?>
                        </span>
                        <span class="flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            <?php echo get_the_date(); ?>
                        </span>
                    </div>
                    <?php

                    get_template_part('template-parts/content', 'single');

                    // --- Added: Social Share ---
                    ?>
                    <div class="social-share my-8 p-6 bg-bg-alt rounded-xl border border-border">
                        <h4 class="font-heading font-bold mb-4 text-lg"><?php esc_html_e('Share this article', 'gizmodotech'); ?></h4>
                        <div class="flex gap-3 flex-wrap">
                            <a href="https://twitter.com/intent/tweet?text=<?php echo urlencode(get_the_title()); ?>&url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-black text-white rounded-lg hover:opacity-80 transition-opacity text-sm font-medium">X (Twitter)</a>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:opacity-80 transition-opacity text-sm font-medium">Facebook</a>
                            <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="px-4 py-2 bg-blue-700 text-white rounded-lg hover:opacity-80 transition-opacity text-sm font-medium">LinkedIn</a>
                            <a href="mailto:?subject=<?php echo urlencode(get_the_title()); ?>&body=<?php echo urlencode(get_permalink()); ?>" class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:opacity-80 transition-opacity text-sm font-medium">Email</a>
                        </div>
                    </div>
                    <?php

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
