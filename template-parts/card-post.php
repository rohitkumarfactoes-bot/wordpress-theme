<?php
/**
 * Template Part: Post Card
 * Reusable card for loops, archives, related posts.
 *
 * @package gizmodotech-pro
 */

$categories = get_the_category();
$cat        = $categories ? $categories[0] : null;
$read       = gizmo_get_reading_time( get_the_ID() );
?>

<article class="news-card <?php post_class(); ?>">

	<?php if ( has_post_thumbnail() ) : ?>
	<a class="news-card__image" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php the_post_thumbnail( 'gizmo-thumb', [ 'loading' => 'lazy', 'alt' => '' ] ); ?>
	</a>
	<?php endif; ?>

	<div class="news-card__content">

		<?php if ( $cat ) : ?>
		<a class="bento-card__category" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>"
		   style="font-size:.65rem;margin-bottom:.5rem;display:inline-flex;">
			<?php echo esc_html( $cat->name ); ?>
		</a>
		<?php endif; ?>

		<h3 class="news-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="news-card__meta">
			<span><?php the_author(); ?></span>
			<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( "M j, 'y" ) ); ?>
			</time>
			<span><?php echo esc_html( $read['label'] ); ?></span>
		</div>

	</div>

</article>
