<?php
/**
 * Jetpack Compatibility File
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function gizmodotech_jetpack_setup() {
    // Add support for Infinite Scroll
    add_theme_support('infinite-scroll', array(
        'container' => 'main',
        'render'    => 'gizmodotech_infinite_scroll_render',
        'footer'    => 'page',
    ));
}
add_action('after_setup_theme', 'gizmodotech_jetpack_setup');

function gizmodotech_infinite_scroll_render() {
    while (have_posts()) {
        the_post();
        get_template_part('template-parts/content', get_post_format());
    }
}
