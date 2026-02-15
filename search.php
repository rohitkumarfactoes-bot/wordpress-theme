<?php
/**
 * The template for displaying search results pages
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">

		<header class="page-header" style="margin-bottom: 40px;">
			<h1 class="page-title">
				<?php
				/* translators: %s: search query. */
				printf( esc_html__( 'Search Results for: %s', 'gizmodotech' ), '<span>' . get_search_query() . '</span>' );
				?>
			</h1>
			
			<?php if ( have_posts() ) : ?>
				<p class="search-result-count" style="color: var(--text-secondary); margin-top: 10px;">
					<?php
					/* translators: %d: number of search results. */
					printf( esc_html( _n( 'Found %d result', 'Found %d results', $wp_query->found_posts, 'gizmodotech' ) ), number_format_i18n( $wp_query->found_posts ) );
					?>
				</p>
			<?php endif; ?>
		</header>

		<?php if ( have_posts() ) : ?>

			<div class="search-results-list">
				
				<?php
				while ( have_posts() ) :
					the_post();
					?>
					
					<article id="post-<?php the_ID(); ?>" <?php post_class( 'search-result-item' ); ?> style="display: flex; gap: 20px; padding: 30px 0; border-bottom: 1px solid var(--border-color);">
						
						<?php if ( has_post_thumbnail() ) : ?>
							<div class="search-result-thumbnail" style="flex-shrink: 0; width: 200px; height: 130px; overflow: hidden; border-radius: var(--radius-md);">
								<a href="<?php the_permalink(); ?>">
									<?php the_post_thumbnail( 'medium', array(
										'alt' => the_title_attribute( array( 'echo' => false ) ),
										'style' => 'width: 100%; height: 100%; object-fit: cover;'
									) ); ?>
								</a>
							</div>
						<?php endif; ?>

						<div class="search-result-content" style="flex: 1;">
							
							<div class="entry-meta" style="display: flex; gap: 15px; margin-bottom: 10px; font-size: 14px; color: var(--text-tertiary);">
								<?php
								$categories = get_the_category();
								if ( ! empty( $categories ) ) :
									?>
									<span class="post-category" style="color: var(--color-primary); font-weight: 600;">
										<?php echo esc_html( $categories[0]->name ); ?>
									</span>
								<?php endif; ?>
								
								<span class="post-date">
									<?php echo get_the_date(); ?>
								</span>
							</div>

							<h2 class="entry-title" style="margin-bottom: 10px; font-size: var(--text-2xl);">
								<a href="<?php the_permalink(); ?>" style="color: var(--text-primary);">
									<?php the_title(); ?>
								</a>
							</h2>

							<div class="entry-excerpt" style="color: var(--text-secondary); margin-bottom: 15px;">
								<?php echo wp_trim_words( get_the_excerpt(), 30 ); ?>
							</div>

							<a href="<?php the_permalink(); ?>" class="read-more-link" style="color: var(--color-primary); font-weight: 600; font-size: 14px;">
								<?php esc_html_e( 'Read More →', 'gizmodotech' ); ?>
							</a>

						</div>
					</article>

					<?php
				endwhile;

				// Pagination
				?>
				<div class="pagination" style="margin-top: 40px;">
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
				
				<div class="no-results" style="text-align: center; padding: 60px 20px;">
					<h2><?php esc_html_e( 'Nothing Found', 'gizmodotech' ); ?></h2>
					<p style="color: var(--text-secondary); margin-bottom: 30px;">
						<?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'gizmodotech' ); ?>
					</p>
					
					<div class="search-form-wrapper" style="max-width: 600px; margin: 0 auto;">
						<?php get_search_form(); ?>
					</div>

					<div class="search-suggestions" style="margin-top: 40px;">
						<h3><?php esc_html_e( 'Search Suggestions:', 'gizmodotech' ); ?></h3>
						<ul style="list-style: none; padding: 0; margin-top: 20px; text-align: left; max-width: 600px; margin-left: auto; margin-right: auto;">
							<li style="padding: 8px 0; color: var(--text-secondary);">
								<?php esc_html_e( '• Check your spelling', 'gizmodotech' ); ?>
							</li>
							<li style="padding: 8px 0; color: var(--text-secondary);">
								<?php esc_html_e( '• Try more general keywords', 'gizmodotech' ); ?>
							</li>
							<li style="padding: 8px 0; color: var(--text-secondary);">
								<?php esc_html_e( '• Try different keywords', 'gizmodotech' ); ?>
							</li>
							<li style="padding: 8px 0; color: var(--text-secondary);">
								<?php esc_html_e( '• Try fewer keywords', 'gizmodotech' ); ?>
							</li>
						</ul>
					</div>

					<?php
					// Show recent posts as alternative
					$recent_posts = new WP_Query( array(
						'post_type'      => 'post',
						'posts_per_page' => 3,
						'post_status'    => 'publish',
					) );

					if ( $recent_posts->have_posts() ) :
						?>
						<div class="recent-posts" style="margin-top: 60px;">
							<h3><?php esc_html_e( 'You Might Be Interested In:', 'gizmodotech' ); ?></h3>
							
							<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 20px; margin-top: 30px;">
								<?php
								while ( $recent_posts->have_posts() ) :
									$recent_posts->the_post();
									?>
									<article class="article-card">
										<?php if ( has_post_thumbnail() ) : ?>
											<a href="<?php the_permalink(); ?>">
												<?php the_post_thumbnail( 'medium' ); ?>
											</a>
										<?php endif; ?>
										
										<div class="article-card-content">
											<h4>
												<a href="<?php the_permalink(); ?>">
													<?php the_title(); ?>
												</a>
											</h4>
										</div>
									</article>
									<?php
								endwhile;
								wp_reset_postdata();
								?>
							</div>
						</div>
						<?php
					endif;
					?>

				</div>

			<?php endif; ?>

			</div>

	</div>
</main>

<?php
get_footer();
