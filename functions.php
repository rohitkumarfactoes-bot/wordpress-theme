<?php
/**
 * Gizmodotech Pro — Functions
 *
 * @package gizmodotech-pro
 */

defined( 'ABSPATH' ) || exit;

/* ============================================================
   CONSTANTS
   ============================================================ */
define( 'GIZMO_VERSION',   wp_get_theme()->get( 'Version' ) );
define( 'GIZMO_DIR',       get_template_directory() );
define( 'GIZMO_URI',       get_template_directory_uri() );
define( 'GIZMO_ASSETS',    GIZMO_URI . '/assets' );
define( 'GIZMO_TEXT',      'gizmodotech-pro' );

/* ============================================================
   THEME SETUP
   ============================================================ */
add_action( 'after_setup_theme', 'gizmo_setup' );
function gizmo_setup() {
	load_theme_textdomain( GIZMO_TEXT, GIZMO_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'html5', [
		'search-form', 'comment-form', 'comment-list',
		'gallery', 'caption', 'style', 'script',
	] );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'block-templates' );
	add_theme_support( 'editor-styles' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'appearance-tools' );

	/* Custom thumbnail sizes */
	add_image_size( 'gizmo-hero',     1200, 675, true );  /* 16:9 hero     */
	add_image_size( 'gizmo-card',     600,  400, true );  /* card thumb    */
	add_image_size( 'gizmo-thumb',    400,  267, true );  /* small thumb   */
	add_image_size( 'gizmo-wide',     1920, 600, true );  /* banner        */

	register_nav_menus( [
		'primary'   => __( 'Primary Navigation', GIZMO_TEXT ),
		'secondary' => __( 'Secondary / Categories', GIZMO_TEXT ),
		'footer'    => __( 'Footer Menu', GIZMO_TEXT ),
		'mobile'    => __( 'Mobile Menu', GIZMO_TEXT ),
	] );
}

/* ============================================================
   ENQUEUE SCRIPTS & STYLES
   ============================================================ */
add_action( 'wp_enqueue_scripts', 'gizmo_enqueue_assets' );
function gizmo_enqueue_assets() {

	/* Main stylesheet (loads CSS variables from style.css) */
	wp_enqueue_style(
		'gizmo-style',
		get_stylesheet_uri(),
		[],
		GIZMO_VERSION
	);

	/* Main CSS */
	wp_enqueue_style(
		'gizmo-main',
		GIZMO_ASSETS . '/css/main.css',
		[ 'gizmo-style' ],
		GIZMO_VERSION
	);

	/* Critical inline CSS for above-the-fold */
	wp_add_inline_style( 'gizmo-style', gizmo_get_critical_css() );

	/* App JavaScript (deferred) */
	wp_enqueue_script(
		'gizmo-app',
		GIZMO_ASSETS . '/js/app.js',
		[],
		GIZMO_VERSION,
		[ 'strategy' => 'defer', 'in_footer' => true ]
	);

	/* Pass PHP data to JS */
	wp_localize_script( 'gizmo-app', 'GizmoData', [
		'ajaxUrl'  => admin_url( 'admin-ajax.php' ),
		'nonce'    => wp_create_nonce( 'gizmo_nonce' ),
		'themeUri' => GIZMO_URI,
		'isRTL'    => is_rtl() ? 'true' : 'false',
		'i18n'     => [
			'darkMode'  => __( 'Dark Mode',  GIZMO_TEXT ),
			'lightMode' => __( 'Light Mode', GIZMO_TEXT ),
			'minRead'   => __( 'min read',   GIZMO_TEXT ),
			'share'     => __( 'Share',      GIZMO_TEXT ),
			'copied'    => __( 'Copied!',    GIZMO_TEXT ),
		],
	] );

	/* Comment reply script */
	if ( is_singular() && comments_open() ) {
		wp_enqueue_script( 'comment-reply' );
	}
}

/* ============================================================
   EDITOR ASSETS
   ============================================================ */
add_action( 'enqueue_block_editor_assets', 'gizmo_editor_assets' );
function gizmo_editor_assets() {
	wp_enqueue_style(
		'gizmo-editor',
		GIZMO_ASSETS . '/css/editor.css',
		[],
		GIZMO_VERSION
	);
}

/* ============================================================
   HEAD CLEANUP — Remove Bloat
   ============================================================ */
add_action( 'init', 'gizmo_head_cleanup' );
function gizmo_head_cleanup() {
	/* WordPress emojis */
	remove_action( 'wp_head',             'print_emoji_detection_script', 7 );
	remove_action( 'admin_print_scripts', 'print_emoji_detection_script'    );
	remove_action( 'wp_print_styles',     'print_emoji_styles'               );
	remove_action( 'admin_print_styles',  'print_emoji_styles'               );
	remove_filter( 'the_content_feed',    'wp_staticize_emoji'               );
	remove_filter( 'comment_text_rss',    'wp_staticize_emoji'               );
	remove_filter( 'wp_mail',            'wp_staticize_emoji_for_email'      );

	/* Classic block library (not needed for block theme) */
	add_filter( 'should_load_separate_core_block_assets', '__return_true' );

	/* Gutenberg block CSS on front-end */
	remove_action( 'wp_enqueue_scripts', 'wp_enqueue_global_styles' );
	add_action( 'wp_enqueue_scripts', function() {
		wp_enqueue_global_styles();
	}, 1 );

	/* XML-RPC */
	add_filter( 'xmlrpc_enabled', '__return_false' );

	/* REST API from <head> */
	remove_action( 'wp_head', 'rest_output_link_wp_head' );
	remove_action( 'wp_head', 'wp_oembed_add_discovery_links' );

	/* Generator tag */
	remove_action( 'wp_head', 'wp_generator' );

	/* Shortlink */
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );

	/* WLW manifest */
	remove_action( 'wp_head', 'wlwmanifest_link' );

	/* RSD link */
	remove_action( 'wp_head', 'rsd_link' );
}

/* ============================================================
   DISABLE BLOCK LIBRARY CSS
   ============================================================ */
add_action( 'wp_enqueue_scripts', 'gizmo_dequeue_block_styles', 100 );
function gizmo_dequeue_block_styles() {
	wp_dequeue_style( 'wp-block-library' );
	wp_dequeue_style( 'wp-block-library-theme' );
	wp_dequeue_style( 'wc-blocks-style' );     // WooCommerce blocks
}

/* ============================================================
   READING TIME CALCULATOR
   ============================================================ */
/**
 * Calculate estimated reading time for a post.
 *
 * @param int    $post_id        Post ID (defaults to current post).
 * @param int    $words_per_min  Average reading speed (default: 238 wpm).
 * @return array                 ['minutes' => int, 'label' => string]
 */
function gizmo_get_reading_time( int $post_id = 0, int $words_per_min = 238 ): array {
	if ( ! $post_id ) {
		$post_id = get_the_ID();
	}

	$content   = get_post_field( 'post_content', $post_id );
	$content   = wp_strip_all_tags( $content );
	$word_count = str_word_count( $content );
	$minutes   = max( 1, (int) ceil( $word_count / $words_per_min ) );

	return [
		'minutes' => $minutes,
		'words'   => $word_count,
		/* translators: %d: number of minutes */
		'label'   => sprintf( _n( '%d min read', '%d min read', $minutes, GIZMO_TEXT ), $minutes ),
	];
}

/* ============================================================
   BREADCRUMBS
   ============================================================ */
/**
 * Output SEO-friendly breadcrumbs.
 *
 * @param bool $echo  Whether to echo (true) or return (false) output.
 * @return string|void
 */
function gizmodotech_breadcrumbs( bool $echo = true ) {
	$sep   = '<span class="breadcrumb__sep" aria-hidden="true">/</span>';
	$parts = [];

	$parts[] = sprintf(
		'<li class="breadcrumb__item"><a href="%s">%s</a></li>',
		esc_url( home_url( '/' ) ),
		esc_html__( 'Home', GIZMO_TEXT )
	);

	if ( is_category() || is_single() ) {
		$cat = get_the_category();
		if ( $cat ) {
			$parents = get_category_parents( $cat[0]->term_id, true, null, false );
			if ( $parents && ! is_wp_error( $parents ) ) {
				$cat_links = explode( '&raquo;', $parents );
				foreach ( array_filter( $cat_links ) as $link ) {
					$parts[] = '<li class="breadcrumb__item">' . trim( $link ) . '</li>';
				}
			}
		}
	}

	if ( is_single() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html( get_the_title() )
		);
	} elseif ( is_category() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html( single_cat_title( '', false ) )
		);
	} elseif ( is_page() ) {
		if ( wp_get_post_parent_id( null ) ) {
			$parent_id = wp_get_post_parent_id( null );
			$parts[]   = sprintf(
				'<li class="breadcrumb__item"><a href="%s">%s</a></li>',
				esc_url( get_permalink( $parent_id ) ),
				esc_html( get_the_title( $parent_id ) )
			);
		}
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html( get_the_title() )
		);
	} elseif ( is_tag() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s %s</span></li>',
			esc_html__( 'Tag:', GIZMO_TEXT ),
			esc_html( single_tag_title( '', false ) )
		);
	} elseif ( is_author() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s %s</span></li>',
			esc_html__( 'Author:', GIZMO_TEXT ),
			esc_html( get_the_author() )
		);
	} elseif ( is_search() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s "%s"</span></li>',
			esc_html__( 'Search Results for:', GIZMO_TEXT ),
			esc_html( get_search_query() )
		);
	} elseif ( is_archive() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html( get_the_archive_title() )
		);
	} elseif ( is_404() ) {
		$parts[] = sprintf(
			'<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html__( '404 — Not Found', GIZMO_TEXT )
		);
	}

	$output  = '<nav class="breadcrumb" aria-label="' . esc_attr__( 'Breadcrumb', GIZMO_TEXT ) . '">';
	$output .= '<ol class="breadcrumb__list">';
	$output .= implode( $sep, $parts );
	$output .= '</ol></nav>';

	if ( $echo ) {
		echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	}

	return $output;
}

/* ============================================================
   WIDGET AREAS
   ============================================================ */
add_action( 'widgets_init', 'gizmo_register_sidebars' );
function gizmo_register_sidebars() {
	$shared = [
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	];

	register_sidebar( array_merge( $shared, [
		'name'        => __( 'Sidebar', GIZMO_TEXT ),
		'id'          => 'sidebar-1',
		'description' => __( 'Main sidebar widget area.', GIZMO_TEXT ),
	] ) );

	register_sidebar( array_merge( $shared, [
		'name'        => __( 'Footer — Column 1', GIZMO_TEXT ),
		'id'          => 'footer-1',
	] ) );

	register_sidebar( array_merge( $shared, [
		'name'        => __( 'Footer — Column 2', GIZMO_TEXT ),
		'id'          => 'footer-2',
	] ) );

	register_sidebar( array_merge( $shared, [
		'name'        => __( 'Footer — Column 3', GIZMO_TEXT ),
		'id'          => 'footer-3',
	] ) );

	register_sidebar( array_merge( $shared, [
		'name'        => __( 'Below Post', GIZMO_TEXT ),
		'id'          => 'below-post',
		'description' => __( 'Displays below single post content.', GIZMO_TEXT ),
	] ) );
}

/* ============================================================
   CUSTOMIZER — Global Typography & Layout Controls
   ============================================================ */
add_action( 'customize_register', 'gizmo_customizer' );
function gizmo_customizer( WP_Customize_Manager $wp_customize ) {

	/* ── Panel: Global Typography ── */
	$wp_customize->add_panel( 'gizmo_typography_panel', [
		'title'       => __( 'Global Typography', GIZMO_TEXT ),
		'description' => __( 'Control font families, sizes, weights, and line heights across the entire site.', GIZMO_TEXT ),
		'priority'    => 10,
	] );

	/* Section: Body Font */
	$wp_customize->add_section( 'gizmo_body_font', [
		'title'  => __( 'Body Typography', GIZMO_TEXT ),
		'panel'  => 'gizmo_typography_panel',
	] );

	gizmo_add_font_family_control( $wp_customize, 'body_font_family',   'gizmo_body_font',    __( 'Body Font Family', GIZMO_TEXT ),    "'Inter', sans-serif" );
	gizmo_add_font_size_control(   $wp_customize, 'body_font_size',     'gizmo_body_font',    __( 'Body Font Size (px)', GIZMO_TEXT ),  16, 10, 24 );
	gizmo_add_font_weight_control( $wp_customize, 'body_font_weight',   'gizmo_body_font',    __( 'Body Font Weight', GIZMO_TEXT ),     '400' );
	gizmo_add_number_control(      $wp_customize, 'body_line_height',   'gizmo_body_font',    __( 'Body Line Height', GIZMO_TEXT ),     1.75, 1.0, 2.5, 0.05 );

	/* Section: Heading Font */
	$wp_customize->add_section( 'gizmo_heading_font', [
		'title'  => __( 'Heading Typography', GIZMO_TEXT ),
		'panel'  => 'gizmo_typography_panel',
	] );

	gizmo_add_font_family_control( $wp_customize, 'heading_font_family',   'gizmo_heading_font', __( 'Heading Font Family', GIZMO_TEXT ),   "'Inter', sans-serif" );
	gizmo_add_font_weight_control( $wp_customize, 'heading_font_weight',   'gizmo_heading_font', __( 'Heading Font Weight', GIZMO_TEXT ),    '800' );
	gizmo_add_number_control(      $wp_customize, 'heading_line_height',   'gizmo_heading_font', __( 'Heading Line Height', GIZMO_TEXT ),    1.15, 0.9, 1.8, 0.05 );
	gizmo_add_number_control(      $wp_customize, 'heading_letter_spacing','gizmo_heading_font', __( 'Heading Letter Spacing (em)', GIZMO_TEXT ), -0.025, -0.1, 0.1, 0.005 );

	/* Section: Mono Font */
	$wp_customize->add_section( 'gizmo_mono_font', [
		'title'  => __( 'Code / Mono Typography', GIZMO_TEXT ),
		'panel'  => 'gizmo_typography_panel',
	] );

	gizmo_add_font_family_control( $wp_customize, 'mono_font_family', 'gizmo_mono_font', __( 'Mono Font Family', GIZMO_TEXT ), "'JetBrains Mono', monospace" );
	gizmo_add_font_size_control(   $wp_customize, 'mono_font_size',   'gizmo_mono_font', __( 'Code Font Size (px)', GIZMO_TEXT ), 14, 10, 20 );

	/* ── Panel: Site Layout ── */
	$wp_customize->add_panel( 'gizmo_layout_panel', [
		'title'    => __( 'Site Layout', GIZMO_TEXT ),
		'priority' => 15,
	] );

	$wp_customize->add_section( 'gizmo_layout', [
		'title' => __( 'Content Width', GIZMO_TEXT ),
		'panel' => 'gizmo_layout_panel',
	] );

	gizmo_add_number_control( $wp_customize, 'content_width', 'gizmo_layout', __( 'Article Content Width (px)', GIZMO_TEXT ),  800, 600, 1200, 10 );
	gizmo_add_number_control( $wp_customize, 'wide_width',    'gizmo_layout', __( 'Wide / Full Layout Width (px)', GIZMO_TEXT ), 1320, 1000, 1920, 10 );

	/* ── Panel: Colors ── */
	$wp_customize->add_section( 'gizmo_colors', [
		'title'    => __( 'Gizmodotech Colors', GIZMO_TEXT ),
		'priority' => 20,
	] );

	$colors = [
		'primary_color'       => [ __( 'Primary Blue',  GIZMO_TEXT ), '#2563EB' ],
		'accent_color'        => [ __( 'Accent / Amber', GIZMO_TEXT ), '#F59E0B' ],
		'nav_bg_color'        => [ __( 'Navbar Background', GIZMO_TEXT ), '#FFFFFF' ],
		'footer_bg_color'     => [ __( 'Footer Background', GIZMO_TEXT ), '#0F172A' ],
		'link_color'          => [ __( 'Link Color',    GIZMO_TEXT ), '#2563EB' ],
		'card_radius'         => false, // handled separately
	];

	foreach ( $colors as $id => $args ) {
		if ( ! $args ) { continue; }
		$wp_customize->add_setting( $id, [ 'default' => $args[1], 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage' ] );
		$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, $id, [
			'label'   => $args[0],
			'section' => 'gizmo_colors',
		] ) );
	}

	/* Card Radius */
	gizmo_add_number_control( $wp_customize, 'card_radius', 'gizmo_colors', __( 'Card Border Radius (px)', GIZMO_TEXT ), 16, 0, 32, 1 );
}

/* ── Customizer Helper: Font Family ── */
function gizmo_add_font_family_control( $wpc, $id, $section, $label, $default ) {
	$wpc->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
	$wpc->add_control( $id, [
		'label'   => $label,
		'section' => $section,
		'type'    => 'select',
		'choices' => [
			"'Inter', sans-serif"            => 'Inter (Default)',
			"'Roboto', sans-serif"           => 'Roboto',
			"'Open Sans', sans-serif"        => 'Open Sans',
			"'Lato', sans-serif"             => 'Lato',
			"'Poppins', sans-serif"          => 'Poppins',
			"'Nunito', sans-serif"           => 'Nunito',
			"'Merriweather', serif"          => 'Merriweather (Serif)',
			"Georgia, serif"                 => 'Georgia (Serif)',
			"'JetBrains Mono', monospace"    => 'JetBrains Mono',
			"-apple-system, sans-serif"      => 'System UI',
		],
	] );
}

/* ── Customizer Helper: Font Size ── */
function gizmo_add_font_size_control( $wpc, $id, $section, $label, $default, $min, $max ) {
	$wpc->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'absint', 'transport' => 'postMessage' ] );
	$wpc->add_control( $id, [
		'label'       => $label,
		'section'     => $section,
		'type'        => 'number',
		'input_attrs' => [ 'min' => $min, 'max' => $max, 'step' => 1 ],
	] );
}

/* ── Customizer Helper: Font Weight ── */
function gizmo_add_font_weight_control( $wpc, $id, $section, $label, $default ) {
	$wpc->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
	$wpc->add_control( $id, [
		'label'   => $label,
		'section' => $section,
		'type'    => 'select',
		'choices' => [
			'100' => '100 — Thin',
			'200' => '200 — ExtraLight',
			'300' => '300 — Light',
			'400' => '400 — Regular',
			'500' => '500 — Medium',
			'600' => '600 — SemiBold',
			'700' => '700 — Bold',
			'800' => '800 — ExtraBold',
			'900' => '900 — Black',
		],
	] );
}

/* ── Customizer Helper: Number ── */
function gizmo_add_number_control( $wpc, $id, $section, $label, $default, $min, $max, $step ) {
	$wpc->add_setting( $id, [ 'default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage' ] );
	$wpc->add_control( $id, [
		'label'       => $label,
		'section'     => $section,
		'type'        => 'number',
		'input_attrs' => [ 'min' => $min, 'max' => $max, 'step' => $step ],
	] );
}

/* ============================================================
   OUTPUT CUSTOMIZER CSS INTO <head>
   ============================================================ */
add_action( 'wp_head', 'gizmo_customizer_css', 99 );
function gizmo_customizer_css() {
	$vars = [
		'--font-sans'          => gizmo_get_mod( 'body_font_family',      "'Inter', sans-serif" ),
		'--font-size-base'     => gizmo_get_mod( 'body_font_size',         16 ) . 'px',
		'--font-weight-normal' => gizmo_get_mod( 'body_font_weight',       '400' ),
		'--line-height-normal' => gizmo_get_mod( 'body_line_height',       1.75 ),
		'--heading-font'       => gizmo_get_mod( 'heading_font_family',   "'Inter', sans-serif" ),
		'--heading-weight'     => gizmo_get_mod( 'heading_font_weight',    '800' ),
		'--heading-lh'         => gizmo_get_mod( 'heading_line_height',    1.15 ),
		'--heading-ls'         => gizmo_get_mod( 'heading_letter_spacing', '-0.025' ) . 'em',
		'--font-mono'          => gizmo_get_mod( 'mono_font_family',      "'JetBrains Mono', monospace" ),
		'--width-content'      => gizmo_get_mod( 'content_width',          800 ) . 'px',
		'--width-wide'         => gizmo_get_mod( 'wide_width',             1320 ) . 'px',
		'--color-primary'      => gizmo_get_mod( 'primary_color',         '#2563EB' ),
		'--color-accent'       => gizmo_get_mod( 'accent_color',          '#F59E0B' ),
		'--bg-nav'             => gizmo_get_mod( 'nav_bg_color',          '#FFFFFF' ),
		'--bg-footer'          => gizmo_get_mod( 'footer_bg_color',       '#0F172A' ),
		'--color-link'         => gizmo_get_mod( 'link_color',            '#2563EB' ),
		'--radius-lg'          => gizmo_get_mod( 'card_radius',            16 ) . 'px',
	];

	$css = ':root{';
	foreach ( $vars as $prop => $val ) {
		$css .= esc_attr( $prop ) . ':' . esc_attr( $val ) . ';';
	}
	$css .= '}';

	/* Apply heading font to h1-h6 */
	$css .= 'h1,h2,h3,h4,h5,h6{font-family:var(--heading-font,var(--font-sans));font-weight:var(--heading-weight);line-height:var(--heading-lh);letter-spacing:var(--heading-ls);}';

	printf( '<style id="gizmo-customizer-css">%s</style>', $css ); // phpcs:ignore
}

/* ── Helper to get theme_mod ── */
function gizmo_get_mod( string $id, $default ) {
	return get_theme_mod( $id, $default );
}

/* ============================================================
   CRITICAL CSS (above-the-fold inline)
   ============================================================ */
function gizmo_get_critical_css(): string {
	return '
.site-header{display:flex;align-items:center;height:64px;background:var(--bg-nav);border-bottom:1px solid var(--border-color);position:sticky;top:0;z-index:300;}
.progress-bar{position:fixed;top:0;left:0;width:0;height:3px;background:var(--color-primary);z-index:9999;transition:width .1s linear;}
.site-header__logo{font-weight:800;font-size:1.375rem;color:var(--text-primary);}
';
}

/* ============================================================
   SEO: JSON-LD TECH ARTICLE SCHEMA
   ============================================================ */
add_action( 'wp_head', 'gizmo_jsonld_schema', 5 );
function gizmo_jsonld_schema() {
	if ( ! is_single() ) { return; }

	global $post;
	$categories  = get_the_category( $post->ID );
	$cat_name    = $categories ? $categories[0]->name : '';
	$reading     = gizmo_get_reading_time( $post->ID );
	$thumb_url   = get_the_post_thumbnail_url( $post->ID, 'gizmo-hero' );
	$author      = get_the_author_meta( 'display_name', $post->post_author );
	$author_url  = get_author_posts_url( $post->post_author );

	$schema = [
		'@context'         => 'https://schema.org',
		'@type'            => 'TechArticle',
		'headline'         => get_the_title(),
		'description'      => get_the_excerpt(),
		'url'              => get_permalink(),
		'datePublished'    => get_the_date( 'c' ),
		'dateModified'     => get_the_modified_date( 'c' ),
		'wordCount'        => $reading['words'],
		'timeRequired'     => 'PT' . $reading['minutes'] . 'M',
		'articleSection'   => $cat_name,
		'image'            => $thumb_url ?: '',
		'publisher'        => [
			'@type' => 'Organization',
			'name'  => get_bloginfo( 'name' ),
			'logo'  => [
				'@type' => 'ImageObject',
				'url'   => GIZMO_URI . '/assets/img/logo.png',
			],
		],
		'author'           => [
			'@type' => 'Person',
			'name'  => $author,
			'url'   => $author_url,
		],
		'mainEntityOfPage' => [
			'@type' => 'WebPage',
			'@id'   => get_permalink(),
		],
	];

	printf(
		'<script type="application/ld+json">%s</script>',
		wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
	);
}

/* ============================================================
   EXCERPT
   ============================================================ */
add_filter( 'excerpt_length', fn() => 25 );
add_filter( 'excerpt_more',   fn() => '&hellip;' );

/* ============================================================
   CUSTOM POST CLASSES
   ============================================================ */
add_filter( 'post_class', 'gizmo_post_class' );
function gizmo_post_class( array $classes ): array {
	$classes[] = 'gizmo-post';
	return $classes;
}

/* ============================================================
   BODY CLASS — Dark Mode
   ============================================================ */
add_filter( 'body_class', 'gizmo_body_class' );
function gizmo_body_class( array $classes ): array {
	if ( isset( $_COOKIE['gizmo_theme'] ) && $_COOKIE['gizmo_theme'] === 'dark' ) {
		$classes[] = 'dark-mode';
	}
	return $classes;
}

/* ============================================================
   REGISTER BLOCK PATTERNS
   ============================================================ */
add_action( 'init', 'gizmo_register_block_patterns' );
function gizmo_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern_category' ) ) { return; }

	register_block_pattern_category( 'gizmodotech', [
		'label' => __( 'Gizmodotech Pro', GIZMO_TEXT ),
	] );

	$patterns = [ 'bento-grid', 'hero-section', 'specs-table', 'pros-cons', 'latest-news' ];

	foreach ( $patterns as $pattern ) {
		$file = GIZMO_DIR . '/patterns/' . $pattern . '.php';
		if ( file_exists( $file ) ) {
			register_block_pattern( 'gizmodotech/' . $pattern, require $file );
		}
	}
}

/* ============================================================
   SEARCH FORM
   ============================================================ */
add_filter( 'get_search_form', 'gizmo_search_form' );
function gizmo_search_form(): string {
	$action = esc_url( home_url( '/' ) );
	$label  = esc_attr__( 'Search for:', GIZMO_TEXT );
	$value  = esc_attr( get_search_query() );
	$btn    = esc_html__( 'Search', GIZMO_TEXT );

	return sprintf(
		'<form role="search" method="get" class="search-form" action="%s">
			<label class="screen-reader-text" for="search-field">%s</label>
			<input type="search" id="search-field" class="search-form__input" placeholder="%s" value="%s" name="s">
			<button type="submit" class="search-form__btn" aria-label="%s">
				<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
			</button>
		</form>',
		$action, $label,
		esc_attr__( 'Search…', GIZMO_TEXT ),
		$value, $btn
	);
}

/* ============================================================
   AJAX: Load More Posts
   ============================================================ */
add_action( 'wp_ajax_gizmo_load_more',        'gizmo_ajax_load_more' );
add_action( 'wp_ajax_nopriv_gizmo_load_more', 'gizmo_ajax_load_more' );
function gizmo_ajax_load_more() {
	check_ajax_referer( 'gizmo_nonce', 'nonce' );

	$page     = absint( $_POST['page'] ?? 1 );
	$category = absint( $_POST['cat']  ?? 0 );

	$query = new WP_Query( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => $page,
		'cat'            => $category ?: null,
	] );

	ob_start();
	while ( $query->have_posts() ) {
		$query->the_post();
		get_template_part( 'template-parts/card', 'post' );
	}
	wp_reset_postdata();
	$html = ob_get_clean();

	wp_send_json_success( [
		'html'     => $html,
		'has_more' => $query->max_num_pages > $page,
	] );
}
