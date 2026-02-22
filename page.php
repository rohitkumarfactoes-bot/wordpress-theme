<?php
/**
 * The template for displaying all single pages
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="container container--content" style="padding-top: 3rem; padding-bottom: 3rem;">
	<?php while ( have_posts() ) : the_post(); ?>

		<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
			<header class="entry-header" style="margin-bottom: 2rem; text-align: center;">
				<h1 class="entry-title" style="font-size: clamp(2rem, 4vw, 3rem); font-weight: 800; letter-spacing: -0.03em;">
					<?php the_title(); ?>
				</h1>
			</header>

			<?php if ( has_post_thumbnail() ) : ?>
				<div class="entry-thumbnail" style="margin-bottom: 2rem; border-radius: var(--radius-lg); overflow: hidden;">
					<?php the_post_thumbnail( 'gizmo-wide' ); ?>
				</div>
			<?php endif; ?>

			<div class="single-body">
				<?php the_content(); ?>
			</div>
		</article>

	<?php endwhile; ?>
</div>

<?php
get_footer();