<?php
/**
 * Gizmodotech Pro — search.php
 *
 * Handles search results.
 * Renders a consistent grid layout with pagination.
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="archive-wrap">
	<div class="archive-container">

		<header class="archive-header">
			<h1 class="archive-title">
				<?php
				printf(
					/* translators: %s: search query */
					esc_html__( 'Search Results for: "%s"', 'gizmodotech-pro' ),
					get_search_query()
				);
				?>
			</h1>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="posts-grid" id="posts-container">
				<?php
				while ( have_posts() ) :
					the_post();
					// Use a dedicated template part for search results to allow for different markup (e.g., h3 instead of h2).
					get_template_part( 'content', 'search' );
				endwhile;
				?>
			</div>

			<nav class="posts-pagination" aria-label="<?php esc_attr_e( 'Posts navigation', 'gizmodotech-pro' ); ?>">
				<?php
				echo paginate_links( [
					'prev_text' => '&#8592; ' . __( 'Prev', 'gizmodotech-pro' ),
					'next_text' => __( 'Next', 'gizmodotech-pro' ) . ' &#8594;',
				] );
				?>
			</nav>

		<?php else : ?>
			<?php get_template_part( 'template-parts/content', 'none' ); ?>
		<?php endif; ?>

	</div>
</div>

<?php get_footer(); ?>