<?php
/**
 * Ad Injection Fixes for Gizmodotech Theme
 * 
 * @package gizmodotech-pro
 */

if (!defined('ABSPATH')) exit;

/**
 * Fix 1: Extend ad injection to support technews and review post types
 */
remove_filter('the_content', 'gizmo_inject_ads_in_content', 20);
add_filter('the_content', 'gizmo_inject_ads_in_content_fixed', 20);

function gizmo_inject_ads_in_content_fixed($content) {
	// Debug mode - add ?gizmo_ad_debug=1 to URL
	$debug = isset($_GET['gizmo_ad_debug']) && current_user_can('manage_options');
	
	// Support for technews and review post types in addition to post and page
	$supported_post_types = ['post', 'page', 'technews', 'review', 'reviews'];
	
	if (!is_singular($supported_post_types)) {
		if ($debug) $content = '<!-- DEBUG: Not supported post type -->' . $content;
		return $content;
	}
	
	if (!get_theme_mod('gizmo_ads_enabled', false)) {
		if ($debug) $content = '<!-- DEBUG: Ads disabled in Customizer -->' . $content;
		return $content;
	}
	
	// Check minimum word count
	$min_words = get_theme_mod('gizmo_ads_auto_min_words', 300);
	$content_words = str_word_count(strip_tags($content));
	if ($content_words < $min_words) {
		if ($debug) $content = '<!-- DEBUG: Content too short (' . $content_words . ' words, min ' . $min_words . ') -->' . $content;
		return $content;
	}
	
	$auto_mode = get_theme_mod('gizmo_ads_auto_mode', 'manual');
	
	if ($debug) {
		$content = '<!-- DEBUG: Ads enabled, mode=' . $auto_mode . ', words=' . $content_words . ' -->' . $content;
	}
	
	if ($auto_mode === 'auto') {
		// Smart Auto Placement like AdSense Auto Ads
		$content = gizmo_smart_auto_ads($content, $debug);
	} else {
		// Manual Placement
		$content = gizmo_manual_ad_placement($content, $debug);
	}

	return $content;
}

/**
 * Fix 2: Add homepage/blog feed ad injection
 */
add_action('loop_start', 'gizmo_inject_feed_ads');
function gizmo_inject_feed_ads($query) {
	// Only run on main query in blog/feed/homepage context
	if (!$query->is_main_query() || !is_home() && !is_front_page() && !is_archive()) {
		return;
	}
	
	// Only inject on first post of first page
	if ($query->current_post !== 0 || get_query_var('paged') > 1) {
		return;
	}
	
	if (!get_theme_mod('gizmo_ads_enabled', false)) {
		return;
	}
	
	// Check if we should show feed ads
	$show_feed_ads = get_theme_mod('gizmo_ads_show_in_feed', true);
	if (!$show_feed_ads) {
		return;
	}
	
	// Get feed ad code
	$feed_ad_code = get_theme_mod('gizmo_ad_feed_code', '');
	if (empty($feed_ad_code)) {
		return;
	}
	
	// Inject ad after first post
	add_action('loop_end', function($q) use ($feed_ad_code) {
		if ($q->current_post === 0) {
			echo '<div class="feed-ad-wrapper" style="margin: 2rem 0; text-align: center;">';
			echo $feed_ad_code;
			echo '</div>';
		}
	});
}

/**
 * Add Customizer setting for feed ads
 */
add_action('customize_register', 'gizmo_add_feed_ad_settings');
function gizmo_add_feed_ad_settings($wp_customize) {
	// Add feed ad section
	$wp_customize->add_section('gizmo_feed_ads', array(
		'title' => __('Feed/Archive Ads', 'gizmodotech-pro'),
		'panel' => 'gizmo_ads_panel',
		'priority' => 25,
	));
	
	// Enable feed ads
	$wp_customize->add_setting('gizmo_ads_show_in_feed', array(
		'default' => true,
		'sanitize_callback' => 'wp_validate_boolean',
	));
	
	$wp_customize->add_control('gizmo_ads_show_in_feed', array(
		'label' => __('Show Ads in Blog Feed/Archives', 'gizmodotech-pro'),
		'section' => 'gizmo_feed_ads',
		'type' => 'checkbox',
	));
	
	// Feed ad code
	$wp_customize->add_setting('gizmo_ad_feed_code', array(
		'default' => '',
		'sanitize_callback' => 'gizmo_sanitize_ad_code',
	));
	
	$wp_customize->add_control('gizmo_ad_feed_code', array(
		'label' => __('Feed Ad Code (HTML/JS)', 'gizmodotech-pro'),
		'description' => __('Ad code to show after first post in blog feed/archives', 'gizmodotech-pro'),
		'section' => 'gizmo_feed_ads',
		'type' => 'textarea',
	));
}