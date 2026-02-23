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
<!-- wp:gizmodotech/flex-container {"className":"main-cont-specs"} -->
<div class="wp-block-gizmodotech-flex-container main-cont-specs">
	<!-- wp:gizmodotech/flex-container {"flexDirection":"column","justifyContent":"flex-start","alignItems":"center","gap":".5rem","className":"specs-img-wrap"} -->
	<div class="wp-block-gizmodotech-flex-container specs-img-wrap">
		<!-- wp:image {"sizeSlug":"large","linkDestination":"none"} -->
		<figure class="wp-block-image size-large"><img src="https://placehold.co/600x800" alt="Phone Image"/></figure>
		<!-- /wp:image -->
	</div>
	<!-- /wp:gizmodotech/flex-container -->

	<!-- wp:gizmodotech/flex-container {"flexDirection":"column","className":"specs-text-wrap"} -->
	<div class="wp-block-gizmodotech-flex-container specs-text-wrap">
		<!-- wp:gizmodotech/flex-container {"justifyContent":"space-between","alignItems":"center","gap":".2rem"} -->
		<div class="wp-block-gizmodotech-flex-container">
			<!-- wp:paragraph {"placeholder":"Price"} -->
			<p>₹14,999</p>
			<!-- /wp:paragraph -->
			<!-- wp:paragraph {"className":"text-sm"} -->
			<p class="text-sm">4 + 128GB</p>
			<!-- /wp:paragraph -->
		</div>
		<!-- /wp:gizmodotech/flex-container -->

		<!-- wp:gizmodotech/flex-container {"alignItems":"center","gap":".2rem"} -->
		<div class="wp-block-gizmodotech-flex-container">
			<!-- Spec 1 -->
			<!-- wp:gizmodotech/flex-container -->
			<div class="wp-block-gizmodotech-flex-container">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"min-img"} -->
				<figure class="wp-block-image size-full min-img"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/processor-gradient-icon-1.png" alt="icon"/></figure>
				<!-- /wp:image -->
				<!-- wp:gizmodotech/flex-container {"flexDirection":"column","gap":"0.2rem"} -->
				<div class="wp-block-gizmodotech-flex-container">
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Display</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">6.7" FHD+</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:gizmodotech/flex-container -->
			</div>
			<!-- /wp:gizmodotech/flex-container -->

			<!-- Spec 2 -->
			<!-- wp:gizmodotech/flex-container -->
			<div class="wp-block-gizmodotech-flex-container">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none","className":"min-img"} -->
				<figure class="wp-block-image size-full min-img"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/processor-gradient-icon-1.png" alt="icon"/></figure>
				<!-- /wp:image -->
				<!-- wp:gizmodotech/flex-container {"flexDirection":"column","gap":"0.2rem"} -->
				<div class="wp-block-gizmodotech-flex-container">
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Processor</p>
					<!-- /wp:paragraph -->
					<!-- wp:paragraph {"className":"text-sm"} -->
					<p class="text-sm">Snapdragon 4</p>
					<!-- /wp:paragraph -->
				</div>
				<!-- /wp:gizmodotech/flex-container -->
			</div>
			<!-- /wp:gizmodotech/flex-container -->
		</div>
		<!-- /wp:gizmodotech/flex-container -->

		<!-- wp:gizmodotech/flex-container {"justifyContent":"space-between","alignItems":"center","gap":".5rem"} -->
		<div class="wp-block-gizmodotech-flex-container">
			<!-- wp:paragraph -->
			<p>Buy Now</p>
			<!-- /wp:paragraph -->
			<!-- wp:gizmodotech/flex-container {"gap":"5px"} -->
			<div class="wp-block-gizmodotech-flex-container">
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
				<figure class="wp-block-image size-full"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png" alt="buy-amazon"/></figure>
				<!-- /wp:image -->
				<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
				<figure class="wp-block-image size-full"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png" alt="buy-flipkart"/></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:gizmodotech/flex-container -->
		</div>
		<!-- /wp:gizmodotech/flex-container -->

		<!-- wp:paragraph {"className":"text-cm","fontSize":"xs"} -->
		<p class="text-cm has-xs-font-size"><a href="#">See full specifications</a></p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:gizmodotech/flex-container -->
</div>
<!-- /wp:gizmodotech/flex-container -->',
];