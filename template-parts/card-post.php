<?php
/**
 * Template part for displaying posts in a grid
 * Used by index.php and AJAX Load More
 *
 * @package gizmodotech-pro
 */

$categories = get_the_category();
$cat        = $categories ? $categories[0] : null;
$read       = function_exists('gizmo_get_reading_time') ? gizmo_get_reading_time(get_the_ID()) : ['label' => ''];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

	<?php if ( has_post_thumbnail() ) : ?>
	<a class="post-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1">
		<?php the_post_thumbnail( 'gizmo-card', [ 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ] ); ?>
	</a>
	<?php endif; ?>

	<div class="post-card__body">

		<?php if ( $cat ) : ?>
		<a class="post-cat-badge" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
			<?php echo esc_html( $cat->name ); ?>
		</a>
		<?php endif; ?>

		<h3 class="post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>

		<div class="post-card__meta">
			<?php echo get_avatar( get_the_author_meta('email'), 28, '', '', ['class'=>'post-card__avatar'] ); ?>
			<span class="post-card__author"><?php the_author(); ?></span>
			<span class="post-card__sep" aria-hidden="true">·</span>
			<time class="post-card__date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
				<?php echo esc_html( get_the_date( "M j, 'y" ) ); ?>
			</time>
			<span class="post-card__sep" aria-hidden="true">·</span>
			<span class="post-card__read">
				<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
				<?php echo esc_html( $read['label'] ); ?>
			</span>
		</div>

	</div>

</article>
