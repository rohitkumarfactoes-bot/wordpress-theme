<?php
/**
 * Gizmodotech Pro — page.php
 * Template for displaying all single pages (About, Contact, etc.)
 *
 * @package gizmodotech-pro
 */

get_header();

// Get layout selection (default to sidebar)
$layout = get_post_meta(get_the_ID(), '_gizmo_page_layout', true) ?: 'sidebar';

// Define styles for narrow layout
$content_style = ($layout === 'narrow') ? 'max-width: 800px; margin: 0 auto;' : '';
?>

<div class="hp-container" style="padding-top: 3rem; padding-bottom: 3rem;">
	
	<?php if ( $layout === 'sidebar' ) : ?>
	<!-- ── Layout: Sidebar (Grid) ── -->
	<div class="single-layout" style="padding:0; max-width:none;">
		
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
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=>'']); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Latest Reviews -->
			<?php
			$reviews_q = new WP_Query([
				'post_type'      => 'reviews',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'orderby'        => 'date',
				'order'          => 'DESC',
			]);
			if ($reviews_q->have_posts()) :
			?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('Latest Reviews','gizmodotech-pro'); ?></h3>
				<div class="sidebar-related">
					<?php while ($reviews_q->have_posts()) : $reviews_q->the_post(); ?>
					<a class="sidebar-related__item" href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail()) : ?>
						<div class="sidebar-related__thumb">
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=>'']); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>
		</aside>
	</div>

	<?php else : ?>
	<!-- ── Layout: No Sidebar / Narrow ── -->
	<div class="single-content" style="<?php echo esc_attr($content_style); ?>">
		<?php 
		while (have_posts()) : the_post(); 
		?>
			<header class="single-header">
				<h1 class="single-title"><?php the_title(); ?></h1>
			</header>
			<div class="single-body">
				<?php the_content(); ?>
			</div>
		<?php 
		endwhile; 
		?>
	</div>
	<?php endif; ?>

</div>

<?php get_footer(); ?>