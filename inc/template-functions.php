<?php
/**
 * Functions which enhance the theme by hooking into WordPress
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

/**
 * Adds custom classes to the array of body classes
 */
function gizmodotech_body_class_additions( $classes ) {
	// Adds a class of hfeed to non-singular pages.
	if ( ! is_singular() ) {
		$classes[] = 'hfeed';
	}

	// Adds a class of no-sidebar when there is no sidebar present.
	if ( ! is_active_sidebar( 'sidebar-1' ) ) {
		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'gizmodotech_body_class_additions' );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments
 */
function gizmodotech_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'gizmodotech_pingback_header' );

/**
 * Add preconnect for Google Fonts
 */
function gizmodotech_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'gizmodotech-google-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'gizmodotech_resource_hints', 10, 2 );

/**
 * Custom logo output
 */
function gizmodotech_custom_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
	} else {
		?>
		<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="custom-logo-link" rel="home">
			<span class="site-title"><?php bloginfo( 'name' ); ?></span>
		</a>
		<?php
	}
}

/**
 * Display social media links
 */
function gizmodotech_social_links() {
	$social_links = array(
		'facebook'  => get_theme_mod( 'social_facebook', '' ),
		'twitter'   => get_theme_mod( 'social_twitter', '' ),
		'instagram' => get_theme_mod( 'social_instagram', '' ),
		'youtube'   => get_theme_mod( 'social_youtube', '' ),
		'linkedin'  => get_theme_mod( 'social_linkedin', '' ),
	);

	$social_icons = array(
		'facebook'  => '<svg width="20" height="20" fill="currentColor"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>',
		'twitter'   => '<svg width="20" height="20" fill="currentColor"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>',
		'instagram' => '<svg width="20" height="20" fill="currentColor"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>',
		'youtube'   => '<svg width="20" height="20" fill="currentColor"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.46a2.78 2.78 0 0 0-1.94 2A29 29 0 0 0 1 11.75a29 29 0 0 0 .46 5.33A2.78 2.78 0 0 0 3.4 19c1.72.46 8.6.46 8.6.46s6.88 0 8.6-.46a2.78 2.78 0 0 0 1.94-2 29 29 0 0 0 .46-5.25 29 29 0 0 0-.46-5.33z"></path><polygon points="9.75 15.02 15.5 11.75 9.75 8.48 9.75 15.02"></polygon></svg>',
		'linkedin'  => '<svg width="20" height="20" fill="currentColor"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path><rect x="2" y="9" width="4" height="12"></rect><circle cx="4" cy="4" r="2"></circle></svg>',
	);

	$output = '<div class="social-links" style="display: flex; gap: 15px;">';
	
	foreach ( $social_links as $platform => $url ) {
		if ( ! empty( $url ) ) {
			$output .= sprintf(
				'<a href="%s" target="_blank" rel="noopener noreferrer" aria-label="%s" style="color: var(--text-secondary); transition: color var(--transition-fast);">%s</a>',
				esc_url( $url ),
				esc_attr( ucfirst( $platform ) ),
				$social_icons[ $platform ]
			);
		}
	}
	
	$output .= '</div>';
	
	echo $output; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}
