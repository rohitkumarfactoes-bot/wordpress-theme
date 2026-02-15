<?php
/**
 * Template part for displaying posts
 *
 * @package Gizmodotech
 * @since 1.0.0
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
	<?php gizmodotech_post_thumbnail(); ?>

	<div class="article-card-content">
		<header class="entry-header">
			<?php
			if ( is_singular() ) :
				the_title( '<h1 class="entry-title">', '</h1>' );
			else :
				the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
			endif;

			if ( 'post' === get_post_type() ) :
				?>
				<div class="entry-meta">
					<?php
					gizmodotech_posted_on();
					gizmodotech_posted_by();
					?>
				</div>
			<?php endif; ?>
		</header>

		<div class="entry-content">
			<?php the_excerpt(); ?>
		</div>

	</div>
</article>