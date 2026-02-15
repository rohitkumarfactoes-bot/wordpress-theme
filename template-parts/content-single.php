<?php
/**
 * Template part for displaying single posts
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
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
    </header>

    <?php if (has_post_thumbnail()) : ?>
        <div class="single-post-thumbnail">
            <?php the_post_thumbnail('gizmodotech-featured'); ?>
        </div>
    <?php endif; ?>

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
