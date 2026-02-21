<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Gizmodotech
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Adds an inline script to the head to prevent dark mode FOUC (Flash of Unstyled Content).
 */
function gizmodotech_dark_mode_fouc_fix() {
    ?>
    <script>
        (function() {
            var theme = localStorage.getItem('theme');
            if (theme === 'dark') {
                document.documentElement.setAttribute('data-theme', 'dark');
            } else if (!theme && window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.setAttribute('data-theme', 'dark');
            }
        })();
    </script>
    <?php
}
add_action('wp_head', 'gizmodotech_dark_mode_fouc_fix', 0);

/**
 * Add body classes
 */
function gizmodotech_body_classes($classes) {
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1') && !is_page()) {
        $classes[] = 'has-sidebar';
    }

    // Add singular class
    if (is_singular()) {
        $classes[] = 'singular';
    }

    return $classes;
}
add_filter('body_class', 'gizmodotech_body_classes');

/**
 * Custom excerpt length
 */
function gizmodotech_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'gizmodotech_excerpt_length');

/**
 * Custom excerpt more
 */
function gizmodotech_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'gizmodotech_excerpt_more');

/**
 * Auto-generate Table of Contents for Single Posts
 */
function gizmodotech_add_toc($content) {
    if ( ! is_singular( array( 'post', 'mobile' ) ) || ! in_the_loop() || ! is_main_query() ) {
        return $content;
    }

    $toc_items = '';
    $i = 0;

    // Find H2 tags only, generate links, and add IDs to headings
    $content = preg_replace_callback('/<h(2)([^>]*)>(.*?)<\/h\1>/si', function($matches) use (&$toc_items, &$i) {
        $i++;
        $anchor = 'toc-' . $i;
        $level = $matches[1];
        $attrs = $matches[2];
        $title = $matches[3];
        
        $toc_items .= '<li class="toc-item toc-level-' . $level . '"><a href="#' . $anchor . '">' . strip_tags($title) . '</a></li>';

        return '<h' . $level . ' id="' . $anchor . '"' . $attrs . '>' . $title . '</h' . $level . '>';
    }, $content);

    if ($i >= 2) {
        $toc_html = '<div class="gizmodotech-toc"><div class="toc-header"><h3 class="toc-title">' . esc_html__('Table of Contents', 'gizmodotech') . '</h3><button class="toc-toggle" aria-label="' . esc_attr__('Toggle TOC', 'gizmodotech') . '"><svg class="w-5 h-5 transition-transform" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></button></div><ul class="toc-list">' . $toc_items . '</ul></div>';
        return $toc_html . $content;
    }

    return $content;
}
add_filter('the_content', 'gizmodotech_add_toc');

/**
 * Remove images from post content on 'mobile' post type single views or posts in 'mobile' category.
 */
function gizmodotech_strip_images_from_content($content) {
    // Strip images if it's a 'mobile' CPT OR a standard post in the 'mobile' category.
    if ( (is_singular('mobile') || (is_singular('post') && has_category('mobile'))) && in_the_loop() && is_main_query() ) {
        $content = preg_replace('/<img[^>]+>/i', '', $content);
    }
    return $content;
}
add_filter('the_content', 'gizmodotech_strip_images_from_content', 20);

/**
 * Calculate Estimated Reading Time
 *
 * @return string Reading time in minutes.
 */
function gizmodotech_get_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words/min

    if ($reading_time < 1) {
        return '1 ' . __('min read', 'gizmodotech');
    }
    return $reading_time . ' ' . __('min read', 'gizmodotech');
}