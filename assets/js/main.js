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
        // Check for saved user preference or default to light mode
        const currentTheme = localStorage.getItem('theme') || 'light';
        document.documentElement.setAttribute('data-theme', currentTheme);

        // Create dark mode toggle button if it doesn't exist
        if ($('.dark-mode-toggle').length === 0) {
            const toggleHTML = `
                <button class="dark-mode-toggle" aria-label="Toggle dark mode">
                    <svg class="sun-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="5"></circle>
                        <line x1="12" y1="1" x2="12" y2="3"></line>
                        <line x1="12" y1="21" x2="12" y2="23"></line>
                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                        <line x1="1" y1="12" x2="3" y2="12"></line>
                        <line x1="21" y1="12" x2="23" y2="12"></line>
                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                    </svg>
                    <svg class="moon-icon" width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;">
                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                    </svg>
                </button>
            `;
            $('.site-header .container').append(toggleHTML);
        }

        // Toggle icon based on current theme
        function updateIcon() {
            const theme = document.documentElement.getAttribute('data-theme');
            if (theme === 'dark') {
                $('.sun-icon').hide();
                $('.moon-icon').show();
            } else {
                $('.sun-icon').show();
                $('.moon-icon').hide();
            }
        }

        updateIcon();

        // Toggle dark mode
        $(document).on('click', '.dark-mode-toggle', function() {
            const currentTheme = document.documentElement.getAttribute('data-theme');
            const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Set cookie for PHP access
            document.cookie = `dark_mode=${newTheme === 'dark'}; path=/; max-age=31536000`;
            
            updateIcon();
        });
    }

    /**
     * Mobile Menu Toggle
     */
    function initMobileMenu() {
        // Add mobile menu toggle button
        if ($('.mobile-menu-toggle').length === 0 && $('.main-navigation').length > 0) {
            $('.main-navigation').prepend(`
                <button class="mobile-menu-toggle" aria-label="Toggle menu">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            `);
        }

        // Toggle mobile menu
        $(document).on('click', '.mobile-menu-toggle', function() {
            $(this).toggleClass('active');
            $('.main-navigation ul').slideToggle(300);
        });

        // Close menu when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.main-navigation').length) {
                $('.mobile-menu-toggle').removeClass('active');
                if ($(window).width() < 768) {
                    $('.main-navigation ul').slideUp(300);
                }
            }
        });

        // Handle window resize
        $(window).on('resize', function() {
            if ($(window).width() >= 768) {
                $('.main-navigation ul').removeAttr('style');
                $('.mobile-menu-toggle').removeClass('active');
            }
        });
    }

    /**
     * Smooth Scroll to Top
     */
    function initScrollToTop() {
        // Add scroll to top button
        if ($('.scroll-to-top').length === 0) {
            $('body').append(`
                <button class="scroll-to-top" aria-label="Scroll to top" style="position: fixed; bottom: 20px; right: 20px; width: 50px; height: 50px; border-radius: 50%; background: var(--color-primary); color: white; border: none; cursor: pointer; opacity: 0; visibility: hidden; transition: all 0.3s; z-index: 999; box-shadow: var(--shadow-lg);">
                    <svg width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="12" y1="19" x2="12" y2="5"></line>
                        <polyline points="5 12 12 5 19 12"></polyline>
                    </svg>
                </button>
            `);
        }

        // Show/hide scroll to top button
        $(window).on('scroll', function() {
            if ($(this).scrollTop() > 300) {
                $('.scroll-to-top').css({
                    'opacity': '1',
                    'visibility': 'visible'
                });
            } else {
                $('.scroll-to-top').css({
                    'opacity': '0',
                    'visibility': 'hidden'
                });
            }
        });

        // Scroll to top
        $(document).on('click', '.scroll-to-top', function() {
            $('html, body').animate({
                scrollTop: 0
            }, 600);
            return false;
        });
    }

    /**
     * Sticky Header
     */
    function initStickyHeader() {
        let lastScroll = 0;
        const header = $('.site-header');

        $(window).on('scroll', function() {
            const currentScroll = $(this).scrollTop();

            if (currentScroll > 100) {
                header.addClass('scrolled');
            } else {
                header.removeClass('scrolled');
            }

            lastScroll = currentScroll;
        });
    }

    /**
     * Lazy Load Images
     */
    function initLazyLoad() {
        if ('loading' in HTMLImageElement.prototype) {
            // Browser supports lazy loading natively
            const images = document.querySelectorAll('img[loading="lazy"]');
            images.forEach(img => {
                img.src = img.dataset.src || img.src;
            });
        } else {
            // Fallback for browsers that don't support lazy loading
            const lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            if ('IntersectionObserver' in window) {
                const lazyImageObserver = new IntersectionObserver(function(entries) {
                    entries.forEach(function(entry) {
                        if (entry.isIntersecting) {
                            const lazyImage = entry.target;
                            lazyImage.src = lazyImage.dataset.src;
                            lazyImage.classList.remove('lazy');
                            lazyImageObserver.unobserve(lazyImage);
                        }
                    });
                });

                lazyImages.forEach(function(lazyImage) {
                    lazyImageObserver.observe(lazyImage);
                });
            }
        }
    }

    /**
     * Search Overlay
     */
    function initSearchOverlay() {
        // Add search icon to header if it doesn't exist
        if ($('.search-toggle').length === 0) {
            $('.site-header .container').append(`
                <button class="search-toggle" aria-label="Toggle search" style="background: transparent; border: none; cursor: pointer; padding: 10px; color: var(--text-primary);">
                    <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"></circle>
                        <path d="m21 21-4.35-4.35"></path>
                    </svg>
                </button>
            `);
        }

        // Toggle search overlay
        $(document).on('click', '.search-toggle', function() {
            $('body').toggleClass('search-active');
        });

        // Close search with Escape key
        $(document).on('keyup', function(e) {
            if (e.key === 'Escape' && $('body').hasClass('search-active')) {
                $('body').removeClass('search-active');
            }
        });
    }

    /**
     * Initialize all functions
     */
    $(document).ready(function() {
        initDarkMode();
        initMobileMenu();
        initScrollToTop();
        initStickyHeader();
        initLazyLoad();
        initSearchOverlay();
    });

})(jQuery);
