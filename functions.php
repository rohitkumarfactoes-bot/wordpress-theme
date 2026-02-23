<?php
/**
 * Gizmodotech Pro — functions.php
 * Yoast SEO compatible · No duplicate JSON-LD · Clean head
 *
 * @package gizmodotech-pro
 */

defined('ABSPATH') || exit;

/* ── Constants ── */
define('GIZMO_VERSION', wp_get_theme()->get('Version') ?: '1.0.0');
define('GIZMO_DIR',     get_template_directory());
define('GIZMO_URI',     get_template_directory_uri());
define('GIZMO_ASSETS',  GIZMO_URI . '/assets');
define('GIZMO_TEXT',    'gizmodotech-pro');

/* ============================================================
   THEME SETUP
   ============================================================ */
add_action('after_setup_theme', 'gizmo_setup');
function gizmo_setup() {
	load_theme_textdomain(GIZMO_TEXT, GIZMO_DIR . '/languages');

	add_theme_support('title-tag');
	add_theme_support('post-thumbnails');
	add_theme_support('automatic-feed-links');
	add_theme_support('html5', ['search-form','comment-form','comment-list','gallery','caption','style','script']);
	add_theme_support('customize-selective-refresh-widgets');
	add_theme_support('align-wide');
	add_theme_support('responsive-embeds');
	add_theme_support('wp-block-styles');
	add_theme_support('editor-styles');
	add_theme_support('appearance-tools');

	// Thumbnail sizes
	add_image_size('gizmo-hero',  1200, 630, true);
	add_image_size('gizmo-card',  600,  400, true);
	add_image_size('gizmo-thumb', 400,  267, true);
	add_image_size('gizmo-wide',  1920, 600, true);

	register_nav_menus([
		'primary'   => __('Primary Navigation', GIZMO_TEXT),
		'secondary' => __('Secondary / Categories', GIZMO_TEXT),
		'footer'    => __('Footer Menu', GIZMO_TEXT),
		'mobile'    => __('Mobile Menu', GIZMO_TEXT),
	]);
}

/* ============================================================
   ENQUEUE ASSETS
   ============================================================ */
add_action('wp_enqueue_scripts', 'gizmo_enqueue');
function gizmo_enqueue() {
	wp_enqueue_style('gizmo-style', get_stylesheet_uri(), [], GIZMO_VERSION);

	wp_enqueue_style('gizmo-main', GIZMO_ASSETS . '/css/main.css',
		['gizmo-style'], GIZMO_VERSION);

	wp_enqueue_script('gizmo-app', GIZMO_ASSETS . '/js/app.js',
		[], GIZMO_VERSION, ['strategy' => 'defer', 'in_footer' => true]);

	wp_localize_script('gizmo-app', 'GizmoData', [
		'ajaxUrl' => admin_url('admin-ajax.php'),
		'nonce'   => wp_create_nonce('gizmo_nonce'),
		'i18n'    => [
			'copied'  => __('Copied!', GIZMO_TEXT),
			'minRead' => __('min read', GIZMO_TEXT),
		],
	]);

	if (is_singular() && comments_open()) {
		wp_enqueue_script('comment-reply');
	}

	gizmo_enqueue_google_fonts();
}

/* Enqueue Google Fonts based on Customizer selection */
function gizmo_enqueue_google_fonts() {
	$body_font = get_theme_mod('body_font_family', "'Inter', sans-serif");
	$head_font = get_theme_mod('heading_font_family', "'Inter', sans-serif");

	$fonts = [];
	foreach ([$body_font, $head_font] as $font) {
		if (strpos($font, 'System') !== false) continue;
		if (preg_match("/'([^']+)'/", $font, $m)) {
			$fonts[] = $m[1];
		}
	}
	$fonts = array_unique($fonts);
	if (empty($fonts)) return;

	$font_families = [];
	foreach ($fonts as $font) {
		$font_families[] = 'family=' . urlencode( trim( str_replace( ["'", '"', ", sans-serif", ", serif"], "", $font ) ) ) . ':wght@300;400;500;600;700;800;900';
	}

	$query_args = implode('&', $font_families);
	wp_enqueue_style('gizmo-google-fonts', 'https://fonts.googleapis.com/css2?' . $query_args . '&display=swap', [], null);
}

/* Editor styles */
add_action('enqueue_block_editor_assets', function() {
	if (file_exists(GIZMO_DIR . '/assets/css/editor.css')) {
		wp_enqueue_style('gizmo-editor', GIZMO_ASSETS . '/css/editor.css', [], GIZMO_VERSION);
	}
});

/* ============================================================
   HEAD CLEANUP
   ============================================================ */
add_action('init', 'gizmo_head_cleanup');
function gizmo_head_cleanup() {
	// Emojis
	remove_action('wp_head',             'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles',     'print_emoji_styles');
	remove_action('admin_print_styles',  'print_emoji_styles');
	remove_filter('the_content_feed',    'wp_staticize_emoji');
	remove_filter('comment_text_rss',    'wp_staticize_emoji');
	remove_filter('wp_mail',             'wp_staticize_emoji_for_email');

	// Junk tags
	remove_action('wp_head', 'wp_generator');
	remove_action('wp_head', 'wp_shortlink_wp_head');
	remove_action('wp_head', 'wlwmanifest_link');
	remove_action('wp_head', 'rsd_link');
	remove_action('wp_head', 'rest_output_link_wp_head');
	remove_action('wp_head', 'wp_oembed_add_discovery_links');

	add_filter('xmlrpc_enabled', '__return_false');
}

/* Dequeue block library CSS (not needed for custom theme) */
add_action('wp_enqueue_scripts', function() {
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('classic-theme-styles');
}, 100);

/* ============================================================
   YOAST SEO COMPATIBILITY
   - Disable our own JSON-LD if Yoast is active (Yoast handles it)
   - Remove Yoast's duplicate og: tags if desired
   ============================================================ */
function gizmo_yoast_active(): bool {
	return defined('WPSEO_VERSION') || class_exists('WPSEO_Frontend');
}

// Remove our schema output when Yoast is active
// (Yoast outputs its own Article/WebPage schema)
add_action('wp_head', 'gizmo_output_schema', 5);
function gizmo_output_schema() {
	// Skip if Yoast SEO is active — it handles all schema
	if (gizmo_yoast_active()) {
		return;
	}
	// Skip if RankMath is active
	if (class_exists('RankMath')) {
		return;
	}

	if (!is_single()) {
		return;
	}

	global $post;
	$reading   = gizmo_get_reading_time($post->ID);
	$cats      = get_the_category($post->ID);
	$thumb     = get_the_post_thumbnail_url($post->ID, 'gizmo-hero');
	$author    = get_the_author_meta('display_name', $post->post_author);
	$author_url= get_author_posts_url($post->post_author);

	$schema = [
		'@context'       => 'https://schema.org',
		'@type'          => 'TechArticle',
		'headline'       => get_the_title(),
		'description'    => wp_strip_all_tags(get_the_excerpt()),
		'url'            => get_permalink(),
		'datePublished'  => get_the_date('c'),
		'dateModified'   => get_the_modified_date('c'),
		'wordCount'      => $reading['words'],
		'timeRequired'   => 'PT' . $reading['minutes'] . 'M',
		'articleSection' => $cats ? $cats[0]->name : '',
		'image'          => $thumb ?: '',
		'publisher'      => ['@type' => 'Organization', 'name' => get_bloginfo('name')],
		'author'         => ['@type' => 'Person', 'name' => $author, 'url' => $author_url],
		'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => get_permalink()],
	];

	printf('<script type="application/ld+json">%s</script>',
		wp_json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
}

/* ============================================================
   READING TIME
   ============================================================ */
function gizmo_get_reading_time(int $post_id = 0, int $wpm = 238): array {
	if (!$post_id) $post_id = (int) get_the_ID();
	$content = wp_strip_all_tags(get_post_field('post_content', $post_id));
	$words   = str_word_count($content);
	$mins    = max(1, (int) ceil($words / $wpm));
	return [
		'minutes' => $mins,
		'words'   => $words,
		'label'   => sprintf(_n('%d min read', '%d min read', $mins, GIZMO_TEXT), $mins),
	];
}

/* ============================================================
   BREADCRUMBS
   ============================================================ */
function gizmodotech_breadcrumbs(bool $echo = true): string {
	// Use Yoast breadcrumbs if available
	if (gizmo_yoast_active() && function_exists('yoast_breadcrumb')) {
		ob_start();
		yoast_breadcrumb('<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', GIZMO_TEXT) . '"><ol class="breadcrumb__list">', '</ol></nav>');
		$out = ob_get_clean();
		if ($echo) { echo $out; } // phpcs:ignore
		return $out;
	}

	// Custom breadcrumbs
	$sep   = '<span class="breadcrumb__sep" aria-hidden="true">›</span>';
	$parts = [];
	$parts[] = sprintf('<li class="breadcrumb__item"><a href="%s">%s</a></li>',
		esc_url(home_url('/')), esc_html__('Home', GIZMO_TEXT));

	if (is_category() || is_single()) {
		$cats = get_the_category();
		if ($cats) {
			$parents = get_category_parents($cats[0]->term_id, true, null, false);
			if ($parents && !is_wp_error($parents)) {
				foreach (array_filter(explode('&raquo;', $parents)) as $link) {
					$parts[] = '<li class="breadcrumb__item">' . trim($link) . '</li>';
				}
			}
		}
	}

	if (is_single()) {
		$parts[] = sprintf('<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html(get_the_title()));
	} elseif (is_category()) {
		$parts[] = sprintf('<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html(single_cat_title('', false)));
	} elseif (is_page()) {
		$parts[] = sprintf('<li class="breadcrumb__item breadcrumb__item--current" aria-current="page"><span>%s</span></li>',
			esc_html(get_the_title()));
	}

	$out  = '<nav class="breadcrumb" aria-label="' . esc_attr__('Breadcrumb', GIZMO_TEXT) . '">';
	$out .= '<ol class="breadcrumb__list">' . implode($sep, $parts) . '</ol>';
	$out .= '</nav>';

	if ($echo) { echo $out; } // phpcs:ignore
	return $out;
}

/* ============================================================
   WIDGETS
   ============================================================ */
add_action('widgets_init', function() {
	$args = [
		'before_widget' => '<div id="%1$s" class="widget %2$s">',
		'after_widget'  => '</div>',
		'before_title'  => '<h3 class="widget__title">',
		'after_title'   => '</h3>',
	];
	register_sidebar(array_merge($args, ['name' => __('Sidebar', GIZMO_TEXT), 'id' => 'sidebar-1']));
	register_sidebar(array_merge($args, ['name' => __('Below Post', GIZMO_TEXT), 'id' => 'below-post']));
});

/* ============================================================
   CUSTOMIZER — Social URLs + Colors + Typography
   ============================================================ */
add_action('customize_register', 'gizmo_customizer');
function gizmo_customizer(WP_Customize_Manager $wp_customize) {

	/* ── Homepage Panel ── */
	$wp_customize->add_panel('gizmo_homepage_panel', [
		'title'    => __('Homepage Content', GIZMO_TEXT),
		'priority' => 28,
	]);

	// Get public post types for selects (Moved up to be available for all sections)
	$post_types = get_post_types(['public' => true], 'objects');
	$post_type_choices = [];
	foreach ($post_types as $post_type) {
		$post_type_choices[$post_type->name] = $post_type->labels->singular_name;
	}

	/* ── Homepage Content Selection ── */
	$wp_customize->add_section('gizmo_homepage_content', [
		'title'    => __('Homepage Rows (News, How-To, Tips)', GIZMO_TEXT),
		'panel'    => 'gizmo_homepage_panel',
	]);

	// Latest News Row
	$wp_customize->add_setting('gizmo_news_post_type', ['default' => 'post', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_news_post_type', [
		'label'   => __('"Latest News" Post Type', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'select',
		'choices' => $post_type_choices,
	]);
	
	$wp_customize->add_setting('gizmo_news_count', ['default' => 6, 'sanitize_callback' => 'absint']);
	$wp_customize->add_control('gizmo_news_count', [
		'label'   => __('Number of News Posts', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'number',
	]);

	$wp_customize->add_setting('gizmo_news_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_news_categories', [
		'label'       => __('"Latest News" Categories (if Post Type is Post)', GIZMO_TEXT),
		'description' => __('Select categories for the dark "Latest News" strip.', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_content',
	]));

	// How-To Section
	$wp_customize->add_setting('gizmo_howto_title', ['default' => "How To's", 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_howto_title', [
		'label'   => __('"How To" Section Title', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'text',
	]);
	$wp_customize->add_setting('gizmo_howto_post_type', ['default' => 'post', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_howto_post_type', [
		'label'   => __('"How To" Post Type', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'select',
		'choices' => $post_type_choices,
	]);
	$wp_customize->add_setting('gizmo_howto_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_howto_categories', [
		'label'       => __('"How To" Categories', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_content',
	]));

	// Tech Tips Section
	$wp_customize->add_setting('gizmo_techtips_title', ['default' => "Tech Tips", 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_techtips_title', [
		'label'   => __('"Tech Tips" Section Title', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'text',
	]);
	$wp_customize->add_setting('gizmo_techtips_post_type', ['default' => 'post', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_techtips_post_type', [
		'label'   => __('"Tech Tips" Post Type', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'select',
		'choices' => $post_type_choices,
	]);
	$wp_customize->add_setting('gizmo_techtips_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_techtips_categories', [
		'label'       => __('"Tech Tips" Categories', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_content',
	]));

	/* ── Homepage Slider Section ── */
	$wp_customize->add_section('gizmo_homepage_slider', [
		'title'    => __('Featured Slider Section', GIZMO_TEXT),
		'panel'    => 'gizmo_homepage_panel',
		'priority' => 20,
	]);

	$wp_customize->add_setting('gizmo_slider_section_enabled', [
		'default'           => false,
		'sanitize_callback' => 'wp_validate_boolean',
	]);
	$wp_customize->add_control('gizmo_slider_section_enabled', [
		'label'   => __('Enable Featured Slider Section', GIZMO_TEXT),
		'section' => 'gizmo_homepage_slider',
		'type'    => 'checkbox',
	]);

	$wp_customize->add_setting('gizmo_slider_title', ['default' => 'Latest', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_slider_title', [
		'label'   => __('Slider Section Title (Left)', GIZMO_TEXT),
		'section' => 'gizmo_homepage_slider',
		'type'    => 'text',
	]);

	$wp_customize->add_setting('gizmo_slider_post_type', [
		'default'           => 'post',
		'sanitize_callback' => 'sanitize_key',
	]);
	$wp_customize->add_control('gizmo_slider_post_type', [
		'label'   => __('Slider Post Type (Left)', GIZMO_TEXT),
		'section' => 'gizmo_homepage_slider',
		'type'    => 'select',
		'choices' => $post_type_choices,
		'active_callback' => function() { return get_theme_mod('gizmo_slider_section_enabled', false); },
	]);

	$wp_customize->add_setting('gizmo_slider_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_slider_categories', [
		'label'       => __('Slider Categories', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_slider',
	]));

	$wp_customize->add_setting('gizmo_slider_posts_count', [
		'default'           => 6,
		'sanitize_callback' => 'absint',
	]);
	$wp_customize->add_control('gizmo_slider_posts_count', [
		'label'       => __('Number of Posts in Slider', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_slider',
		'type'        => 'number',
		'input_attrs' => ['min' => 3, 'max' => 15],
		'active_callback' => function() { return get_theme_mod('gizmo_slider_section_enabled', false); },
	]);

	$wp_customize->add_setting('gizmo_reviews_title', ['default' => 'Reviews', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_reviews_title', [
		'label'   => __('Right Column Title', GIZMO_TEXT),
		'section' => 'gizmo_homepage_slider',
		'type'    => 'text',
	]);

	$wp_customize->add_setting('gizmo_reviews_post_type', ['default' => 'reviews', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_reviews_post_type', [
		'label'   => __('Right Column Post Type', GIZMO_TEXT),
		'section' => 'gizmo_homepage_slider',
		'type'    => 'select',
		'choices' => $post_type_choices,
	]);

	$wp_customize->add_setting('gizmo_reviews_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_reviews_categories', [
		'label'       => __('Right Column Categories', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_slider',
	]));

	$wp_customize->add_setting('gizmo_horizontal_posts_count', [
		'default'           => 3,
		'sanitize_callback' => 'absint',
	]);
	$wp_customize->add_control('gizmo_horizontal_posts_count', [
		'label'       => __('Number of Horizontal Cards (Right)', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_slider',
		'type'        => 'number',
		'input_attrs' => ['min' => 1, 'max' => 5],
		'active_callback' => function() { return get_theme_mod('gizmo_slider_section_enabled', false); },
	]);

	/* ── Social URLs ── */
	$wp_customize->add_section('gizmo_socials', [
		'title'    => __('Social Media URLs', GIZMO_TEXT),
		'priority' => 30,
	]);
	$socials = [
		'gizmo_facebook'  => 'Facebook URL',
		'gizmo_twitter'   => 'X / Twitter URL',
		'gizmo_instagram' => 'Instagram URL',
		'gizmo_youtube'   => 'YouTube URL',
		'gizmo_linkedin'  => 'LinkedIn URL',
	];
	foreach ($socials as $id => $label) {
		$wp_customize->add_setting($id, ['default' => '#', 'sanitize_callback' => 'esc_url_raw', 'transport' => 'refresh']);
		$wp_customize->add_control($id, ['label' => __($label, GIZMO_TEXT), 'section' => 'gizmo_socials', 'type' => 'url']);
	}

	/* ── Colors ── */
	$wp_customize->add_section('gizmo_colors', ['title' => __('Gizmodotech Colors', GIZMO_TEXT), 'priority' => 10]);
	$colors = [
		'primary_color'   => ['Primary Blue',       '#2563EB'],
		'accent_color'    => ['Accent / Amber',      '#F59E0B'],
		'nav_bg'          => ['Navbar Background',   '#FFFFFF'],
		'footer_bg'       => ['Footer Background',   '#0F172A'],
	];
	foreach ($colors as $id => [$label, $default]) {
		$wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'refresh']);
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, ['label' => __($label, GIZMO_TEXT), 'section' => 'gizmo_colors']));
	}

	/* ── Global Typography ── */
	$wp_customize->add_panel('gizmo_typo_panel', ['title' => __('Global Typography', GIZMO_TEXT), 'priority' => 20]);

	// Body font
	$wp_customize->add_section('gizmo_body_typo', ['title' => __('Body Font', GIZMO_TEXT), 'panel' => 'gizmo_typo_panel']);
	gizmo_add_font_control($wp_customize, 'body_font_family', 'gizmo_body_typo', __('Font Family', GIZMO_TEXT), "'Inter', sans-serif");
	gizmo_add_px_control($wp_customize,   'body_font_size',   'gizmo_body_typo', __('Font Size (px)', GIZMO_TEXT), 16, 12, 22);
	gizmo_add_weight_control($wp_customize,'body_font_weight', 'gizmo_body_typo', __('Font Weight', GIZMO_TEXT), '400');
	gizmo_add_num_control($wp_customize,  'body_line_height', 'gizmo_body_typo', __('Line Height', GIZMO_TEXT), 1.75, 1.2, 2.2, 0.05);

	// Heading font
	$wp_customize->add_section('gizmo_heading_typo', ['title' => __('Headings (H1-H6)', GIZMO_TEXT), 'panel' => 'gizmo_typo_panel']);
	gizmo_add_font_control($wp_customize, 'heading_font_family',   'gizmo_heading_typo', __('Font Family', GIZMO_TEXT), "'Inter', sans-serif");
	gizmo_add_weight_control($wp_customize,'heading_font_weight',  'gizmo_heading_typo', __('Font Weight', GIZMO_TEXT), '800');
	gizmo_add_num_control($wp_customize,  'heading_line_height',   'gizmo_heading_typo', __('Line Height', GIZMO_TEXT), 1.2, 1.0, 1.8, 0.05);

	// Heading Sizes (H1-H6)
	gizmo_add_px_control($wp_customize, 'h1_size', 'gizmo_heading_typo', 'H1 Size (px)', 40, 20, 80);
	gizmo_add_px_control($wp_customize, 'h2_size', 'gizmo_heading_typo', 'H2 Size (px)', 32, 18, 60);
	gizmo_add_px_control($wp_customize, 'h3_size', 'gizmo_heading_typo', 'H3 Size (px)', 26, 16, 50);
	gizmo_add_px_control($wp_customize, 'h4_size', 'gizmo_heading_typo', 'H4 Size (px)', 22, 14, 40);
	gizmo_add_px_control($wp_customize, 'h5_size', 'gizmo_heading_typo', 'H5 Size (px)', 18, 12, 30);
	gizmo_add_px_control($wp_customize, 'h6_size', 'gizmo_heading_typo', 'H6 Size (px)', 16, 10, 24);

	// Layout widths
	$wp_customize->add_section('gizmo_layout', ['title' => __('Layout Widths', GIZMO_TEXT), 'priority' => 25]);
	gizmo_add_px_control($wp_customize, 'content_width', 'gizmo_layout', __('Content Width (px)', GIZMO_TEXT), 800, 600, 1200);
	gizmo_add_px_control($wp_customize, 'wide_width',    'gizmo_layout', __('Wide Width (px)',    GIZMO_TEXT), 1320, 1000, 1920);
	gizmo_add_px_control($wp_customize, 'card_radius',   'gizmo_layout', __('Card Border Radius (px)', GIZMO_TEXT), 16, 0, 32);
}

/* Customizer helpers */
function gizmo_add_font_control($wpc, $id, $section, $label, $default) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
	
	// Expanded Google Fonts List
	$fonts = [
		"'Inter', sans-serif"         => 'Inter',
		"'Roboto', sans-serif"        => 'Roboto',
		"'Open Sans', sans-serif"     => 'Open Sans',
		"'Lato', sans-serif"          => 'Lato',
		"'Montserrat', sans-serif"    => 'Montserrat',
		"'Poppins', sans-serif"       => 'Poppins',
		"'Oswald', sans-serif"        => 'Oswald',
		"'Raleway', sans-serif"       => 'Raleway',
		"'Nunito', sans-serif"        => 'Nunito',
		"'Merriweather', serif"       => 'Merriweather',
		"'Playfair Display', serif"   => 'Playfair Display',
		"'Rubik', sans-serif"         => 'Rubik',
		"'Ubuntu', sans-serif"        => 'Ubuntu',
		"'Kanit', sans-serif"         => 'Kanit',
		"'PT Sans', sans-serif"       => 'PT Sans',
		"'Lora', serif"               => 'Lora',
		"'Work Sans', sans-serif"     => 'Work Sans',
		"'Mukta', sans-serif"         => 'Mukta',
		"'Quicksand', sans-serif"     => 'Quicksand',
		"'Barlow', sans-serif"        => 'Barlow',
		"'Mulish', sans-serif"        => 'Mulish',
		"'Titillium Web', sans-serif" => 'Titillium Web',
		"'Inconsolata', monospace"    => 'Inconsolata',
		"'IBM Plex Sans', sans-serif" => 'IBM Plex Sans',
		"'DM Sans', sans-serif"       => 'DM Sans',
		"'Crimson Text', serif"       => 'Crimson Text',
		"'Libre Baskerville', serif"  => 'Libre Baskerville',
		"'Anton', sans-serif"         => 'Anton',
		"'Josefin Sans', sans-serif"  => 'Josefin Sans',
		"'Bebas Neue', sans-serif"    => 'Bebas Neue',
		"'Fjalla One', sans-serif"    => 'Fjalla One',
		"'Cabin', sans-serif"         => 'Cabin',
		"'Bitter', serif"             => 'Bitter',
		"'Hind', sans-serif"          => 'Hind',
		"'Arvo', serif"               => 'Arvo',
		"'Noto Sans', sans-serif"     => 'Noto Sans',
		"'Noto Serif', serif"         => 'Noto Serif',
		"-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" => 'System UI',
	];
	
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'select', 'choices' => $fonts]);
}
function gizmo_add_weight_control($wpc, $id, $section, $label, $default) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'select', 'choices' => ['400'=>'400 Regular','500'=>'500 Medium','600'=>'600 SemiBold','700'=>'700 Bold','800'=>'800 ExtraBold','900'=>'900 Black']]);
}
function gizmo_add_px_control($wpc, $id, $section, $label, $default, $min = 0, $max = 2000) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'absint', 'transport' => 'refresh']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'number', 'input_attrs' => ['min' => $min, 'max' => $max, 'step' => 1]]);
}
function gizmo_add_num_control($wpc, $id, $section, $label, $default, $min, $max, $step) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'refresh']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'number', 'input_attrs' => ['min' => $min, 'max' => $max, 'step' => $step]]);
}

/* Output Customizer CSS */
add_action('wp_head', 'gizmo_customizer_css', 1000); // High priority to override main.css
function gizmo_customizer_css() {
	$vars = [
		'--color-primary'      => get_theme_mod('primary_color', '#2563EB'),
		'--color-accent'       => get_theme_mod('accent_color', '#F59E0B'),
		'--bg-nav'             => get_theme_mod('nav_bg', '#FFFFFF'),
		'--bg-footer'          => get_theme_mod('footer_bg', '#0F172A'),
		'--font-sans'          => get_theme_mod('body_font_family', "'Inter', sans-serif"),
		'--font-size-base'     => get_theme_mod('body_font_size', 16) . 'px',
		'--font-weight-normal' => get_theme_mod('body_font_weight', '400'),
		'--line-height-normal' => get_theme_mod('body_line_height', 1.75),
		'--heading-font'       => get_theme_mod('heading_font_family', "'Inter', sans-serif"),
		'--heading-weight'     => get_theme_mod('heading_font_weight', '800'),
		'--heading-lh'         => get_theme_mod('heading_line_height', 1.2),
		'--width-content'      => get_theme_mod('content_width', 800) . 'px',
		'--width-wide'         => get_theme_mod('wide_width', 1320) . 'px',
		'--radius-lg'          => get_theme_mod('card_radius', 16) . 'px',
		'--h1'                 => get_theme_mod('h1_size', 40) . 'px',
		'--h2'                 => get_theme_mod('h2_size', 32) . 'px',
		'--h3'                 => get_theme_mod('h3_size', 26) . 'px',
		'--h4'                 => get_theme_mod('h4_size', 22) . 'px',
		'--h5'                 => get_theme_mod('h5_size', 18) . 'px',
		'--h6'                 => get_theme_mod('h6_size', 16) . 'px',
	];

	$css_rules = [];
	foreach ($vars as $key => $val) {
		// Do NOT use esc_attr() on values here, as it breaks font strings containing quotes (e.g. 'Inter')
		$css_rules[] = $key . ':' . $val;
	}

	$css  = ':root{' . implode(';', $css_rules) . '}';
	$css .= 'body{font-family:var(--font-sans) !important;font-size:var(--font-size-base);font-weight:var(--font-weight-normal);line-height:var(--line-height-normal);}';
	// Apply heading font family and weight to all heading elements and common heading classes with !important to ensure it overrides more specific selectors.
	$css .= 'h1,h2,h3,h4,h5,h6,.single-title,.post-card__title,.archive-title,.widget-title,.widget__title,.section-title,.news-card__title,.horizontal-card__title,.post-item-card__title{font-family:var(--heading-font) !important;font-weight:var(--heading-weight) !important;line-height:var(--heading-lh);}';
	
	// Apply heading sizes with !important to override more specific selectors.
	$css .= 'h1,.single-title,.archive-title{font-size:var(--h1) !important;}';
	$css .= 'h2,.post-card__title{font-size:var(--h2) !important;}';
	$css .= 'h3,.widget-title,.widget__title,.section-title,.author-box__name,.news-card__title,.horizontal-card__title,.post-item-card__title,.toc__title{font-size:var(--h3) !important;}';
	$css .= 'h4{font-size:var(--h4) !important;}';
	$css .= 'h5{font-size:var(--h5) !important;}';
	$css .= 'h6{font-size:var(--h6) !important;}';

	printf('<style id="gizmo-customizer">%s</style>', $css); // phpcs:ignore
}

/* ============================================================
   EXCERPT
   ============================================================ */
add_filter('excerpt_length', fn() => 22);
add_filter('excerpt_more',   fn() => '&hellip;');

/* ============================================================
   BODY CLASS — persist dark mode cookie
   ============================================================ */
add_filter('body_class', function(array $classes): array {
	if (!empty($_COOKIE['gizmo_theme']) && $_COOKIE['gizmo_theme'] === 'dark') {
		$classes[] = 'dark-mode';
	}
	return $classes;
});

/* ============================================================
   COMMENT FORM DEFAULTS
   ============================================================ */
add_filter('comment_form_defaults', function(array $defaults): array {
	$defaults['title_reply']       = __('Leave a Comment', GIZMO_TEXT);
	$defaults['label_submit']      = __('Post Comment', GIZMO_TEXT);
	$defaults['comment_notes_before'] = '';
	$defaults['class_submit']      = 'submit btn-primary';
	return $defaults;
});

/* ============================================================
   YOAST: Remove breadcrumbs from single.php if we render them
   (Prevents double breadcrumbs when using yoast_breadcrumb() in theme)
   ============================================================ */
// We call yoast_breadcrumb() directly in gizmodotech_breadcrumbs(), so
// we don't need to remove Yoast hooks — the theme controls placement.

/* ============================================================
   REGISTER BLOCK PATTERNS
   ============================================================ */
add_action('init', function() {
	if (!function_exists('register_block_pattern_category')) return;
	register_block_pattern_category('gizmodotech', ['label' => __('Gizmodotech Blocks', GIZMO_TEXT)]);

	foreach (['bento-grid','pros-cons','specs-table'] as $pattern) {
		$file = GIZMO_DIR . '/patterns/' . $pattern . '.php';
		if (file_exists($file)) {
			register_block_pattern('gizmodotech/' . $pattern, require $file);
		}
	}

	// Register "Post Layout" Block Pattern (Query Loop)
	register_block_pattern(
		'gizmodotech/post-grid',
		[
			'title'       => __('Gizmo Post Grid', GIZMO_TEXT),
			'categories'  => ['gizmodotech'],
			'blockTypes'  => ['core/query'],
			'inserter'    => true,
			'description' => __('A query loop that mimics the theme post card layout.', GIZMO_TEXT),
			'content'     => '
				<!-- wp:query {"query":{"perPage":3,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":3}} -->
				<div class="wp-block-query">
					<!-- wp:post-template -->
					<!-- wp:group {"style":{"border":{"width":"1px","radius":"16px"}},"borderColor":"border-color","backgroundColor":"bg-surface","layout":{"type":"default"}} -->
					<div class="wp-block-group has-border-color-border-color has-bg-surface-background-color has-text-color has-background" style="border-width:1px;border-radius:16px;overflow:hidden;margin-bottom:1.5rem;">
						<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","style":{"border":{"radius":"0px"}}} /-->
						<!-- wp:group {"style":{"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1.25rem","left":"1rem"}}},"layout":{"type":"default"}} -->
						<div class="wp-block-group" style="padding-top:1rem;padding-right:1rem;padding-bottom:1.25rem;padding-left:1rem">
							<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium"} /-->
							<!-- wp:post-excerpt {"moreText":"...","showMoreOnNewLine":false,"fontSize":"small"} /-->
							<!-- wp:post-date {"fontSize":"small","style":{"color":{"text":"#64748b"}}} /-->
						</div>
						<!-- /wp:group -->
					</div>
					<!-- /wp:group -->
					<!-- /wp:post-template -->
					<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"center"}} -->
					<!-- wp:query-pagination-numbers /-->
					<!-- /wp:query-pagination -->
				</div>
				<!-- /wp:query -->',
		]
	);

	register_block_pattern(
		'gizmodotech/featured-card',
		[
			'title'       => __('Featured Post Card', GIZMO_TEXT),
			'categories'  => ['gizmodotech'],
			'blockTypes'  => ['core/group'],
			'inserter'    => true,
			'description' => __('A single card to feature a post or link.', GIZMO_TEXT),
			'content'     => '
				<!-- wp:group {"style":{"border":{"width":"1px","radius":"16px"}},"borderColor":"border-color","backgroundColor":"bg-surface","layout":{"type":"default"}} -->
				<div class="wp-block-group has-border-color-border-color has-bg-surface-background-color has-text-color has-background" style="border-width:1px;border-radius:16px;overflow:hidden;">
					<!-- wp:image {"aspectRatio":"16/9","scale":"cover","sizeSlug":"large","linkDestination":"none"} -->
					<figure class="wp-block-image size-large"></figure>
					<!-- /wp:image -->
					<!-- wp:group {"style":{"spacing":{"padding":{"top":"1rem","right":"1rem","bottom":"1.25rem","left":"1rem"}}},"layout":{"type":"default"}} -->
					<div class="wp-block-group" style="padding-top:1rem;padding-right:1rem;padding-bottom:1.25rem;padding-left:1rem">
						<!-- wp:heading {"level":3,"style":{"typography":{"fontStyle":"normal","fontWeight":"700"}},"fontSize":"medium"} -->
						<h3 class="has-medium-font-size" style="font-style:normal;font-weight:700">Your Awesome Post Title</h3>
						<!-- /wp:heading -->
						<!-- wp:paragraph {"fontSize":"small"} -->
						<p class="has-small-font-size">This is a short excerpt describing the featured content. Make it catchy and informative to draw the reader in.</p>
						<!-- /wp:paragraph -->
						<!-- wp:buttons {"layout":{"type":"flex","justifyContent":"left"}} -->
						<div class="wp-block-buttons">
							<!-- wp:button {"backgroundColor":"primary","textColor":"white","style":{"border":{"radius":"8px"}}} -->
							<div class="wp-block-button"><a class="wp-block-button__link has-white-color has-primary-background-color has-text-color has-background" href="#" style="border-radius:8px">Read More</a></div>
							<!-- /wp:button -->
						</div>
						<!-- /wp-buttons -->
					</div>
					<!-- /wp:group -->
				</div>
				<!-- /wp:group -->',
		]
	);
});

/**
 * Customizer control for category checklist.
 */
if ( class_exists( 'WP_Customize_Control' ) ) {
	class Gizmo_Customize_Category_Checklist_Control extends WP_Customize_Control {
		public $type = 'category-checklist';

		public function render_content() {
			$categories = get_categories( [ 'hide_empty' => false ] );

			if ( empty( $categories ) ) {
				echo '<p>' . esc_html__( 'No categories found.', 'gizmodotech-pro' ) . '</p>';
				return;
			}

			$saved_values = explode( ',', $this->value() );
			?>
			<label>
				<span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
				<?php if ( ! empty( $this->description ) ) : ?>
					<span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
				<?php endif; ?>

				<ul class="gizmo-category-checklist">
					<?php foreach ( $categories as $category ) : ?>
						<li>
							<label>
								<input type="checkbox" class="gizmo-cat-checklist-item" value="<?php echo esc_attr( $category->term_id ); ?>" <?php checked( in_array( $category->term_id, $saved_values, true ) ); ?> />
								<?php echo esc_html( $category->name ); ?>
							</label>
						</li>
					<?php endforeach; ?>
				</ul>
				<input type="hidden" class="gizmo-cat-checklist-value" <?php $this->link(); ?> value="<?php echo esc_attr( $this->value() ); ?>" />
			</label>
			<?php
		}
	}
}

/**
 * Enqueue JS and CSS for the customizer category checklist control.
 */
add_action( 'customize_controls_print_footer_scripts', function() { ?>
	<style>.gizmo-category-checklist{background-color:#fff;border:1px solid #ddd;border-radius:3px;padding:5px;max-height:200px;overflow-y:auto;margin-top:5px}.gizmo-category-checklist li{margin:0}.gizmo-category-checklist label{display:block;padding:2px 4px}</style>
	<script type="text/javascript">
	jQuery(document).ready(function($){$('.customize-control-category-checklist').on('change','.gizmo-cat-checklist-item',function(){var e=$(this).closest('.customize-control-category-checklist'),t=e.find('.gizmo-cat-checklist-item:checked').map(function(){return this.value}).get().join(',');e.find('.gizmo-cat-checklist-value').val(t).trigger('change')})});
	</script>
<?php });

/* ============================================================
   AJAX: Load More
   ============================================================ */
add_action('wp_ajax_gizmo_load_more',        'gizmo_ajax_load_more');
add_action('wp_ajax_nopriv_gizmo_load_more', 'gizmo_ajax_load_more');
function gizmo_ajax_load_more() {
	check_ajax_referer('gizmo_nonce', 'nonce');
	$page = absint($_POST['page'] ?? 1);
	$cat  = absint($_POST['cat']  ?? 0);

	$q = new WP_Query([
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 9,
		'paged'          => $page,
		'cat'            => $cat ?: null,
	]);

	ob_start();
	while ($q->have_posts()) { $q->the_post(); get_template_part('template-parts/card','post'); }
	wp_reset_postdata();

	wp_send_json_success([
		'html'     => ob_get_clean(),
		'has_more' => $q->max_num_pages > $page,
	]);
}

/* ============================================================
   HELPER: Get Gallery Images (Featured + Content)
   ============================================================ */
function gizmo_get_gallery_images($post_id) {
	$images = [];

	// 1. Get Featured Image
	if (has_post_thumbnail($post_id)) {
		$images[] = get_the_post_thumbnail_url($post_id, 'full');
	}

	// 2. Extract images from content
	$content = get_post_field('post_content', $post_id);
	if (preg_match_all('/<img[^>]+src=[\'"]([^\'"]+)[\'"][^>]*>/i', $content, $matches)) {
		foreach ($matches[1] as $src) {
			$images[] = $src;
		}
	}

	return array_unique($images);
}

/* ============================================================
   HELPER: Display Post Categories with Colors
   ============================================================ */
function gizmo_get_category_color_class_from_id($category_id) {
	$colors = ['blue', 'green', 'amber', 'red', 'indigo'];
	$index = $category_id % count($colors);
	return 'post-cat-badge--' . $colors[$index];
}

function gizmo_the_post_categories($post_id = null, $class = 'post-cat-badge') {
	if (null === $post_id) {
		$post_id = get_the_ID();
	}
	$categories = get_the_category($post_id);
	if (empty($categories)) {
		return;
	}

	echo '<div class="post-cat-badges">';
	foreach ($categories as $category) {
		$color_class = gizmo_get_category_color_class_from_id($category->term_id);
		printf(
			'<a class="%s %s" href="%s">%s</a>',
			esc_attr($class),
			esc_attr($color_class),
			esc_url(get_category_link($category->term_id)),
			esc_html($category->name)
		);
	}
	echo '</div>';
}

/* ============================================================
   PAGE LAYOUT META BOX
   ============================================================ */
add_action('add_meta_boxes', 'gizmo_add_layout_meta_box');
function gizmo_add_layout_meta_box() {
	add_meta_box(
		'gizmo_page_layout',
		__('Page Layout', GIZMO_TEXT),
		'gizmo_render_layout_meta_box',
		'page',
		'side',
		'default'
	);
}

function gizmo_render_layout_meta_box($post) {
	$value = get_post_meta($post->ID, '_gizmo_page_layout', true) ?: 'sidebar';
	?>
	<label for="gizmo_page_layout" class="screen-reader-text"><?php esc_html_e('Select Layout', GIZMO_TEXT); ?></label>
	<select name="gizmo_page_layout" id="gizmo_page_layout" style="width:100%">
		<option value="sidebar" <?php selected($value, 'sidebar'); ?>><?php esc_html_e('Sidebar (Default)', GIZMO_TEXT); ?></option>
		<option value="no-sidebar" <?php selected($value, 'no-sidebar'); ?>><?php esc_html_e('No Sidebar (Original Width)', GIZMO_TEXT); ?></option>
		<option value="narrow" <?php selected($value, 'narrow'); ?>><?php esc_html_e('Narrow Width', GIZMO_TEXT); ?></option>
	</select>
	<?php
	wp_nonce_field('gizmo_save_layout', 'gizmo_layout_nonce');
}

add_action('save_post', 'gizmo_save_layout_meta');
function gizmo_save_layout_meta($post_id) {
	if (!isset($_POST['gizmo_layout_nonce']) || !wp_verify_nonce($_POST['gizmo_layout_nonce'], 'gizmo_save_layout')) return;
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
	if (!current_user_can('edit_post', $post_id)) return;

	if (isset($_POST['gizmo_page_layout'])) {
		update_post_meta($post_id, '_gizmo_page_layout', sanitize_key($_POST['gizmo_page_layout']));
	}
}

/* ============================================================
   SHORTCODES (Fallback for Blocks)
   ============================================================ */

/**
 * 1. Post Grid Shortcode
 * Usage: [gizmo_posts count="3" cat="5" type="post"]
 */
add_shortcode('gizmo_posts', 'gizmo_shortcode_posts');
function gizmo_shortcode_posts($atts) {
	$atts = shortcode_atts([
		'count' => 3,
		'cat'   => '',
		'type'  => 'post',
		'ids'   => '',
		'pagination' => false,
	], $atts);

	$pagination = filter_var($atts['pagination'], FILTER_VALIDATE_BOOLEAN);
	$paged      = 1;

	if ($pagination) {
 		if ( isset( $_GET['g_paged'] ) ) {
			$paged = absint( $_GET['g_paged'] );
		} else {
			$paged = (get_query_var('paged')) ? get_query_var('paged') : ((get_query_var('page')) ? get_query_var('page') : 1);
		}
	}

	$args = [
		'post_type'      => $atts['type'],
		'posts_per_page' => $atts['count'],
		'post_status'    => 'publish',
		'paged'          => $paged,
	];

	if (!empty($atts['cat'])) {
		$args['cat'] = $atts['cat'];
	}
	if (!empty($atts['ids'])) {
		$args['post__in'] = array_map('trim', explode(',', $atts['ids']));
		$args['orderby']  = 'post__in';
	}

	$q = new WP_Query($args);
	if (!$q->have_posts()) {
		// Show a placeholder in the editor if no posts are found
		if ( defined( 'REST_REQUEST' ) && REST_REQUEST ) {
			return '<div style="padding:2rem; text-align:center; background:var(--bg-surface-2); color:var(--text-muted); border:1px dashed var(--border-color); border-radius:8px;">' . sprintf( __( 'No posts found for type: <strong>%s</strong>.<br>Check if posts exist and are published.', GIZMO_TEXT ), esc_html( $atts['type'] ) ) . '</div>';
		}
		return '';
	}

	ob_start();
	echo '<div class="posts-grid">';
	while ($q->have_posts()) {
		$q->the_post();
		$read = function_exists('gizmo_get_reading_time') ? gizmo_get_reading_time(get_the_ID()) : ['label'=>''];
		?>
		<article <?php post_class('post-card'); ?>>
			<?php if (has_post_thumbnail()) : ?>
			<a class="post-card__thumb" href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail('gizmo-card', ['loading'=>'lazy', 'alt' => esc_attr(get_the_title())]); ?>
			</a>
			<?php endif; ?>
			<div class="post-card__body">
				<?php if (function_exists('gizmo_the_post_categories')) gizmo_the_post_categories(get_the_ID()); ?>
				<h3 class="post-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
				<div class="post-card__meta">
					<span class="post-card__date"><?php echo get_the_date(); ?></span>
					<span class="post-card__sep">·</span>
					<span class="post-card__read"><?php echo esc_html($read['label']); ?></span>
				</div>
			</div>
		</article>
		<?php
	}
	echo '</div>';

	// Reset post data so get_permalink() returns the current page URL, not the last post's URL
	wp_reset_postdata();

	if ($pagination && $q->max_num_pages > 1) {
		echo '<nav class="posts-pagination">';
		echo paginate_links([
			'base'      => add_query_arg( 'g_paged', '%#%', get_permalink() ),
			'format'    => '',
			'current'   => max(1, $paged),
			'total'     => $q->max_num_pages,
			'prev_text' => '&#8592; ' . __('Prev', GIZMO_TEXT),
			'next_text' => __('Next', GIZMO_TEXT) . ' &#8594;',
		]);
		echo '</nav>';
	}

	return ob_get_clean();
}

/**
 * 2. Pros & Cons Shortcode
 * Usage: [gizmo_pros_cons pros="Good|Great" cons="Bad|Expensive"]
 */
add_shortcode('gizmo_pros_cons', 'gizmo_shortcode_review_box');
function gizmo_shortcode_review_box($atts) {
	$atts = shortcode_atts([
		'pros' => '',
		'cons' => '',
	], $atts);

	$pros = !empty($atts['pros']) ? explode('|', $atts['pros']) : [];
	$cons = !empty($atts['cons']) ? explode('|', $atts['cons']) : [];

	ob_start();
	?>
	<div class="pros-cons-main">
		<div class="pros">
			<h3><?php esc_html_e('Pros', GIZMO_TEXT); ?></h3>
			<ul>
				<?php foreach ($pros as $item) echo '<li>' . esc_html(trim($item)) . '</li>'; ?>
			</ul>
		</div>
		<div class="cons">
			<h3><?php esc_html_e('Cons', GIZMO_TEXT); ?></h3>
			<ul>
				<?php foreach ($cons as $item) echo '<li>' . esc_html(trim($item)) . '</li>'; ?>
			</ul>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

/* ============================================================
   REGISTER CUSTOM BLOCKS (JS + PHP)
   ============================================================ */

add_action('enqueue_block_editor_assets', 'gizmo_enqueue_editor_blocks');
function gizmo_enqueue_editor_blocks() {
	$file_path = GIZMO_DIR . '/assets/js/editor-blocks.js';
	$version   = file_exists($file_path) ? filemtime($file_path) : GIZMO_VERSION;

	wp_enqueue_script(
		'gizmo-editor-blocks',
		GIZMO_ASSETS . '/js/editor-blocks.js',
		['wp-blocks', 'wp-element', 'wp-components', 'wp-block-editor', 'wp-server-side-render'],
		$version,
		true
	);
}

add_action('init', 'gizmo_register_dynamic_blocks');
function gizmo_register_dynamic_blocks() {
	// 1. Post Grid Block
	register_block_type('gizmodotech/post-grid-block', [
		'render_callback' => 'gizmo_shortcode_posts', // Reuses the shortcode logic
		'attributes' => [
			'count' => ['type' => 'string', 'default' => '3'],
			'type'  => ['type' => 'string', 'default' => 'post'],
			'cat'   => ['type' => 'string', 'default' => ''],
			'pagination' => ['type' => 'boolean', 'default' => false],
		]
	]);

	// 2. Pros & Cons Block
	register_block_type('gizmodotech/pros-cons-block', [
		'render_callback' => 'gizmo_shortcode_review_box', // Reuses the shortcode logic
	]);
}

/* ============================================================
   COMMENT CALLBACK
   ============================================================ */
function gizmo_comment_callback($comment, $args, $depth) {
	$tag     = ( 'div' === $args['style'] ) ? 'div' : 'li';
	$classes = implode( ' ', get_comment_class( '', $comment ) );
	?>
	<<?php echo esc_attr( $tag ); ?> id="comment-<?php comment_ID(); ?>" class="comment <?php echo esc_attr( $classes ); ?>">
		<div class="comment__header">
			<?php echo get_avatar( $comment, 80, '', '', [ 'class' => 'comment__avatar' ] ); ?>
			<div>
				<div class="comment__author-name">
					<?php comment_author_link( $comment ); ?>
				</div>
				<time class="comment__date" datetime="<?php echo esc_attr( get_comment_date( 'c', $comment ) ); ?>">
					<?php echo esc_html( get_comment_date( '', $comment ) ); ?>
				</time>
			</div>
		</div>
		<div class="comment__body">
			<?php comment_text(); ?>
			<?php
			comment_reply_link( array_merge( $args, [
				'depth'     => $depth,
				'max_depth' => $args['max_depth'],
			] ) );
			?>
		</div>
	</<?php echo esc_attr( $tag ); ?>>
	<?php
}
