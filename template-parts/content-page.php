<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Gizmodotech
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <header class="page-header">
        <?php the_title('<h1 class="page-title">', '</h1>'); ?>
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