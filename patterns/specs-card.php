<?php
/**
 * Pattern: Best Phones Specs Card
 *
 * @package gizmodotech-pro
 */

return [
	'title'       => __( 'Best Phones Specs Card', 'gizmodotech-pro' ),
	'description' => __( 'A detailed specifications card for the "Best Phones" section.', 'gizmodotech-pro' ),
	'categories'  => [ 'gizmodotech' ],
	'content'     => '
<!-- wp:group {"className":"main-cont-specs","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group main-cont-specs">
	<!-- wp:group {"className":"specs-img-wrap","layout":{"type":"flex","orientation":"vertical","justifyContent":"left"}} -->
	<div class="wp-block-group specs-img-wrap">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="https://placehold.co/600x800" alt="Phone Image"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"specs-text-wrap","layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group specs-text-wrap">
		<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph {"placeholder":"Price"} -->
			<p>₹14,999</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"text-sm"} -->
			<p class="text-sm">4 + 128GB</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"flex"}} -->
		<div class="wp-block-group">
			<!-- Spec 1 -->
			<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"min-img"} -->
				<figure class="wp-block-image size-full min-img"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/processor-gradient-icon-1.png" alt="icon"/></figure>
				<!-- /wp:image -->
				<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group">
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Display</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">6.7" FHD+</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->

			<!-- Spec 2 -->
			<!-- wp:group {"layout":{"type":"flex","flexWrap":"nowrap"}} -->
			<div class="wp-block-group">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"min-img"} -->
				<figure class="wp-block-image size-full min-img"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/processor-gradient-icon-1.png" alt="icon"/></figure>
				<!-- /wp:image -->
				<!-- wp:group {"layout":{"type":"flex","orientation":"vertical"}} -->
				<div class="wp-block-group">
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Processor</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Snapdragon 4</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:group -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"layout":{"type":"flex","justifyContent":"space-between"}} -->
		<div class="wp-block-group">
			<!-- wp:paragraph -->
			<p>Buy Now</p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"layout":{"type":"flex"}} -->
			<div class="wp-block-group">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
				<figure class="wp-block-image size-full"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png" alt="buy-amazon"/></figure>
				<!-- /wp:image -->
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
				<figure class="wp-block-image size-full"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png" alt="buy-flipkart"/></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:paragraph {"className":"text-cm","fontSize":"xs"} -->
		<p class="text-cm has-xs-font-size"><a href="#">See full specifications</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->',
];