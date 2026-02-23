<?php
/**
 * Template part for displaying posts in archive pages (index.php, archive.php)
 *
 * @package gizmodotech-pro
 */

$is_hero = get_query_var( 'is_hero', false );
$read    = gizmo_get_reading_time( get_the_ID() );
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