<?php
/**
 * Gizmodotech Pro — functions.php
 * Yoast SEO compatible · No duplicate JSON-LD · Clean head
 *
 * @package gizmodotech-pro
 */

defined('ABSPATH') || exit;

// Intelephense ignore - WordPress functions
// @codingStandardsIgnoreFile

/* ── Constants ── */
define('GIZMO_VERSION', wp_get_theme()->get('Version') ?: '2.2.0');
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
	// Load frontend stylesheets into the editor for a true WYSIWYG experience.
	// Loads style.css (for variables), main.css (for components), and editor.css (for overrides).
	add_editor_style( ['style.css', 'assets/css/main.css', 'assets/css/editor.css'] );

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

/* Enqueue Google Fonts based on Customizer selection - Optimized */
function gizmo_enqueue_google_fonts() {
	$body_font = get_theme_mod('body_font_family', "'Inter', sans-serif");
	$head_font = get_theme_mod('heading_font_family', "'Inter', sans-serif");
	$body_font_weight = get_theme_mod('body_font_weight', '400');
	$heading_font_weight = get_theme_mod('heading_font_weight', '700');

	$fonts                = [];
	$font_weights         = [];
	$system_font_keywords = ['System', 'system-ui', '-apple-system', 'BlinkMacSystemFont', 'Segoe UI', 'Roboto', 'Helvetica', 'Arial'];

	foreach ([$body_font, $head_font] as $index => $font) {
		$is_system = false;
		foreach ($system_font_keywords as $keyword) {
			if (stripos($font, $keyword) !== false) {
				$is_system = true;
				break;
			}
		}
		if ($is_system) continue;

		// Extract font name from string like "'Inter', sans-serif" or "Inter, sans-serif"
		if (preg_match("/'?([^',]+)'?/", $font, $m)) {
			$font_name = trim($m[1]);
			if (!empty($font_name) && !in_array($font_name, $fonts)) {
				$fonts[] = $font_name;
				// Get specific weights for each font
				$weight = ($index === 0) ? $body_font_weight : $heading_font_weight;
				$font_weights[$font_name] = gizmo_get_font_weights_range($weight);
			}
		}
	}

	if (empty($fonts)) return;

	$font_families = [];
	foreach ($fonts as $font) {
		$weights = $font_weights[$font] ?? '300;400;500;600;700';
		$font_families[] = 'family=' . urlencode($font) . ':wght@' . $weights;
	}

	$query_args = implode('&', $font_families);
	
	// Preconnect to Google Fonts for faster loading
	add_action('wp_head', function() {
		echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
		echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
	}, 1);
	
	// Load with display=swap to prevent FOIT
	wp_enqueue_style('gizmo-google-fonts', 'https://fonts.googleapis.com/css2?' . $query_args . '&display=swap', [], null);
}

/**
 * Get optimized font weights range based on selected weight
 */
function gizmo_get_font_weights_range($base_weight) {
	$weight = intval($base_weight);
	// Load a range around the selected weight for flexibility
	$weights = [];
	for ($w = max(300, $weight - 200); $w <= min(900, $weight + 200); $w += 100) {
		$weights[] = $w;
	}
	return implode(';', $weights);
}

/* Editor styles */
add_action('enqueue_block_editor_assets', function() {
	// Enqueue Google Fonts in the editor to match the frontend.
	// The stylesheets are loaded via add_editor_style() in the setup function.
	gizmo_enqueue_google_fonts();
});

/* Customizer Live Preview */
add_action('customize_preview_init', function() {
	wp_enqueue_script(
		'gizmo-customizer-preview',
		GIZMO_ASSETS . '/js/customizer-preview.js',
		['customize-preview'],
		GIZMO_VERSION,
		true
	);
});

/* Customizer Controls - Conditional Fields */
add_action('customize_controls_enqueue_scripts', function() {
	wp_enqueue_script(
		'gizmo-customizer-controls',
		GIZMO_ASSETS . '/js/customizer-controls.js',
		['customize-controls'],
		GIZMO_VERSION,
		true
	);
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

/* ============================================================
   DISABLE CORE IMAGE LIGHTBOX
   Forcefully disables the "click to zoom" feature from WP 6.4+
   ============================================================ */
add_filter( 'block_core_image_render_lightbox', '__return_false', 999 );


/* Dequeue block library CSS (not needed for custom theme) */
add_action('wp_enqueue_scripts', function() {
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
	wp_dequeue_style('classic-theme-styles');
	wp_dequeue_script('core-image-lightbox'); // Force remove core lightbox script
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

	// Static cache so repeated calls in the same request don't re-query DB
	static $cache = [];
	if (isset($cache[$post_id])) {
		return $cache[$post_id];
	}

	$content = wp_strip_all_tags(get_post_field('post_content', $post_id));
	$words   = str_word_count($content);
	$mins    = max(1, (int) ceil($words / $wpm));

	$cache[$post_id] = [
		'minutes' => $mins,
		'words'   => $words,
		'label'   => sprintf(_n('%d min read', '%d min read', $mins, GIZMO_TEXT), $mins),
	];

	return $cache[$post_id];
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
	$out .= '<ol class="breadcrumb__list">' . implode('', $parts) . '</ol>';
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

	// Mobiles Section
	$wp_customize->add_setting('gizmo_mobiles_title', ['default' => "Mobiles", 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_mobiles_title', [
		'label'   => __('"Mobiles" Section Title', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'text',
	]);
	$wp_customize->add_setting('gizmo_mobiles_post_type', ['default' => 'post', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_mobiles_post_type', [
		'label'   => __('"Mobiles" Post Type', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'select',
		'choices' => $post_type_choices,
	]);
	$wp_customize->add_setting('gizmo_mobiles_filter_categories', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new Gizmo_Customize_Category_Checklist_Control($wp_customize, 'gizmo_mobiles_filter_categories', [
		'label'       => __('"Mobiles" Filter Categories', GIZMO_TEXT),
		'description' => __('Select categories to show as filters in the Mobiles section.', GIZMO_TEXT),
		'section'     => 'gizmo_homepage_content',
	]));
    $wp_customize->add_setting('gizmo_mobiles_count', ['default' => 8, 'sanitize_callback' => 'absint']);
	$wp_customize->add_control('gizmo_mobiles_count', [
		'label'   => __('Number of Mobiles to Show', GIZMO_TEXT),
		'section' => 'gizmo_homepage_content',
		'type'    => 'number',
	]);

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

	/* ── Amazon Affiliate Section ── */
	$wp_customize->add_section('gizmo_amazon_section', [
		'title'    => __('Amazon Affiliate', GIZMO_TEXT),
		'priority' => 45,
	]);

	$wp_customize->add_setting('gizmo_amazon_enabled', ['default' => false, 'sanitize_callback' => 'wp_validate_boolean']);
	$wp_customize->add_control('gizmo_amazon_enabled', [
		'label'   => __('Enable Amazon Sidebar Widget', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type'    => 'checkbox',
	]);

	// API Selection: Creators API vs PA API 5.0
	$wp_customize->add_setting('gizmo_amazon_api_type', ['default' => 'creators', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_amazon_api_type', [
		'label'   => __('Select Amazon API', GIZMO_TEXT),
		'description' => __('Choose which Amazon API to use for product search', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type'    => 'select',
		'choices' => [
			'creators' => __('Creators API (New - OAuth 2.0)', GIZMO_TEXT),
			'paapi5'   => __('PA API 5.0 (Legacy - AWS Signature)', GIZMO_TEXT),
		],
	]);

	// Credentials Section Header
	$wp_customize->add_setting('gizmo_amazon_creds_header', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control(new WP_Customize_Control($wp_customize, 'gizmo_amazon_creds_header', [
		'label'    => __('API Credentials', GIZMO_TEXT),
		'section'  => 'gizmo_amazon_section',
		'settings' => 'gizmo_amazon_creds_header',
		'type'     => 'hidden',
	]));

	$wp_customize->add_setting('gizmo_creators_client_id', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_creators_client_id', [
		'label' => __('Access Key / Credential ID', GIZMO_TEXT),
		'description' => __('For Creators API: Credential ID from Associates Central<br>For PA API 5.0: Access Key ID', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type' => 'text'
	]);

	$wp_customize->add_setting('gizmo_creators_client_secret', ['default' => '', 'sanitize_callback' => function($i){return trim($i);}]);
	$wp_customize->add_control('gizmo_creators_client_secret', [
		'label' => __('Secret Key / Credential Secret', GIZMO_TEXT),
		'description' => __('For Creators API: Credential Secret<br>For PA API 5.0: Secret Access Key', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type' => 'password'
	]);

	// Credential Version (only for Creators API)
	$wp_customize->add_setting('gizmo_amazon_credential_version', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_amazon_credential_version', [
		'label' => __('API Version (Creators API only)', GIZMO_TEXT),
		'description' => __('Enter version if shown in credentials (e.g., 3.2, 2.2). Leave empty for PA API 5.0.', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type' => 'text'
	]);

	$wp_customize->add_setting('gizmo_amazon_associate_tag', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_amazon_associate_tag', [
		'label'       => 'Associate Tag (Partner Tag)',
		'description' => 'Enter your Store ID here (e.g., yourname-21).',
		'section'     => 'gizmo_amazon_section',
		'type'        => 'text'
	]);

	$wp_customize->add_setting('gizmo_amazon_marketplace', ['default' => 'www.amazon.in', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_amazon_marketplace', [
		'label'   => __('Amazon Marketplace', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type'    => 'select',
		'choices' => [
			'www.amazon.in' => 'India (www.amazon.in)',
			'www.amazon.com' => 'USA (www.amazon.com)',
			'www.amazon.co.uk' => 'UK (www.amazon.co.uk)',
		],
	]);

	$wp_customize->add_setting('gizmo_amazon_title', ['default' => 'Buy on Amazon', 'sanitize_callback' => 'sanitize_text_field']);
	$wp_customize->add_control('gizmo_amazon_title', [
		'label'   => __('Widget Title', GIZMO_TEXT),
		'section' => 'gizmo_amazon_section',
		'type'    => 'text',
	]);

	/* ── Smart Advertisement System ── */
	$wp_customize->add_panel('gizmo_ads_panel', [
		'title'    => __('Advertisement Settings', GIZMO_TEXT),
		'priority' => 50,
	]);

	// Global Settings
	$wp_customize->add_section('gizmo_ads_global', [
		'title' => __('Global Settings', GIZMO_TEXT),
		'panel' => 'gizmo_ads_panel',
	]);
	$wp_customize->add_setting('gizmo_ads_enabled', ['default' => false, 'sanitize_callback' => 'wp_validate_boolean']);
	$wp_customize->add_control('gizmo_ads_enabled', [
		'label'   => __('Enable Advertisements', GIZMO_TEXT),
		'section' => 'gizmo_ads_global',
		'type'    => 'checkbox',
	]);
	
	// Smart Auto-Ads Mode
	$wp_customize->add_setting('gizmo_ads_auto_mode', ['default' => 'manual', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_ads_auto_mode', [
		'label'       => __('Ad Placement Mode', GIZMO_TEXT),
		'description' => __('Auto: Smart placement like AdSense Auto Ads. Manual: Control each ad position.', GIZMO_TEXT),
		'section'     => 'gizmo_ads_global',
		'type'        => 'select',
		'choices'     => [
			'manual' => __('Manual Placement', GIZMO_TEXT),
			'auto'   => __('Smart Auto Placement', GIZMO_TEXT),
		],
	]);
	
	// Auto-Ads Settings
	$wp_customize->add_setting('gizmo_ads_auto_frequency', ['default' => 'medium', 'sanitize_callback' => 'sanitize_key']);
	$wp_customize->add_control('gizmo_ads_auto_frequency', [
		'label'       => __('Ad Frequency (Auto Mode)', GIZMO_TEXT),
		'description' => __('How often to show ads in content', GIZMO_TEXT),
		'section'     => 'gizmo_ads_global',
		'type'        => 'select',
		'choices'     => [
			'low'    => __('Low - Every 8 paragraphs', GIZMO_TEXT),
			'medium' => __('Medium - Every 5 paragraphs', GIZMO_TEXT),
			'high'   => __('High - Every 3 paragraphs', GIZMO_TEXT),
		],
	]);
	
	$wp_customize->add_setting('gizmo_ads_auto_exclude_first', ['default' => 2, 'sanitize_callback' => 'absint']);
	$wp_customize->add_control('gizmo_ads_auto_exclude_first', [
		'label'       => __('Skip First N Paragraphs', GIZMO_TEXT),
		'description' => __('Don\'t show ads in first N paragraphs', GIZMO_TEXT),
		'section'     => 'gizmo_ads_global',
		'type'        => 'number',
		'input_attrs' => ['min' => 0, 'max' => 10],
	]);
	
	$wp_customize->add_setting('gizmo_ads_auto_min_words', ['default' => 300, 'sanitize_callback' => 'absint']);
	$wp_customize->add_control('gizmo_ads_auto_min_words', [
		'label'       => __('Minimum Content Words', GIZMO_TEXT),
		'description' => __('Only show ads in posts with at least this many words', GIZMO_TEXT),
		'section'     => 'gizmo_ads_global',
		'type'        => 'number',
		'input_attrs' => ['min' => 100, 'max' => 2000],
	]);
	
	// Ad Locations
	$ad_locations = [
		'none'           => __('Select Location...', GIZMO_TEXT),
		'sidebar'        => __('Sidebar', GIZMO_TEXT),
		'content_top'    => __('Before Content', GIZMO_TEXT),
		'content_3'      => __('After Paragraph 3', GIZMO_TEXT),
		'content_5'      => __('After Paragraph 5', GIZMO_TEXT),
		'content_8'      => __('After Paragraph 8', GIZMO_TEXT),
		'content_10'     => __('After Paragraph 10', GIZMO_TEXT),
		'content_middle' => __('Middle of Content', GIZMO_TEXT),
		'content_bot'    => __('After Content', GIZMO_TEXT),
		'sticky_footer'  => __('Sticky Footer', GIZMO_TEXT),
		'sticky_sidebar' => __('Sticky Sidebar', GIZMO_TEXT),
		'after_toc'      => __('After Table of Contents', GIZMO_TEXT),
		'in_feed'        => __('In Feed (Archive Pages)', GIZMO_TEXT),
	];

	// Device Targets
	$ad_devices = [
		'all'     => __('All Devices', GIZMO_TEXT),
		'desktop' => __('Desktop Only (> 1024px)', GIZMO_TEXT),
		'tablet'  => __('Tablet Only (768px - 1024px)', GIZMO_TEXT),
		'mobile'  => __('Mobile Only (< 768px)', GIZMO_TEXT),
		'no_mobile' => __('Exclude Mobile', GIZMO_TEXT),
	];

	// Register 10 Flexible Ad Units
	for ($i = 1; $i <= 10; $i++) {
		$section_id = 'gizmo_ad_unit_' . $i;
		$wp_customize->add_section($section_id, [
			'title' => sprintf(__('Ad Unit %d', GIZMO_TEXT), $i),
			'panel' => 'gizmo_ads_panel',
		]);

		$wp_customize->add_setting("gizmo_ad_{$i}_code", ['default' => '', 'sanitize_callback' => 'gizmo_sanitize_ad_code']);
		$wp_customize->add_control("gizmo_ad_{$i}_code", [
			'label'       => __('Ad Code (HTML/JS)', GIZMO_TEXT),
			'description' => __('Paste your ad script here. Supports Google AdSense, Media.net, etc.', GIZMO_TEXT),
			'section'     => $section_id,
			'type'        => 'textarea',
		]);

		$wp_customize->add_setting("gizmo_ad_{$i}_location", ['default' => 'none', 'sanitize_callback' => 'sanitize_key']);
		$wp_customize->add_control("gizmo_ad_{$i}_location", [
			'label'       => __('Placement', GIZMO_TEXT),
			'section'     => $section_id,
			'type'        => 'select',
			'choices'     => $ad_locations,
		]);

		$wp_customize->add_setting("gizmo_ad_{$i}_device", ['default' => 'all', 'sanitize_callback' => 'sanitize_key']);
		$wp_customize->add_control("gizmo_ad_{$i}_device", [
			'label'       => __('Target Device', GIZMO_TEXT),
			'section'     => $section_id,
			'type'        => 'select',
			'choices'     => $ad_devices,
		]);
		
		// Post Type Targeting
		$wp_customize->add_setting("gizmo_ad_{$i}_post_types", ['default' => ['post', 'page'], 'sanitize_callback' => function($input) {
			return is_array($input) ? array_map('sanitize_key', $input) : ['post'];
		}]);
		$wp_customize->add_control(new WP_Customize_Control($wp_customize, "gizmo_ad_{$i}_post_types", [
			'label'       => __('Show On', GIZMO_TEXT),
			'section'     => $section_id,
			'settings'    => "gizmo_ad_{$i}_post_types",
			'type'        => 'select',
			'choices'     => [
				'post' => __('Posts Only', GIZMO_TEXT),
				'page' => __('Pages Only', GIZMO_TEXT),
				'both' => __('Posts & Pages', GIZMO_TEXT),
			],
		]));
		
		// Category Exclusion
		$wp_customize->add_setting("gizmo_ad_{$i}_exclude_cats", ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
		$wp_customize->add_control("gizmo_ad_{$i}_exclude_cats", [
			'label'       => __('Exclude Categories (IDs)', GIZMO_TEXT),
			'description' => __('Comma-separated category IDs to exclude', GIZMO_TEXT),
			'section'     => $section_id,
			'type'        => 'text',
		]);
	}

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
		// Brand Colors
		'primary_color'       => ['Primary Brand Color', '#2563EB'],
		'primary_hover'       => ['Primary Hover Color', '#1D4ED8'],
		'accent_color'        => ['Accent / Secondary',  '#F59E0B'],
		'accent_hover'        => ['Accent Hover Color',  '#D97706'],
		
		// Text Colors
		'text_primary'        => ['Primary Text',        '#1F2937'],
		'text_secondary'      => ['Secondary Text',      '#6B7280'],
		'text_light'          => ['Light Text / Muted',  '#9CA3AF'],
		'text_link'           => ['Link Color',          '#2563EB'],
		'text_link_hover'     => ['Link Hover Color',    '#1D4ED8'],
		
		// Background Colors
		'bg_primary'          => ['Main Background',     '#FFFFFF'],
		'bg_secondary'        => ['Secondary Background','#F9FAFB'],
		'bg_card'             => ['Card Background',     '#FFFFFF'],
		'bg_dark'             => ['Dark Background',     '#111827'],
		
		// UI Colors
		'border_color'        => ['Border Color',        '#E5E7EB'],
		'border_light'        => ['Light Border',        '#F3F4F6'],
		'shadow_color'        => ['Shadow Color',        'rgba(0,0,0,0.1)'],
		
		// Header/Footer
		'nav_bg'              => ['Navbar Background',   '#FFFFFF'],
		'nav_text'            => ['Navbar Text',         '#1F2937'],
		'footer_bg'           => ['Footer Background',   '#0F172A'],
		'footer_text'         => ['Footer Text',         '#E5E7EB'],
		
		// Status Colors
		'success_color'       => ['Success / Green',     '#10B981'],
		'error_color'         => ['Error / Red',         '#EF4444'],
		'warning_color'       => ['Warning / Yellow',    '#F59E0B'],
		'info_color'          => ['Info / Blue',         '#3B82F6'],
	];
	foreach ($colors as $id => [$label, $default]) {
		$wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage']);
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

/* Allow raw HTML/JS for ads (Admin only) */
function gizmo_sanitize_ad_code($input) {
	return current_user_can('unfiltered_html') ? $input : wp_kses_post($input);
}

/* Customizer helpers */
function gizmo_add_font_control($wpc, $id, $section, $label, $default) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage']);
	
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

/* ============================================================
   CUSTOMIZER LIVE CSS OUTPUT
   Applies Customizer settings as inline CSS
   ============================================================ */
add_action('wp_enqueue_scripts', 'gizmo_customizer_css', 100);
function gizmo_customizer_css() {
	$css = '';

	// Font Families
	$body_font = get_theme_mod('body_font_family', "'Inter', sans-serif");
	$heading_font = get_theme_mod('heading_font_family', "'Inter', sans-serif");

	$css .= "--font-sans: {$body_font};";
	$css .= "--font-heading: {$heading_font};";

	// Font Sizes (Body)
	$body_size = get_theme_mod('body_font_size', 16);
	if ($body_size && $body_size != 16) {
		$css .= "--font-size-base: " . ($body_size / 16) . "rem;";
	}

	// Font Weights
	$body_weight = get_theme_mod('body_font_weight', '400');
	if ($body_weight && $body_weight != '400') {
		$css .= "--font-weight-normal: {$body_weight};";
	}

	$heading_weight = get_theme_mod('heading_font_weight', '800');
	if ($heading_weight && $heading_weight != '800') {
		$css .= "--font-weight-bold: {$heading_weight};";
	}

	// Line Heights
	$body_line_height = get_theme_mod('body_line_height', 1.75);
	if ($body_line_height && $body_line_height != 1.75) {
		$css .= "--line-height-normal: {$body_line_height};";
	}

	$heading_line_height = get_theme_mod('heading_line_height', 1.2);
	if ($heading_line_height && $heading_line_height != 1.2) {
		$css .= "--line-height-tight: {$heading_line_height};";
	}

	// Heading Sizes
	$h1_size = get_theme_mod('h1_size', 40);
	if ($h1_size && $h1_size != 40) {
		$css .= "--font-size-4xl: " . ($h1_size / 16) . "rem;";
	}

	$h2_size = get_theme_mod('h2_size', 32);
	if ($h2_size && $h2_size != 32) {
		$css .= "--font-size-3xl: " . ($h2_size / 16) . "rem;";
	}

	$h3_size = get_theme_mod('h3_size', 26);
	if ($h3_size && $h3_size != 26) {
		$css .= "--font-size-2xl: " . ($h3_size / 16) . "rem;";
	}

	$h4_size = get_theme_mod('h4_size', 22);
	if ($h4_size && $h4_size != 22) {
		$css .= "--font-size-xl: " . ($h4_size / 16) . "rem;";
	}

	$h5_size = get_theme_mod('h5_size', 18);
	if ($h5_size && $h5_size != 18) {
		$css .= "--font-size-lg: " . ($h5_size / 16) . "rem;";
	}

	$h6_size = get_theme_mod('h6_size', 16);
	if ($h6_size && $h6_size != 16) {
		$css .= "--font-size-md: " . ($h6_size / 16) . "rem;";
	}

	// Layout Widths
	$content_width = get_theme_mod('content_width', 800);
	if ($content_width && $content_width != 800) {
		$css .= "--width-content: {$content_width}px;";
	}

	$wide_width = get_theme_mod('wide_width', 1320);
	if ($wide_width && $wide_width != 1320) {
		$css .= "--width-wide: {$wide_width}px;";
	}

	$card_radius = get_theme_mod('card_radius', 16);
	if ($card_radius && $card_radius != 16) {
		$css .= "--radius-lg: {$card_radius}px;";
		$css .= "--radius-card: {$card_radius}px;";
	}

	// Colors - All color variables with defaults
	$color_vars = [
		// Brand
		'--color-primary'       => ['primary_color', '#2563EB'],
		'--color-primary-hover' => ['primary_hover', '#1D4ED8'],
		'--color-accent'        => ['accent_color', '#F59E0B'],
		'--color-accent-hover'  => ['accent_hover', '#D97706'],
		
		// Text
		'--text-primary'        => ['text_primary', '#1F2937'],
		'--text-secondary'      => ['text_secondary', '#6B7280'],
		'--text-light'          => ['text_light', '#9CA3AF'],
		'--text-link'           => ['text_link', '#2563EB'],
		'--text-link-hover'     => ['text_link_hover', '#1D4ED8'],
		
		// Background
		'--bg-primary'          => ['bg_primary', '#FFFFFF'],
		'--bg-secondary'        => ['bg_secondary', '#F9FAFB'],
		'--bg-card'             => ['bg_card', '#FFFFFF'],
		'--bg-dark'             => ['bg_dark', '#111827'],
		
		// UI
		'--border-color'        => ['border_color', '#E5E7EB'],
		'--border-light'        => ['border_light', '#F3F4F6'],
		'--shadow-color'        => ['shadow_color', 'rgba(0,0,0,0.1)'],
		
		// Header/Footer
		'--bg-nav'              => ['nav_bg', '#FFFFFF'],
		'--text-nav'            => ['nav_text', '#1F2937'],
		'--bg-footer'           => ['footer_bg', '#0F172A'],
		'--text-footer'         => ['footer_text', '#E5E7EB'],
		
		// Status
		'--color-success'       => ['success_color', '#10B981'],
		'--color-error'         => ['error_color', '#EF4444'],
		'--color-warning'       => ['warning_color', '#F59E0B'],
		'--color-info'          => ['info_color', '#3B82F6'],
	];
	
	foreach ($color_vars as $css_var => [$theme_mod, $default]) {
		$value = get_theme_mod($theme_mod, $default);
		if ($value && $value != $default) {
			$css .= "{$css_var}: {$value};";
		}
	}

	// Output CSS if we have any custom values
	if (!empty($css)) {
		$custom_css = ":root {" . $css . "}";

		// Also apply body font to body element for immediate effect
		$custom_css .= "body { font-family: var(--font-sans); }";
		$custom_css .= "h1, h2, h3, h4, h5, h6 { font-family: var(--font-heading); }";

		wp_add_inline_style('gizmo-main', $custom_css);
	}
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

	if (is_page_template('compare.php') || is_page('compare')) {
		$classes[] = 'compare-page';
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

	foreach (['bento-grid','pros-cons','specs-table','specs-card','pros-cons-product'] as $pattern) {
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
	$page = isset($_POST['page']) && is_numeric($_POST['page']) ? max(1, absint($_POST['page'])) : 1;
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
   AJAX: Filter Mobiles
   ============================================================ */
add_action('wp_ajax_gizmo_filter_mobiles',        'gizmo_ajax_filter_mobiles');
add_action('wp_ajax_nopriv_gizmo_filter_mobiles', 'gizmo_ajax_filter_mobiles');
function gizmo_ajax_filter_mobiles() {
	check_ajax_referer('gizmo_nonce', 'nonce');

	$cat_id    = isset($_POST['category']) ? absint($_POST['category']) : 0;
    $post_type = get_theme_mod('gizmo_mobiles_post_type', 'post');
    $count     = get_theme_mod('gizmo_mobiles_count', 8);

	$args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => $count,
	];

    // Get 'mobile' category for intersection
    $mobile_term    = get_term_by('slug', 'mobile', 'category');
    $mobile_term_id = $mobile_term ? $mobile_term->term_id : 0;
    $tax_query      = [];

    // 1. Always require 'mobile' category if it exists
    if ($mobile_term_id) {
        $tax_query[] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $mobile_term_id];
    }

    if ($cat_id > 0) {
        // 2a. Specific brand selected
        $tax_query[] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $cat_id];
    } else {
        // 2b. "All" selected -> limit to defined filter categories (brands)
        $all_cats_str = get_theme_mod('gizmo_mobiles_filter_categories', '');
        $brand_ids    = [];

        if (!empty($all_cats_str)) {
            $brand_ids = array_map('intval', explode(',', $all_cats_str));
        } else {
            // Fallback brands (same as front-page.php)
            $brand_slugs = ['samsung', 'vivo', 'oppo', 'nothing', 'xiaomi', 'apple', 'asus', 'google', "asus", "oneplus", "realme", "motorola", "oppo", "iqoo"];
            $brand_terms = get_terms(['taxonomy' => 'category', 'slug' => $brand_slugs, 'hide_empty' => true]);
            if (!is_wp_error($brand_terms) && !empty($brand_terms)) {
                $brand_ids = wp_list_pluck($brand_terms, 'term_id');
            }
        }

        if (!empty($brand_ids)) {
            $tax_query[] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $brand_ids, 'operator' => 'IN'];
        }
    }

    if (!empty($tax_query)) {
        $tax_query['relation'] = 'AND';
        $args['tax_query'] = $tax_query;
    }

	$q = new WP_Query($args);

	ob_start();
	if ($q->have_posts()) {
        while ($q->have_posts()) {
            $q->the_post();
            get_template_part('template-parts/card', 'mobile');
        }
    } else {
        echo '<p class="no-mobiles-found">' . esc_html__('No mobiles found in this category.', 'gizmodotech-pro') . '</p>';
    }
	wp_reset_postdata();

	wp_send_json_success([
		'html' => ob_get_clean(),
	]);
}

/**
 * ============================================================
 * AJAX: Device Comparison Functionality (Merged from Plugin)
 * ============================================================
 */

// Helper to extract price from post content (from your plugin)
function gizmo_dc_get_price_from_content($post_id) {
    $content = get_post_field('post_content', $post_id);
    preg_match('/₹[\d,]+/', $content, $matches);
    return $matches[0] ?? __('Price not available', 'gizmodotech-pro');
}

// AJAX handler for searching devices
add_action('wp_ajax_gizmo_dc_search_devices', 'gizmo_ajax_dc_search_devices');
add_action('wp_ajax_nopriv_gizmo_dc_search_devices', 'gizmo_ajax_dc_search_devices');
function gizmo_ajax_dc_search_devices() {
    check_ajax_referer('gizmo_nonce', 'security');

    $query = isset($_GET['query']) ? sanitize_text_field(wp_unslash($_GET['query'])) : '';

    if (empty($query) || strlen($query) < 2) {
        wp_send_json_error(__('Please enter at least 2 characters', 'gizmodotech-pro'));
        return;
    }

    $args = [
        'post_type'      => ['post', 'mobile', 'review'],
        'posts_per_page' => 5,
        's'              => $query,
        'category_name'  => 'mobile',
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ];

    $query_result = new WP_Query($args);
    $results      = [];

    foreach ($query_result->posts as $post_id) {
        $results[] = [
            'id'      => $post_id,
            'title'   => get_the_title($post_id),
            'slug'    => get_post_field('post_name', $post_id),
            'image'   => get_the_post_thumbnail_url($post_id, 'thumbnail') ?: '',
            'price'   => gizmo_dc_get_price_from_content($post_id),
            'content' => get_post_field('post_content', $post_id),
        ];
    }

    wp_send_json_success($results);
}

// AJAX handler for fetching full device data for comparison
add_action('wp_ajax_gizmo_dc_handle_comparison', 'gizmo_ajax_dc_handle_comparison');
add_action('wp_ajax_nopriv_gizmo_dc_handle_comparison', 'gizmo_ajax_dc_handle_comparison');
function gizmo_ajax_dc_handle_comparison() {
	check_ajax_referer('gizmo_nonce', 'security');

    $raw_slugs = isset($_GET['slugs']) ? (array) $_GET['slugs'] : [];
    $slugs     = array_filter(
        array_slice(array_map('sanitize_text_field', $raw_slugs), 0, 3)
    );

    if (empty($slugs)) {
        wp_send_json_error(__('No devices selected', 'gizmodotech-pro'));
        return;
    }

    $devices = [];

    foreach ($slugs as $slug) {
        $post = get_page_by_path($slug, OBJECT, ['post', 'mobile', 'review']);
        if ($post) {
            $devices[] = [
                'id'      => $post->ID,
                'title'   => $post->post_title,
                'slug'    => $slug,
                'content' => $post->post_content,
                'url'     => get_permalink($post->ID),
                'image'   => get_the_post_thumbnail_url($post->ID, 'medium') ?: '',
            ];
        }
    }

    wp_send_json_success(['devices' => $devices]);
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

/**
 * 3. Ad Slot Helper (Async)
 * Outputs ad code in a <template> tag for JS to inject based on device.
 */
function gizmo_get_ad_location_html($location) {
	if ( ! get_theme_mod( 'gizmo_ads_enabled', false ) ) {
		return '';
	}

	$matching_ads = [];
	// Increased loop to 10 to match new number of ad units.
	for ( $i = 1; $i <= 10; $i++ ) {
		$loc = get_theme_mod("gizmo_ad_{$i}_location", 'none');
		if ( $loc === $location ) {
			$code = get_theme_mod("gizmo_ad_{$i}_code", '');
			$dev  = get_theme_mod("gizmo_ad_{$i}_device", 'all');
			if ( $code ) {
				$matching_ads[] = [
					'code' => $code,
					'dev'  => $dev,
				];
			}
		}
	}

	if ( empty( $matching_ads ) ) {
		return '';
	}

	// If multiple ads are assigned to the same location, pick one at random for each page load.
	$ad = $matching_ads[ array_rand( $matching_ads ) ];

	ob_start();
	echo '<div class="gizmo-async-ad" data-device="' . esc_attr( $ad['dev'] ) . '">';
	// Raw code hidden in template
	echo '<template>' . $ad['code'] . '</template>';
	echo '</div>';
	return ob_get_clean();
}

/* Automatically inject ads into single post content */
add_filter('the_content', 'gizmo_inject_ads_in_content', 20);
function gizmo_inject_ads_in_content($content) {
	if (!is_singular(['post', 'page']) || !get_theme_mod('gizmo_ads_enabled', false)) {
		return $content;
	}
	
	// Check minimum word count
	$min_words = get_theme_mod('gizmo_ads_auto_min_words', 300);
	$content_words = str_word_count(strip_tags($content));
	if ($content_words < $min_words) {
		return $content;
	}
	
	$auto_mode = get_theme_mod('gizmo_ads_auto_mode', 'manual');
	
	if ($auto_mode === 'auto') {
		// Smart Auto Placement like AdSense Auto Ads
		$content = gizmo_smart_auto_ads($content);
	} else {
		// Manual Placement
		$content = gizmo_manual_ad_placement($content);
	}

	return $content;
}

/**
 * Smart Auto Ads - Automatically places ads based on content length
 */
function gizmo_smart_auto_ads($content) {
	$frequency = get_theme_mod('gizmo_ads_auto_frequency', 'medium');
	$skip_first = get_theme_mod('gizmo_ads_auto_exclude_first', 2);
	
	// Get frequency interval
	$intervals = ['low' => 8, 'medium' => 5, 'high' => 3];
	$interval = $intervals[$frequency] ?? 5;
	
	// Get available ad codes for auto placement
	$auto_ads = [];
	for ($i = 1; $i <= 10; $i++) {
		$ad_code = get_theme_mod("gizmo_ad_{$i}_code", '');
		$ad_location = get_theme_mod("gizmo_ad_{$i}_location", 'none');
		$ad_device = get_theme_mod("gizmo_ad_{$i}_device", 'all');
		
		// Use ads marked for auto or middle placement
		if (!empty($ad_code) && ($ad_location === 'content_middle' || $ad_location === 'none')) {
			$auto_ads[] = [
				'code' => $ad_code,
				'device' => $ad_device,
			];
		}
	}
	
	if (empty($auto_ads)) {
		return gizmo_manual_ad_placement($content);
	}
	
	// Count paragraphs
	$paragraphs = explode('</p>', $content);
	$total_paragraphs = count($paragraphs);
	
	if ($total_paragraphs <= $skip_first + 2) {
		return $content; // Too few paragraphs
	}
	
	// Insert ads at calculated intervals
	$ad_index = 0;
	$new_content = '';
	
	foreach ($paragraphs as $index => $paragraph) {
		$new_content .= $paragraph;
		
		if ($index < count($paragraphs) - 1) {
			$new_content .= '</p>';
		}
		
		// Skip first N paragraphs
		if ($index < $skip_first) {
			continue;
		}
		
		// Check if this is an insertion point
		$position = $index + 1;
		if (($position - $skip_first) % $interval === 0 && $position < $total_paragraphs - 1) {
			// Rotate through available ads
			$ad = $auto_ads[$ad_index % count($auto_ads)];
			$ad_html = gizmo_wrap_ad_code($ad['code'], $ad['device']);
			$new_content .= $ad_html;
			$ad_index++;
		}
	}
	
	return $new_content;
}

/**
 * Manual Ad Placement - Places ads at specific locations
 */
function gizmo_manual_ad_placement($content) {
	// Before Content
	$ad_top = gizmo_get_ad_location_html('content_top');
	if ($ad_top) {
		$content = $ad_top . $content;
	}

	// After specific paragraphs
	$locations = [
		'content_3' => 3,
		'content_5' => 5,
		'content_8' => 8,
		'content_10' => 10,
	];
	
	foreach ($locations as $location => $paragraph_num) {
		$ad = gizmo_get_ad_location_html($location);
		if ($ad) {
			$content = gizmo_insert_after_paragraph($ad, $paragraph_num, $content);
		}
	}
	
	// Middle of content
	$ad_middle = gizmo_get_ad_location_html('content_middle');
	if ($ad_middle) {
		$paragraphs = explode('</p>', $content);
		$middle = intval(count($paragraphs) / 2);
		$content = gizmo_insert_after_paragraph($ad_middle, $middle, $content);
	}

	// After Content
	$ad_bot = gizmo_get_ad_location_html('content_bot');
	if ($ad_bot) {
		$content .= $ad_bot;
	}

	return $content;
}

/**
 * Wrap ad code with device targeting
 */
function gizmo_wrap_ad_code($code, $device = 'all') {
	if (empty($code)) return '';
	
	$device_class = '';
	switch ($device) {
		case 'desktop':
			$device_class = 'gizmo-ad-desktop';
			break;
		case 'tablet':
			$device_class = 'gizmo-ad-tablet';
			break;
		case 'mobile':
			$device_class = 'gizmo-ad-mobile';
			break;
		case 'no_mobile':
			$device_class = 'gizmo-ad-no-mobile';
			break;
	}
	
	$class_attr = $device_class ? ' class="gizmo-ad-unit ' . esc_attr($device_class) . '"' : ' class="gizmo-ad-unit"';
	
	return '<div' . $class_attr . ' style="margin: 2rem 0; text-align: center;">' . $code . '</div>';
}

function gizmo_insert_after_paragraph( $insertion, $paragraph_id, $content ) {
	$closing_p  = '</p>';
	$paragraphs = explode( $closing_p, $content );
	// Check if there are enough paragraphs
	if ( count( $paragraphs ) <= $paragraph_id ) {
		return $content;
	}
	$new_content = '';
	foreach ( $paragraphs as $index => $paragraph ) {
		// Add the paragraph and its closing tag back
		$new_content .= $paragraph;
		// Don't add the closing tag if it's the last (and likely empty) element from explode
		if ( $index < count( $paragraphs ) - 1 ) {
			$new_content .= $closing_p;
		}
		// After the Nth paragraph, add the insertion
		if ( $paragraph_id === ( $index + 1 ) ) {
			$new_content .= $insertion;
		}
	}
	return $new_content;
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

	register_block_type('gizmodotech/post-grid-block', [
		'render_callback' => 'gizmo_shortcode_posts', 
		'attributes' => [
			'count' => ['type' => 'string', 'default' => '3'],
			'type'  => ['type' => 'string', 'default' => 'post'],
			'cat'   => ['type' => 'string', 'default' => ''],
			'pagination' => ['type' => 'boolean', 'default' => false],
		]
	]);


	register_block_type('gizmodotech/review-card');

	register_block_type('gizmodotech/flex-container');

	register_block_type('gizmodotech/grid-container');


	register_block_type('gizmodotech/product-review');


	register_block_type('gizmodotech/specs-card-block');

	register_block_type('gizmodotech/featured-card');

	register_block_type('gizmodotech/carousel-slider');
}

/* COMMENT CALLBACK*/
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

/* ============================================================
   AJAX: Load Amazon Products
   ============================================================ */
add_action('wp_ajax_gizmo_load_amazon_products', 'gizmo_ajax_load_amazon_products');
add_action('wp_ajax_nopriv_gizmo_load_amazon_products', 'gizmo_ajax_load_amazon_products');
function gizmo_ajax_load_amazon_products() {
    check_ajax_referer('gizmo_nonce', 'nonce');
    $keyword = isset($_POST['keyword']) ? sanitize_text_field($_POST['keyword']) : '';
    
    if (empty($keyword)) {
        wp_send_json_error([
            'message' => 'No keyword provided',
            'debug'   => gizmo_amazon_get_debug_log(),
        ]);
    }

    // Check if Amazon feature is enabled
    if (!get_theme_mod('gizmo_amazon_enabled', false)) {
        wp_send_json_error([
            'message' => 'Amazon feature is disabled',
            'debug'   => ['Amazon affiliate feature is disabled in Customizer'],
        ]);
    }

    // Check credentials
    $access_key = trim(get_theme_mod('gizmo_creators_client_id'));
    $secret_key = trim(get_theme_mod('gizmo_creators_client_secret'));
    $partner_tag = trim(get_theme_mod('gizmo_amazon_associate_tag'));
    
    if (empty($access_key) || empty($secret_key) || empty($partner_tag)) {
        // Return fallback HTML with manual search link
        $fallback_html = gizmo_get_amazon_fallback_html($keyword);
        wp_send_json_success([
            'html'    => $fallback_html,
            'fallback' => true,
            'debug'   => ['Using fallback - credentials not configured'],
        ]);
    }

    $products = gizmo_get_amazon_products($keyword);

    if (is_wp_error($products)) {
        // Return fallback HTML on API error
        $error_msg = $products->get_error_message();
        $fallback_html = gizmo_get_amazon_fallback_html($keyword, $error_msg);
        wp_send_json_success([
            'html'     => $fallback_html,
            'fallback' => true,
            'message'  => $error_msg,
            'debug'    => gizmo_amazon_get_debug_log(),
        ]);
    }

    if (empty($products)) {
        // Return fallback HTML when no products found
        $fallback_html = gizmo_get_amazon_fallback_html($keyword);
        wp_send_json_success([
            'html'     => $fallback_html,
            'fallback' => true,
            'debug'    => array_merge(['No products found'], gizmo_amazon_get_debug_log()),
        ]);
    }

    $amz_title = get_theme_mod('gizmo_amazon_title', 'Buy on Amazon');
    ob_start();
    ?>
    <div class="sidebar-widget sidebar-amazon">
        <h3 class="sidebar-widget__title"><?php echo esc_html($amz_title); ?></h3>
        <div class="sidebar-amazon-list">
            <?php foreach ($products as $item) : 
                $img   = $item['Images']['Primary']['Small']['URL'] ?? '';
                $medium_img = $item['Images']['Primary']['Medium']['URL'] ?? $img;
                $url   = $item['DetailPageURL'] ?? '#';
                $title = $item['ItemInfo']['Title']['DisplayValue'] ?? '';
                $price = $item['Offers']['Listings'][0]['Price']['DisplayAmount'] ?? 'Check Price';
            ?>
            <a class="sidebar-amazon-item" href="<?php echo esc_url($url); ?>" target="_blank" rel="nofollow noopener sponsored">
                <?php if ($img) : ?><div class="sidebar-amazon-thumb"><img src="<?php echo esc_url($img); ?>" alt="<?php echo esc_attr($title); ?>" loading="lazy"></div><?php endif; ?>
                <div class="sidebar-amazon-details">
                    <span class="sidebar-amazon-title"><?php echo esc_html($title); ?></span>
                    <span class="sidebar-amazon-price"><?php echo esc_html($price); ?></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <div class="sidebar-amazon-footer">
            <small>As an Amazon Associate, we earn from qualifying purchases.</small>
        </div>
    </div>
    <?php
    $html = ob_get_clean();
    wp_send_json_success([
        'html'  => $html,
        'debug' => gizmo_amazon_get_debug_log(),
    ]);
}

/**
 * Generate fallback HTML when API is not available
 *
 * @param string $keyword Search keyword
 * @param string $error_message Optional error message to display
 * @return string HTML
 */
function gizmo_get_amazon_fallback_html(string $keyword, string $error_message = ''): string {
    $marketplace = get_theme_mod('gizmo_amazon_marketplace', 'www.amazon.in');
    $partner_tag = trim(get_theme_mod('gizmo_amazon_associate_tag'));
    $amz_title = get_theme_mod('gizmo_amazon_title', 'Buy on Amazon');
    
    // Build Amazon search URL
    $search_url = 'https://' . $marketplace . '/s?k=' . urlencode($keyword);
    if (!empty($partner_tag)) {
        $search_url .= '&tag=' . $partner_tag;
    }
    
    ob_start();
    ?>
    <div class="sidebar-widget sidebar-amazon sidebar-amazon--fallback">
        <h3 class="sidebar-widget__title"><?php echo esc_html($amz_title); ?></h3>
        <div class="sidebar-amazon-fallback">
            <p>Find <strong><?php echo esc_html($keyword); ?></strong> on Amazon:</p>
            <a href="<?php echo esc_url($search_url); ?>" target="_blank" rel="nofollow noopener sponsored" class="btn-amazon-search">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
                Search on Amazon
            </a>
            <?php if (!empty($error_message)) : ?>
            <div class="amazon-api-error" style="margin-top: 10px; padding: 8px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 4px; font-size: 11px; color: #dc2626;">
                <strong>API Error:</strong> <?php echo esc_html($error_message); ?>
                <br><br>
                <em>Need help? <a href="https://affiliate-program.amazon.com/help/node/topic/202095050" target="_blank" style="color: #dc2626; text-decoration: underline;">Check PA API requirements</a></em>
            </div>
            <?php endif; ?>
        </div>
        <div class="sidebar-amazon-footer">
            <small>As an Amazon Associate, we earn from qualifying purchases.</small>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
function gizmo_amazon_debug(string $message) {
    global $gizmo_amazon_debug_log;
    $gizmo_amazon_debug_log[] = $message;
}

function gizmo_amazon_get_debug_log(): array {
    global $gizmo_amazon_debug_log;
    return $gizmo_amazon_debug_log ?? [];
}

/* ============================================================
   AMAZON CREATORS API INTEGRATION (OAuth 2.0)
   New API replacing PA API 5.0
   ============================================================ */

/**
 * Get Creators API configuration based on marketplace
 *
 * @param string $marketplace Marketplace domain
 * @return array API configuration
 */
function gizmo_get_creators_api_config(string $marketplace): array {
    // Get stored credential version from theme mod, or use default
    $stored_version = get_theme_mod('gizmo_amazon_credential_version', '');
    
    // If user has stored a version (like 3.2), use it; otherwise use defaults
    if (!empty($stored_version)) {
        $version = $stored_version;
    } else {
        // Version mapping based on region (legacy defaults)
        // 2.1 = NA, 2.2 = EU, 2.3 = FE
        $versions = [
            'www.amazon.com'   => '2.1',
            'www.amazon.ca'    => '2.1',
            'www.amazon.com.br'=> '2.1',
            'www.amazon.com.mx'=> '2.1',
            'www.amazon.co.uk' => '2.2',
            'www.amazon.de'    => '2.2',
            'www.amazon.fr'    => '2.2',
            'www.amazon.it'    => '2.2',
            'www.amazon.es'    => '2.2',
            'www.amazon.nl'    => '2.2',
            'www.amazon.se'    => '2.2',
            'www.amazon.pl'    => '2.2',
            'www.amazon.in'    => '2.2',
            'www.amazon.ae'    => '2.2',
            'www.amazon.sa'    => '2.2',
            'www.amazon.tr'    => '2.2',
            'www.amazon.co.jp' => '2.3',
            'www.amazon.com.au'=> '2.3',
            'www.amazon.sg'    => '2.3',
        ];
        $version = $versions[$marketplace] ?? '2.2';
    }
    
    return [
        'api_endpoint' => 'https://api.amazon.com/creator/v' . $version . '/products/search',
        'auth_endpoint' => 'https://api.amazon.com/auth/o2/token',
        'version' => $version,
        'marketplace' => $marketplace,
    ];
}

/**
 * Get OAuth 2.0 access token for Creators API
 *
 * @param string $credential_id Creators API Credential ID
 * @param string $credential_secret Creators API Credential Secret
 * @return string|WP_Error Access token or error
 */
function gizmo_get_creators_api_token(string $credential_id, string $credential_secret) {
    // Check for cached token
    $cache_key = 'gizmo_creators_token_' . md5($credential_id);
    $cached_token = get_transient($cache_key);
    
    if ($cached_token) {
        gizmo_amazon_debug('✅ Using cached OAuth token');
        return $cached_token;
    }
    
    gizmo_amazon_debug('🔄 Requesting new OAuth token...');
    
    // Determine correct auth endpoint based on version and marketplace
    // v3.2 EU (including India) uses api.amazon.co.uk
    $auth_endpoint = 'https://api.amazon.co.uk/auth/o2/token';
    
    // Build JSON request body for v3.x credentials
    $request_body = [
        'grant_type' => 'client_credentials',
        'client_id' => $credential_id,
        'client_secret' => $credential_secret,
        'scope' => 'creatorsapi::default',
    ];
    
    gizmo_amazon_debug('Auth Endpoint: ' . $auth_endpoint);
    gizmo_amazon_debug('OAuth Request Body (JSON):');
    gizmo_amazon_debug('  grant_type: ' . $request_body['grant_type']);
    gizmo_amazon_debug('  client_id: ' . substr($request_body['client_id'], 0, 20) . '...');
    gizmo_amazon_debug('  client_secret: ' . strlen($request_body['client_secret']) . ' chars');
    gizmo_amazon_debug('  scope: ' . $request_body['scope']);
    
    $response = wp_remote_post($auth_endpoint, [
        'timeout' => 30,
        'headers' => [
            'Content-Type' => 'application/json',
        ],
        'body' => json_encode($request_body),
    ]);
    
    if (is_wp_error($response)) {
        gizmo_amazon_debug('❌ Token request failed: ' . $response->get_error_message());
        return $response;
    }
    
    $http_code = wp_remote_retrieve_response_code($response);
    $body = json_decode(wp_remote_retrieve_body($response), true);
    
    gizmo_amazon_debug('Token HTTP status: ' . $http_code);
    
    if ($http_code >= 400 || isset($body['error'])) {
        $error_msg = $body['error_description'] ?? $body['error'] ?? 'Unknown OAuth error';
        gizmo_amazon_debug('❌ OAuth error: ' . $error_msg);
        gizmo_amazon_debug('Full response: ' . wp_remote_retrieve_body($response));
        return new WP_Error('oauth_error', $error_msg);
    }
    
    if (isset($body['access_token'])) {
        $token = $body['access_token'];
        $expires_in = isset($body['expires_in']) ? intval($body['expires_in']) - 60 : 3540;
        
        set_transient($cache_key, $token, $expires_in);
        gizmo_amazon_debug('✅ New OAuth token acquired and cached for ' . $expires_in . ' seconds');
        
        return $token;
    }
    
    return new WP_Error('no_token', 'Failed to obtain access token');
}

/* ============================================================
   AMAZON PA API 5.0 INTEGRATION (Legacy - AWS Signature V4)
   ============================================================ */

/**
 * Get the appropriate Amazon PA API 5.0 host based on marketplace
 *
 * @param string $marketplace Marketplace domain
 * @return string API host
 */
function gizmo_get_paapi5_host(string $marketplace): string {
    $hosts = [
        'www.amazon.in'    => 'webservices.amazon.in',
        'www.amazon.com'   => 'webservices.amazon.com',
        'www.amazon.co.uk' => 'webservices.amazon.co.uk',
        'www.amazon.ca'    => 'webservices.amazon.ca',
        'www.amazon.de'    => 'webservices.amazon.de',
        'www.amazon.fr'    => 'webservices.amazon.fr',
        'www.amazon.it'    => 'webservices.amazon.it',
        'www.amazon.es'    => 'webservices.amazon.es',
        'www.amazon.co.jp' => 'webservices.amazon.co.jp',
        'www.amazon.com.au'=> 'webservices.amazon.com.au',
        'www.amazon.com.br'=> 'webservices.amazon.com.br',
        'www.amazon.com.mx'=> 'webservices.amazon.com.mx',
        'www.amazon.ae'    => 'webservices.amazon.ae',
        'www.amazon.sg'    => 'webservices.amazon.sg',
        'www.amazon.nl'    => 'webservices.amazon.nl',
        'www.amazon.sa'    => 'webservices.amazon.sa',
        'www.amazon.se'    => 'webservices.amazon.se',
        'www.amazon.pl'    => 'webservices.amazon.pl',
        'www.amazon.tr'    => 'webservices.amazon.com.tr',
    ];
    return $hosts[$marketplace] ?? 'webservices.amazon.in';
}

/**
 * Get the AWS region for signing based on marketplace
 *
 * @param string $marketplace Marketplace domain
 * @return string AWS region
 */
function gizmo_get_paapi5_region(string $marketplace): string {
    $regions = [
        'www.amazon.in'    => 'eu-west-1',
        'www.amazon.com'   => 'us-east-1',
        'www.amazon.co.uk' => 'eu-west-1',
        'www.amazon.ca'    => 'us-east-1',
        'www.amazon.de'    => 'eu-west-1',
        'www.amazon.fr'    => 'eu-west-1',
        'www.amazon.it'    => 'eu-west-1',
        'www.amazon.es'    => 'eu-west-1',
        'www.amazon.co.jp' => 'us-west-2',
        'www.amazon.com.au'=> 'us-west-2',
        'www.amazon.com.br'=> 'us-east-1',
        'www.amazon.com.mx'=> 'us-east-1',
        'www.amazon.ae'    => 'eu-west-1',
        'www.amazon.sg'    => 'us-west-2',
        'www.amazon.nl'    => 'eu-west-1',
        'www.amazon.sa'    => 'eu-west-1',
        'www.amazon.se'    => 'eu-west-1',
        'www.amazon.pl'    => 'eu-west-1',
        'www.amazon.tr'    => 'eu-west-1',
    ];
    return $regions[$marketplace] ?? 'eu-west-1';
}

/**
 * Generate AWS Signature V4 for Amazon PA API 5.0
 *
 * @param string $access_key AWS Access Key ID
 * @param string $secret_key AWS Secret Key
 * @param string $payload Request payload
 * @param string $host API host
 * @param string $region AWS region
 * @return array Headers array
 */
function gizmo_generate_aws_signature_v4(string $access_key, string $secret_key, string $payload, string $host, string $region): array {
    $service = 'ProductAdvertisingAPI';
    $algorithm = 'AWS4-HMAC-SHA256';
    $date = gmdate('Ymd');
    $amz_date = gmdate('Ymd\THis\Z');
    $content_type = 'application/json; charset=utf-8';
    
    $method = 'POST';
    $uri = '/paapi5/searchitems';
    $query_string = '';
    
    $headers = [
        'content-encoding' => 'amz-1.0',
        'content-type' => $content_type,
        'host' => $host,
        'x-amz-date' => $amz_date,
        'x-amz-target' => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems',
    ];
    
    ksort($headers);
    
    $canonical_headers = '';
    $signed_headers = '';
    foreach ($headers as $key => $value) {
        $canonical_headers .= strtolower($key) . ':' . trim($value) . "\n";
        $signed_headers .= strtolower($key) . ';';
    }
    $signed_headers = rtrim($signed_headers, ';');
    
    $payload_hash = hash('sha256', $payload);
    
    $canonical_request = $method . "\n" .
                        $uri . "\n" .
                        $query_string . "\n" .
                        $canonical_headers . "\n" .
                        $signed_headers . "\n" .
                        $payload_hash;
    
    $credential_scope = $date . '/' . $region . '/' . $service . '/aws4_request';
    $string_to_sign = $algorithm . "\n" .
                     $amz_date . "\n" .
                     $credential_scope . "\n" .
                     hash('sha256', $canonical_request);
    
    $k_secret = 'AWS4' . $secret_key;
    $k_date = hash_hmac('sha256', $date, $k_secret, true);
    $k_region = hash_hmac('sha256', $region, $k_date, true);
    $k_service = hash_hmac('sha256', $service, $k_region, true);
    $k_signing = hash_hmac('sha256', 'aws4_request', $k_service, true);
    $signature = hash_hmac('sha256', $string_to_sign, $k_signing);
    
    $authorization_header = $algorithm . ' ' .
                           'Credential=' . $access_key . '/' . $credential_scope . ', ' .
                           'SignedHeaders=' . $signed_headers . ', ' .
                           'Signature=' . $signature;
    
    return [
        'Authorization' => $authorization_header,
        'Content-Type' => $content_type,
        'Content-Encoding' => 'amz-1.0',
        'X-Amz-Date' => $amz_date,
        'X-Amz-Target' => 'com.amazon.paapi5.v1.ProductAdvertisingAPIv1.SearchItems',
    ];
}

/**
 * Fetch products from Amazon PA API 5.0 (Legacy)
 *
 * @param string $keyword The search keyword.
 * @return array|WP_Error Array of products on success, WP_Error on failure.
 */
function gizmo_get_amazon_products_paapi5(string $keyword) {
    gizmo_amazon_debug('Using PA API 5.0 (Legacy)');
    
    $access_key = trim(get_theme_mod('gizmo_creators_client_id'));
    $secret_key = trim(get_theme_mod('gizmo_creators_client_secret'));
    $partner_tag = trim(get_theme_mod('gizmo_amazon_associate_tag'));
    $marketplace = get_theme_mod('gizmo_amazon_marketplace', 'www.amazon.in');

    gizmo_amazon_debug('Access Key: ' . substr($access_key, 0, 8) . '...');
    gizmo_amazon_debug('Partner Tag: ' . $partner_tag);
    gizmo_amazon_debug('Marketplace: ' . $marketplace);
    gizmo_amazon_debug('Keyword: ' . $keyword);

    if (empty($access_key) || empty($secret_key)) {
        gizmo_amazon_debug('❌ Missing AWS credentials');
        return new WP_Error('no_creds', 'AWS Access Key and Secret Key are required.');
    }

    // Check cache
    $cache_key = 'gizmo_amazon_paapi5_' . md5($keyword . $marketplace . $partner_tag);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        gizmo_amazon_debug('✅ Returning cached result');
        return $cached;
    }

    $host = gizmo_get_paapi5_host($marketplace);
    $region = gizmo_get_paapi5_region($marketplace);

    $payload = json_encode([
        'Keywords' => $keyword,
        'SearchIndex' => 'All',
        'ItemCount' => 4,
        'Resources' => [
            'Images.Primary.Small',
            'Images.Primary.Medium',
            'ItemInfo.Title',
            'Offers.Listings.Price',
            'DetailPageURL',
        ],
        'PartnerTag' => $partner_tag,
        'PartnerType' => 'Associates',
        'Marketplace' => $marketplace,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $signature_headers = gizmo_generate_aws_signature_v4($access_key, $secret_key, $payload, $host, $region);

    $api_url = 'https://' . $host . '/paapi5/searchitems';
    
    $response = wp_remote_post($api_url, [
        'timeout' => 30,
        'headers' => array_merge(['Host' => $host], $signature_headers),
        'body' => $payload,
    ]);

    if (is_wp_error($response)) {
        gizmo_amazon_debug('❌ WP_Error: ' . $response->get_error_message());
        return $response;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $raw_body = wp_remote_retrieve_body($response);
    
    gizmo_amazon_debug('HTTP status: ' . $http_code);

    $body = json_decode($raw_body, true);

    if ($http_code >= 400 || isset($body['Errors'])) {
        $error_msg = $body['Errors'][0]['Message'] ?? $body['Errors'][0]['Code'] ?? 'Unknown error';
        gizmo_amazon_debug('❌ API Error: ' . $error_msg);
        return new WP_Error('paapi5_error', $error_msg);
    }

    if (isset($body['SearchResult']['Items']) && is_array($body['SearchResult']['Items'])) {
        $transformed_products = [];
        foreach ($body['SearchResult']['Items'] as $item) {
            $transformed_products[] = [
                'DetailPageURL' => $item['DetailPageURL'] ?? '#',
                'Images' => [
                    'Primary' => [
                        'Small' => ['URL' => $item['Images']['Primary']['Small']['URL'] ?? ''],
                        'Medium' => ['URL' => $item['Images']['Primary']['Medium']['URL'] ?? ''],
                    ]
                ],
                'ItemInfo' => [
                    'Title' => ['DisplayValue' => $item['ItemInfo']['Title']['DisplayValue'] ?? 'Unknown Product']
                ],
                'Offers' => [
                    'Listings' => [
                        ['Price' => ['DisplayAmount' => $item['Offers']['Listings'][0]['Price']['DisplayAmount'] ?? 'Check Price']]
                    ]
                ],
            ];
        }
        
        gizmo_amazon_debug('✅ Found ' . count($transformed_products) . ' items');
        set_transient($cache_key, $transformed_products, 3600);
        return $transformed_products;
    }

    gizmo_amazon_debug('⚠️ No items in response');
    return new WP_Error('no_items', 'No products found');
}

/**
 * Fetch products from Amazon Creators API
 *
 * @param string $keyword The search keyword.
 * @return array|WP_Error Array of products on success, WP_Error on failure.
 */
function gizmo_get_amazon_products_creators(string $keyword) {
    gizmo_amazon_debug('Using Creators API (v3.x)');
    
    // Get credentials from theme mods
    $credential_id = trim(get_theme_mod('gizmo_creators_client_id'));
    $credential_secret = trim(get_theme_mod('gizmo_creators_client_secret'));
    $partner_tag = trim(get_theme_mod('gizmo_amazon_associate_tag'));
    $marketplace = get_theme_mod('gizmo_amazon_marketplace', 'www.amazon.in');

    gizmo_amazon_debug('Credential ID: ' . substr($credential_id, 0, 8) . '...');
    gizmo_amazon_debug('Credential Secret length: ' . strlen($credential_secret));
    gizmo_amazon_debug('Partner Tag: ' . $partner_tag);
    gizmo_amazon_debug('Marketplace: ' . $marketplace);
    gizmo_amazon_debug('Keyword: ' . $keyword);

    // Validate credentials
    if (empty($credential_id) || empty($credential_secret)) {
        gizmo_amazon_debug('❌ Missing Creators API credentials');
        return new WP_Error('no_creds', 'Creators API Credential ID and Secret are required. Please configure them in Customizer > Amazon Affiliate.');
    }

    if (empty($partner_tag)) {
        gizmo_amazon_debug('❌ Missing Partner Tag');
        return new WP_Error('no_partner_tag', 'Associate Tag (Partner Tag) is required. Please configure it in Customizer > Amazon Affiliate.');
    }

    // Check cache
    $cache_key = 'gizmo_creators_api_' . md5($keyword . $marketplace . $partner_tag);
    $cached = get_transient($cache_key);
    if ($cached !== false) {
        gizmo_amazon_debug('✅ Returning cached result');
        return $cached;
    }

    // Get API configuration
    $config = gizmo_get_creators_api_config($marketplace);
    
    gizmo_amazon_debug('API Version: ' . $config['version']);

    // Get OAuth token
    $token = gizmo_get_creators_api_token($credential_id, $credential_secret);
    if (is_wp_error($token)) {
        return $token;
    }

    
    // Check if keyword looks like an ASIN (10 chars, alphanumeric)
    $is_asin = preg_match('/^[A-Z0-9]{10}$/i', trim($keyword));
    
    if ($is_asin) {
        // Use getItems for ASIN lookup
        $payload = json_encode([
            'itemIds' => [trim($keyword)],
            'itemIdType' => 'ASIN',
            'marketplace' => $marketplace,
            'partnerTag' => $partner_tag,
            'resources' => [
                'images.primary.small',
                'images.primary.medium',
                'itemInfo.title',
                'offersV2.listings.price',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        gizmo_amazon_debug('Using getItems endpoint (ASIN detected)');
    } else {
        // Use searchItems for keyword search
        // Note: marketplace is passed in x-marketplace header, not body
        $payload = json_encode([
            'keywords' => $keyword,
            'partnerTag' => $partner_tag,
            'itemCount' => 4,
            'resources' => [
                'images.primary.small',
                'images.primary.medium',
                'itemInfo.title',
                'offersV2.listings.price',
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        gizmo_amazon_debug('Using searchItems endpoint (keyword search)');
    }

    gizmo_amazon_debug('Payload: ' . $payload);

    // Select endpoint based on search type
    if ($is_asin) {
        $api_endpoint = 'https://creatorsapi.amazon/catalog/v1/getItems';
    } else {
        $api_endpoint = 'https://creatorsapi.amazon/catalog/v1/searchItems';
    }
    gizmo_amazon_debug('API Endpoint: ' . $api_endpoint);
    
    $response = wp_remote_post($api_endpoint, [
        'timeout' => 30,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Content-Type' => 'application/json',
            'x-marketplace' => $marketplace,
        ],
        'body' => $payload,
    ]);

    if (is_wp_error($response)) {
        gizmo_amazon_debug('❌ WP_Error: ' . $response->get_error_message());
        return $response;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $raw_body = wp_remote_retrieve_body($response);
    
    gizmo_amazon_debug('HTTP status: ' . $http_code);
    gizmo_amazon_debug('Raw response: ' . $raw_body);

    $body = json_decode($raw_body, true);

    // Handle errors
    if ($http_code >= 400 || isset($body['error']) || isset($body['message'])) {
        $error_msg = $body['message'] ?? $body['error_description'] ?? $body['error'] ?? 'Unknown API Error';
        $error_reason = $body['reason'] ?? '';
        $error_type = $body['type'] ?? '';
        
        gizmo_amazon_debug('❌ API Error: ' . $error_msg);
        gizmo_amazon_debug('   Reason: ' . $error_reason);
        gizmo_amazon_debug('   Type: ' . $error_type);
        
        // Provide helpful message for eligibility issues
        if ($error_reason === 'AssociateNotEligible') {
            $error_msg = 'Your Associates account is not eligible for Creators API. Please verify: (1) Account is approved, (2) You have 3+ qualifying sales in 180 days, (3) Creators API access is enabled in Associates Central.';
        }
        
        return new WP_Error('creators_api_error', $error_msg);
    }

    // Transform response to expected format (Creators API v3.x format)
    // Handle both getItems and searchItems response formats
    $items = [];
    if (isset($body['itemsResult']['items']) && is_array($body['itemsResult']['items'])) {
        // getItems response format
        $items = $body['itemsResult']['items'];
    } elseif (isset($body['searchResult']['items']) && is_array($body['searchResult']['items'])) {
        // searchItems response format
        $items = $body['searchResult']['items'];
        gizmo_amazon_debug('✅ Search returned ' . count($items) . ' items (total: ' . ($body['searchResult']['totalResultCount'] ?? 'unknown') . ')');
    }
    
    if (!empty($items)) {
        $transformed_products = [];
        foreach ($items as $item) {
            $transformed_products[] = [
                'DetailPageURL' => $item['detailPageURL'] ?? '#',
                'Images' => [
                    'Primary' => [
                        'Small' => [
                            'URL' => $item['images']['primary']['small']['url'] ?? ''
                        ],
                        'Medium' => [
                            'URL' => $item['images']['primary']['medium']['url'] ?? ''
                        ],
                    ]
                ],
                'ItemInfo' => [
                    'Title' => [
                        'DisplayValue' => $item['itemInfo']['title']['displayValue'] ?? 'Unknown Product'
                    ]
                ],
                'Offers' => [
                    'Listings' => [
                        [
                            'Price' => [
                                'DisplayAmount' => $item['offersV2']['listings'][0]['price']['displayAmount'] ?? $item['offersV2']['listings'][0]['price']['amount'] ?? 'Check Price'
                            ]
                        ]
                    ]
                ],
            ];
        }
        
        gizmo_amazon_debug('✅ Found ' . count($transformed_products) . ' items');
        
        // Cache successful results for 1 hour
        set_transient($cache_key, $transformed_products, 3600);
        
        return $transformed_products;
    }

    gizmo_amazon_debug('⚠️ No products in response');
    return new WP_Error('no_items', 'No products found for this keyword');
}

/**
 * Main router function to fetch products from Amazon
 * Routes to either Creators API or PA API 5.0 based on Customizer setting
 *
 * @param string $keyword The search keyword.
 * @return array|WP_Error Array of products on success, WP_Error on failure.
 */
function gizmo_get_amazon_products(string $keyword) {
    if (!get_theme_mod('gizmo_amazon_enabled', false)) {
        gizmo_amazon_debug('❌ Feature disabled in Customizer');
        return new WP_Error('disabled', 'Amazon feature is disabled in Customizer');
    }

    // Get API type from Customizer setting
    $api_type = get_theme_mod('gizmo_amazon_api_type', 'creators');
    
    gizmo_amazon_debug('API Type selected: ' . $api_type);
    
    if ($api_type === 'paapi5') {
        return gizmo_get_amazon_products_paapi5($keyword);
    } else {
        return gizmo_get_amazon_products_creators($keyword);
    }
}