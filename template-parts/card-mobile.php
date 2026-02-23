<?php
/**
 * Template part for displaying a mobile card.
 * Used in the homepage Mobiles section.
 *
 * @package gizmodotech-pro
 */
?>
<article <?php post_class('mobile-card'); ?>>
	<?php if (has_post_thumbnail()) : ?>
	<a class="mobile-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
		<?php the_post_thumbnail('gizmo-thumb',['loading'=>'lazy', 'alt' => esc_attr(get_the_title())]); ?>
	</a>
	<?php endif; ?>
	<div class="mobile-card__body">
		<h3 class="mobile-card__title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h3>
		<div class="mobile-card__meta">
			<time><?php echo esc_html(get_the_date()); ?></time>
		</div>
	</div>
</article>