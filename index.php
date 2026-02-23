<?php
/**
 * Gizmodotech Pro — index.php
 *
 * WordPress uses this file for:
 *  - The blog posts index (when Settings > Reading = "Your latest posts")
 *  - The "Posts page" (when a static front page is set)
 *  - Category / tag / author / date archives
 *
 * For the FRONT PAGE with bento grid, WordPress uses front-page.php
 * (created separately). This file handles the blog archive / posts page.
 *
 * @package gizmodotech-pro
 */

get_header();

// Determine context
$is_home    = is_home();     // posts index (blog page)
$is_archive = is_archive();
$is_search  = is_search();
?>

<div class="archive-wrap">
	<div class="archive-container">

		<?php
		/* ── Page title for archives ── */
		if ( $is_archive || $is_search ) :
		?>
		<header class="archive-header">
			<h1 class="archive-title">
				<?php
				if ( is_search() ) {
					printf(
						/* translators: %s: search query */
						esc_html__( 'Search Results for: "%s"', 'gizmodotech-pro' ),
						get_search_query()
					);
				} elseif ( is_category() ) {
					single_cat_title();
				} elseif ( is_tag() ) {
					echo '#' . single_tag_title( '', false ); // phpcs:ignore
				} elseif ( is_author() ) {
					the_author();
				} else {
					the_archive_title();
				}
				?>
			</h1>
			<?php if ( is_category() && category_description() ) : ?>
			<p class="archive-desc"><?php echo esc_html( category_description() ); ?></p>
			<?php endif; ?>
		</header>
		<?php endif; ?>

		<?php if ( have_posts() ) : ?>

		<!-- Posts Grid -->
		<div class="posts-grid" id="posts-container">
			<?php
			$post_index = 0;
			while ( have_posts() ) :
				the_post();

				// Hero card for first post on first page
				set_query_var( 'is_hero', ( $post_index === 0 && ! is_paged() ) );
				get_template_part( 'template-parts/content', 'archive' );

				$post_index++;
			endwhile; ?>
		</div><!-- /.posts-grid -->

		<!-- Pagination -->
		<nav class="posts-pagination" aria-label="<?php esc_attr_e( 'Posts navigation', 'gizmodotech-pro' ); ?>">
			<?php
			echo paginate_links([
				'prev_text' => '&#8592; ' . __( 'Prev', 'gizmodotech-pro' ),
				'next_text' => __( 'Next', 'gizmodotech-pro' ) . ' &#8594;',
				'before_page_number' => '<span>',
				'after_page_number'  => '</span>',
			]);
			?>
		</nav>

		<?php else : ?>

		<div class="no-results">
			<h2><?php esc_html_e( 'Nothing found', 'gizmodotech-pro' ); ?></h2>
			<p><?php esc_html_e( 'Try a different search or browse by category.', 'gizmodotech-pro' ); ?></p>
			<?php get_search_form(); ?>
		</div>

		<?php endif; ?>

	</div><!-- /.archive-container -->
</div><!-- /.archive-wrap -->

<?php get_footer(); ?>
