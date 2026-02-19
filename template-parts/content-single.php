<?php
/**
 * Template part for displaying single posts
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <?php
    // Display breadcrumbs
    gizmodotech_breadcrumbs(); ?>
    <?php if (has_post_thumbnail()) : ?>
        <div class="single-post-thumbnail">
            <?php the_post_thumbnail('gizmodotech-featured'); ?>
        </div>
    <?php endif; ?>

    <header class="single-post-header">
        <?php
        $categories = get_the_category();
        if ( ! empty( $categories ) ) {
            printf(
                '<a href="%s" class="is-style-gizmodotech-category-badge">%s</a>',
                esc_url( get_category_link( $categories[0]->term_id ) ),
                esc_html( $categories[0]->name )
            );
        }
        ?>
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

        </div>

        <?php gizmodotech_the_social_share_buttons(); ?>
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
</article>
