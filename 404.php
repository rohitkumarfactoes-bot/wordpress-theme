<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="hp-container" style="padding-block: 5rem; text-align: center;">
	<header class="page-header">
		<h1 class="page-title" style="font-size: clamp(3rem, 10vw, 6rem); font-weight: 800; color: var(--color-primary); line-height: 1; margin-bottom: 1rem;">404</h1>
		<h2 class="page-subtitle" style="font-size: 1.5rem; margin-bottom: 1.5rem;"><?php esc_html_e( 'Oops! That page can&rsquo;t be found.', 'gizmodotech-pro' ); ?></h2>
	</header>

	<div class="page-content" style="max-width: 600px; margin: 0 auto;">
		<p style="margin-bottom: 2rem; color: var(--text-secondary);">
			<?php esc_html_e( 'It looks like nothing was found at this location. Maybe try a search?', 'gizmodotech-pro' ); ?>
		</p>
		<?php get_search_form(); ?>
		
		<div style="margin-top: 3rem;">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="footer-subscribe__btn" style="text-decoration: none; display: inline-block;">
				<?php esc_html_e( 'Back to Home', 'gizmodotech-pro' ); ?>
			</a>
		</div>
	</div>
</div>

<?php
get_footer();