<?php
/**
 * The template for displaying search forms
 *
 * @package Gizmodotech
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="search-form-1" class="screen-reader-text"><?php esc_html_e( 'Search for:', 'gizmodotech' ); ?></label>
	<input type="search" id="search-form-1" class="search-field" placeholder="<?php esc_attr_e( 'Search &hellip;', 'gizmodotech' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" class="search-submit">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path>
        </svg>
        <span class="screen-reader-text"><?php esc_html_e( 'Search', 'gizmodotech' ); ?></span>
    </button>
</form>
