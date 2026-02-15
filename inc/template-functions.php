<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add custom body classes
 */
function gizmodotech_custom_body_class($classes) {
    // Add has-post-thumbnail class
    if (has_post_thumbnail()) {
        $classes[] = 'has-featured-image';
    }
    
    return $classes;
}
add_filter('body_class', 'gizmodotech_custom_body_class');

/**
 * Add custom post class
 */
function gizmodotech_custom_post_class($classes) {
    if (!is_singular()) {
        $classes[] = 'card';
    }
    
    return $classes;
}
add_filter('post_class', 'gizmodotech_custom_post_class');
