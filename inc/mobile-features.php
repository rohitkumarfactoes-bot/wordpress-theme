<?php
/**
 * Mobile Post Type Features (Specs, Image Extraction)
 *
 * @package Gizmodotech
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Image Extraction Functionality
 */

// Function to extract image URLs from post content
function gizmodotech_extract_images_from_post($post_content) {
    // Fixed regex to handle actual HTML tags instead of entities
    preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $post_content, $matches);
    return $matches[1]; // Return only the src attributes
}

// Function to get the featured image URL
function gizmodotech_get_featured_image_url($post_id) {
    $featured_image_url = '';
    if (has_post_thumbnail($post_id)) {
        $featured_image_url = get_the_post_thumbnail_url($post_id, 'full');
    }
    return $featured_image_url;
}

// Shortcode function to display extracted images
function gizmodotech_display_extracted_images($atts) {
    $atts = shortcode_atts(array(
        'post_id' => get_the_ID(), // Default to the current post ID
    ), $atts);

    $post_id = intval($atts['post_id']);
    if (!$post_id) {
        return 'Invalid post ID.';
    }

    $post_content = get_post_field('post_content', $post_id);
    $images = gizmodotech_extract_images_from_post($post_content);

    // Get the featured image URL
    $featured_image_url = gizmodotech_get_featured_image_url($post_id);
    if ($featured_image_url) {
        array_unshift($images, $featured_image_url); // Add the featured image URL at the beginning
    }

    if (empty($images)) {
        return '<div class="extracted-images">No images found in this post.</div>';
    }

    $output = '<div class="extracted-images-wrapper">';
    
    // Main Display Area
    $output .= '<div id="image-display" class="image-display">';
    if ($featured_image_url) {
        $output .= '<img src="' . esc_url($featured_image_url) . '" alt="Full Image">';
    } elseif (!empty($images)) {
        $output .= '<img src="' . esc_url($images[0]) . '" alt="Full Image">';
    }
    $output .= '</div>';

    // Thumbnails
    $output .= '<div class="extracted-images-grid">';
    foreach ($images as $image) {
        $output .= '<div class="thumbnail"><img src="' . esc_url($image) . '" alt="Thumbnail" data-full-image="' . esc_url($image) . '"></div>';
    }
    $output .= '</div>';
    $output .= '</div>'; // End wrapper

    return $output;
}
add_shortcode('extracted_images', 'gizmodotech_display_extracted_images');

/**
 * Adds a meta box for mobile specifications.
 */
function gizmodotech_add_specs_meta_box() {
    add_meta_box(
        'gizmodotech_specs_box',
        __('Mobile Specifications', 'gizmodotech'),
        'gizmodotech_specs_meta_box_html',
        'mobile', // Only show on 'mobile' post type
        'normal',
        'high'
    );
}
add_action('add_meta_boxes', 'gizmodotech_add_specs_meta_box');

/**
 * Renders the HTML for the specifications meta box.
 */
function gizmodotech_specs_meta_box_html($post) {
    wp_nonce_field('gizmodotech_specs_save', 'gizmodotech_specs_nonce');
    $specs = array(
        'display' => __('Display', 'gizmodotech'),
        'processor' => __('Processor', 'gizmodotech'),
        'ram' => __('RAM', 'gizmodotech'),
        'storage' => __('Storage', 'gizmodotech'),
        'camera' => __('Camera', 'gizmodotech'),
        'battery' => __('Battery', 'gizmodotech'),
    );

    echo '<table>';
    foreach ($specs as $key => $label) {
        $value = get_post_meta($post->ID, '_spec_' . $key, true);
        echo '<tr>';
        echo '<td><label for="spec_' . esc_attr($key) . '">' . esc_html($label) . '</label></td>';
        echo '<td><input type="text" id="spec_' . esc_attr($key) . '" name="spec_' . esc_attr($key) . '" value="' . esc_attr($value) . '" style="width:100%;"></td>';
        echo '</tr>';
    }
    echo '</table>';
}

/**
 * Saves the custom meta box data.
 */
function gizmodotech_save_specs_meta_box($post_id) {
    if (!isset($_POST['gizmodotech_specs_nonce']) || !wp_verify_nonce($_POST['gizmodotech_specs_nonce'], 'gizmodotech_specs_save')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $specs = array('display', 'processor', 'ram', 'storage', 'camera', 'battery');

    foreach ($specs as $key) {
        if (isset($_POST['spec_' . $key])) {
            update_post_meta($post_id, '_spec_' . $key, sanitize_text_field($_POST['spec_' . $key]));
        }
    }
}
add_action('save_post', 'gizmodotech_save_specs_meta_box');