<?php
/**
 * The template for displaying archive pages
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">
		
		<header class="page-header" style="margin-bottom: 40px; padding-bottom: 20px; border-bottom: 2px solid var(--border-color);">
			<?php
			the_archive_title( '<h1 class="page-title">', '</h1>' );
			the_archive_description( '<div class="archive-description">', '</div>' );
			?>
		</header>

		<div class="content-area">
			<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(320px, 1fr)); gap: 30px;">
				
				<?php
				if ( have_posts() ) :
					
					while ( have_posts() ) :
						the_post();
						?>
						
						<article id="post-<?php the_ID(); ?>" <?php post_class( 'article-card' ); ?>>
							
							<?php if ( has_post_thumbnail() ) : ?>
								<div class="article-thumbnail">
									<a href="<?php the_permalink(); ?>">
										<?php the_post_thumbnail( 'medium_large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
									</a>
								</div>
							<?php endif; ?>

							<div class="article-card-content">
								
								<div class="article-meta" style="display: flex; gap: 15px; margin-bottom: 12px; font-size: 14px; color: var(--text-tertiary);">
									<span class="post-date">
										<svg width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
											<use xlink:href="#icon-calendar"></use>
										</svg>
										<?php echo get_the_date(); ?>
									</span>
									
									<?php if ( get_comments_number() > 0 ) : ?>
										<span class="post-comments">
											<svg width="16" height="16" fill="currentColor" style="vertical-align: middle; margin-right: 4px;">
												<use xlink:href="#icon-comment"></use>
											</svg>
											<?php comments_number( '0', '1', '%' ); ?>
										</span>
									<?php endif; ?>
								</div>

								<h2 class="entry-title" style="margin-bottom: 12px;">
									<a href="<?php the_permalink(); ?>" style="color: var(--text-primary);">
										<?php the_title(); ?>
									</a>
								</h2>

								<div class="entry-excerpt" style="color: var(--text-secondary); margin-bottom: 15px;">
									<?php echo wp_trim_words( get_the_excerpt(), 20 ); ?>
								</div>

								<div class="article-footer" style="display: flex; justify-content: space-between; align-items: center;">
									<?php
									$categories = get_the_category();
									if ( ! empty( $categories ) ) :
										?>
										<span class="post-category" style="background-color: var(--color-primary); color: white; padding: 4px 12px; border-radius: var(--radius-full); font-size: 12px; font-weight: 600;">
											<?php echo esc_html( $categories[0]->name ); ?>
										</span>
									<?php endif; ?>
									
									<a href="<?php the_permalink(); ?>" class="read-more" style="color: var(--color-primary); font-weight: 600; font-size: 14px;">
										<?php esc_html_e( 'Read More →', 'gizmodotech' ); ?>
									</a>
								</div>

							</div>
						</article>

						<?php
					endwhile;

					// Pagination
					?>
					<div class="pagination" style="grid-column: 1 / -1; margin-top: 40px;">
						<?php
						the_posts_pagination( array(
							'mid_size'  => 2,
							'prev_text' => __( '← Previous', 'gizmodotech' ),
							'next_text' => __( 'Next →', 'gizmodotech' ),
						) );
						?>
					</div>
					<?php

				else :
					?>
					<div style="grid-column: 1 / -1; text-align: center; padding: 60px 20px;">
						<h2><?php esc_html_e( 'Nothing Found', 'gizmodotech' ); ?></h2>
						<p><?php esc_html_e( 'It seems we can\'t find what you\'re looking for. Perhaps searching can help.', 'gizmodotech' ); ?></p>
						<?php get_search_form(); ?>
					</div>
					<?php
				endif;
				?>

			</div>
		</div>

	</div>
</main>

<?php
get_footer();
