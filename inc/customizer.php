<?php
/**
 * Theme Customizer
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

function gizmodotech_customizer_register($wp_customize) {
    // This is handled in functions.php
}
add_action('customize_register', 'gizmodotech_customizer_register');
