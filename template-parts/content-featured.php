<?php
/**
 * Template part for displaying the featured post on the homepage
 *
 * @package Gizmodotech
 */
$show_read_more = get_theme_mod('gizmodotech_show_hero_read_more', true);
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('featured-hero-post'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="featured-hero-thumbnail">
            <a href="<?php the_permalink(); ?>" class="featured-hero-thumbnail-link" aria-hidden="true" tabindex="-1">
                <?php the_post_thumbnail('gizmodotech-featured'); ?>
            </a>
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
        <?php if ($show_read_more) : ?>
            <div class="featured-hero-cta">
                <a href="<?php the_permalink(); ?>" class="hero-read-more">
                    <?php esc_html_e('Read article', 'gizmodotech'); ?>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        <?php endif; ?>
    </div>
</article>