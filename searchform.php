<?php
/**
 * Custom Search Form
 */
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="display: flex; gap: 0.5rem;">
	<label style="flex: 1;">
		<span class="screen-reader-text"><?php echo _x( 'Search for:', 'label', 'gizmodotech-pro' ); ?></span>
		<input type="search" class="search-field footer-subscribe__input" style="color: var(--text-primary); border-color: var(--border-color); background: var(--bg-surface); width: 100%; max-width: none;"
			placeholder="<?php echo esc_attr_x( 'Search &hellip;', 'placeholder', 'gizmodotech-pro' ); ?>"
			value="<?php echo get_search_query(); ?>" name="s" />
	</label>
	<button type="submit" class="search-submit footer-subscribe__btn">
		<?php echo _x( 'Search', 'submit button', 'gizmodotech-pro' ); ?>
	</button>
</form>