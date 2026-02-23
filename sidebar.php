<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package gizmodotech-pro
 */
?>

<aside class="single-sidebar" aria-label="<?php esc_attr_e('Sidebar','gizmodotech-pro'); ?>">
	
	<!-- Manual Widgets -->
	<?php if (is_active_sidebar('sidebar-1')) : ?>
		<?php dynamic_sidebar('sidebar-1'); ?>
	<?php endif; ?>

	<!-- Latest Tech News -->
	<?php
	$technews_q = new WP_Query([
		'post_type'      => 'technews',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'orderby'        => 'date',
		'order'          => 'DESC',
	]);
	if ($technews_q->have_posts()) :
	?>
	<div class="sidebar-widget">
		<h3 class="sidebar-widget__title"><?php esc_html_e('Latest Tech News','gizmodotech-pro'); ?></h3>
		<div class="sidebar-related">
			<?php while ($technews_q->have_posts()) : $technews_q->the_post(); ?>
			<a class="sidebar-related__item" href="<?php the_permalink(); ?>">
				<?php if (has_post_thumbnail()) : ?>
				<div class="sidebar-related__thumb">
					<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=> esc_attr(get_the_title())]); ?>
				</div>
				<?php endif; ?>
				<span class="sidebar-related__title"><?php the_title(); ?></span>
			</a>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
	<?php endif; ?>

	<!-- Note: "Latest Reviews" and other context-specific widgets 
	     can be added here or kept in specific templates if they rely on 
	     variables like $post_id exclusion. -->

</aside>