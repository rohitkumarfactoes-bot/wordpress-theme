<?php
/**
 * Template part for displaying posts in a grid
 * Used by index.php and AJAX Load More
 */

$cats = get_the_category();
$cat  = $cats ? $cats[0] : null;
$read = function_exists('gizmo_get_reading_time') ? gizmo_get_reading_time(get_the_ID()) : ['label' => ''];
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

	<?php if (has_post_thumbnail()) : ?>
	<a class="post-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php the_post_thumbnail('gizmo-card', ['loading' => 'lazy', 'alt' => '']); ?>
	</a>
	<?php endif; ?>

	<div class="post-card__body">
		<?php if ($cat) : ?>
		<a class="post-cat-badge" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
			<?php echo esc_html($cat->name); ?>
		</a>
		<?php endif; ?>

		<h2 class="post-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h2>

		<div class="post-card__meta">
			<?php echo get_avatar(get_the_author_meta('email'), 28, '', '', ['class'=>'post-card__avatar']); ?>
			<span class="post-card__author"><?php the_author(); ?></span>
			<span class="post-card__sep" aria-hidden="true">·</span>
			<time class="post-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date("M j, 'y")); ?></time>
			<span class="post-card__sep" aria-hidden="true">·</span>
			<span class="post-card__read"><?php echo esc_html($read['label']); ?></span>
		</div>
	</div>
</article>