<?php
/**
 * Gizmodotech Theme Functions
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

// Prevent direct file access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define theme constants
 */
define( 'GIZMODOTECH_VERSION', '1.0.0' );
define( 'GIZMODOTECH_THEME_DIR', get_template_directory() );
define( 'GIZMODOTECH_THEME_URI', get_template_directory_uri() );

/**
 * Theme Setup
 */
function gizmodotech_theme_setup() {
	
	// Add default posts and comments RSS feed links to head
	add_theme_support( 'automatic-feed-links' );

	// Let WordPress manage the document title
	add_theme_support( 'title-tag' );

	// Enable support for Post Thumbnails
	add_theme_support( 'post-thumbnails' );

	// Add custom image sizes
	add_image_size( 'gizmodotech-featured', 1200, 675, true );
	add_image_size( 'gizmodotech-large', 800, 450, true );
	add_image_size( 'gizmodotech-medium', 600, 400, true );
	add_image_size( 'gizmodotech-small', 400, 300, true );

	// Register navigation menus
	register_nav_menus( array(
		'primary' => esc_html__( 'Primary Menu', 'gizmodotech' ),
		'footer'  => esc_html__( 'Footer Menu', 'gizmodotech' ),
	) );

	// Switch default core markup to output valid HTML5
	add_theme_support( 'html5', array(
		'search-form',
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
		'style',
		'script',
	) );

	// Add theme support for selective refresh for widgets
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Add support for custom logo
	add_theme_support( 'custom-logo', array(
		'height'      => 100,
		'width'       => 400,
		'flex-width'  => true,
		'flex-height' => true,
	) );

	// Add support for editor styles
	add_theme_support( 'editor-styles' );
	add_editor_style( 'style.css' );

	// Add support for responsive embeds
	add_theme_support( 'responsive-embeds' );

	// Add support for wide and full alignment
	add_theme_support( 'align-wide' );

	// Load translation files
	load_theme_textdomain( 'gizmodotech', GIZMODOTECH_THEME_DIR . '/languages' );
}
add_action( 'after_setup_theme', 'gizmodotech_theme_setup' );

/**
 * Set content width
 */
function gizmodotech_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'gizmodotech_content_width', 1200 );
}
add_action( 'after_setup_theme', 'gizmodotech_content_width', 0 );

/**
 * Register widget areas
 */
function gizmodotech_widgets_init() {
	
	// Sidebar
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'gizmodotech' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'gizmodotech' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );

	// Footer widgets
	for ( $i = 1; $i <= 4; $i++ ) {
		register_sidebar( array(
			'name'          => sprintf( esc_html__( 'Footer %d', 'gizmodotech' ), $i ),
			'id'            => 'footer-' . $i,
			'description'   => sprintf( esc_html__( 'Footer widget area %d', 'gizmodotech' ), $i ),
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'gizmodotech_widgets_init' );

/**
 * Enqueue scripts and styles
 */
function gizmodotech_enqueue_scripts() {
	
	// Google Fonts
	wp_enqueue_style( 
		'gizmodotech-google-fonts',
		'https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=DM+Sans:wght@400;500;700&display=swap',
		array(),
		null
	);

	// Main stylesheet
	wp_enqueue_style( 
		'gizmodotech-style', 
		get_stylesheet_uri(),
		array(),
		GIZMODOTECH_VERSION
	);

	// Main JavaScript
	wp_enqueue_script( 
		'gizmodotech-scripts', 
		get_template_directory_uri() . '/assets/js/main.js',
		array( 'jquery' ),
		GIZMODOTECH_VERSION,
		true
	);

	// Comment reply script
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Localize script for AJAX
	wp_localize_script( 'gizmodotech-scripts', 'gizmodotech_vars', array(
		'ajax_url' => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'gizmodotech_nonce' ),
	) );
}
add_action( 'wp_enqueue_scripts', 'gizmodotech_enqueue_scripts' );

/**
 * Add custom body classes
 */
function gizmodotech_body_classes( $classes ) {
	
	// Add dark mode class if enabled
	if ( isset( $_COOKIE['dark_mode'] ) && $_COOKIE['dark_mode'] === 'true' ) {
		$classes[] = 'dark-mode';
	}

	// Add class if no sidebar
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'gizmodotech_body_classes' );

/**
 * Custom excerpt length
 */
function gizmodotech_excerpt_length( $length ) {
	return 30;
}
add_filter( 'excerpt_length', 'gizmodotech_excerpt_length', 999 );

/**
 * Custom excerpt more text
 */
function gizmodotech_excerpt_more( $more ) {
	return '...';
}
add_filter( 'excerpt_more', 'gizmodotech_excerpt_more' );

/**
 * Add post views counter
 */
function gizmodotech_set_post_views( $post_id ) {
	$count_key = 'post_views_count';
	$count = get_post_meta( $post_id, $count_key, true );
	if ( $count == '' ) {
		$count = 0;
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
	} else {
		$count++;
		update_post_meta( $post_id, $count_key, $count );
	}
}

function gizmodotech_get_post_views( $post_id ) {
	$count_key = 'post_views_count';
	$count = get_post_meta( $post_id, $count_key, true );
	if ( $count == '' ) {
		delete_post_meta( $post_id, $count_key );
		add_post_meta( $post_id, $count_key, '0' );
		return "0 Views";
	}
	return $count . ' Views';
}

/**
 * Calculate reading time
 */
function gizmodotech_reading_time() {
	$content = get_post_field( 'post_content', get_the_ID() );
	$word_count = str_word_count( strip_tags( $content ) );
	$reading_time = ceil( $word_count / 200 );
	
	return $reading_time . ' min read';
}

/**
 * Add SVG support to media library
 */
function gizmodotech_mime_types( $mimes ) {
	$mimes['svg'] = 'image/svg+xml';
	return $mimes;
}
add_filter( 'upload_mimes', 'gizmodotech_mime_types' );

/**
 * Sanitize SVG uploads
 */
function gizmodotech_fix_svg() {
	echo '<style type="text/css">
		.attachment-266x266, .thumbnail img {
			width: 100% !important;
			height: auto !important;
		}
	</style>';
}
add_action( 'admin_head', 'gizmodotech_fix_svg' );

/**
 * Add security headers
 */
function gizmodotech_security_headers() {
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'X-XSS-Protection: 1; mode=block' );
}
add_action( 'send_headers', 'gizmodotech_security_headers' );

/**
 * Remove WordPress version from head
 */
remove_action( 'wp_head', 'wp_generator' );

/**
 * Disable XML-RPC
 */
add_filter( 'xmlrpc_enabled', '__return_false' );

/**
 * Pagination function
 */
function gizmodotech_pagination() {
	if ( is_singular() ) {
		return;
	}

	global $wp_query;

	if ( $wp_query->max_num_pages <= 1 ) {
		return;
	}

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max = intval( $wp_query->max_num_pages );

	if ( $paged >= 1 ) {
		$links[] = $paged;
	}

	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="pagination"><ul>' . "\n";

	if ( get_previous_posts_link() ) {
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( '← Previous' ) );
	}

	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) ) {
			echo '<li>…</li>';
		}
	}

	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) ) {
			echo '<li>…</li>' . "\n";
		}

		$class = $paged == $max ? ' class="active"' : '';
		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	if ( get_next_posts_link() ) {
		printf( '<li>%s</li>' . "\n", get_next_posts_link( 'Next →' ) );
	}

	echo '</ul></div>' . "\n";
}

/**
 * Customizer settings
 */
function gizmodotech_customize_register( $wp_customize ) {
	
	// Dark Mode Toggle
	$wp_customize->add_section( 'gizmodotech_dark_mode', array(
		'title'    => __( 'Dark Mode', 'gizmodotech' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'dark_mode_toggle', array(
		'default'           => true,
		'sanitize_callback' => 'gizmodotech_sanitize_checkbox',
	) );

	$wp_customize->add_control( 'dark_mode_toggle', array(
		'label'    => __( 'Enable Dark Mode Toggle', 'gizmodotech' ),
		'section'  => 'gizmodotech_dark_mode',
		'type'     => 'checkbox',
	) );

	// Social Media Links
	$wp_customize->add_section( 'gizmodotech_social', array(
		'title'    => __( 'Social Media Links', 'gizmodotech' ),
		'priority' => 40,
	) );

	$social_links = array(
		'facebook'  => 'Facebook',
		'twitter'   => 'Twitter',
		'instagram' => 'Instagram',
		'youtube'   => 'YouTube',
		'linkedin'  => 'LinkedIn',
	);

	foreach ( $social_links as $key => $label ) {
		$wp_customize->add_setting( 'social_' . $key, array(
			'default'           => '',
			'sanitize_callback' => 'esc_url_raw',
		) );

		$wp_customize->add_control( 'social_' . $key, array(
			'label'    => $label . ' ' . __( 'URL', 'gizmodotech' ),
			'section'  => 'gizmodotech_social',
			'type'     => 'url',
		) );
	}
}
add_action( 'customize_register', 'gizmodotech_customize_register' );

/**
 * Sanitize checkbox
 */
function gizmodotech_sanitize_checkbox( $checked ) {
	return ( ( isset( $checked ) && true == $checked ) ? true : false );
}

/**
 * Include custom template parts
 */
require_once GIZMODOTECH_THEME_DIR . '/inc/template-tags.php';
require_once GIZMODOTECH_THEME_DIR . '/inc/template-functions.php';
