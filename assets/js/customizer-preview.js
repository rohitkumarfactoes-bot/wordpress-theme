/**
 * Gizmodotech Pro — Customizer Live Preview
 * Handles live preview of Customizer settings
 */

(function($) {
    'use strict';

    // Helper function to update CSS variable
    function updateCSSVariable(variable, value) {
        document.documentElement.style.setProperty(variable, value);
    }

    // Body Font Family
    wp.customize('body_font_family', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-sans', newVal);
            document.body.style.fontFamily = newVal;
        });
    });

    // Heading Font Family
    wp.customize('heading_font_family', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-heading', newVal);
            var headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
            headings.forEach(function(heading) {
                heading.style.fontFamily = newVal;
            });
        });
    });

    // Body Font Size
    wp.customize('body_font_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-base', (newVal / 16) + 'rem');
        });
    });

    // Body Font Weight
    wp.customize('body_font_weight', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-weight-normal', newVal);
        });
    });

    // Heading Font Weight
    wp.customize('heading_font_weight', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-weight-bold', newVal);
        });
    });

    // Body Line Height
    wp.customize('body_line_height', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--line-height-normal', newVal);
        });
    });

    // Heading Line Height
    wp.customize('heading_line_height', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--line-height-tight', newVal);
        });
    });

    // Heading Sizes
    wp.customize('h1_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-4xl', (newVal / 16) + 'rem');
        });
    });

    wp.customize('h2_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-3xl', (newVal / 16) + 'rem');
        });
    });

    wp.customize('h3_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-2xl', (newVal / 16) + 'rem');
        });
    });

    wp.customize('h4_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-xl', (newVal / 16) + 'rem');
        });
    });

    wp.customize('h5_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-lg', (newVal / 16) + 'rem');
        });
    });

    wp.customize('h6_size', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--font-size-md', (newVal / 16) + 'rem');
        });
    });

    // Layout Widths
    wp.customize('content_width', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--width-content', newVal + 'px');
        });
    });

    wp.customize('wide_width', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--width-wide', newVal + 'px');
        });
    });

    wp.customize('card_radius', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--radius-lg', newVal + 'px');
            updateCSSVariable('--radius-card', newVal + 'px');
        });
    });

    // Colors
    wp.customize('primary_color', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--color-primary', newVal);
        });
    });

    wp.customize('accent_color', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--color-accent', newVal);
        });
    });

    wp.customize('nav_bg', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--bg-nav', newVal);
        });
    });

    wp.customize('footer_bg', function(value) {
        value.bind(function(newVal) {
            updateCSSVariable('--bg-footer', newVal);
        });
    });

})(jQuery);
