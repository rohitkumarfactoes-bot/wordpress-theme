<?php
/**
 * The template for displaying search forms
 *
 * @package Gizmodotech
 */
?>

<form role="search" method="get" class="search-form" action="<?php echo esc_url(home_url('/')); ?>">
    <label>
        <span class="screen-reader-text"><?php esc_html_e('Search for:', 'gizmodotech'); ?></span>
        <input type="search" 
               class="search-field" 
               placeholder="<?php esc_attr_e('Search...', 'gizmodotech'); ?>" 
               value="<?php echo get_search_query(); ?>" 
               name="s" />
    </label>
    <button type="submit" class="search-submit">
        <?php esc_html_e('Search', 'gizmodotech'); ?>
    </button>
</form>
