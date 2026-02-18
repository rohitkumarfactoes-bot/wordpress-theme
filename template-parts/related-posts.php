<?php
/**
 * Template part for displaying related posts
 *
 * @package Gizmodotech
 */

$related_args = array(
    'post_type'      => get_post_type(),
    'posts_per_page' => 3,
    'post__not_in'   => array(get_the_ID()),
    'no_found_rows'  => true,
);

$categories = get_the_category(get_the_ID());
if ($categories) {
    $category_ids = array();
    foreach ($categories as $individual_category) {
        $category_ids[] = $individual_category->term_id;
    }
    $related_args['category__in'] = $category_ids;
}

$related_query = new WP_Query($related_args);

if ($related_query->have_posts()) :
?>
<section class="related-posts-section">
    <h2 class="section-title"><span><?php esc_html_e('Related Articles', 'gizmodotech'); ?></span></h2>
    <div class="posts-grid">
        <?php
        while ($related_query->have_posts()) :
            $related_query->the_post();
            get_template_part('template-parts/content', get_post_type());
        endwhile;
        ?>
    </div>
</section>
<?php
endif;
wp_reset_postdata();