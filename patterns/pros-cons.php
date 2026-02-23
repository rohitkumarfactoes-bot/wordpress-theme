<?php
/**
 * Pattern: Pros & Cons Review Card
 * Like the Beebom/Gizmodotech review widget shown in screenshots.
 *
 * @package gizmodotech-pro
 */

return [
	'title'       => __( 'Product Review Card — Pros & Cons', 'gizmodotech-pro' ),
	'description' => __( 'A review card with product image, price, buy buttons, and pros/cons columns.', 'gizmodotech-pro' ),
	'categories'  => [ 'gizmodotech' ],
	'keywords'    => [ 'pros', 'cons', 'review', 'product', 'price' ],
	'content'     => '
<!-- wp:html -->
<div class="review-card">
  <div class="review-card__product">
    <figure class="review-card__img">
        <img src="https://placehold.co/600x400" alt="Product Name" />
    </figure>
    <div class="review-card__meta">
        <div class="review-card__price">₹99,999</div>
        <div class="review-card__buy-btns">
          <a href="#" class="btn-buy btn-amazon" target="_blank" rel="noopener sponsored">Buy on Amazon</a>
          <a href="#" class="btn-buy btn-flipkart" target="_blank" rel="noopener sponsored">Buy on Flipkart</a>
        </div>
    </div>
  </div>
  <div class="pros-cons-main">
    <div class="pros">
      <h3>Pros</h3>
      <ul>
        <li>Excellent main and telephoto cameras</li>
        <li>Strong battery life with fast wired charging</li>
        <li>Smooth day-to-day performance</li>
      </ul>
    </div>
    <div class="cons">
      <h3>Cons</h3>
      <ul>
        <li>Speakers are average for the price</li>
        <li>Gets warm during extended gaming</li>
      </ul>
    </div>
  </div>
</div>
<!-- /wp:html -->
',
];
