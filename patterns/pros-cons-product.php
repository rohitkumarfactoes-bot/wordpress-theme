<?php
/**
 * Pattern: Product Pros & Cons Block
 *
 * @package gizmodotech-pro
 */

return [
	'title'       => __( 'Product Pros & Cons', 'gizmodotech-pro' ),
	'description' => __( 'A detailed product review block with image, price, rating, pros, cons, and buy buttons.', 'gizmodotech-pro' ),
	'categories'  => [ 'gizmodotech' ],
	'content'     => '
<!-- wp:group {"className":"pros-cons-block","layout":{"type":"flex","flexWrap":"nowrap"}} -->
<div class="wp-block-group pros-cons-block">
	<!-- wp:group {"className":"pcb-left","layout":{"type":"flex","orientation":"vertical"}} -->
	<div class="wp-block-group pcb-left">
		<!-- wp:group {"className":"pcb-image-wrap","layout":{"type":"default"}} -->
		<div class="wp-block-group pcb-image-wrap">
			<!-- wp:image {"sizeSlug":"full","linkDestination":"none"} -->
			<figure class="wp-block-image size-full"><img src="https://placehold.co/600x800" alt="Product Image"/></figure>
			<!-- /wp:image -->
		</div>
		<!-- /wp:group -->
		<!-- wp:paragraph {"className":"pcb-price","placeholder":"Price"} -->
		<p class="pcb-price">₹1,09,999</p>
		<!-- /wp:paragraph -->
		<!-- wp:paragraph {"className":"pcb-rating","placeholder":"Rating"} -->
		<p class="pcb-rating">Rating: 9/10</p>
		<!-- /wp:paragraph -->
	</div>
	<!-- /wp:group -->

	<!-- wp:group {"className":"pcb-right","layout":{"type":"default"}} -->
	<div class="wp-block-group pcb-right">
		<!-- wp:group {"className":"pcb-pros-cons-row","layout":{"type":"flex","flexWrap":"nowrap"}} -->
		<div class="wp-block-group pcb-pros-cons-row">
			<!-- wp:group {"className":"pcb-pros","layout":{"type":"default"}} -->
			<div class="wp-block-group pcb-pros">
				<!-- wp:paragraph {"className":"pcb-pros-title"} -->
				<p class="pcb-pros-title">Pros</p>
				<!-- /wp:paragraph -->
				<!-- wp:list {"className":"pcb-list pros-list"} -->
				<ul class="pcb-list pros-list">
					<!-- wp:list-item -->
					<li>Excellent main and telephoto cameras</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li>Strong battery life with fast wired charging</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->

			<!-- wp:group {"className":"pcb-cons","layout":{"type":"default"}} -->
			<div class="wp-block-group pcb-cons">
				<!-- wp:paragraph {"className":"pcb-cons-title"} -->
				<p class="pcb-cons-title">Cons</p>
				<!-- /wp:paragraph -->
				<!-- wp:list {"className":"pcb-list cons-list"} -->
				<ul class="pcb-list cons-list">
					<!-- wp:list-item -->
					<li>Speakers are average for the price</li>
					<!-- /wp:list-item -->
					<!-- wp:list-item -->
					<li>Gets warm during extended gaming</li>
					<!-- /wp:list-item -->
				</ul>
				<!-- /wp:list -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->

		<!-- wp:group {"className":"pcb-buy-row","layout":{"type":"flex","justifyContent":"space-between"}} -->
		<div class="wp-block-group pcb-buy-row">
			<!-- wp:paragraph {"className":"pcb-buy-label"} -->
			<p class="pcb-buy-label">Buy Now</p>
			<!-- /wp:paragraph -->
			<!-- wp:group {"className":"pcb-buy-buttons","layout":{"type":"flex"}} -->
			<div class="wp-block-group pcb-buy-buttons">
				<!-- wp:image {"width":"auto","height":"40px","sizeSlug":"full","linkDestination":"custom"} -->
				<figure class="wp-block-image size-full is-resized"><a href="#"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png" alt="Buy on Amazon" style="width:auto;height:40px"/></a></figure>
				<!-- /wp:image -->
				<!-- wp:image {"width":"auto","height":"40px","sizeSlug":"full","linkDestination":"custom"} -->
				<figure class="wp-block-image size-full is-resized"><a href="#"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png" alt="Buy on Flipkart" style="width:auto;height:40px"/></a></figure>
				<!-- /wp:image -->
			</div>
			<!-- /wp:group -->
		</div>
		<!-- /wp:group -->
	</div>
	<!-- /wp:group -->
</div>
<!-- /wp:group -->',
];