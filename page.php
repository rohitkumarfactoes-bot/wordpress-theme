<?php
/**
 * Gizmodotech Pro — page.php
 * Template for displaying all single pages (About, Contact, etc.)
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="hp-container" style="padding-top: 3rem; padding-bottom: 3rem;">
	<div class="single-layout">
		
		<!-- Left Column: Content -->
		<div class="single-content">
			<?php while (have_posts()) : the_post(); ?>
				<header class="single-header">
					<h1 class="single-title"><?php the_title(); ?></h1>
				</header>
				<div class="single-body">
					<?php the_content(); ?>
				</div>
			<?php endwhile; ?>
		</div>

		<!-- Right Column: Sidebar -->
		<aside class="single-sidebar">
			<?php if (is_active_sidebar('sidebar-1')) : ?>
				<?php dynamic_sidebar('sidebar-1'); ?>
			<?php else : ?>
				<!-- Fallback: Latest News if no widgets are set -->
				<?php
				$fallback_q = new WP_Query([
					'post_type' => 'post',
					'posts_per_page' => 5,
					'ignore_sticky_posts' => 1
				]);
				if ($fallback_q->have_posts()) : ?>
				<div class="sidebar-widget">
					<h3 class="sidebar-widget__title"><?php esc_html_e('Latest News', 'gizmodotech-pro'); ?></h3>
					<div class="sidebar-related">
						<?php while ($fallback_q->have_posts()) : $fallback_q->the_post(); ?>
						<a class="sidebar-related__item" href="<?php the_permalink(); ?>">
							<?php if (has_post_thumbnail()) : ?>
							<div class="sidebar-related__thumb">
								<?php the_post_thumbnail('thumbnail', ['loading'=>'lazy']); ?>
							</div>
							<?php endif; ?>
							<span class="sidebar-related__title"><?php the_title(); ?></span>
						</a>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
				</div>
				<?php endif; ?>
			<?php endif; ?>
		</aside>

	</div>
</div>

<?php get_footer(); ?>