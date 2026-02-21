<?php
/**
 * The template for displaying all single posts of type 'mobile'
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content">
    <div class="container">
        <div class="content-area">
            <?php
            while (have_posts()) :
                the_post();
                
                // Check if we are in the 'mobile' category if this template is forced for standard posts,
                // otherwise this file automatically applies to 'mobile' CPT.
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <div class="mobile-gallery-section mobile-gallery-above-header">
                        <?php echo do_shortcode('[extracted_images]'); ?>
                    </div>

                    <header class="single-post-header">
                        <div class="single-post-meta">
                            <?php gizmodotech_post_meta(); ?>
                        </div>
                        <?php the_title('<h1 class="single-post-title">', '</h1>'); ?>
                    </header>

                    <div class="mobile-review-layout">
                        <div class="mobile-review-media">

                            <?php
                            // --- Display Specifications Table ---
                            $specs_keys = array('display', 'processor', 'ram', 'storage', 'camera', 'battery');
                            $has_specs = false;
                            $specs_html = '<div class="mobile-specs-box"><h3 class="specs-title">' . esc_html__('Key Specifications', 'gizmodotech') . '</h3><table class="specs-table"><tbody>';

                            foreach ($specs_keys as $key) {
                                $value = get_post_meta(get_the_ID(), '_spec_' . $key, true);
                                if (!empty($value)) {
                                    $has_specs = true;
                                    $specs_html .= '<tr class="specs-row">';
                                    $specs_html .= '<th class="specs-label">' . esc_html(ucfirst($key)) . '</th>';
                                    $specs_html .= '<td class="specs-value">' . esc_html($value) . '</td>';
                                    $specs_html .= '</tr>';
                                }
                            }
                            $specs_html .= '</tbody></table></div>';

                            if ($has_specs) {
                                echo $specs_html;
                            }
                            ?>
                        </div>

                        <div class="entry-content">
                            <?php
                            the_content();

                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'gizmodotech'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>
                    </div>

                    <footer class="mobile-footer">
                        <?php gizmodotech_the_social_share_buttons(); ?>
                    </footer>
                </article>

                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

                // Related Posts
                get_template_part('template-parts/related-posts');

            endwhile; // End of the loop.
            ?>
        </div>
    </div>
</main>

<?php
get_footer();