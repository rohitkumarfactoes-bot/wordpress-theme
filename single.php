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
                    get_template_part('template-parts/content', 'single');

                    // Post navigation
                    $prev_post = get_previous_post();
                    $next_post = get_next_post();

                    if ($prev_post || $next_post) : ?>
                        <nav class="post-navigation-modern" aria-label="<?php esc_attr_e('Posts', 'gizmodotech'); ?>">
                            <div class="nav-links-modern">
                                <?php if ($prev_post) : ?>
                                    <a href="<?php echo esc_url(get_permalink($prev_post->ID)); ?>" class="nav-previous">
                                        <div class="nav-content">
                                            <span class="nav-label">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                                                <?php esc_html_e('Previous Article', 'gizmodotech'); ?>
                                            </span>
                                            <h4 class="nav-title"><?php echo get_the_title($prev_post->ID); ?></h4>
                                        </div>
                                        <?php if (has_post_thumbnail($prev_post->ID)) : ?>
                                            <div class="nav-image">
                                                <?php echo get_the_post_thumbnail($prev_post->ID, 'thumbnail'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>

                                <?php if ($next_post) : ?>
                                    <a href="<?php echo esc_url(get_permalink($next_post->ID)); ?>" class="nav-next">
                                        <div class="nav-content">
                                            <span class="nav-label">
                                                <?php esc_html_e('Next Article', 'gizmodotech'); ?>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                                            </span>
                                            <h4 class="nav-title"><?php echo get_the_title($next_post->ID); ?></h4>
                                        </div>
                                        <?php if (has_post_thumbnail($next_post->ID)) : ?>
                                            <div class="nav-image">
                                                <?php echo get_the_post_thumbnail($next_post->ID, 'thumbnail'); ?>
                                            </div>
                                        <?php endif; ?>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </nav>
                    <?php endif;

                    // Comments
                    if (comments_open() || get_comments_number()) :
                        comments_template();
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
