<?php
/**
 * Custom template tags for this theme
 *
 * @package Gizmodotech
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Display post meta information
 */
function gizmodotech_post_meta() {
    $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
    
    $time_string = sprintf($time_string,
        esc_attr(get_the_date(DATE_W3C)),
        esc_html(get_the_date())
    );

    printf('<div class="article-meta">');
    
    // Category
    $categories = get_the_category();
    if ($categories) {
        printf('<span class="category-badge">%s</span>', esc_html($categories[0]->name));
    }
    
    // Date
    printf('<span class="posted-on">%s</span>', $time_string);
    
    printf('</div>');
}

/**
 * Pagination
 */
function gizmodotech_pagination() {
    the_posts_pagination(array(
        'mid_size'  => 2,
        'prev_text' => '&laquo;',
        'next_text' => '&raquo;',
        'screen_reader_text' => esc_html__('Posts navigation', 'gizmodotech'),
    ));
}

/**
 * Comments Template
 */
function gizmodotech_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">
        <article class="comment-body">
            <footer class="comment-meta">
                <div class="comment-author vcard">
                    <?php echo get_avatar($comment, 50); ?>
                    <?php printf('<cite class="fn">%s</cite>', get_comment_author_link()); ?>
                </div>
                <div class="comment-metadata">
                    <a href="<?php echo esc_url(get_comment_link($comment->comment_ID)); ?>">
                        <?php printf('%1$s at %2$s', get_comment_date(), get_comment_time()); ?>
                    </a>
                    <?php edit_comment_link(esc_html__('Edit', 'gizmodotech'), '<span class="edit-link">', '</span>'); ?>
                </div>
            </footer>
            
            <?php if ('0' == $comment->comment_approved) : ?>
                <p class="comment-awaiting-moderation"><?php esc_html_e('Your comment is awaiting moderation.', 'gizmodotech'); ?></p>
            <?php endif; ?>

            <div class="comment-content">
                <?php comment_text(); ?>
            </div>

            <?php
            comment_reply_link(array_merge($args, array(
                'depth'     => $depth,
                'max_depth' => $args['max_depth'],
            )));
            ?>
        </article>
    <?php
}

/**
 * Displays social share buttons with SVG icons.
 */
function gizmodotech_the_social_share_buttons() {
    $url_raw = get_permalink();
    $url   = urlencode($url_raw);
    $title = urlencode(get_the_title());

    $links = array(
        'facebook' => array(
            'url'   => 'https://www.facebook.com/sharer/sharer.php?u=' . $url,
            'label' => 'Facebook',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
        ),
        'twitter' => array(
            'url'   => 'https://twitter.com/intent/tweet?url=' . $url . '&text=' . $title,
            'label' => 'Twitter',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>',
        ),
        'whatsapp' => array(
            'url'   => 'https://wa.me/?text=' . $title . '%20' . $url_raw,
            'label' => 'WhatsApp',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z"></path></svg>',
        ),
        'copy' => array(
            'url'   => $url_raw,
            'label' => 'Copy Link',
            'icon'  => '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path></svg>',
        ),
    );
    ?>
    <div class="gizmodotech-share-buttons">
        <span class="share-label"><?php esc_html_e('Share:', 'gizmodotech'); ?></span>
        <?php foreach ($links as $network => $data) : ?>
            <?php if ($network === 'copy') : ?>
                <button class="share-btn share-copy" data-link="<?php echo esc_url($data['url']); ?>" aria-label="<?php echo esc_attr($data['label']); ?>">
                    <?php echo $data['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span><?php echo esc_html($data['label']); ?></span>
                </button>
            <?php else : ?>
                <a href="<?php echo esc_url($data['url']); ?>" target="_blank" rel="noopener noreferrer" class="share-btn share-<?php echo esc_attr($network); ?>" aria-label="<?php printf(esc_attr__('Share on %s', 'gizmodotech'), $data['label']); ?>">
                    <?php echo $data['icon']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    <span><?php echo esc_html($data['label']); ?></span>
                </a>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
    <?php
}

/**
 * Displays breadcrumbs.
 */
function gizmodotech_breadcrumbs() {
    if ( ! is_singular( 'post' ) ) {
        return;
    }

    $crumbs = array();
    $crumbs[] = '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'Home', 'gizmodotech' ) . '</a>';

    $categories = get_the_category();
    if ( ! empty( $categories ) ) {
        $category = $categories[0];
        if ( $category->parent ) {
            $parent_cats = get_category_parents( $category->parent, true, '||' );
            $parent_cats = array_filter( explode( '||', $parent_cats ) );
            foreach ( $parent_cats as $parent_cat ) {
                $crumbs[] = $parent_cat;
            }
        }
        $crumbs[] = '<a href="' . esc_url( get_category_link( $category->term_id ) ) . '">' . esc_html( $category->name ) . '</a>';
    }

    $crumbs[] = '<span>' . get_the_title() . '</span>';

    echo '<nav class="gizmodotech-breadcrumbs" aria-label="breadcrumb"><p>';
    echo implode( ' <span class="breadcrumb-separator">&rsaquo;</span> ', $crumbs );
    echo '</p></nav>';
}

/**
 * Displays the modern post navigation for single posts.
 */
function gizmodotech_the_post_navigation() {
    $prev_post = get_previous_post();
    $next_post = get_next_post();

    if ( ! $prev_post && ! $next_post ) {
        return;
    }
    ?>
    <nav class="post-navigation-modern" aria-label="<?php esc_attr_e( 'Posts', 'gizmodotech' ); ?>">
        <div class="nav-links-modern">
            <?php if ( $prev_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $prev_post->ID ) ); ?>" class="nav-previous">
                    <div class="nav-content">
                        <span class="nav-label">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                            <?php esc_html_e( 'Previous Article', 'gizmodotech' ); ?>
                        </span>
                        <h4 class="nav-title"><?php echo get_the_title( $prev_post->ID ); ?></h4>
                    </div>
                    <?php if ( has_post_thumbnail( $prev_post->ID ) ) : ?>
                        <div class="nav-image">
                            <?php echo get_the_post_thumbnail( $prev_post->ID, 'thumbnail' ); ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>

            <?php if ( $next_post ) : ?>
                <a href="<?php echo esc_url( get_permalink( $next_post->ID ) ); ?>" class="nav-next">
                    <div class="nav-content">
                        <span class="nav-label">
                            <?php esc_html_e( 'Next Article', 'gizmodotech' ); ?>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                        </span>
                        <h4 class="nav-title"><?php echo get_the_title( $next_post->ID ); ?></h4>
                    </div>
                    <?php if ( has_post_thumbnail( $next_post->ID ) ) : ?>
                        <div class="nav-image">
                            <?php echo get_the_post_thumbnail( $next_post->ID, 'thumbnail' ); ?>
                        </div>
                    <?php endif; ?>
                </a>
            <?php endif; ?>
        </div>
    </nav>
    <?php
}