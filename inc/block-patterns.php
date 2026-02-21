<?php
/**
 * Gizmodotech Block Patterns
 *
 * @package Gizmodotech
 * @since 1.0.1
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Register Block Patterns and Categories
 */
function gizmodotech_register_block_patterns() {
    if (function_exists('register_block_pattern_category')) {
        register_block_pattern_category(
            'gizmodotech',
            array('label' => esc_html__('Gizmodotech', 'gizmodotech'))
        );
    }

    if (function_exists('register_block_pattern')) {

        // Helper variables for translated strings to use in heredoc syntax
        $latest_stories_title = esc_html__('Latest Stories', 'gizmodotech');
        $trending_now_title = esc_html__('Trending Now', 'gizmodotech');
        $newsletter_title = esc_html__('Join Our Newsletter', 'gizmodotech');
        $newsletter_desc = esc_html__('Get the latest tech news, reviews, and deals delivered to your inbox. No spam, ever.', 'gizmodotech');
        $newsletter_placeholder = esc_attr__('Enter your email...', 'gizmodotech');
        $newsletter_button = esc_html__('Subscribe', 'gizmodotech');

        // --- Featured News Grid Pattern ---
        $featured_news_grid_content = <<<EOT
        <!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignwide">
            <!-- wp:heading {"level":2,"className":"text-2xl font-bold mb-6"} -->
            <h2 class="wp-block-heading text-2xl font-bold mb-6">{$latest_stories_title}</h2>
            <!-- /wp:heading -->

            <!-- wp:columns {"className":"gap-8"} -->
            <div class="wp-block-columns gap-8">
                <!-- wp:column {"width":"50%"} -->
                <div class="wp-block-column" style="flex-basis:50%">
                    <!-- wp:query {"queryId":1,"query":{"perPage":1,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"},"className":"featured-grid-main"} -->
                    <div class="wp-block-query featured-grid-main">
                        <!-- wp:post-template -->
                            <!-- wp:group {"style":{"spacing":{"blockGap":"0"}},"layout":{"type":"constrained"}} -->
                            <div class="wp-block-group">
                                <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"16/9","className":"rounded-t-lg"} /-->
                                <!-- wp:group {"style":{"spacing":{"padding":{"top":"var:preset|spacing|40","right":"var:preset|spacing|40","bottom":"var:preset|spacing|40","left":"var:preset|spacing|40"}}},"backgroundColor":"bg-alt","layout":{"type":"constrained"}} -->
                                <div class="wp-block-group has-bg-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--40);padding-right:var(--wp--preset--spacing--40);padding-bottom:var(--wp--preset--spacing--40);padding-left:var(--wp--preset--spacing--40)">
                                    <!-- wp:post-terms {"term":"category","className":"is-style-gizmodotech-category-badge"} /-->
                                    <!-- wp:post-title {"isLink":true,"level":3} /-->
                                    <!-- wp:post-excerpt /-->
                                    <!-- wp:group {"style":{"spacing":{"blockGap":"0.5em"}},"layout":{"type":"flex"},"fontSize":"small"} -->
                                    <div class="wp-block-group has-small-font-size">
                                        <!-- wp:post-author {"showAvatar":false} /-->
                                        <!-- wp:paragraph -->
                                        <p>·</p>
                                        <!-- /wp:paragraph -->
                                        <!-- wp:post-date /-->
                                    </div>
                                    <!-- /wp:group -->
                                </div>
                                <!-- /wp:group -->
                            </div>
                            <!-- /wp:group -->
                        <!-- /wp:post-template -->
                    </div>
                    <!-- /wp:query -->
                </div>
                <!-- /wp:column -->

                <!-- wp:column {"width":"50%"} -->
                <div class="wp-block-column" style="flex-basis:50%">
                    <!-- wp:query {"queryId":2,"query":{"perPage":4,"pages":0,"offset":1,"postType":"post","order":"desc","orderBy":"date","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"list"},"className":"featured-grid-secondary"} -->
                    <div class="wp-block-query featured-grid-secondary">
                        <!-- wp:post-template -->
                            <!-- wp:columns {"verticalAlignment":"center"} -->
                            <div class="wp-block-columns are-vertically-aligned-center">
                                <!-- wp:column {"width":"33.33%"} -->
                                <div class="wp-block-column" style="flex-basis:33.33%">
                                    <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"1","width":"100%","height":"100%"} /-->
                                </div>
                                <!-- /wp:column -->
                                <!-- wp:column {"width":"66.66%"} -->
                                <div class="wp-block-column" style="flex-basis:66.66%">
                                    <!-- wp:post-terms {"term":"category","className":"is-style-gizmodotech-category-badge","fontSize":"small"} /-->
                                    <!-- wp:post-title {"isLink":true,"level":4,"fontSize":"medium"} /-->
                                    <!-- wp:group {"style":{"spacing":{"blockGap":"0.5em"}},"layout":{"type":"flex"},"fontSize":"small"} -->
                                    <div class="wp-block-group has-small-font-size">
                                        <!-- wp:post-date /-->
                                    </div>
                                    <!-- /wp:group -->
                                </div>
                                <!-- /wp:column -->
                            </div>
                            <!-- /wp:columns -->
                        <!-- /wp:post-template -->
                    </div>
                    <!-- /wp:query -->
                </div>
                <!-- /wp:column -->
            </div>
            <!-- /wp:columns -->
        </div>
        <!-- /wp:group -->
EOT;

        // Featured News Grid Pattern
        register_block_pattern(
            'gizmodotech/featured-news-grid',
            array(
                'title'       => esc_html__('Featured News Grid', 'gizmodotech'),
                'description' => esc_html__('A grid with one large featured post and four smaller posts.', 'gizmodotech'),
                'categories'  => array('gizmodotech', 'query'),
                'keywords'    => array('posts', 'grid', 'featured'),
                'content'     => $featured_news_grid_content,
            )
        );

        // --- Trending Posts Pattern ---
        $trending_posts_content = <<<EOT
        <!-- wp:group {"align":"wide","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignwide">
            <!-- wp:heading {"level":2,"className":"text-2xl font-bold mb-6"} -->
            <h2 class="wp-block-heading text-2xl font-bold mb-6">{$trending_now_title}</h2>
            <!-- /wp:heading -->

            <!-- wp:query {"queryId":3,"query":{"perPage":5,"pages":0,"offset":0,"postType":"post","order":"desc","orderBy":"comment_count","author":"","search":"","exclude":[],"sticky":"exclude","inherit":false},"displayLayout":{"type":"flex","columns":3},"className":"gizmodotech-trending-posts"} -->
            <div class="wp-block-query gizmodotech-trending-posts">
                <!-- wp:post-template -->
                    <!-- wp:group {"style":{"spacing":{"blockGap":"0.5em"}},"layout":{"type":"constrained"}} -->
                    <div class="wp-block-group">
                        <!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
                        <!-- wp:post-title {"isLink":true,"level":5,"fontSize":"medium"} /-->
                        <!-- wp:post-date {"fontSize":"small"} /-->
                    </div>
                    <!-- /wp:group -->
                <!-- /wp:post-template -->
            </div>
            <!-- /wp:query -->
        </div>
        <!-- /wp:group -->
EOT;

        register_block_pattern(
            'gizmodotech/trending-posts',
            array(
                'title'       => esc_html__('Trending Posts (Horizontal)', 'gizmodotech'),
                'description' => esc_html__('A horizontal list of posts, ideal for a "Trending" section.', 'gizmodotech'),
                'categories'  => array('gizmodotech', 'query'),
                'keywords'    => array('posts', 'horizontal', 'trending', 'slider'),
                'content'     => $trending_posts_content,
            )
        );

        // --- Newsletter CTA Pattern ---
        $newsletter_cta_content = <<<EOT
        <!-- wp:group {"align":"full","style":{"spacing":{"padding":{"top":"var:preset|spacing|2xl","bottom":"var:preset|spacing|2xl","left":"var:preset|spacing|lg","right":"var:preset|spacing|lg"}}},"backgroundColor":"bg-alt","layout":{"type":"constrained"}} -->
        <div class="wp-block-group alignfull has-bg-alt-background-color has-background" style="padding-top:var(--wp--preset--spacing--2xl);padding-bottom:var(--wp--preset--spacing--2xl);padding-right:var(--wp--preset--spacing--lg);padding-left:var(--wp--preset--spacing--lg)">
            <!-- wp:group {"layout":{"type":"constrained","contentSize":"800px"}} -->
            <div class="wp-block-group">
                <!-- wp:heading {"textAlign":"center"} -->
                <h2 class="wp-block-heading has-text-align-center">{$newsletter_title}</h2>
                <!-- /wp:heading -->

                <!-- wp:paragraph {"align":"center"} -->
                <p class="has-text-align-center">{$newsletter_desc}</p>
                <!-- /wp:paragraph -->

                <!-- wp:group {"style":{"spacing":{"blockGap":"var:preset|spacing|sm"}},"layout":{"type":"flex","flexWrap":"nowrap","justifyContent":"center"}} -->
                <div class="wp-block-group">
                    <!-- wp:search {"label":"Email","showLabel":false,"placeholder":"{$newsletter_placeholder}","width":100,"widthUnit":"%","buttonText":"{$newsletter_button}","buttonPosition":"button-inside","buttonUseIcon":false} /-->
                </div>
                <!-- /wp:group -->
            </div>
            <!-- /wp:group -->
        </div>
        <!-- /wp:group -->
EOT;

        register_block_pattern(
            'gizmodotech/newsletter-cta',
            array(
                'title'       => esc_html__('Newsletter CTA', 'gizmodotech'),
                'description' => esc_html__('A full-width call-to-action block for newsletter subscriptions.', 'gizmodotech'),
                'categories'  => array('gizmodotech', 'call-to-action'),
                'keywords'    => array('newsletter', 'subscribe', 'cta', 'form'),
                'content'     => $newsletter_cta_content,
            )
        );
    }
}
add_action('init', 'gizmodotech_register_block_patterns');