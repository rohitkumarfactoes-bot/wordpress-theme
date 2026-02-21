<?php
/**
 * Template part for displaying a message when no posts are found
 *
 * @package Gizmodotech
 */
?>

<section class="no-results not-found text-center py-16 md:py-24">
    <header class="page-header mb-4">
        <h1 class="text-4xl font-bold font-heading"><?php esc_html_e('Nothing Found', 'gizmodotech'); ?></h1>
    </header>

    <div class="page-content max-w-xl mx-auto text-text-light">
        <?php if (is_home() && current_user_can('publish_posts')) : ?>

            <p>
                <?php
                printf(
                    wp_kses(
                        __('Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'gizmodotech'),
                        array(
                            'a' => array(
                                'href' => array(),
                            ),
                        )
                    ),
                    esc_url(admin_url('post-new.php'))
                );
                ?>
            </p>

        <?php elseif (is_search()) : ?>

            <p><?php esc_html_e('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'gizmodotech'); ?></p>
            <?php get_search_form(); ?>

        <?php else : ?>

            <p><?php esc_html_e('It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'gizmodotech'); ?></p>
            <?php get_search_form(); ?>

        <?php endif; ?>
    </div>
</section>
