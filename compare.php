<?php
/**
 * Template Name: Compare Devices
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<div class="container" style="padding-top: 2rem; padding-bottom: 4rem;">
	<div class="compare-container">

		<!-- Search Box -->
		<div class="search-box">
			<div class="highlight-differences">
				<div class="highlight-toggle">
					<input id="my-compare-input-1" type="checkbox" class="compare-input-1">
					<label for="my-compare-input-1" class="highlight-text">
						<?php esc_html_e('Highlight Differences', 'gizmodotech-pro'); ?>
					</label>
				</div>
				<!-- Share button injected by JS -->
			</div>

			<?php for ($i = 1; $i <= 3; $i++) : ?>
				<div class="search-group" data-slot="<?php echo esc_attr($i); ?>">
					<input
						type="text"
						id="search-input-<?php echo esc_attr($i); ?>"
						placeholder="<?php esc_attr_e('Search device...', 'gizmodotech-pro'); ?>"
						autocomplete="off"
					>
					<div id="search-results-<?php echo esc_attr($i); ?>" class="search-results"></div>
					<div id="compare-list-<?php echo esc_attr($i); ?>" class="compare-list"></div>
				</div>
			<?php endfor; ?>
		</div>

		<!-- Comparison Table -->
		<div class="comparison-table-container">
			<!-- Table is built by JS -->
		</div>

	</div>
</div>

<?php get_footer(); ?>