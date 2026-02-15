<?php
/**
 * The main template file
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

get_header(); ?>

<main id="main" class="site-main">
    <div class="container">
        
        <?php if (is_home() && !is_paged()) : ?>
        <!-- Trending Tags Bar -->
        <section class="trending-section">
            <div class="trending-header">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M13 20h-2V8l-5.5 5.5-1.42-1.42L12 4.16l7.92 7.92-1.42 1.42L13 8v12z"/>
                </svg>
                <h3><?php esc_html_e('Trending:', 'gizmodotech'); ?></h3>
            </div>
            <div class="trending-tags">
                <?php
                $popular_tags = get_tags(array(
                    'orderby' => 'count',
                    'order'   => 'DESC',
                    'number'  => 8
                ));
                foreach ($popular_tags as $tag) :
                ?>
                    <a href="<?php echo get_tag_link($tag->term_id); ?>" class="trending-tag">
                        <?php echo esc_html($tag->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>

        <div class="content-area <?php echo is_active_sidebar('sidebar-1') ? 'has-sidebar' : 'full-width'; ?>">
            
            <!-- Main Content -->
            <div class="main-content">
                
                <?php if (is_home() && !is_paged()) : ?>
                <!-- Featured Post -->
                <?php
                $featured_args = array(
                    'posts_per_page' => 1,
                    'meta_key'       => '_is_featured',
                    'meta_value'     => '1'
                );
                $featured_query = new WP_Query($featured_args);
                
                if ($featured_query->have_posts()) :
                    while ($featured_query->have_posts()) : $featured_query->the_post();
                ?>
                    <article <?php post_class('featured-post'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                        <div class="featured-image">
                            <a href="<?php the_permalink(); ?>">
                                <?php the_post_thumbnail('gizmodotech-featured'); ?>
                            </a>
                            <div class="featured-overlay">
                                <?php
                                $categories = get_the_category();
                                if (!empty($categories)) :
                                ?>
                                    <span class="badge">
                                        <?php echo esc_html($categories[0]->name); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="featured-content">
                            <h2 class="entry-title">
                                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                            </h2>
                            
                            <div class="entry-excerpt">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <?php gizmodotech_post_meta(); ?>
                            
                            <a href="<?php the_permalink(); ?>" class="btn btn-primary">
                                <?php esc_html_e('Read More', 'gizmodotech'); ?>
                                <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M1 8a.5.5 0 01.5-.5h11.793l-3.147-3.146a.5.5 0 01.708-.708l4 4a.5.5 0 010 .708l-4 4a.5.5 0 01-.708-.708L13.293 8.5H1.5A.5.5 0 011 8z"/>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php
                    endwhile;
                    wp_reset_postdata();
                endif;
                ?>
                <?php endif; ?>

                <!-- Section Header -->
                <div class="section-header">
                    <h2 class="section-title">
                        <?php
                        if (is_home()) {
                            esc_html_e('Latest Articles', 'gizmodotech');
                        } elseif (is_category()) {
                            single_cat_title();
                        } elseif (is_tag()) {
                            single_tag_title();
                        } elseif (is_archive()) {
                            the_archive_title();
                        } elseif (is_search()) {
                            printf(esc_html__('Search Results for: %s', 'gizmodotech'), get_search_query());
                        }
                        ?>
                    </h2>
                </div>

                <!-- Posts Grid -->
                <?php if (have_posts()) : ?>
                <div class="posts-grid">
                    <?php
                    while (have_posts()) : the_post();
                        get_template_part('template-parts/content', get_post_format());
                    endwhile;
                    ?>
                </div>

                <!-- Pagination -->
                <div class="pagination">
                    <?php
                    the_posts_pagination(array(
                        'mid_size'  => 2,
                        'prev_text' => sprintf(
                            '<svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M15 8a.5.5 0 00-.5-.5H2.707l3.147-3.146a.5.5 0 10-.708-.708l-4 4a.5.5 0 000 .708l4 4a.5.5 0 00.708-.708L2.707 8.5H14.5A.5.5 0 0015 8z"/></svg> %s',
                            __('Previous', 'gizmodotech')
                        ),
                        'next_text' => sprintf(
                            '%s <svg width="16" height="16" viewBox="0 0 16 16" fill="currentColor"><path fill-rule="evenodd" d="M1 8a.5.5 0 01.5-.5h11.793l-3.147-3.146a.5.5 0 01.708-.708l4 4a.5.5 0 010 .708l-4 4a.5.5 0 01-.708-.708L13.293 8.5H1.5A.5.5 0 011 8z"/></svg>',
                            __('Next', 'gizmodotech')
                        ),
                    ));
                    ?>
                </div>

                <?php else : ?>
                
                <!-- No Posts Found -->
                <div class="no-posts">
                    <svg width="100" height="100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <circle cx="12" cy="12" r="10" stroke-width="2"/>
                        <path d="M16 16s-1.5-2-4-2-4 2-4 2" stroke-width="2"/>
                        <line x1="9" y1="9" x2="9.01" y2="9" stroke-width="2"/>
                        <line x1="15" y1="9" x2="15.01" y2="9" stroke-width="2"/>
                    </svg>
                    <h2><?php esc_html_e('Nothing Found', 'gizmodotech'); ?></h2>
                    <p><?php esc_html_e('Sorry, no posts matched your criteria. Try searching for something else.', 'gizmodotech'); ?></p>
                    <?php get_search_form(); ?>
                </div>

                <?php endif; ?>
            </div>

            <!-- Sidebar -->
            <?php get_sidebar(); ?>

        </div>
    </div>
</main>

<?php get_footer(); ?>
