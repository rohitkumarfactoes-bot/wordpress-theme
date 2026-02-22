<?php
/**
 * Pattern: Phone Specs Widget
 * GSMArena/Beebom-style specs card with icon grid + table.
 *
 * @package gizmodotech-pro
 */

return [
	'title'       => __( 'Phone Specs Table', 'gizmodotech-pro' ),
	'description' => __( 'Display phone specifications with icon highlights and a detailed specs table.', 'gizmodotech-pro' ),
	'categories'  => [ 'gizmodotech' ],
	'keywords'    => [ 'specs', 'specifications', 'phone', 'gsmarena', 'table' ],
	'content'     => '
<!-- wp:html -->
<div class="specs-widget">
  <div class="specs-widget__header">
    <span class="specs-widget__title">POCO M7 5G Specifications</span>
    <span class="specs-widget__price">₹9,999</span>
  </div>

  <!-- Icon Highlights (like screenshot: Display / Battery / Processor / Camera) -->
  <div class="specs-widget__icons">
    <div class="specs-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2" ry="2"/><line x1="12" y1="18" x2="12.01" y2="18"/></svg>
      <span class="specs-icon__label">Display</span>
      <span class="specs-icon__value">6.88″ HD+ IPS | 120Hz</span>
    </div>
    <div class="specs-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="7" width="18" height="11" rx="2" ry="2"/><path d="M22 11v2"/></svg>
      <span class="specs-icon__label">Battery</span>
      <span class="specs-icon__value">5160mAh</span>
    </div>
    <div class="specs-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="4" y="4" width="16" height="16" rx="2" ry="2"/><rect x="9" y="9" width="6" height="6"/><line x1="9" y1="1" x2="9" y2="4"/><line x1="15" y1="1" x2="15" y2="4"/><line x1="9" y1="20" x2="9" y2="23"/><line x1="15" y1="20" x2="15" y2="23"/><line x1="20" y1="9" x2="23" y2="9"/><line x1="20" y1="14" x2="23" y2="14"/><line x1="1" y1="9" x2="4" y2="9"/><line x1="1" y1="14" x2="4" y2="14"/></svg>
      <span class="specs-icon__label">Processor</span>
      <span class="specs-icon__value">Snapdragon 4 Gen 2</span>
    </div>
    <div class="specs-icon">
      <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="var(--color-primary)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 19a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h4l2-3h6l2 3h4a2 2 0 0 1 2 2z"/><circle cx="12" cy="13" r="4"/></svg>
      <span class="specs-icon__label">Camera</span>
      <span class="specs-icon__value">50MP + 8MP Selfie</span>
    </div>
  </div>

  <!-- Full Specs Table -->
  <table class="specs-widget__table">
    <tbody>
      <tr><th>Network</th><td>5G / 4G LTE / 3G / 2G</td></tr>
      <tr><th>Display</th><td>6.88″ HD+ IPS LCD, 120Hz, 1640×720</td></tr>
      <tr><th>Processor</th><td>Qualcomm Snapdragon 4 Gen 2</td></tr>
      <tr><th>RAM</th><td>6GB / 8GB</td></tr>
      <tr><th>Storage</th><td>128GB, expandable via microSD</td></tr>
      <tr><th>Rear Camera</th><td>50MP (Wide) + 2MP (Depth)</td></tr>
      <tr><th>Front Camera</th><td>8MP</td></tr>
      <tr><th>Battery</th><td>5160mAh, 18W charging</td></tr>
      <tr><th>OS</th><td>Android 14, MIUI 14</td></tr>
      <tr><th>Dimensions</th><td>168.9 × 77.0 × 8.3mm</td></tr>
      <tr><th>Weight</th><td>204g</td></tr>
      <tr><th>Price</th><td>₹9,999</td></tr>
    </tbody>
  </table>
</div>
<!-- /wp:html -->
',
];
