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
    <img src="" alt="Product Name" loading="lazy" />
    <div class="review-card__price">₹99,999</div>
    <div class="review-card__buy-btns">
      <a href="#" class="btn-amazon" target="_blank" rel="noopener sponsored">🛒 Buy on Amazon</a>
      <a href="#" class="btn-flipkart" target="_blank" rel="noopener sponsored">🛍️ Buy on Flipkart</a>
    </div>
  </div>
  <div class="review-card__pros">
    <h3>Pros</h3>
    <ul class="review-card__pros-list">
      <li class="pros-item">Excellent main and telephoto cameras</li>
      <li class="pros-item">Strong battery life with fast wired charging</li>
      <li class="pros-item">Smooth day-to-day performance</li>
      <li class="pros-item">Optional extender adds extra reach for enthusiasts</li>
    </ul>
  </div>
  <div class="review-card__cons">
    <h3>Cons</h3>
    <ul class="review-card__cons-list">
      <li class="cons-item">Speakers are average for the price</li>
      <li class="cons-item">Gets warm during extended gaming</li>
    </ul>
  </div>
</div>
<!-- /wp:html -->
',
];
