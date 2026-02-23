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
<!-- wp:html -->
<div class="pros-cons-block">
  <div class="pcb-left">
    <div class="pcb-image-wrap">
      <img src="https://placehold.co/600x800" alt="Product Image" />
    </div>
    <p class="pcb-price">₹1,09,999</p>
    <p class="pcb-rating">Rating: 9/10</p>
  </div>

  <div class="pcb-right">
    <div class="pcb-pros-cons-row">
      <div class="pcb-pros">
        <p class="pcb-pros-title">Pros</p>
        <ul class="pcb-list pros-list">
          <li>Excellent main and telephoto cameras</li>
          <li>Strong battery life with fast wired charging</li>
          <li>Smooth day-to-day performance</li>
        </ul>
      </div>

      <div class="pcb-cons">
        <p class="pcb-cons-title">Cons</p>
        <ul class="pcb-list cons-list">
          <li>Speakers are average for the price</li>
          <li>Gets warm during extended gaming</li>
        </ul>
      </div>
    </div>

    <div class="pcb-buy-row">
      <span class="pcb-buy-label">Buy Now</span>
      <div class="pcb-buy-buttons">
        <a href="#" target="_blank" rel="nofollow"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png" alt="Buy on Amazon" /></a>
        <a href="#" target="_blank" rel="nofollow"><img src="https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png" alt="Buy on Flipkart" /></a>
      </div>
    </div>
  </div>
</div>
<!-- /wp:html -->',
];