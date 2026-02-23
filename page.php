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

				<?php if ( comments_open() || get_comments_number() ) : ?>
					<?php comments_template(); ?>
				<?php endif; ?>

			<?php endwhile; ?>
		</div>

		<?php get_sidebar(); ?>
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

			<?php if ( comments_open() || get_comments_number() ) : ?>
				<?php comments_template(); ?>
			<?php endif; ?>
		<?php 
		endwhile; 
		?>
	</div>
	<?php endif; ?>

</div>

<?php get_footer(); ?>