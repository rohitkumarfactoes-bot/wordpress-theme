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

})();