<?php
/**
 * Custom search form template
 *
 * @package Gizmodotech
 * @since 1.0.0
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" style="position: relative; max-width: 100%;">
	<label for="search-field" class="sr-only">
		<?php esc_html_e( 'Search for:', 'gizmodotech' ); ?>
	</label>
	<div class="search-form-wrapper" style="display: flex; align-items: center; border: 2px solid var(--border-color); border-radius: var(--radius-lg); overflow: hidden; background: var(--bg-card); transition: border-color var(--transition-fast);">
		<input 
			type="search" 
			id="search-field"
			class="search-field" 
			placeholder="<?php echo esc_attr_x( 'Search articles...', 'placeholder', 'gizmodotech' ); ?>" 
			value="<?php echo get_search_query(); ?>" 
			name="s"
			style="flex: 1; padding: 14px 18px; border: none; background: transparent; font-size: 16px; color: var(--text-primary); outline: none;"
		/>
		<button 
			type="submit" 
			class="search-submit"
			style="padding: 14px 24px; background: var(--color-primary); color: white; border: none; cursor: pointer; transition: background-color var(--transition-fast); font-weight: 600;"
			onmouseover="this.style.backgroundColor='var(--color-primary-dark)'"
			onmouseout="this.style.backgroundColor='var(--color-primary)'"
		>
			<svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
				<circle cx="11" cy="11" r="8"></circle>
				<path d="m21 21-4.35-4.35"></path>
			</svg>
			<span class="sr-only"><?php echo esc_html_x( 'Search', 'submit button', 'gizmodotech' ); ?></span>
		</button>
	</div>
</form>
