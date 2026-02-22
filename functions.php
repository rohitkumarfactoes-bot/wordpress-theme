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
	$words   = count(preg_split('/\s+/u', $content, -1, PREG_SPLIT_NO_EMPTY));
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
		$wp_customize->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_hex_color', 'transport' => 'postMessage']);
		$wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, $id, ['label' => __($label, GIZMO_TEXT), 'section' => 'gizmo_colors']));
	}

	/* ── Global Typography ── */
	$wp_customize->add_panel('gizmo_typo_panel', ['title' => __('Global Typography', GIZMO_TEXT), 'priority' => 20]);

	// Body font
	$wp_customize->add_section('gizmo_body_typo', ['title' => __('Body Font', GIZMO_TEXT), 'panel' => 'gizmo_typo_panel']);
	gizmo_add_font_control($wp_customize, 'body_font_family', 'gizmo_body_typo', __('Font Family', GIZMO_TEXT), "'Inter', sans-serif");
	gizmo_add_px_control($wp_customize,   'body_font_size',   'gizmo_body_typo', __('Font Size (px)', GIZMO_TEXT), 16, 12, 22);
	gizmo_add_num_control($wp_customize,  'body_line_height', 'gizmo_body_typo', __('Line Height', GIZMO_TEXT), 1.75, 1.2, 2.2, 0.05);

	// Heading font
	$wp_customize->add_section('gizmo_heading_typo', ['title' => __('Heading Font', GIZMO_TEXT), 'panel' => 'gizmo_typo_panel']);
	gizmo_add_font_control($wp_customize, 'heading_font_family',   'gizmo_heading_typo', __('Font Family', GIZMO_TEXT), "'Inter', sans-serif");
	gizmo_add_weight_control($wp_customize,'heading_font_weight',  'gizmo_heading_typo', __('Font Weight', GIZMO_TEXT), '800');
	gizmo_add_num_control($wp_customize,  'heading_line_height',   'gizmo_heading_typo', __('Line Height', GIZMO_TEXT), 1.2, 1.0, 1.8, 0.05);

	// Layout widths
	$wp_customize->add_section('gizmo_layout', ['title' => __('Layout Widths', GIZMO_TEXT), 'priority' => 25]);
	gizmo_add_px_control($wp_customize, 'content_width', 'gizmo_layout', __('Content Width (px)', GIZMO_TEXT), 800, 600, 1200);
	gizmo_add_px_control($wp_customize, 'wide_width',    'gizmo_layout', __('Wide Width (px)',    GIZMO_TEXT), 1320, 1000, 1920);
	gizmo_add_px_control($wp_customize, 'card_radius',   'gizmo_layout', __('Card Border Radius (px)', GIZMO_TEXT), 16, 0, 32);
}

/* Customizer helpers */
function gizmo_add_font_control($wpc, $id, $section, $label, $default) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'select', 'choices' => [
		"'Inter', sans-serif"         => 'Inter (Default)',
		"'Roboto', sans-serif"        => 'Roboto',
		"'Open Sans', sans-serif"     => 'Open Sans',
		"'Poppins', sans-serif"       => 'Poppins',
		"'Lato', sans-serif"          => 'Lato',
		"'Nunito', sans-serif"        => 'Nunito',
		"'Merriweather', serif"       => 'Merriweather (Serif)',
		"Georgia, serif"              => 'Georgia (Serif)',
		"'JetBrains Mono', monospace" => 'JetBrains Mono',
		"-apple-system, sans-serif"   => 'System UI',
	]]);
}
function gizmo_add_weight_control($wpc, $id, $section, $label, $default) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'select', 'choices' => ['400'=>'400 Regular','500'=>'500 Medium','600'=>'600 SemiBold','700'=>'700 Bold','800'=>'800 ExtraBold','900'=>'900 Black']]);
}
function gizmo_add_px_control($wpc, $id, $section, $label, $default, $min = 0, $max = 2000) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'absint', 'transport' => 'postMessage']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'number', 'input_attrs' => ['min' => $min, 'max' => $max, 'step' => 1]]);
}
function gizmo_add_num_control($wpc, $id, $section, $label, $default, $min, $max, $step) {
	$wpc->add_setting($id, ['default' => $default, 'sanitize_callback' => 'sanitize_text_field', 'transport' => 'postMessage']);
	$wpc->add_control($id, ['label' => $label, 'section' => $section, 'type' => 'number', 'input_attrs' => ['min' => $min, 'max' => $max, 'step' => $step]]);
}

/* Output Customizer CSS */
add_action('wp_head', 'gizmo_customizer_css', 99);
function gizmo_customizer_css() {
	$p  = get_theme_mod('primary_color',      '#2563EB');
	$a  = get_theme_mod('accent_color',       '#F59E0B');
	$nb = get_theme_mod('nav_bg',             '#FFFFFF');
	$fb = get_theme_mod('footer_bg',          '#0F172A');
	$ff = get_theme_mod('body_font_family',   "'Inter', sans-serif");
	$fs = get_theme_mod('body_font_size',     16);
	$lh = get_theme_mod('body_line_height',   1.75);
	$hf = get_theme_mod('heading_font_family',"'Inter', sans-serif");
	$hw = get_theme_mod('heading_font_weight','800');
	$hl = get_theme_mod('heading_line_height',1.2);
	$cw = get_theme_mod('content_width',      800);
	$ww = get_theme_mod('wide_width',         1320);
	$cr = get_theme_mod('card_radius',        16);

	$css = sprintf(
		':root{--color-primary:%s;--color-accent:%s;--bg-nav:%s;--bg-footer:%s;--font-sans:%s;--font-size-base:%spx;--line-height-normal:%s;--heading-font:%s;--heading-weight:%s;--heading-lh:%s;--width-content:%spx;--width-wide:%spx;--radius-lg:%spx;}',
		esc_attr($p), esc_attr($a), esc_attr($nb), esc_attr($fb),
		esc_attr($ff), absint($fs), esc_attr($lh),
		esc_attr($hf), esc_attr($hw), esc_attr($hl),
		absint($cw), absint($ww), absint($cr)
	);
	$css .= sprintf('body{font-family:var(--font-sans);font-size:var(--font-size-base);line-height:var(--line-height-normal);}');
	$css .= sprintf('h1,h2,h3,h4,h5,h6{font-family:var(--heading-font);font-weight:var(--heading-weight);line-height:var(--heading-lh);}');

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
	register_block_pattern_category('gizmodotech', ['label' => __('Gizmodotech Pro', GIZMO_TEXT)]);

	foreach (['bento-grid','pros-cons','specs-table'] as $pattern) {
		$file = GIZMO_DIR . '/patterns/' . $pattern . '.php';
		if (file_exists($file)) {
			register_block_pattern('gizmodotech/' . $pattern, require $file);
		}
	}
});

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
