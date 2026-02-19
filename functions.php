<?php
/**
 * Gizmodotech functions and definitions
 *
 * @package Gizmodotech
 * @since 1.0.1 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Theme Setup
 */
function gizmodotech_setup() {
    // Make theme available for translation
    load_theme_textdomain('gizmodotech', get_template_directory() . '/languages');

    // Add default posts and comments RSS feed links to head
    add_theme_support('automatic-feed-links');

    // Let WordPress manage the document title
    add_theme_support('title-tag');

    // Enable support for Post Thumbnails
    add_theme_support('post-thumbnails');

    // Set custom image sizes
    add_image_size('gizmodotech-featured', 1200, 675, true);
    add_image_size('gizmodotech-large', 800, 450, true);
    add_image_size('gizmodotech-medium', 600, 400, true);
    add_image_size('gizmodotech-small', 400, 300, true);

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'gizmodotech'),
        'footer'  => esc_html__('Footer Menu', 'gizmodotech'),
    ));

    // Switch default core markup for search form, comment form, and comments
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));

    // Add theme support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ));

    // Add support for responsive embedded content
    add_theme_support('responsive-embeds');

    // Add support for editor styles
    add_theme_support('editor-styles');

    // Add support for Block Editor features
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
}
add_action('after_setup_theme', 'gizmodotech_setup');

/**
 * Register custom block styles
 */
function gizmodotech_register_block_styles() {
    if (function_exists('register_block_style')) {
        register_block_style(
            'core/post-terms',
            [
                'name'  => 'gizmodotech-category-badge',
                'label' => __('Category Badge', 'gizmodotech'),
            ]
        );
    }
}
add_action('init', 'gizmodotech_register_block_styles');

/**
 * Set the content width in pixels
 */
function gizmodotech_content_width() {
    $GLOBALS['content_width'] = apply_filters('gizmodotech_content_width', 800);
}
add_action('after_setup_theme', 'gizmodotech_content_width', 0);

/**
 * Enqueue scripts and styles
 */
function gizmodotech_scripts() {
    // Dynamically enqueue Google Fonts selected in the Customizer
    gizmodotech_enqueue_google_fonts();

    // Main stylesheet
    wp_enqueue_style('gizmodotech-style', get_stylesheet_uri(), array(), '1.0.0');

    // Localize script for Ajax
    wp_localize_script('gizmodotech-navigation', 'gizmodotech_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('gizmodotech_subscribe_nonce')
    ));

    // Navigation script (no jQuery dependency needed)
    wp_enqueue_script('gizmodotech-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), '1.0.0', true);

    // Dark mode script
    wp_enqueue_script(
        'gizmodotech-dark-mode',
        get_template_directory_uri() . '/assets/js/dark-mode.js',
        array(),
        '1.0.0',
        true
    );

    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
add_action('wp_enqueue_scripts', 'gizmodotech_scripts');

/**
 * Register widget areas
 */
function gizmodotech_widgets_init() {
    // Sidebar
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'gizmodotech'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'gizmodotech'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));

    // Footer Widget Areas
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer %d', 'gizmodotech'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Add widgets here to appear in footer column %d.', 'gizmodotech'), $i),
            'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
            'after_widget'  => '</section>',
            'before_title'  => '<h3 class="widget-title">',
            'after_title'   => '</h3>',
        ));
    }

    // Homepage Widget Area
    register_sidebar(array(
        'name'          => esc_html__('Homepage Widgets', 'gizmodotech'),
        'id'            => 'homepage-widgets',
        'description'   => esc_html__('Add widgets here to design your homepage (Slider, Category Posts, etc).', 'gizmodotech'),
        'before_widget' => '<section id="%1$s" class="widget homepage-widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h2 class="widget-title">',
        'after_title'   => '</h2>',
    ));
}
add_action('widgets_init', 'gizmodotech_widgets_init');

/**
 * Security improvements
 */
// Remove WordPress version from head
remove_action('wp_head', 'wp_generator');

// Disable XML-RPC
add_filter('xmlrpc_enabled', '__return_false');

// Remove RSD link
remove_action('wp_head', 'rsd_link');

// Remove wlwmanifest link
remove_action('wp_head', 'wlwmanifest_link');

/**
 * Performance optimizations
 */
// Remove emoji scripts
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Disable embeds
function gizmodotech_disable_embeds() {
    wp_deregister_script('wp-embed');
}
add_action('wp_footer', 'gizmodotech_disable_embeds');

/**
 * Handle Subscription AJAX
 */
function gizmodotech_handle_subscribe() {
    check_ajax_referer('gizmodotech_subscribe_nonce', 'nonce');
    
    $email = isset($_POST['email']) ? sanitize_email($_POST['email']) : '';
    
    if (is_email($email)) {
        // In a real scenario, save to DB or mailing list API
        // For now, we simulate success
        wp_send_json_success(array('message' => esc_html__('Thanks for subscribing!', 'gizmodotech')));
    } else {
        wp_send_json_error(array('message' => esc_html__('Please enter a valid email.', 'gizmodotech')));
    }
}
add_action('wp_ajax_gizmodotech_subscribe', 'gizmodotech_handle_subscribe');
add_action('wp_ajax_nopriv_gizmodotech_subscribe', 'gizmodotech_handle_subscribe');

/**
 * Load Customizer functionality.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Template Tags.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Load Template Functions.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Load Custom Widgets
 */
require get_template_directory() . '/inc/widgets.php';

function gizmodotech_register_custom_widgets() {
    if (class_exists('Gizmodotech_Featured_Slider_Widget')) {
        register_widget('Gizmodotech_Featured_Slider_Widget');
    }
    if (class_exists('Gizmodotech_Category_Posts_Widget')) {
        register_widget('Gizmodotech_Category_Posts_Widget');
    }
    if (class_exists('Gizmodotech_Author_Bio_Widget')) {
        register_widget('Gizmodotech_Author_Bio_Widget');
    }
}
add_action('widgets_init', 'gizmodotech_register_custom_widgets');

/**
 * Load Block Patterns
 */
require get_template_directory() . '/inc/block-patterns.php';

/**
 * Load Mobile Features (Specs, Image Extraction).
 */
require get_template_directory() . '/inc/mobile-features.php';

/**
 * Enqueue selected Google Fonts from Customizer.
 */
function gizmodotech_enqueue_google_fonts() {
    $elements = array('body', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6');
    $font_families = array();

    foreach ($elements as $el) {
        $default_font = ($el === 'body') ? 'Inter' : 'DM Sans';
        $font_families[] = get_theme_mod('gizmodotech_' . $el . '_font_family', $default_font);
    }

    $unique_fonts = array_unique(array_filter($font_families));

    if (empty($unique_fonts)) {
        return;
    }

    $font_query_args = array(
        'family' => urlencode(implode('|', $unique_fonts) . ':300,400,500,600,700,800'),
        'display' => 'swap',
    );

    $fonts_url = add_query_arg($font_query_args, 'https://fonts.googleapis.com/css');
    wp_enqueue_style('gizmodotech-google-fonts', $fonts_url, array(), null);
}
