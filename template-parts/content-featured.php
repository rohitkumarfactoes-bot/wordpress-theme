<?php
/**
 * Template part for displaying the featured post on the homepage
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('featured-hero-post'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="featured-hero-thumbnail">
            <?php the_post_thumbnail('gizmodotech-featured'); ?>
        </div>
    <?php endif; ?>

    <div class="featured-hero-content">
        <div class="article-meta">
            <?php
            $categories = get_the_category();
            if ($categories) {
                printf('<a href="%s" class="category-badge">%s</a>', esc_url(get_category_link($categories[0]->term_id)), esc_html($categories[0]->name));
            }
            ?>
        </div>
        <?php the_title('<h2 class="featured-hero-title"><a href="' . esc_url(get_permalink()) . '" rel="bookmark">', '</a></h2>'); ?>
        <div class="article-excerpt"><?php the_excerpt(); ?></div>
    </div>
</article>