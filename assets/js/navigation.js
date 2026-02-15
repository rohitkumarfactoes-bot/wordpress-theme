(function($) {
    'use strict';

    // Mobile Menu Toggle
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const mainNavigation = document.querySelector('.main-navigation');
    const body = document.body;

    if (mobileMenuToggle && mainNavigation) {
        mobileMenuToggle.addEventListener('click', function() {
            mainNavigation.classList.toggle('active');
            body.classList.toggle('menu-open');
            
            // Update aria-expanded
            const expanded = mainNavigation.classList.contains('active');
            mobileMenuToggle.setAttribute('aria-expanded', expanded);
        });

        // Close menu when clicking outside
        document.addEventListener('click', function(event) {
            const isClickInside = mainNavigation.contains(event.target) || 
                                 mobileMenuToggle.contains(event.target);
            
            if (!isClickInside && mainNavigation.classList.contains('active')) {
                mainNavigation.classList.remove('active');
                body.classList.remove('menu-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
        });

        // Close menu on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && mainNavigation.classList.contains('active')) {
                mainNavigation.classList.remove('active');
                body.classList.remove('menu-open');
                mobileMenuToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }

    // Search Overlay Toggle
    const searchToggle = document.querySelector('.search-toggle');
    const searchOverlay = document.getElementById('search-overlay');
    const searchClose = document.querySelector('.search-close');

    if (searchToggle && searchOverlay) {
        searchToggle.addEventListener('click', function() {
            searchOverlay.style.display = 'flex';
            setTimeout(function() {
                searchOverlay.classList.add('active');
                const searchInput = searchOverlay.querySelector('input[type="search"]');
                if (searchInput) {
                    searchInput.focus();
                }
            }, 10);
        });

        if (searchClose) {
            searchClose.addEventListener('click', function() {
                searchOverlay.classList.remove('active');
                setTimeout(function() {
                    searchOverlay.style.display = 'none';
                }, 300);
            });
        }

        // Close on overlay click
        searchOverlay.addEventListener('click', function(event) {
            if (event.target === searchOverlay) {
                searchOverlay.classList.remove('active');
                setTimeout(function() {
                    searchOverlay.style.display = 'none';
                }, 300);
            }
        });

        // Close on escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && searchOverlay.classList.contains('active')) {
                searchOverlay.classList.remove('active');
                setTimeout(function() {
                    searchOverlay.style.display = 'none';
                }, 300);
            }
        });
    }

    // Sticky Header on Scroll
    const siteHeader = document.querySelector('.site-header');
    let lastScrollTop = 0;

    if (siteHeader) {
        window.addEventListener('scroll', function() {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            
            if (scrollTop > 100) {
                siteHeader.classList.add('scrolled');
            } else {
                siteHeader.classList.remove('scrolled');
            }

            lastScrollTop = scrollTop;
        });
    }

    // Dropdown Menu for Desktop
    const menuItems = document.querySelectorAll('.primary-menu .menu-item-has-children');

    menuItems.forEach(function(item) {
        // For desktop hover
        if (window.innerWidth > 768) {
            item.addEventListener('mouseenter', function() {
                const subMenu = this.querySelector('.sub-menu');
                if (subMenu) {
                    subMenu.style.display = 'block';
                }
            });

            item.addEventListener('mouseleave', function() {
                const subMenu = this.querySelector('.sub-menu');
                if (subMenu) {
                    subMenu.style.display = 'none';
                }
            });
        }

        // For mobile click
        const link = item.querySelector('a');
        if (link && window.innerWidth <= 768) {
            link.addEventListener('click', function(e) {
                const subMenu = item.querySelector('.sub-menu');
                if (subMenu) {
                    e.preventDefault();
                    subMenu.style.display = subMenu.style.display === 'block' ? 'none' : 'block';
                    item.classList.toggle('active');
                }
            });
        }
    });

    // Smooth Scroll for Anchor Links
    const anchorLinks = document.querySelectorAll('a[href^="#"]');
    
    anchorLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');
            
            if (href !== '#' && href !== '#0') {
                const target = document.querySelector(href);
                
                if (target) {
                    e.preventDefault();
                    const headerOffset = 100;
                    const elementPosition = target.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });
                }
            }
        });
    });

})(jQuery);
