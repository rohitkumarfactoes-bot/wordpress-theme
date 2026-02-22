<?php
/**
 * Pattern: Bento Grid
 * Returns the pattern configuration array for register_block_pattern().
 *
 * @package gizmodotech-pro
 */

return [
	'title'       => __( 'Bento Grid — Featured Posts', 'gizmodotech-pro' ),
	'description' => __( 'A Beebom-inspired bento grid with a hero card and smaller surrounding cards.', 'gizmodotech-pro' ),
	'categories'  => [ 'gizmodotech' ],
	'keywords'    => [ 'bento', 'grid', 'hero', 'featured', 'posts', 'cards' ],
	'content'     => '
<!-- wp:group {"className":"bento-grid","layout":{"type":"constrained","contentSize":"1320px"}} -->
<div class="wp-block-group bento-grid">

  <!-- Hero Card (col 1 full width) -->
  <!-- wp:group {"className":"bento-card bento-card--hero"} -->
  <div class="wp-block-group bento-card bento-card--hero">
    <!-- wp:image {"className":"bento-card__image","aspectRatio":"21/9","scale":"cover"} -->
    <figure class="wp-block-image bento-card__image"><img src="" alt="" style="aspect-ratio:21/9;object-fit:cover;" /></figure>
    <!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} -->
    <div class="wp-block-group bento-card__content">
      <!-- wp:paragraph {"className":"bento-card__category"} --><p class="bento-card__category">Category</p><!-- /wp:paragraph -->
      <!-- wp:heading {"level":2,"className":"bento-card__title"} --><h2 class="wp-block-heading bento-card__title">Hero Post Headline Goes Here</h2><!-- /wp:heading -->
      <!-- wp:paragraph {"className":"bento-card__excerpt"} --><p class="bento-card__excerpt">Short excerpt summarizing the article in two lines or less for maximum impact on readers.</p><!-- /wp:paragraph -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- Half Card 1 -->
  <!-- wp:group {"className":"bento-card bento-card--half"} -->
  <div class="wp-block-group bento-card bento-card--half">
    <!-- wp:image {"className":"bento-card__image","aspectRatio":"16/9","scale":"cover"} -->
    <figure class="wp-block-image bento-card__image"><img src="" alt="" style="aspect-ratio:16/9;object-fit:cover;" /></figure>
    <!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} -->
    <div class="wp-block-group bento-card__content">
      <!-- wp:paragraph {"className":"bento-card__category"} --><p class="bento-card__category">Reviews</p><!-- /wp:paragraph -->
      <!-- wp:heading {"level":3,"className":"bento-card__title"} --><h3 class="wp-block-heading bento-card__title">Second Featured Article Title</h3><!-- /wp:heading -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- Half Card 2 -->
  <!-- wp:group {"className":"bento-card bento-card--half"} -->
  <div class="wp-block-group bento-card bento-card--half">
    <!-- wp:image {"className":"bento-card__image","aspectRatio":"16/9","scale":"cover"} -->
    <figure class="wp-block-image bento-card__image"><img src="" alt="" style="aspect-ratio:16/9;object-fit:cover;" /></figure>
    <!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} -->
    <div class="wp-block-group bento-card__content">
      <!-- wp:paragraph {"className":"bento-card__category"} --><p class="bento-card__category">News</p><!-- /wp:paragraph -->
      <!-- wp:heading {"level":3,"className":"bento-card__title"} --><h3 class="wp-block-heading bento-card__title">Third Featured Article Title</h3><!-- /wp:heading -->
    </div>
    <!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- Third Cards (3-up) -->
  <!-- wp:group {"className":"bento-card bento-card--third"} -->
  <div class="wp-block-group bento-card bento-card--third">
    <!-- wp:image {"className":"bento-card__image"} --><figure class="wp-block-image bento-card__image"><img src="" alt="" /></figure><!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} --><div class="wp-block-group bento-card__content"><!-- wp:heading {"level":3,"className":"bento-card__title"} --><h3 class="wp-block-heading bento-card__title">Fourth Article</h3><!-- /wp:heading --></div><!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"bento-card bento-card--third"} -->
  <div class="wp-block-group bento-card bento-card--third">
    <!-- wp:image {"className":"bento-card__image"} --><figure class="wp-block-image bento-card__image"><img src="" alt="" /></figure><!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} --><div class="wp-block-group bento-card__content"><!-- wp:heading {"level":3,"className":"bento-card__title"} --><h3 class="wp-block-heading bento-card__title">Fifth Article</h3><!-- /wp:heading --></div><!-- /wp:group -->
  </div>
  <!-- /wp:group -->

  <!-- wp:group {"className":"bento-card bento-card--third"} -->
  <div class="wp-block-group bento-card bento-card--third">
    <!-- wp:image {"className":"bento-card__image"} --><figure class="wp-block-image bento-card__image"><img src="" alt="" /></figure><!-- /wp:image -->
    <!-- wp:group {"className":"bento-card__content"} --><div class="wp-block-group bento-card__content"><!-- wp:heading {"level":3,"className":"bento-card__title"} --><h3 class="wp-block-heading bento-card__title">Sixth Article</h3><!-- /wp:heading --></div><!-- /wp:group -->
  </div>
  <!-- /wp:group -->

</div>
<!-- /wp:group -->
',
];
