<?php
/**
 * The template for displaying all single posts of type 'mobile'
 *
 * @package Gizmodotech
 */

get_header();
?>

<main id="primary" class="site-content">
    <div class="max-w-container mx-auto px-4 sm:px-6 lg:px-8">
        <div class="content-area">
            <?php
            while (have_posts()) :
                the_post();
                
                // Check if we are in the 'mobile' category if this template is forced for standard posts,
                // otherwise this file automatically applies to 'mobile' CPT.
                ?>

                <article id="post-<?php the_ID(); ?>" <?php post_class('max-w-4xl mx-auto'); ?>>
                    <header class="mb-8">
                        <div class="mb-4 text-sm text-text-light">
                            <?php gizmodotech_post_meta(); ?>
                        </div>
                        
                        <?php the_title('<h1 class="text-3xl md:text-4xl font-bold font-heading text-text dark:text-text">', '</h1>'); ?>
                    </header>

                    <div class="mt-8 lg:grid lg:grid-cols-[350px_1fr] lg:gap-12">
                        <div class="mobile-review-media lg:order-1">
                            <!-- Custom Image Extraction Gallery -->
                            <div class="mobile-gallery-section mb-8">
                                <?php 
                                // Display the extracted images gallery
                                echo do_shortcode('[extracted_images]'); 
                                ?>
                            </div>

                            <?php
                            // --- Display Specifications Table ---
                            $specs_keys = array('display', 'processor', 'ram', 'storage', 'camera', 'battery');
                            $has_specs = false;
                            $specs_html = '<div class="my-8 bg-bg-alt dark:bg-bg-alt border border-border dark:border-border rounded-lg p-6"><h3 class="text-lg font-bold font-heading mb-4">' . esc_html__('Key Specifications', 'gizmodotech') . '</h3><table class="w-full"><tbody>';

                            foreach ($specs_keys as $key) {
                                $value = get_post_meta(get_the_ID(), '_spec_' . $key, true);
                                if (!empty($value)) {
                                    $has_specs = true;
                                    $specs_html .= '<tr class="border-b border-border dark:border-border last:border-0">';
                                    $specs_html .= '<th class="p-3 text-left font-semibold text-text-light text-sm w-28">' . esc_html(ucfirst($key)) . '</th>';
                                    $specs_html .= '<td class="p-3 text-left text-sm">' . esc_html($value) . '</td>';
                                    $specs_html .= '</tr>';
                                }
                            }
                            $specs_html .= '</tbody></table></div>';

                            if ($has_specs) {
                                echo $specs_html;
                            }
                            ?>
                        </div>

                        <div class="entry-content lg:order-2 text-base leading-relaxed">
                            <?php
                            the_content();

                            wp_link_pages(array(
                                'before' => '<div class="page-links">' . esc_html__('Pages:', 'gizmodotech'),
                                'after'  => '</div>',
                            ));
                            ?>
                        </div>
                    </div>

                    <footer class="mt-12 pt-8 border-t border-border dark:border-border">
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