<?php
class Related_Posts_Widget extends WP_Widget {

    public function __construct() {
        parent::__construct(
            'related_posts_widget',
            'Related Posts Widget',
            array('description' => __('A Widget to show related posts by category and post type.'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        echo $args['before_title'] . apply_filters('widget_title', 'Related Posts') . $args['after_title'];

        // Get current post categories
        $categories = get_the_category();
        $category_ids = wp_list_pluck($categories, 'term_id');
        $post_type = get_post_type();

        // Query related posts
        $related_posts = new WP_Query(array(
            'category__in' => $category_ids,
            'post_type' => $post_type,
            'posts_per_page' => 5,
            'post__not_in' => array(get_the_ID()),
        ));

        if ($related_posts->have_posts()) {
            echo '<ul>';
            while ($related_posts->have_posts()) {
                $related_posts->the_post();
                echo '<li><a href="' . get_permalink() . '">' . get_the_title() . '</a></li>'; 
            }
            echo '</ul>'; 
            wp_reset_postdata();
        } else {
            echo 'No related posts found.';
        }

        echo $args['after_widget'];
    }

    public function form($instance) {
        // Form options (if any) can go here.
    }

    public function update($new_instance, $old_instance) {
        // Update widget settings (if any).
    }
}

function register_related_posts_widget() {
    register_widget('Related_Posts_Widget');
}
add_action('widgets_init', 'register_related_posts_widget');