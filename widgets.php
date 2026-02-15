<?php
/**
 * Custom Widgets for Gizmodotech
 */

/**
 * Featured Slider Widget
 */
class Gizmodotech_Featured_Slider_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'gizmodotech_featured_slider',
            esc_html__('Gizmodotech: Featured Slider', 'gizmodotech'),
            array('description' => esc_html__('Displays a modern slider of latest posts.', 'gizmodotech'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $query_args = array(
            'posts_per_page' => 5,
            'ignore_sticky_posts' => 1,
        );

        if (!empty($instance['category'])) {
            $query_args['cat'] = intval($instance['category']);
        }

        $query = new WP_Query($query_args);

        if ($query->have_posts()) : ?>
            <div class="gizmodotech-slider-container">
                <div class="gizmodotech-slider">
                    <?php while ($query->have_posts()) : $query->the_post(); ?>
                        <div class="slider-item">
                            <a href="<?php the_permalink(); ?>" class="slider-link">
                                <?php if (has_post_thumbnail()) : ?>
                                    <?php the_post_thumbnail('gizmodotech-featured', array('class' => 'slider-image')); ?>
                                <?php endif; ?>
                                <div class="slider-content">
                                    <span class="slider-cat"><?php $cat = get_the_category(); if($cat) echo esc_html($cat[0]->name); ?></span>
                                    <h3 class="slider-title"><?php the_title(); ?></h3>
                                    <span class="slider-date"><?php echo get_the_date(); ?></span>
                                </div>
                            </a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
            <?php wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $category = !empty($instance['category']) ? $instance['category'] : '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'gizmodotech'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'gizmodotech'); ?></label>
            <?php wp_dropdown_categories(array(
                'show_option_all' => 'All Categories',
                'name' => $this->get_field_name('category'),
                'selected' => $category,
                'class' => 'widefat',
                'value_field' => 'term_id',
            )); ?>
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['category'] = (!empty($new_instance['category'])) ? intval($new_instance['category']) : '';
        return $instance;
    }
}

/**
 * Category Posts Widget
 */
class Gizmodotech_Category_Posts_Widget extends WP_Widget {
    public function __construct() {
        parent::__construct(
            'gizmodotech_category_posts',
            esc_html__('Gizmodotech: Category Posts', 'gizmodotech'),
            array('description' => esc_html__('Displays a grid of posts from a specific category.', 'gizmodotech'))
        );
    }

    public function widget($args, $instance) {
        echo $args['before_widget'];
        
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $category_id = !empty($instance['category']) ? intval($instance['category']) : 0;
        $count = !empty($instance['count']) ? intval($instance['count']) : 4;

        // Header with Title and "View All" link
        echo '<div class="widget-header-flex">';
        if ($title) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }
        if ($category_id) {
            $cat_link = get_category_link($category_id);
            echo '<a href="' . esc_url($cat_link) . '" class="view-all-link">' . esc_html__('View All', 'gizmodotech') . ' &rarr;</a>';
        }
        echo '</div>';

        $query_args = array(
            'posts_per_page' => $count,
            'ignore_sticky_posts' => 1,
        );

        if ($category_id) {
            $query_args['cat'] = $category_id;
        }

        $query = new WP_Query($query_args);

        if ($query->have_posts()) : ?>
            <div class="posts-grid category-widget-grid">
                <?php while ($query->have_posts()) : $query->the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('article-card'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="article-thumbnail">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('gizmodotech-medium'); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        <div class="article-content">
                            <header class="entry-header">
                                <div class="article-meta">
                                    <?php
                                    $categories = get_the_category();
                                    if ($categories) {
                                        printf('<span class="category-badge">%s</span>', esc_html($categories[0]->name));
                                    }
                                    ?>
                                </div>
                                <h3 class="article-title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
                            </header>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            <?php wp_reset_postdata();
        endif;

        echo $args['after_widget'];
    }

    public function form($instance) {
        $title = !empty($instance['title']) ? $instance['title'] : '';
        $category = !empty($instance['category']) ? $instance['category'] : '';
        $count = !empty($instance['count']) ? $instance['count'] : 4;
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'gizmodotech'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>">
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category:', 'gizmodotech'); ?></label>
            <?php wp_dropdown_categories(array(
                'show_option_all' => 'All Categories',
                'name' => $this->get_field_name('category'),
                'selected' => $category,
                'class' => 'widefat',
                'value_field' => 'term_id',
            )); ?>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('count')); ?>"><?php esc_html_e('Number of Posts:', 'gizmodotech'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('count')); ?>" name="<?php echo esc_attr($this->get_field_name('count')); ?>" type="number" value="<?php echo esc_attr($count); ?>">
        </p>
        <?php
    }

    public function update($new_instance, $old_instance) {
        $instance = array();
        $instance['title'] = (!empty($new_instance['title'])) ? sanitize_text_field($new_instance['title']) : '';
        $instance['category'] = (!empty($new_instance['category'])) ? intval($new_instance['category']) : '';
        $instance['count'] = (!empty($new_instance['count'])) ? intval($new_instance['count']) : 4;
        return $instance;
    }
}