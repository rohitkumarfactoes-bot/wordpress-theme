<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">
		<section class="error-404 not-found">
			<div class="page-content" style="text-align: center; padding: 60px 20px;">
				
				<div class="error-404-icon" style="font-size: 120px; margin-bottom: 20px;">
					<span style="color: var(--color-accent);">404</span>
				</div>
				
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'gizmodotech' ); ?></h1>
				</header>

				<div class="error-content">
					<p style="font-size: 18px; color: var(--text-secondary); margin-bottom: 30px;">
						<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try searching?', 'gizmodotech' ); ?>
					</p>

					<div class="search-form-wrapper" style="max-width: 600px; margin: 0 auto 40px;">
						<?php get_search_form(); ?>
					</div>

					<div class="error-404-links">
						<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary" style="margin-right: 10px;">
							<?php esc_html_e( 'Back to Home', 'gizmodotech' ); ?>
						</a>
						<a href="<?php echo esc_url( home_url( '/blog' ) ); ?>" class="btn btn-secondary">
							<?php esc_html_e( 'View Latest Posts', 'gizmodotech' ); ?>
						</a>
					</div>
				</div>

				<?php
				// Popular posts widget
				if ( is_active_sidebar( 'sidebar-1' ) ) :
				?>
					<div class="popular-posts" style="margin-top: 60px;">
						<h2><?php esc_html_e( 'You Might Also Like', 'gizmodotech' ); ?></h2>
						
						<div class="posts-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-top: 30px;">
							<?php
							// Get recent posts
							$recent_posts = new WP_Query( array(
								'post_type'      => 'post',
								'posts_per_page' => 3,
								'post_status'    => 'publish',
							) );

							if ( $recent_posts->have_posts() ) :
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
											<h3>
												<a href="<?php the_permalink(); ?>">
													<?php the_title(); ?>
												</a>
											</h3>
											<p><?php echo wp_trim_words( get_the_excerpt(), 15 ); ?></p>
										</div>
									</article>
									<?php
								endwhile;
								wp_reset_postdata();
							endif;
							?>
						</div>
					</div>
				<?php endif; ?>

			</div>
		</section>
	</div>
</main>

<?php
get_footer();
