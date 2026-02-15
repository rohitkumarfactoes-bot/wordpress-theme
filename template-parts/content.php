<?php
/**
 * Template part for displaying posts
 *
 * @package Gizmodotech
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>
    
    <?php if (has_post_thumbnail()) : ?>
    <div class="post-thumbnail">
        <a href="<?php the_permalink(); ?>">
            <?php the_post_thumbnail('gizmodotech-medium'); ?>
        </a>
        
        <?php
        $categories = get_the_category();
        if (!empty($categories)) :
        ?>
        <span class="badge category-badge">
            <?php echo esc_html($categories[0]->name); ?>
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <div class="post-content">
        <?php the_title(sprintf('<h3 class="entry-title"><a href="%s">', esc_url(get_permalink())), '</a></h3>'); ?>
        
        <div class="entry-excerpt">
            <?php the_excerpt(); ?>
        </div>
        
        <?php gizmodotech_post_meta(); ?>
    </div>
    
</article>
