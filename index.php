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
				$categories = get_the_category();
				$cat        = $categories ? $categories[0] : null;
				$read       = gizmo_get_reading_time( get_the_ID() );

				// Hero card for first post on first page
				$is_hero = ( $post_index === 0 && ! is_paged() );
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class( $is_hero ? 'post-card post-card--hero' : 'post-card' ); ?>>

					<?php if ( has_post_thumbnail() ) : ?>
					<a class="post-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1">
						<?php the_post_thumbnail( $is_hero ? 'gizmo-hero' : 'gizmo-card', [
							'loading' => $is_hero ? 'eager' : 'lazy',
							'alt'     => esc_attr( get_the_title() ),
						] ); ?>
						<?php if ( $is_hero ) : ?>
						<span class="trending-badge">🔥 <?php esc_html_e( 'Trending', 'gizmodotech-pro' ); ?></span>
						<?php endif; ?>
					</a>
					<?php endif; ?>

					<div class="post-card__body">

						<?php gizmo_the_post_categories( get_the_ID(), 'post-cat-badge' ); ?>

						<h2 class="post-card__title">
							<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
						</h2>

						<?php if ( $is_hero ) : ?>
						<p class="post-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 22 ) ); ?></p>
						<?php endif; ?>

						<div class="post-card__meta">
							<?php echo get_avatar( get_the_author_meta('email'), 28, '', '', ['class'=>'post-card__avatar'] ); // phpcs:ignore ?>
							<span class="post-card__author"><?php the_author(); ?></span>
							<span class="post-card__sep" aria-hidden="true">·</span>
							<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date('c') ); ?>">
								<?php echo esc_html( get_the_date("M j, 'y") ); ?>
							</time>
							<span class="post-card__sep" aria-hidden="true">·</span>
							<span class="post-card__read">
								<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
								<?php echo esc_html( $read['label'] ); ?>
							</span>
						</div>

					</div><!-- /.post-card__body -->

				</article>

				<?php $post_index++; ?>
			<?php endwhile; ?>
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
