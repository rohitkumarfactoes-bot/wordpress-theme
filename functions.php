<?php
/**
 * Gizmodotech Theme Functions
 * 
 * @package Gizmodotech
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Theme version
define('GIZMODOTECH_VERSION', '1.0.0');

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
    
    // Enable support for Post Thumbnails on posts and pages
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(1200, 630, true);
    
    // Add additional image sizes
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
    
    // Add theme support for selective refresh for widgets
    add_theme_support('customize-selective-refresh-widgets');
    
    // Add support for custom logo
    add_theme_support('custom-logo', array(
        'height'      => 60,
        'width'       => 200,
        'flex-width'  => true,
        'flex-height' => true,
    ));
    
    // Add support for Block Styles
    add_theme_support('wp-block-styles');
    
    // Add support for full and wide align images
    add_theme_support('align-wide');
    
    // Add support for editor styles
    add_theme_support('editor-styles');
    add_editor_style('assets/css/editor-style.css');
    
    // Add support for responsive embeds
    add_theme_support('responsive-embeds');
    
    // Add support for custom colors
    add_theme_support('editor-color-palette', array(
        array(
            'name'  => esc_html__('Primary', 'gizmodotech'),
            'slug'  => 'primary',
            'color' => '#0ea5e9',
        ),
        array(
            'name'  => esc_html__('Accent', 'gizmodotech'),
            'slug'  => 'accent',
            'color' => '#ef4444',
        ),
    ));
}
add_action('after_setup_theme', 'gizmodotech_setup');

/**
 * Set the content width
 */
function gizmodotech_content_width() {
    $GLOBALS['content_width'] = apply_filters('gizmodotech_content_width', 1200);
}
add_action('after_setup_theme', 'gizmodotech_content_width', 0);

/**
 * Register widget areas
 */
function gizmodotech_widgets_init() {
    register_sidebar(array(
        'name'          => esc_html__('Sidebar', 'gizmodotech'),
        'id'            => 'sidebar-1',
        'description'   => esc_html__('Add widgets here to appear in your sidebar.', 'gizmodotech'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ));
    
    // Footer widget areas
    for ($i = 1; $i <= 4; $i++) {
        register_sidebar(array(
            'name'          => sprintf(esc_html__('Footer %d', 'gizmodotech'), $i),
            'id'            => 'footer-' . $i,
            'description'   => sprintf(esc_html__('Footer column %d', 'gizmodotech'), $i),
            'before_widget' => '<div id="%1$s" class="widget %2$s">',
            'after_widget'  => '</div>',
            'before_title'  => '<h4 class="widget-title">',
            'after_title'   => '</h4>',
        ));
    }
}
add_action('widgets_init', 'gizmodotech_widgets_init');

/**
 * Enqueue scripts and styles
 */
function gizmodotech_scripts() {
    // Google Fonts
    wp_enqueue_style(
        'gizmodotech-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=DM+Sans:wght@400;500;600;700&display=swap',
        array(),
        null
    );
    
    // Theme stylesheet
    wp_enqueue_style('gizmodotech-style', get_stylesheet_uri(), array(), GIZMODOTECH_VERSION);
    
    // Custom styles
    wp_enqueue_style('gizmodotech-custom', get_template_directory_uri() . '/assets/css/custom.css', array('gizmodotech-style'), GIZMODOTECH_VERSION);
    
    // Main JavaScript
    wp_enqueue_script('gizmodotech-script', get_template_directory_uri() . '/assets/js/main.js', array('jquery'), GIZMODOTECH_VERSION, true);
    
    // Comment reply script
    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
    
    // Localize script for AJAX
    wp_localize_script('gizmodotech-script', 'gizmodotech', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('gizmodotech_nonce'),
    ));
}
add_action('wp_enqueue_scripts', 'gizmodotech_scripts');

/**
 * Custom excerpt length
 */
function gizmodotech_excerpt_length($length) {
    return 25;
}
add_filter('excerpt_length', 'gizmodotech_excerpt_length', 999);

/**
 * Custom excerpt more
 */
function gizmodotech_excerpt_more($more) {
    return '...';
}
add_filter('excerpt_more', 'gizmodotech_excerpt_more');

/**
 * Add custom classes to body
 */
function gizmodotech_body_classes($classes) {
    // Add a class if dark mode is enabled
    if (isset($_COOKIE['dark_mode']) && $_COOKIE['dark_mode'] === 'true') {
        $classes[] = 'dark-mode';
    }
    
    // Add class if sidebar is active
    if (is_active_sidebar('sidebar-1')) {
        $classes[] = 'has-sidebar';
    }
    
    return $classes;
}
add_filter('body_class', 'gizmodotech_body_classes');

/**
 * Add custom post meta
 */
function gizmodotech_post_meta() {
    ?>
    <div class="post-meta">
        <span class="post-author">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M8 8a3 3 0 100-6 3 3 0 000 6zm2-3a2 2 0 11-4 0 2 2 0 014 0zm4 8c0 1-1 1-1 1H3s-1 0-1-1 1-4 6-4 6 3 6 4zm-1-.004c-.001-.246-.154-.986-.832-1.664C11.516 10.68 10.289 10 8 10c-2.29 0-3.516.68-4.168 1.332-.678.678-.83 1.418-.832 1.664h10z"/>
            </svg>
            <?php the_author_posts_link(); ?>
        </span>
        <span class="post-date">
            <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                <path d="M3.5 0a.5.5 0 01.5.5V1h8V.5a.5.5 0 011 0V1h1a2 2 0 012 2v11a2 2 0 01-2 2H2a2 2 0 01-2-2V3a2 2 0 012-2h1V.5a.5.5 0 01.5-.5zM1 4v10a1 1 0 001 1h12a1 1 0 001-1V4H1z"/>
            </svg>
            <?php echo get_the_date(); ?>
        </span>
        <?php if (has_category()) : ?>
        <span class="post-category">
            <?php the_category(', '); ?>
        </span>
        <?php endif; ?>
    </div>
    <?php
}

/**
 * Custom comment output
 */
function gizmodotech_comment($comment, $args, $depth) {
    $GLOBALS['comment'] = $comment;
    ?>
    <li id="comment-<?php comment_ID(); ?>" <?php comment_class('comment-item'); ?>>
        <article class="comment-body">
            <div class="comment-author">
                <?php echo get_avatar($comment, 48); ?>
                <div class="comment-meta">
                    <b class="fn"><?php comment_author_link(); ?></b>
                    <time datetime="<?php comment_date('c'); ?>">
                        <?php comment_date(); ?>
                    </time>
                </div>
            </div>
            <div class="comment-content">
                <?php comment_text(); ?>
            </div>
            <div class="comment-reply">
                <?php comment_reply_link(array_merge($args, array(
                    'depth'     => $depth,
                    'max_depth' => $args['max_depth']
                ))); ?>
            </div>
        </article>
    <?php
}

/**
 * Customizer additions
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Custom template tags
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Load Jetpack compatibility file
 */
if (defined('JETPACK__VERSION')) {
    require get_template_directory() . '/inc/jetpack.php';
}

/**
 * Add theme customization options
 */
function gizmodotech_customize_register($wp_customize) {
    // Add Dark Mode Toggle
    $wp_customize->add_setting('dark_mode_enabled', array(
        'default'           => false,
        'sanitize_callback' => 'gizmodotech_sanitize_checkbox',
    ));
    
    $wp_customize->add_control('dark_mode_enabled', array(
        'label'    => __('Enable Dark Mode Toggle', 'gizmodotech'),
        'section'  => 'title_tagline',
        'type'     => 'checkbox',
    ));
    
    // Add Social Media Links
    $social_sites = array('facebook', 'twitter', 'instagram', 'youtube', 'linkedin');
    
    $wp_customize->add_section('gizmodotech_social', array(
        'title'    => __('Social Media Links', 'gizmodotech'),
        'priority' => 30,
    ));
    
    foreach ($social_sites as $social_site) {
        $wp_customize->add_setting($social_site, array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));
        
        $wp_customize->add_control($social_site, array(
            'label'   => ucfirst($social_site) . ' URL',
            'section' => 'gizmodotech_social',
            'type'    => 'url',
        ));
    }
}
add_action('customize_register', 'gizmodotech_customize_register');

/**
 * Sanitize checkbox
 */
function gizmodotech_sanitize_checkbox($checked) {
    return ((isset($checked) && true == $checked) ? true : false);
}

/**
 * Add async/defer attributes to enqueued scripts
 */
function gizmodotech_defer_scripts($tag, $handle, $src) {
    if (is_admin()) {
        return $tag;
    }
    
    $defer_scripts = array('gizmodotech-script');
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'gizmodotech_defer_scripts', 10, 3);

/**
 * Custom search form
 */
function gizmodotech_search_form() {
    $form = '<form role="search" method="get" class="search-form" action="' . home_url('/') . '">
        <input type="search" class="search-field" placeholder="' . esc_attr__('Search...', 'gizmodotech') . '" value="' . get_search_query() . '" name="s" />
        <button type="submit" class="search-submit">
            <svg width="20" height="20" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
            </svg>
        </button>
    </form>';
    
    return $form;
}
add_filter('get_search_form', 'gizmodotech_search_form');
