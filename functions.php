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
    // Dynamically enqueue Google Fonts selected in the Customizer
    gizmodotech_enqueue_google_fonts();

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

    // Global Colors
    $wp_customize->add_section('gizmodotech_colors', array(
        'title'    => esc_html__('Global Colors', 'gizmodotech'),
        'priority' => 20,
    ));

    $colors = array(
        'primary_color' => array('label' => 'Primary Color', 'default' => '#0ea5e9', 'var' => '--color-primary'),
        'bg_color'      => array('label' => 'Background Color', 'default' => '#ffffff', 'var' => '--color-bg'),
        'text_color'    => array('label' => 'Text Color', 'default' => '#1f2937', 'var' => '--color-text'),
    );

    foreach ($colors as $id => $args) {
        $wp_customize->add_setting('gizmodotech_' . $id, array(
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'gizmodotech_' . $id, array(
            'label'   => esc_html__($args['label'], 'gizmodotech'),
            'section' => 'gizmodotech_colors',
        )));
    }

    // --- Enhanced Typography Section ---
    $wp_customize->add_section('gizmodotech_typography', array(
        'title'    => esc_html__('Typography', 'gizmodotech'),
        'priority' => 25,
    ));

    $google_fonts = gizmodotech_get_google_fonts();
    $font_weights = array(
        '300' => 'Light (300)',
        '400' => 'Normal (400)',
        '500' => 'Medium (500)',
        '600' => 'Semi-Bold (600)',
        '700' => 'Bold (700)',
        '800' => 'Extra-Bold (800)',
    );
    $text_transforms = array(
        'none'       => 'None',
        'uppercase'  => 'Uppercase',
        'lowercase'  => 'Lowercase',
        'capitalize' => 'Capitalize',
    );

    $elements = array(
        'body' => array('label' => 'Body', 'defaults' => ['family' => 'Inter', 'size' => 16, 'weight' => '400', 'transform' => 'none']),
        'h1'   => array('label' => 'Heading 1', 'defaults' => ['family' => 'DM Sans', 'size' => 40, 'weight' => '700', 'transform' => 'none']),
        'h2'   => array('label' => 'Heading 2', 'defaults' => ['family' => 'DM Sans', 'size' => 32, 'weight' => '700', 'transform' => 'none']),
        'h3'   => array('label' => 'Heading 3', 'defaults' => ['family' => 'DM Sans', 'size' => 28, 'weight' => '700', 'transform' => 'none']),
        'h4'   => array('label' => 'Heading 4', 'defaults' => ['family' => 'DM Sans', 'size' => 24, 'weight' => '700', 'transform' => 'none']),
        'h5'   => array('label' => 'Heading 5', 'defaults' => ['family' => 'DM Sans', 'size' => 20, 'weight' => '700', 'transform' => 'none']),
        'h6'   => array('label' => 'Heading 6', 'defaults' => ['family' => 'DM Sans', 'size' => 18, 'weight' => '700', 'transform' => 'none']),
    );

    $priority = 10;

    foreach ($elements as $id => $props) {
        // Font Family
        $wp_customize->add_setting("gizmodotech_{$id}_font_family", ['default' => $props['defaults']['family'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_font_family", [
            'label' => $props['label'] . ' Font Family',
            'section' => 'gizmodotech_typography',
            'type' => 'select',
            'choices' => $google_fonts,
            'priority' => $priority++,
        ]);

        // Font Size
        $wp_customize->add_setting("gizmodotech_{$id}_font_size", ['default' => $props['defaults']['size'], 'sanitize_callback' => 'absint']);
        $wp_customize->add_control("gizmodotech_{$id}_font_size", [
            'label' => $props['label'] . ' Font Size (px)',
            'section' => 'gizmodotech_typography',
            'type' => 'number',
            'input_attrs' => ['min' => 8, 'max' => 100, 'step' => 1],
            'priority' => $priority++,
        ]);

        // Font Weight
        $wp_customize->add_setting("gizmodotech_{$id}_font_weight", ['default' => $props['defaults']['weight'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_font_weight", [
            'label' => $props['label'] . ' Font Weight',
            'section' => 'gizmodotech_typography',
            'type' => 'select',
            'choices' => $font_weights,
            'priority' => $priority++,
        ]);

        // Text Transform
        $wp_customize->add_setting("gizmodotech_{$id}_text_transform", ['default' => $props['defaults']['transform'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_text_transform", [
            'label' => $props['label'] . ' Text Transform',
            'section' => 'gizmodotech_typography',
            'type' => 'select',
            'choices' => $text_transforms,
            'priority' => $priority++,
        ]);
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
    if (class_exists('Gizmodotech_Author_Bio_Widget')) {
        register_widget('Gizmodotech_Author_Bio_Widget');
    }
}
add_action('widgets_init', 'gizmodotech_register_custom_widgets');

/**
 * Load Block Patterns
 */
if (file_exists(get_template_directory() . '/inc/block-patterns.php')) {
    require get_template_directory() . '/inc/block-patterns.php';
} elseif (file_exists(get_template_directory() . '/block-patterns.php')) {
    require get_template_directory() . '/block-patterns.php';
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

/**
 * Output Customizer CSS Variables
 */
function gizmodotech_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --color-primary: <?php echo esc_attr(get_theme_mod('gizmodotech_primary_color', '#0ea5e9')); ?>;
            --color-bg: <?php echo esc_attr(get_theme_mod('gizmodotech_bg_color', '#ffffff')); ?>;
            --color-text: <?php echo esc_attr(get_theme_mod('gizmodotech_text_color', '#1f2937')); ?>;
        }

        <?php
        $elements = array(
            'body' => array('selector' => 'body', 'defaults' => ['family' => 'Inter', 'size' => 16, 'weight' => '400', 'transform' => 'none']),
            'h1'   => array('selector' => 'h1, .h1, .single-post-title, .page-title', 'defaults' => ['family' => 'DM Sans', 'size' => 40, 'weight' => '700', 'transform' => 'none']),
            'h2'   => array('selector' => 'h2, .h2, .widget-title', 'defaults' => ['family' => 'DM Sans', 'size' => 32, 'weight' => '700', 'transform' => 'none']),
            'h3'   => array('selector' => 'h3, .h3, .article-title', 'defaults' => ['family' => 'DM Sans', 'size' => 28, 'weight' => '700', 'transform' => 'none']),
            'h4'   => array('selector' => 'h4, .h4', 'defaults' => ['family' => 'DM Sans', 'size' => 24, 'weight' => '700', 'transform' => 'none']),
            'h5'   => array('selector' => 'h5, .h5', 'defaults' => ['family' => 'DM Sans', 'size' => 20, 'weight' => '700', 'transform' => 'none']),
            'h6'   => array('selector' => 'h6, .h6', 'defaults' => ['family' => 'DM Sans', 'size' => 18, 'weight' => '700', 'transform' => 'none']),
        );

        foreach ($elements as $id => $props) {
            $family = get_theme_mod("gizmodotech_{$id}_font_family", $props['defaults']['family']);
            $size = get_theme_mod("gizmodotech_{$id}_font_size", $props['defaults']['size']);
            $weight = get_theme_mod("gizmodotech_{$id}_font_weight", $props['defaults']['weight']);
            $transform = get_theme_mod("gizmodotech_{$id}_text_transform", $props['defaults']['transform']);

            echo "{$props['selector']} {\n";
            if ($family) echo "    font-family: '{$family}', sans-serif;\n";
            if ($size) echo "    font-size: {$size}px;\n";
            if ($weight) echo "    font-weight: {$weight};\n";
            if ($transform) echo "    text-transform: {$transform};\n";
            echo "}\n\n";
        }

        // Base font size for rem units
        $base_size = get_theme_mod("gizmodotech_body_font_size", 16);
        echo "html { font-size: {$base_size}px; }\n";
        ?>
    </style>
    <?php
}
add_action('wp_head', 'gizmodotech_customizer_css');

/**
 * Helper function to get a list of Google Fonts.
 */
function gizmodotech_get_google_fonts() {
    return array(
        'DM Sans'          => 'DM Sans',
        'Inter'            => 'Inter',
        'Roboto'           => 'Roboto',
        'Open Sans'        => 'Open Sans',
        'Lato'             => 'Lato',
        'Montserrat'       => 'Montserrat',
        'Poppins'          => 'Poppins',
        'Source Sans Pro'  => 'Source Sans Pro',
        'Oswald'           => 'Oswald',
        'Raleway'          => 'Raleway',
        'Nunito'           => 'Nunito',
        'Merriweather'     => 'Merriweather',
        'Playfair Display' => 'Playfair Display',
        'PT Serif'         => 'PT Serif',
    );
}

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

    // Inline Script for functionality
    $output .= '<script>
    jQuery(document).ready(function($) {
        const thumbnails = $(".extracted-images-grid .thumbnail img");
        const imageDisplay = $("#image-display"); 

        thumbnails.on("click", function() {
            const fullImageSrc = $(this).data("full-image");
            imageDisplay.html("<img src=\'" + fullImageSrc + "\' alt=\'Full Image\'>");
        });
    });
    </script>';

    // Inline CSS for basic styling
    $output .= '<style>
        .extracted-images-wrapper { margin: 2rem 0; }
        .image-display { margin-bottom: 1rem; border-radius: 8px; overflow: hidden; }
        .image-display img { width: 100%; height: auto; display: block; }
        .extracted-images-grid { display: flex; gap: 10px; overflow-x: auto; padding-bottom: 10px; }
        .extracted-images-grid .thumbnail { flex: 0 0 80px; cursor: pointer; border: 2px solid transparent; border-radius: 4px; overflow: hidden; }
        .extracted-images-grid .thumbnail:hover { border-color: var(--color-primary); }
        .extracted-images-grid .thumbnail img { width: 100%; height: 60px; object-fit: cover; display: block; }
    </style>';

    return $output;
}
add_shortcode('extracted_images', 'gizmodotech_display_extracted_images');

/**
 * Remove images from post content on 'mobile' post type single views.
 * This prevents images from showing twice (in the gallery and in the content).
 */
function gizmodotech_strip_images_from_content($content) {
    if (is_singular('mobile') && in_the_loop() && is_main_query()) {
        $content = preg_replace('/<img[^>]+>/i', '', $content);
    }
    return $content;
}
add_filter('the_content', 'gizmodotech_strip_images_from_content', 20);

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
