<?php
/**
 * Yoast SEO Plugin Stubs
 * 
 * This file exists only for Intelephense static analysis.
 * These functions are provided by the Yoast SEO plugin.
 *
 * @see https://yoast.com/help/yoast-seo-theme-integration-guide/
 */

if (!function_exists('yoast_breadcrumb')) {
    /**
     * Display or retrieve the Yoast SEO breadcrumbs.
     *
     * @param string $before  Optional. Markup to prepend to the breadcrumbs. Default empty.
     * @param string $after   Optional. Markup to append to the breadcrumbs. Default empty.
     * @param bool   $display Optional. Whether to echo or return the breadcrumbs. Default true (echo).
     * @return string|void Breadcrumbs markup if $display is false, void otherwise.
     */
    function yoast_breadcrumb(string $before = '', string $after = '', bool $display = true) {}
}
