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
                    <header class="single-post-header">
                        <div class="single-post-meta">
                            <?php gizmodotech_post_meta(); ?>
                        </div>
                        
                        <?php the_title('<h1 class="single-post-title">', '</h1>'); ?>
                    </header>

                    <!-- Custom Image Extraction Gallery -->
                    <div class="mobile-gallery-section">
                        <?php 
                        // Display the extracted images gallery
                        echo do_shortcode('[extracted_images]'); 
                        ?>
                    </div>

                    <?php
                    // --- Display Specifications Table ---
                    $specs_keys = array('display', 'processor', 'ram', 'storage', 'camera', 'battery');
                    $has_specs = false;
                    $specs_html = '<div class="mobile-specs-table"><h3>' . esc_html__('Key Specifications', 'gizmodotech') . '</h3><table><tbody>';

                    foreach ($specs_keys as $key) {
                        $value = get_post_meta(get_the_ID(), '_spec_' . $key, true);
                        if (!empty($value)) {
                            $has_specs = true;
                            $specs_html .= '<tr>';
                            $specs_html .= '<th>' . esc_html(ucfirst($key)) . '</th>';
                            $specs_html .= '<td>' . esc_html($value) . '</td>';
                            $specs_html .= '</tr>';
                        }
                    }
                    $specs_html .= '</tbody></table></div>';

                    if ($has_specs) {
                        echo $specs_html;
                    }
                    ?>

                    <div class="entry-content">
                        <?php
                        the_content();

                        wp_link_pages(array(
                            'before' => '<div class="page-links">' . esc_html__('Pages:', 'gizmodotech'),
                            'after'  => '</div>',
                        ));
                        ?>
                    </div>

                    <footer class="entry-footer">
                        <div class="gizmodotech-share-buttons">
                            <span class="share-label"><?php esc_html_e('Share:', 'gizmodotech'); ?></span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>" target="_blank" class="share-btn share-fb">Facebook</a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>" target="_blank" class="share-btn share-tw">Twitter</a>
                            <a href="https://api.whatsapp.com/send?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>" target="_blank" class="share-btn share-wa">WhatsApp</a>
                        </div>
                    </footer>
                </article>

                <?php
                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    comments_template();
                endif;

            endwhile; // End of the loop.
            ?>
        </div>
    </div>
</main>

<?php
get_footer();