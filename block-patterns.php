<?php
/**
 * Register Block Patterns for Gizmodotech
 */

function gizmodotech_register_block_patterns() {
    register_block_pattern_category(
        'gizmodotech',
        array('label' => __('Gizmodotech', 'gizmodotech'))
    );

    // 1. Hero Section Pattern (Large Left, 2 Small Right)
    register_block_pattern(
        'gizmodotech/hero-section',
        array(
            'title'       => __('Gizmodotech: Hero Section', 'gizmodotech'),
            'categories'  => array('gizmodotech'),
            'description' => __('A hero section with one large featured post and two side posts.', 'gizmodotech'),
            'content'     => '<!-- wp:group {"align":"full","className":"gizmodotech-hero-section","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignfull gizmodotech-hero-section"><!-- wp:columns {"align":"wide","style":{"spacing":{"blockGap":"2rem"}}} -->
<div class="wp-block-columns alignwide"><!-- wp:column {"width":"66.66%"} -->
<div class="wp-block-column" style="flex-basis:66.66%"><!-- wp:query {"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"list"}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:group {"className":"hero-main-post relative-card"} -->
<div class="wp-block-group hero-main-post relative-card"><!-- wp:post-featured-image {"isLink":true,"height":"450px","align":"wide","style":{"border":{"radius":"12px"}}} /-->
<!-- wp:group {"className":"hero-overlay-content"} -->
<div class="wp-block-group hero-overlay-content"><!-- wp:post-terms {"term":"category","style":{"typography":{"textTransform":"uppercase","fontSize":"12px","fontWeight":"700"}},"className":"hero-badge"} /-->
<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"2rem","fontWeight":"700","lineHeight":"1.2"},"elements":{"link":{"color":{"text":"#ffffff"}}}},"textColor":"white"} /-->
<!-- wp:post-date {"style":{"typography":{"fontSize":"14px"}},"textColor":"white"} /--></div>
<!-- /wp:group --></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column -->

<!-- wp:column {"width":"33.33%"} -->
<div class="wp-block-column" style="flex-basis:33.33%"><!-- wp:query {"query":{"perPage":2,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"list"}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:group {"style":{"spacing":{"margin":{"bottom":"1.5rem"}}},"className":"hero-side-post"} -->
<div class="wp-block-group hero-side-post" style="margin-bottom:1.5rem"><!-- wp:post-featured-image {"isLink":true,"height":"200px","style":{"border":{"radius":"8px"}}} /-->
<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"1.1rem","fontWeight":"600","lineHeight":"1.4"},"spacing":{"margin":{"top":"0.5rem"}}}} /-->
<!-- wp:post-date {"style":{"typography":{"fontSize":"12px"}},"textColor":"gray"} /--></div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:column --></div>
<!-- /wp:columns --></div>
<!-- /wp:group -->',
        )
    );

    // 2. Category Grid Pattern
    register_block_pattern(
        'gizmodotech/category-grid',
        array(
            'title'       => __('Gizmodotech: Category Grid', 'gizmodotech'),
            'categories'  => array('gizmodotech'),
            'content'     => '<!-- wp:group {"align":"wide","style":{"spacing":{"margin":{"top":"3rem","bottom":"3rem"}}}} -->
<div class="wp-block-group alignwide" style="margin-top:3rem;margin-bottom:3rem"><!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"space-between"},"style":{"border":{"bottom":{"color":"#e5e7eb","width":"2px"}},"spacing":{"padding":{"bottom":"0.5rem"},"margin":{"bottom":"1.5rem"}}}} -->
<div class="wp-block-group" style="border-bottom-color:#e5e7eb;border-bottom-width:2px;margin-bottom:1.5rem;padding-bottom:0.5rem"><!-- wp:heading {"style":{"typography":{"fontSize":"1.5rem"}}} -->
<h2 class="wp-block-heading" style="font-size:1.5rem">Latest Reviews</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p><a href="#">View All →</a></p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:query {"query":{"perPage":4,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"","inherit":false},"displayLayout":{"type":"flex","columns":4}} -->
<div class="wp-block-query"><!-- wp:post-template -->
<!-- wp:group {"className":"grid-post-item"} -->
<div class="wp-block-group grid-post-item"><!-- wp:post-featured-image {"isLink":true,"height":"180px","style":{"border":{"radius":"8px"}}} /-->
<!-- wp:post-terms {"term":"category","style":{"typography":{"fontSize":"11px","textTransform":"uppercase","fontWeight":"700"},"spacing":{"margin":{"top":"0.75rem","bottom":"0.25rem"}}},"className":"has-primary-color"} /-->
<!-- wp:post-title {"isLink":true,"style":{"typography":{"fontSize":"1.1rem","fontWeight":"600","lineHeight":"1.4"}}} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template --></div>
<!-- /wp:query --></div>
<!-- /wp:group -->',
        )
    );
}
add_action('init', 'gizmodotech_register_block_patterns');