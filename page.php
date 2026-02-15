<?php
/**
 * The template for displaying all pages
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header();
?>

<main id="primary" class="site-main">
	<div class="container">
		
		<?php
		while ( have_posts() ) :
			the_post();
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				
				<?php if ( has_post_thumbnail() ) : ?>
					<div class="page-featured-image" style="margin-bottom: 40px; border-radius: var(--radius-lg); overflow: hidden;">
						<?php the_post_thumbnail( 'large', array( 'alt' => the_title_attribute( array( 'echo' => false ) ) ) ); ?>
					</div>
				<?php endif; ?>

				<header class="entry-header" style="margin-bottom: 30px;">
					<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
				</header>

				<div class="entry-content" style="max-width: 800px; margin: 0 auto;">
					<?php
					the_content();

					wp_link_pages( array(
						'before' => '<div class="page-links">' . esc_html__( 'Pages:', 'gizmodotech' ),
						'after'  => '</div>',
					) );
					?>
				</div>

				<?php if ( get_edit_post_link() ) : ?>
					<footer class="entry-footer" style="margin-top: 40px; padding-top: 20px; border-top: 1px solid var(--border-color);">
						<?php
						edit_post_link(
							sprintf(
								wp_kses(
									/* translators: %s: Name of current post. Only visible to screen readers */
									__( 'Edit <span class="sr-only">%s</span>', 'gizmodotech' ),
									array(
										'span' => array(
											'class' => array(),
										),
									)
								),
								wp_kses_post( get_the_title() )
							),
							'<span class="edit-link">',
							'</span>'
						);
						?>
					</footer>
				<?php endif; ?>

			</article>

			<?php
			// If comments are open or we have at least one comment, load up the comment template.
			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
		?>

	</div>
</main>

<?php
get_footer();
