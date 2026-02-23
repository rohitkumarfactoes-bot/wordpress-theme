	<?php
	/**
	 * Gizmodotech Pro — archive.php
	 * * Handles:
	 * - Custom Post Type Archives (Tech News, Reviews)
	 * - Category / Tag / Author Archives
	 * - Search Results
	 * * Renders a consistent grid layout with pagination.
	 *
	 * @package gizmodotech-pro
	 */

	get_header();

	// Determine context
	$is_archive = is_archive();
	$is_search  = is_search();

	// Add custom class based on post type (e.g., archive-reviews, archive-technews)
	$wrap_class = 'archive-wrap';
	if ( is_post_type_archive() ) {
		$pt = get_query_var( 'post_type' );
		if ( is_array( $pt ) ) $pt = $pt[0]; // Handle edge case if multiple types
		$wrap_class .= ' cpt-archive archive-' . $pt;
	}
	?>

	<div class="<?php echo esc_attr( $wrap_class ); ?>">
		<div class="archive-container">

			<?php
			/* ── Page title for archives ── */
			if ( $is_archive || $is_search ) :
			?>
			<header class="archive-header">
				<h1 class="archive-title">
					<?php
					if ( $is_search ) {
						printf(
							/* translators: %s: search query */
							esc_html__( 'Search Results for: "%s"', 'gizmodotech-pro' ),
							get_search_query()
						);
					} elseif ( is_category() ) {
						single_cat_title();
					} elseif ( is_tag() ) {
						echo '#' . single_tag_title( '', false );
					} elseif ( is_author() ) {
						the_author();
					} elseif ( is_post_type_archive() ) {
						post_type_archive_title();
					} else {
						the_archive_title();
					}
					?>
				</h1>
				<?php 
				// Removed esc_html to allow formatting (links/bold) in category descriptions
				if ( is_category() && category_description() ) : ?>
					<div class="archive-desc"><?php echo category_description(); ?></div>
				<?php endif; ?>
			</header>
			<?php endif; ?>

			<?php if ( have_posts() ) : ?>

				<div class="posts-grid" id="posts-container">
					<?php
					$post_index = 0;
					while ( have_posts() ) :
						the_post();

						/**
						 * Hero card logic: 
						 * Only applies to the very first post, on the first page, 
						 * and specifically for archives (not search results).
						 */
						$is_hero = ( $post_index === 0 && ! is_paged() && ! $is_search );
						set_query_var( 'is_hero', $is_hero );

						get_template_part( 'template-parts/content', 'archive' );

						$post_index++; 
					endwhile;
					?>
				</div><nav class="posts-pagination" aria-label="<?php esc_attr_e( 'Posts navigation', 'gizmodotech-pro' ); ?>">
					<?php
					echo paginate_links( array(
						'prev_text'          => '&#8592; ' . __( 'Prev', 'gizmodotech-pro' ),
						'next_text'          => __( 'Next', 'gizmodotech-pro' ) . ' &#8594;',
						'before_page_number' => '<span>',
						'after_page_number'  => '</span>',
					) );
					?>
				</nav>

			<?php else : ?>

				<div class="no-results">
					<h2><?php esc_html_e( 'Nothing found', 'gizmodotech-pro' ); ?></h2>
					<p><?php esc_html_e( 'Try a different search or browse by category.', 'gizmodotech-pro' ); ?></p>
					<?php get_search_form(); ?>
				</div>

			<?php endif; ?>

		</div></div><?php get_footer(); ?>