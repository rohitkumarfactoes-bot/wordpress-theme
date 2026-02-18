<?php
/**
 * Template part for displaying single posts
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <div class="single-post-thumbnail" style="margin-bottom: 2rem;">
            <?php the_post_thumbnail('gizmodotech-featured'); ?>
        </div>
    <?php endif; ?>

    <header class="single-post-header">
        <?php the_title('<h1 class="single-post-title">', '</h1>'); ?>

        <div class="single-post-meta">
            <span class="posted-on">
                <?php
                printf(
                    esc_html__('Posted on %s', 'gizmodotech'),
                    '<time datetime="' . esc_attr(get_the_date(DATE_W3C)) . '">' . esc_html(get_the_date()) . '</time>'
                );
                ?>
            </span>

            <span class="byline">
                <?php
                printf(
                    esc_html__('By %s', 'gizmodotech'),
                    '<span class="author vcard"><a class="url fn n" href="' . esc_url(get_author_posts_url(get_the_author_meta('ID'))) . '">' . esc_html(get_the_author()) . '</a></span>'
                );
                ?>
            </span>

            <?php if (has_category()) : ?>
                <span class="categories">
                    <?php
                    printf(
                        esc_html__('In %s', 'gizmodotech'),
                        get_the_category_list(', ')
                    );
                    ?>
                </span>
            <?php endif; ?>

            <span class="reading-time">
                <?php
                printf(
                    esc_html__('%d min read', 'gizmodotech'),
                    gizmodotech_reading_time()
                );
                ?>
            </span>

            <span class="post-views">
                <?php
                printf(
                    esc_html__('%s views', 'gizmodotech'),
                    gizmodotech_post_views()
                );
                ?>
            </span>
        </div>

        <div class="gizmodotech-share-buttons">
            <span class="share-label"><?php esc_html_e('Share:', 'gizmodotech'); ?></span>
            
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-fb" aria-label="Share on Facebook">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                <span>Facebook</span>
            </a>
            
            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-tw" aria-label="Share on Twitter">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                <span>Twitter</span>
            </a>
            
            <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' - ' . get_permalink()); ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-wa" aria-label="Share on WhatsApp">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>
                <span>WhatsApp</span>
            </a>
            
            <button class="share-btn share-copy" data-link="<?php echo esc_url(get_permalink()); ?>" aria-label="Copy Link">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>
                <span>Copy Link</span>
            </button>
        </div>
    </header>

    <div class="entry-content">
        <?php
        the_content();

        wp_link_pages(array(
            'before' => '<div class="page-links">' . esc_html__('Pages:', 'gizmodotech'),
            'after'  => '</div>',
        ));
        ?>
    </div>

    <?php if (has_tag()) : ?>
        <footer class="entry-footer">
            <div class="post-tags">
                <?php
                the_tags(
                    '<span class="tags-label">' . esc_html__('Tags:', 'gizmodotech') . '</span> ',
                    ', '
                );
                ?>
            </div>
        </footer>
    <?php endif; ?>
</article>
