<?php
/**
 * Gizmodotech functions and definitions
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

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
    // Google Fonts
    wp_enqueue_style(
        'gizmodotech-fonts',
        'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Sans:wght@700&display=swap',
        array(),
        null
    );

    // Main stylesheet
    wp_enqueue_style('gizmodotech-style', get_stylesheet_uri(), array(), '1.0.0');

    // Dark mode styles
    wp_enqueue_style(
        'gizmodotech-dark-mode',
        get_template_directory_uri() . '/assets/css/dark-mode.css',
        array('gizmodotech-style'),
        '1.0.0'
    );

    // Navigation script
    wp_enqueue_script(
        'gizmodotech-navigation',
        get_template_directory_uri() . '/assets/js/navigation.js',
        array('jquery'),
        '1.0.0',
        true
    );

    // Localize script for Ajax
    wp_localize_script('gizmodotech-navigation', 'gizmodotech_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('gizmodotech_subscribe_nonce')
    ));

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
 * Custom template tags
 */

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
    
    // Reading time
    $reading_time = gizmodotech_reading_time();
    if ($reading_time) {
        printf('<span class="reading-time">%d min read</span>', absint($reading_time));
    }
    
    printf('</div>');
}

/**
 * Calculate reading time
 */
function gizmodotech_reading_time() {
    $content = get_post_field('post_content', get_the_ID());
    $word_count = str_word_count(strip_tags($content));
    $reading_time = ceil($word_count / 200); // Average reading speed: 200 words per minute
    return $reading_time;
}

/**
 * Display post view count
 */
function gizmodotech_post_views() {
    $post_id = get_the_ID();
    $views = get_post_meta($post_id, 'gizmodotech_post_views', true);
    
    if (!$views) {
        $views = 0;
    }
    
    return number_format_i18n($views);
}

/**
 * Track post views
 */
function gizmodotech_set_post_views() {
    if (is_single()) {
        $post_id = get_the_ID();
        $views = get_post_meta($post_id, 'gizmodotech_post_views', true);
        
        if (!$views) {
            $views = 0;
        }
        
        $views++;
        update_post_meta($post_id, 'gizmodotech_post_views', $views);
    }
}
add_action('wp_head', 'gizmodotech_set_post_views');

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
 * Customizer additions
 */
function gizmodotech_customize_register($wp_customize) {
    // Dark Mode Toggle
    $wp_customize->add_section('gizmodotech_dark_mode', array(
        'title'    => esc_html__('Dark Mode', 'gizmodotech'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('gizmodotech_enable_dark_mode', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('gizmodotech_enable_dark_mode', array(
        'label'   => esc_html__('Enable Dark Mode Toggle', 'gizmodotech'),
        'section' => 'gizmodotech_dark_mode',
        'type'    => 'checkbox',
    ));

    // Social Media Links
    $wp_customize->add_section('gizmodotech_social_links', array(
        'title'    => esc_html__('Social Media Links', 'gizmodotech'),
        'priority' => 40,
    ));

    $social_networks = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'linkedin'  => 'LinkedIn',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('gizmodotech_' . $network . '_url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control('gizmodotech_' . $network . '_url', array(
            'label'   => $label . ' ' . esc_html__('URL', 'gizmodotech'),
            'section' => 'gizmodotech_social_links',
            'type'    => 'url',
        ));
    }
}
add_action('customize_register', 'gizmodotech_customize_register');

/**
 * Pagination
 */
function gizmodotech_pagination() {
    global $wp_query;

    if ($wp_query->max_num_pages <= 1) {
        return;
    }

    $paged = get_query_var('paged') ? absint(get_query_var('paged')) : 1;
    $max   = intval($wp_query->max_num_pages);

    // Add current page to the array
    if ($paged >= 1) {
        $links[] = $paged;
    }

    // Add the pages around the current page to the array
    if ($paged >= 3) {
        $links[] = $paged - 1;
        $links[] = $paged - 2;
    }

    if (($paged + 2) <= $max) {
        $links[] = $paged + 2;
        $links[] = $paged + 1;
    }

    echo '<div class="pagination">' . "\n";

    // Previous Post Link
    if (get_previous_posts_link()) {
        printf('<a href="%s">%s</a>' . "\n", get_previous_posts_page_link(), '&laquo;');
    }

    // Link to first page
    if (!in_array(1, $links)) {
        $class = 1 == $paged ? ' class="current"' : '';
        printf('<a href="%s"%s>%s</a>' . "\n", esc_url(get_pagenum_link(1)), $class, '1');

        if (!in_array(2, $links)) {
            echo '<span>...</span>';
        }
    }

    // Link to current page, plus 2 pages in either direction if necessary
    sort($links);
    foreach ((array) $links as $link) {
        $class = $paged == $link ? ' class="current"' : '';
        printf('<a href="%s"%s>%s</a>' . "\n", esc_url(get_pagenum_link($link)), $class, $link);
    }

    // Link to last page
    if (!in_array($max, $links)) {
        if (!in_array($max - 1, $links)) {
            echo '<span>...</span>' . "\n";
        }

        $class = $paged == $max ? ' class="current"' : '';
        printf('<a href="%s"%s>%s</a>' . "\n", esc_url(get_pagenum_link($max)), $class, $max);
    }

    // Next Post Link
    if (get_next_posts_link()) {
        printf('<a href="%s">%s</a>' . "\n", get_next_posts_page_link(), '&raquo;');
    }

    echo '</div>' . "\n";
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
 * Load Custom Widgets
 */
if (file_exists(get_template_directory() . '/inc/widgets.php')) {
    require get_template_directory() . '/inc/widgets.php';
} elseif (file_exists(get_template_directory() . '/widgets.php')) {
    require get_template_directory() . '/widgets.php';
}
function gizmodotech_register_custom_widgets() {
    if (class_exists('Gizmodotech_Featured_Slider_Widget')) {
        register_widget('Gizmodotech_Featured_Slider_Widget');
    }
    if (class_exists('Gizmodotech_Category_Posts_Widget')) {
        register_widget('Gizmodotech_Category_Posts_Widget');
    }
}
add_action('widgets_init', 'gizmodotech_register_custom_widgets');

/**
 * Load Block Patterns
 */
if (file_exists(get_template_directory() . '/inc/block-patterns.php')) {
    require get_template_directory() . '/inc/block-patterns.php';
}

/**
 * Auto-generate Table of Contents for Single Posts
 */
function gizmodotech_add_toc($content) {
    if (!is_singular('post') || !in_the_loop() || !is_main_query()) {
        return $content;
    }

    $toc_items = '';
    $i = 0;

    // Find H2 and H3 tags, generate links, and add IDs to headings
    $content = preg_replace_callback('/<h([2-3])([^>]*)>(.*?)<\/h\1>/si', function($matches) use (&$toc_items, &$i) {
        $i++;
        $anchor = 'toc-' . $i;
        $level = $matches[1];
        $attrs = $matches[2];
        $title = $matches[3];
        
        $toc_items .= '<li class="toc-item toc-level-' . $level . '"><a href="#' . $anchor . '">' . strip_tags($title) . '</a></li>';
        
        return '<h' . $level . $attrs . ' id="' . $anchor . '">' . $title . '</h' . $level . '>';
    }, $content);

    if ($i >= 2) {
        $toc_html = '<div class="gizmodotech-toc"><div class="toc-header"><h3 class="toc-title">' . esc_html__('Table of Contents', 'gizmodotech') . '</h3><button class="toc-toggle" aria-label="' . esc_attr__('Toggle TOC', 'gizmodotech') . '"><svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg></button></div><ul class="toc-list">' . $toc_items . '</ul></div>';
        return $toc_html . $content;
    }

    return $content;
}
add_filter('the_content', 'gizmodotech_add_toc');
