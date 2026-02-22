<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="container" style="text-align: center; padding: 6rem 1rem;">
	<header class="page-header">
		<h1 class="page-title" style="font-size: 6rem; font-weight: 800; color: var(--color-primary); line-height: 1;">404</h1>
		<h2 style="font-size: 2rem; margin-bottom: 1rem;"><?php esc_html_e( 'Page Not Found', 'gizmodotech-pro' ); ?></h2>
	</header>

	<div class="page-content" style="max-width: 500px; margin: 0 auto;">
		<p style="color: var(--text-secondary); margin-bottom: 2rem;">
			<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'gizmodotech-pro' ); ?>
		</p>
		<?php get_search_form(); ?>
		<div style="margin-top: 2rem;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary"><?php esc_html_e( 'Back to Home', 'gizmodotech-pro' ); ?></a>
		</div>
	</div>
</div>

<?php
get_footer();