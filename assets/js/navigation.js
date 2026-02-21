/**
 * File navigation.js.
 *
 * Handles toggling the navigation menu for small screens.
 */
(function() {
    const siteNavigation = document.getElementById('site-navigation');
    const mobileMenuToggle = document.querySelector('.mobile-menu-toggle');
    const body = document.body;

    if (!siteNavigation || !mobileMenuToggle) {
        return;
    }

    mobileMenuToggle.addEventListener('click', function() {
        siteNavigation.classList.toggle('active');
        mobileMenuToggle.setAttribute('aria-expanded', siteNavigation.classList.contains('active'));
        body.classList.toggle('menu-open');
    });

    // Close menu when clicking outside
    document.addEventListener('click', function(event) {
        if (siteNavigation.classList.contains('active') && !siteNavigation.contains(event.target) && !mobileMenuToggle.contains(event.target)) {
            siteNavigation.classList.remove('active');
            mobileMenuToggle.setAttribute('aria-expanded', 'false');
            body.classList.remove('menu-open');
        }
    });

    // Copy Link functionality
    document.addEventListener('click', function(event) {
        const button = event.target.closest('.share-copy');
        if (!button) {
            return;
        }

        const link = button.dataset.link;
        const span = button.querySelector('span');
        const originalText = span.textContent;

        navigator.clipboard.writeText(link).then(() => {
            span.textContent = 'Copied!';
            button.classList.add('copied');
            setTimeout(() => {
                span.textContent = originalText;
                button.classList.remove('copied');
            }, 2000);
        });
    });

    // Interactive Table of Contents
    const tocContainer = document.querySelector('.gizmodotech-toc');
    if (tocContainer) {
        const tocToggle = tocContainer.querySelector('.toc-toggle');
        const tocList = tocContainer.querySelector('.toc-list');

        if (tocToggle && tocList) {
            // Collapse on mobile by default for a cleaner initial view
            if (window.innerWidth < 768) {
                tocContainer.classList.add('collapsed');
            }

            tocToggle.addEventListener('click', (e) => {
                e.preventDefault();
                tocContainer.classList.toggle('collapsed');
            });
        }
    }

    // Comments Toggle
    const toggleCommentsButton = document.getElementById('toggle-comments-button');
    const commentsWrapper = document.getElementById('comments-wrapper');

    if (toggleCommentsButton && commentsWrapper) {
        toggleCommentsButton.addEventListener('click', function() {
            const isHidden = commentsWrapper.classList.contains('comments-hidden');

            if (isHidden) {
                commentsWrapper.classList.remove('comments-hidden');
                this.textContent = gizmodotech_vars.hideComments;
                // Scroll to the comments section after it becomes visible
                setTimeout(() => {
                    commentsWrapper.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }, 100);
            } else {
                commentsWrapper.classList.add('comments-hidden');
                this.textContent = gizmodotech_vars.leaveComment;
            }
        });
    }

    // Extracted Images Gallery (Mobile Reviews)
    const thumbnails = document.querySelectorAll('.extracted-images-grid .thumbnail img');
    const imageDisplay = document.getElementById('image-display');

    if (thumbnails.length > 0 && imageDisplay) {
        thumbnails.forEach(thumb => {
            thumb.addEventListener('click', function() {
                const fullImageSrc = this.getAttribute('data-full-image');
                imageDisplay.innerHTML = '<img src="' + fullImageSrc + '" alt="Full Image">';
            });
        });
    }

    // Search Overlay
    const searchToggle = document.querySelector('.search-toggle');
    const searchOverlay = document.getElementById('search-overlay');
    const searchClose = document.querySelector('.search-close');
    const searchInput = searchOverlay ? searchOverlay.querySelector('.search-field') : null;

    if (searchToggle && searchOverlay && searchClose && searchInput) {
        searchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            searchOverlay.classList.add('active');
            setTimeout(() => searchInput.focus(), 300); // Focus after transition
        });

        searchClose.addEventListener('click', function(e) {
            e.preventDefault();
            searchOverlay.classList.remove('active');
        });
    }

    // Header scroll effect
    const header = document.getElementById('masthead');
    if (header) {
        window.addEventListener('scroll', function() {
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    }

    // Back to Top Button
    const backToTopButton = document.getElementById('back-to-top');
    if (backToTopButton) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 500) {
                backToTopButton.classList.add('is-visible');
            } else {
                backToTopButton.classList.remove('is-visible');
            }
        });

        backToTopButton.addEventListener('click', (e) => {
            e.preventDefault();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }

})();