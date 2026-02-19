/**
 * Post slider: horizontal scroll with prev/next buttons
 *
 * @package Gizmodotech
 */
(function () {
    'use strict';

    const container = document.querySelector('.post-slider-container, .gizmodotech-post-slider');
    if (!container) return;

    const track = container.querySelector('.post-slider-track');
    const prevBtn = container.querySelector('.slider-button-prev');
    const nextBtn = container.querySelector('.slider-button-next');
    if (!track || !prevBtn || !nextBtn) return;

    const cards = track.querySelectorAll('.post-item-card');
    const cardCount = cards.length;
    if (cardCount === 0) return;

    const gap = 24;
    function getStep() {
        const first = cards[0];
        return (first ? first.getBoundingClientRect().width : 300) + gap;
    }
    let step = getStep();
    let visibleCount = Math.max(1, Math.floor(container.offsetWidth / step));
    let index = 0;
    let maxIndex = Math.max(0, cardCount - visibleCount);

    function updateMaxIndex() {
        visibleCount = Math.max(1, Math.floor(container.offsetWidth / step));
        maxIndex = Math.max(0, cardCount - visibleCount);
    }
    function scrollToIndex(i) {
        updateMaxIndex();
        index = Math.max(0, Math.min(i, maxIndex));
        track.style.transform = 'translateX(-' + index * step + 'px)';
        prevBtn.style.visibility = index <= 0 ? 'hidden' : 'visible';
        nextBtn.style.visibility = index >= maxIndex ? 'hidden' : 'visible';
    }

    prevBtn.addEventListener('click', function () {
        scrollToIndex(index - 1);
    });
    nextBtn.addEventListener('click', function () {
        scrollToIndex(index + 1);
    });

    updateMaxIndex();
    scrollToIndex(0);

    window.addEventListener('resize', function () {
        step = getStep();
        scrollToIndex(index);
    });
})();
