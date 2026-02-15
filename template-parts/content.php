<?php
/**
 * Template part for displaying posts
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="article-thumbnail">
            <a href="<?php the_permalink(); ?>">
                <?php the_post_thumbnail('gizmodotech-medium'); ?>
            </a>
        </div>
    <?php endif; ?>

    <div class="article-content">
        <?php gizmodotech_post_meta(); ?>

        <h2 class="article-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h2>

        <div class="article-excerpt">
            <?php the_excerpt(); ?>
        </div>

        <a href="<?php the_permalink(); ?>" class="read-more">
            <?php esc_html_e('Read More', 'gizmodotech'); ?>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"></line>
                <polyline points="12 5 19 12 12 19"></polyline>
            </svg>
        </a>
    </div>
</article>
