// Swiper slider initialization script

// Import Swiper and related styles
import Swiper from 'swiper/bundle';
import 'swiper/swiper-bundle.css';

// Initialize Swiper
const swiper = new Swiper('.swiper-container', {
    // Optional parameters
    direction: 'horizontal',
    loop: true,
    // pagination
    pagination: {
        el: '.swiper-pagination',
        clickable: true,
    },
    // navigation
    navigation: {
        nextEl: '.swiper-button-next',
        prevEl: '.swiper-button-prev',
    },
});