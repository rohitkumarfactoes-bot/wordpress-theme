/**
 * Gizmodotech Theme JavaScript
 * 
 * @package Gizmodotech
 * @since 1.0.0
 */

(function($) {
    'use strict';

    /**
     * Dark Mode Toggle
     */
    function initDarkMode() {
        const toggle = $('#dark-mode-toggle');
        const sunIcon = toggle.find('.sun-icon');
        const moonIcon = toggle.find('.moon-icon');
        
        // Check for saved preference or default to light mode
        const darkMode = localStorage.getItem('darkMode') === 'true' || document.body.classList.contains('dark-mode');
        
        if (darkMode) {
            document.body.classList.add('dark-mode');
            sunIcon.addClass('hidden');
            moonIcon.removeClass('hidden');
        }
        
        toggle.on('click', function() {
            document.body.classList.toggle('dark-mode');
            const isDark = document.body.classList.contains('dark-mode');
            
            // Save preference
            localStorage.setItem('darkMode', isDark);
            document.cookie = `dark_mode=${isDark}; path=/; max-age=31536000`;
            
            // Toggle icons
            sunIcon.toggleClass('hidden', isDark);
            moonIcon.toggleClass('hidden', !isDark);
        });
    }

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        const toggle = $('#mobile-menu-toggle');
        const menu = $('#mobile-menu');
        const menuIcon = toggle.find('.menu-icon');
        const closeIcon = toggle.find('.close-icon');
        
        toggle.on('click', function() {
            menu.toggleClass('active');
            menuIcon.toggleClass('hidden');
            closeIcon.toggleClass('hidden');
            
            // Prevent body scroll when menu is open
            if (menu.hasClass('active')) {
                $('body').css('overflow', 'hidden');
            } else {
                $('body').css('overflow', '');
            }
        });
        
        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (menu.hasClass('active') && !$(e.target).closest('#mobile-menu, #mobile-menu-toggle').length) {
                menu.removeClass('active');
                menuIcon.removeClass('hidden');
                closeIcon.addClass('hidden');
                $('body').css('overflow', '');
            }
        });
    }

    /**
     * Search Toggle
     */
    function initSearchToggle() {
        const searchToggle = $('#search-toggle');
        const searchOverlay = $('#search-overlay');
        const searchClose = $('#search-close');
        const searchField = searchOverlay.find('.search-field');
        
        searchToggle.on('click', function() {
            searchOverlay.addClass('active');
            setTimeout(function() {
                searchField.focus();
            }, 100);
        });
        
        searchClose.on('click', function() {
            searchOverlay.removeClass('active');
        });
        
        // Close on ESC key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape' && searchOverlay.hasClass('active')) {
                searchOverlay.removeClass('active');
            }
        });
        
        // Close when clicking outside
        searchOverlay.on('click', function(e) {
            if ($(e.target).is('#search-overlay')) {
                searchOverlay.removeClass('active');
            }
        });
    }

    /**
     * Sticky Header on Scroll
     */
    function initStickyHeader() {
        const header = $('.site-header');
        let lastScroll = 0;
        
        $(window).on('scroll', function() {
            const currentScroll = $(this).scrollTop();
            
            if (currentScroll > 20) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }
            
            lastScroll = currentScroll;
        });
    }

    /**
     * Scroll to Top Button
     */
    function initScrollToTop() {
        const scrollBtn = $('#scroll-top');
        
        // Show/hide button based on scroll position
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 500) {
                scrollBtn.removeClass('hidden');
            } else {
                scrollBtn.addClass('hidden');
            }
        });
        
        // Smooth scroll to top
        scrollBtn.on('click', function() {
            $('html, body').animate({ scrollTop: 0 }, 600);
        });
    }

    /**
     * Smooth Scroll for Anchor Links
     */
    function initSmoothScroll() {
        $('a[href*="#"]:not([href="#"])').on('click', function(e) {
            if (location.pathname.replace(/^\//, '') === this.pathname.replace(/^\//, '') && 
                location.hostname === this.hostname) {
                
                let target = $(this.hash);
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                
                if (target.length) {
                    e.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top - 80
                    }, 600);
                }
            }
        });
    }

    /**
     * Lazy Load Images
     */
    function initLazyLoad() {
        if ('loading' in HTMLImageElement.prototype) {
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const script = document.createElement('script');
            script.src = 'https://cdnjs.cloudflare.com/ajax/libs/lazysizes/5.3.2/lazysizes.min.js';
            document.body.appendChild(script);
        }
    }

    /**
     * AJAX Newsletter Subscription
     */
    function initNewsletter() {
        $('.newsletter-form').on('submit', function(e) {
            e.preventDefault();
            
            const form = $(this);
            const email = form.find('input[type="email"]').val();
            const button = form.find('button[type="submit"]');
            const originalText = button.text();
            
            // Disable button and show loading state
            button.prop('disabled', true).text('Subscribing...');
            
            $.ajax({
                url: gizmodotech.ajax_url,
                type: 'POST',
                data: {
                    action: 'newsletter_subscribe',
                    nonce: gizmodotech.nonce,
                    email: email
                },
                success: function(response) {
                    if (response.success) {
                        alert('Thanks for subscribing!');
                        form[0].reset();
                    } else {
                        alert('Subscription failed. Please try again.');
                    }
                },
                error: function() {
                    alert('An error occurred. Please try again.');
                },
                complete: function() {
                    button.prop('disabled', false).text(originalText);
                }
            });
        });
    }

    /**
     * Add Animation on Scroll
     */
    function initScrollAnimations() {
        const animateElements = $('.post-card, .widget');
        
        function checkVisibility() {
            animateElements.each(function() {
                const element = $(this);
                const elementTop = element.offset().top;
                const elementBottom = elementTop + element.outerHeight();
                const viewportTop = $(window).scrollTop();
                const viewportBottom = viewportTop + $(window).height();
                
                if (elementBottom > viewportTop && elementTop < viewportBottom - 100) {
                    element.addClass('animate-in');
                }
            });
        }
        
        $(window).on('scroll', checkVisibility);
        checkVisibility(); // Initial check
    }

    /**
     * Initialize all functions on document ready
     */
    $(document).ready(function() {
        initDarkMode();
        initMobileMenu();
        initSearchToggle();
        initStickyHeader();
        initScrollToTop();
        initSmoothScroll();
        initLazyLoad();
        initNewsletter();
        initScrollAnimations();
    });

    /**
     * Add animation classes to CSS
     */
    const style = document.createElement('style');
    style.textContent = `
        .post-card, .widget {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }
        .post-card.animate-in, .widget.animate-in {
            opacity: 1;
            transform: translateY(0);
        }
    `;
    document.head.appendChild(style);

})(jQuery);
