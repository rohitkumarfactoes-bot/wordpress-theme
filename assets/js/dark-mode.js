/**
 * Dark Mode Toggle Logic
 */
document.addEventListener('DOMContentLoaded', function() {
    const darkModeToggle = document.querySelector('.dark-mode-toggle');
    const html = document.documentElement;
    
    // Check for saved preference on load is handled by inline script in head to prevent FOUC
    // This script handles the toggle interaction

    if (darkModeToggle) {
        darkModeToggle.addEventListener('click', function(e) {
            e.preventDefault();
            
            let theme = 'light';
            if (html.getAttribute('data-theme') !== 'dark') {
                html.setAttribute('data-theme', 'dark');
                theme = 'dark';
            } else {
                html.removeAttribute('data-theme');
            }
            
            localStorage.setItem('theme', theme);
        });
    }
});