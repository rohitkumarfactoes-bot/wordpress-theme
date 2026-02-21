<?php
/**
 * Theme shortcodes: related posts, tech/review sections, post slider
 *
 * @package Gizmodotech
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Display related posts by category (excludes a configurable category).
 * Use shortcode [related_posts] or call gizmodotech_display_related_posts().
 */
function gizmodotech_display_related_posts() {
    global $post;

    if (!isset($post) || !$post instanceof WP_Post) {
        return;
    }

    $categories = get_the_category($post->ID);
    $category_ids = array();

    if (!$categories) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No categories found.', 'gizmodotech') . '</p>';
        return;
    }

    foreach ($categories as $category) {
        $category_ids[] = $category->term_id;
    }

    $exclude_category_id = apply_filters('gizmodotech_related_posts_exclude_category', 3);
    $category_ids = array_diff($category_ids, array((int) $exclude_category_id));
    $category_ids = array_values($category_ids);

    if (empty($category_ids)) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No categories found to filter posts.', 'gizmodotech') . '</p>';
        return;
    }

    $count = apply_filters('gizmodotech_related_posts_count', 4);
    $args = array(
        'category__in'          => $category_ids,
        'post__not_in'          => array($post->ID),
        'posts_per_page'        => (int) $count,
        'orderby'               => 'date',
        'order'                 => 'DESC',
        'ignore_sticky_posts'   => 1,
    );

    $related = new WP_Query($args);

    if (!$related->have_posts()) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No related posts found.', 'gizmodotech') . '</p>';
        return;
    }

    echo '<div class="related-posts gizmodotech-related-posts">';
    while ($related->have_posts()) {
        $related->the_post();
        ?>
        <div class="gizmodotech-related-card card">
            <?php if (has_post_thumbnail()) : ?>
                <div class="img-wrapper">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('gizmodotech-medium'); ?></a>
                </div>
            <?php endif; ?>
            <a href="<?php the_permalink(); ?>" class="text-cm"><?php the_title(); ?></a>
        </div>
        <?php
    }
    echo '</div>';
    wp_reset_postdata();
}

function gizmodotech_related_posts_shortcode($atts) {
    ob_start();
    gizmodotech_display_related_posts();
    return ob_get_clean();
}
add_shortcode('related_posts', 'gizmodotech_related_posts_shortcode');

/**
 * Get posts that are either in category (slug) or of given post type.
 * WordPress has no "post_type" taxonomy, so we run two queries and merge by date.
 */
function gizmodotech_get_posts_by_category_or_type($category_slug, $post_type_cpt, $per_page = 6) {
    $posts = array();
    $seen_ids = array();

    if (post_type_exists($post_type_cpt)) {
        $q1 = new WP_Query(array(
            'post_type'              => $post_type_cpt,
            'posts_per_page'         => $per_page,
            'orderby'                => 'date',
            'order'                  => 'DESC',
            'ignore_sticky_posts'    => 1,
        ));
        while ($q1->have_posts()) {
            $q1->the_post();
            $id = get_the_ID();
            if (!isset($seen_ids[$id])) {
                $seen_ids[$id] = true;
                $posts[] = get_post($id);
            }
        }
        wp_reset_postdata();
    }

    $q2 = new WP_Query(array(
        'post_type'              => 'post',
        'posts_per_page'         => $per_page,
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'ignore_sticky_posts'    => 1,
        'category_name'         => $category_slug,
    ));
    while ($q2->have_posts()) {
        $q2->the_post();
        $id = get_the_ID();
        if (!isset($seen_ids[$id])) {
            $seen_ids[$id] = true;
            $posts[] = get_post($id);
        }
    }
    wp_reset_postdata();

    usort($posts, function ($a, $b) {
        return strtotime($b->post_date) - strtotime($a->post_date);
    });
    $posts = array_slice($posts, 0, $per_page);

    return new WP_Query(array(
        'post_type'    => array('post', $post_type_cpt),
        'post__in'     => wp_list_pluck($posts, 'ID'),
        'orderby'      => 'post__in',
        'posts_per_page' => $per_page,
    ));
}

/**
 * Tech-related posts: category "technews" or post type "technews".
 */
function gizmodotech_display_tech_related_posts() {
    $per_page = apply_filters('gizmodotech_tech_posts_count', 6);
    $query = gizmodotech_get_posts_by_category_or_type('technews', 'technews', $per_page);

    if (!$query->have_posts()) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No recent tech-related articles found.', 'gizmodotech') . '</p>';
        return;
    }

    echo '<div class="tech-related-container gizmodotech-tech-related">';
    echo '<div class="tech-related-posts">';
    while ($query->have_posts()) {
        $query->the_post();
        ?>
        <div class="tech-card gizmodotech-tech-card">
            <?php if (has_post_thumbnail()) : ?>
                <div class="tech-img-wrapper aspect-img">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('gizmodotech-medium'); ?></a>
                </div>
            <?php endif; ?>
            <div class="tech-desc-wrapper">
                <a href="<?php the_permalink(); ?>"><span class="tech-title"><?php the_title(); ?></span></a>
                <p class="tech-desc"><?php the_excerpt(); ?></p>
            </div>
        </div>
        <?php
    }
    echo '</div></div>';
    wp_reset_postdata();
}

function gizmodotech_tech_related_posts_shortcode($atts) {
    ob_start();
    gizmodotech_display_tech_related_posts();
    return ob_get_clean();
}
add_shortcode('tech_related_posts', 'gizmodotech_tech_related_posts_shortcode');

/**
 * Review-related posts: category "review" or post type "review".
 */
function gizmodotech_display_review_related_posts() {
    $per_page = apply_filters('gizmodotech_review_posts_count', 6);
    $query = gizmodotech_get_posts_by_category_or_type('review', 'review', $per_page);

    if (!$query->have_posts()) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No recent review-related articles found.', 'gizmodotech') . '</p>';
        return;
    }

    echo '<div class="review-related-container gizmodotech-review-related">';
    echo '<div class="review-related-posts">';
    while ($query->have_posts()) {
        $query->the_post();
        ?>
        <div class="review-card gizmodotech-review-card">
            <?php if (has_post_thumbnail()) : ?>
                <div class="review-img-wrapper aspect-img">
                    <a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('gizmodotech-medium'); ?></a>
                </div>
            <?php endif; ?>
            <div class="review-desc-wrapper">
                <a href="<?php the_permalink(); ?>"><span class="review-title"><?php the_title(); ?></span></a>
            </div>
        </div>
        <?php
    }
    echo '</div></div>';
    wp_reset_postdata();
}

function gizmodotech_review_related_posts_shortcode($atts) {
    ob_start();
    gizmodotech_display_review_related_posts();
    return ob_get_clean();
}
add_shortcode('review_related_posts', 'gizmodotech_review_related_posts_shortcode');

/**
 * All recent posts slider.
 */
function gizmodotech_display_all_recent_posts_slider() {
    $count = apply_filters('gizmodotech_slider_posts_count', 20);
    $query = new WP_Query(array(
        'post_type'              => 'post',
        'posts_per_page'         => (int) $count,
        'orderby'                => 'date',
        'order'                  => 'DESC',
        'ignore_sticky_posts'    => 1,
    ));

    if (!$query->have_posts()) {
        echo '<p class="gizmodotech-no-posts">' . esc_html__('No recent posts found.', 'gizmodotech') . '</p>';
        return;
    }

    echo '<div class="post-slider-container gizmodotech-post-slider">';
    echo '<div class="post-slider-track">';
    while ($query->have_posts()) {
        $query->the_post();
        ?>
        <div class="post-item-card">
            <?php if (has_post_thumbnail()) : ?>
                <div class="post-img-wrapper aspect-img">
                    <a href="<?php echo esc_url(get_permalink()); ?>"><?php the_post_thumbnail('gizmodotech-medium'); ?></a>
                </div>
            <?php endif; ?>
            <div class="post-desc-wrapper">
                <a class="post-title" href="<?php echo esc_url(get_permalink()); ?>"><span><?php the_title(); ?></span></a>
                <p class="post-excerpt"><?php the_excerpt(); ?></p>
                <span class="post-date"><?php echo get_the_date(); ?></span>
            </div>
        </div>
        <?php
    }
    echo '</div>';
    echo '<div class="slider-buttons">';
    echo '<button type="button" class="slider-button slider-button-prev" aria-label="' . esc_attr__('Previous', 'gizmodotech') . '"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="30" width="30" xmlns="http://www.w3.org/2000/svg"><path d="M217.9 256L345 129c9.4-9.4 9.4-24.6 0-33.9-9.4-9.4-24.6-9.3-34 0L167 239c-9.1 9.1-9.3 23.7-.7 33.1L310.9 417c4.7 4.7 10.9 7 17 7s12.3-2.3 17-7c9.4-9.4 9.4-24.6 0-33.9L217.9 256z"></path></svg></button>';
    echo '<button type="button" class="slider-button slider-button-next" aria-label="' . esc_attr__('Next', 'gizmodotech') . '"><svg stroke="currentColor" fill="currentColor" stroke-width="0" viewBox="0 0 512 512" height="30" width="30" xmlns="http://www.w3.org/2000/svg"><path d="M294.1 256L167 129c-9.4-9.4-9.4-24.6 0-33.9s24.6-9.3 34 0L345 239c9.1 9.1 9.3 23.7.7 33.1L201.1 417c-4.7 4.7-10.9 7-17 7s-12.3-2.3-17-7c-9.4-9.4-9.4-24.6 0-33.9l127-127.1z"></path></svg></button>';
    echo '</div>';
    echo '</div>';
    wp_reset_postdata();
}

function gizmodotech_all_recent_posts_slider_shortcode($atts) {
    ob_start();
    gizmodotech_display_all_recent_posts_slider();
    return ob_get_clean();
}
add_shortcode('all_recent_posts_slider', 'gizmodotech_all_recent_posts_slider_shortcode');

/**
 * Enqueue slider script (no-op if container not in DOM).
 */
function gizmodotech_enqueue_slider_assets() {
    wp_enqueue_script(
        'gizmodotech-post-slider',
        get_template_directory_uri() . '/assets/js/post-slider.js',
        array(),
        '1.0.0',
        true
    );
}
add_action('wp_enqueue_scripts', 'gizmodotech_enqueue_slider_assets');
