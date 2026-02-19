<?php
/**
 * Template part for displaying related posts
 *
 * @package Gizmodotech
 */

$categories = get_the_category();
if (!$categories) {
    return;
}

$category_ids = array();
foreach ($categories as $category) {
    $category_ids[] = $category->term_id;
}

$related_query = new WP_Query(array(
    'category__in'   => $category_ids,
    'post__not_in'   => array(get_the_ID()),
    'posts_per_page' => 3,
    'ignore_sticky_posts' => 1,
));

if ($related_query->have_posts()) : ?>
    <section class="related-posts-section">
        <h3 class="section-title"><?php esc_html_e('You Might Also Like', 'gizmodotech'); ?></h3>
        <div class="posts-grid">
            <?php while ($related_query->have_posts()) : $related_query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
                    <?php if (has_post_thumbnail()) : ?>
                        <div class="article-thumbnail">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('gizmodotech-medium'); ?>
                            </a>
                        </div>
                    <?php endif; ?>
                    <div class="article-content">
                        <h4 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
                        <div class="article-meta">
                            <span class="posted-on"><?php echo get_the_date(); ?></span>
                        </div>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
    </section>
    <?php wp_reset_postdata(); ?>
<?php endif; ?>