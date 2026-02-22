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
	<div class="single-content" style="max-width: 800px; margin: 0 auto;">
		<?php while (have_posts()) : the_post(); ?>
			<header class="single-header">
				<h1 class="single-title"><?php the_title(); ?></h1>
			</header>
			<div class="single-body">
				<?php the_content(); ?>
			</div>
		<?php endwhile; ?>
	</div>
</div>

<?php get_footer(); ?>