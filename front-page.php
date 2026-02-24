<?php
/**
 * Gizmodotech Pro — front-page.php
 *
 * WordPress loads this file ONLY when:
 *  - Settings > Reading: A static page is set as front page, OR
 *  - Settings > Reading: "Your latest posts" is selected (still loads this file)
 *
 * This gives the homepage the Bento Grid layout.
 *
 * @package gizmodotech-pro
 */

get_header();

$exclude_ids = []; // Initialize exclusions
?>

<!-- ============================================================
     FEATURED SLIDER SECTION
     ============================================================ -->
<?php
$slider_enabled = get_theme_mod('gizmo_slider_section_enabled', false);

if ($slider_enabled) :
	$slider_title     = get_theme_mod('gizmo_slider_title', 'Latest');
	$slider_post_type = get_theme_mod('gizmo_slider_post_type', 'post');
	$slider_cats_str  = get_theme_mod('gizmo_slider_categories', '');
	$slider_cats      = !empty($slider_cats_str) ? array_map('intval', explode(',', $slider_cats_str)) : [];
	$slider_count     = get_theme_mod('gizmo_slider_posts_count', 6);

	$reviews_title    = get_theme_mod('gizmo_reviews_title', 'Reviews');
	$reviews_post_type= get_theme_mod('gizmo_reviews_post_type', 'reviews');
	$reviews_cats_str = get_theme_mod('gizmo_reviews_categories', '');
	$reviews_cats     = !empty($reviews_cats_str) ? array_map('intval', explode(',', $reviews_cats_str)) : [];
	$horizontal_count = get_theme_mod('gizmo_horizontal_posts_count', 3);

	// Query for slider posts
	$slider_args = [
		'post_type'      => $slider_post_type,
		'posts_per_page' => $slider_count,
		'post__not_in'   => $exclude_ids,
	];
	if (!empty($slider_cats)) {
		$slider_args['category__in'] = $slider_cats;
	}
	$slider_q = new WP_Query($slider_args);

	// Add slider posts to exclusion list for next query
	if ($slider_q->have_posts()) {
		$exclude_ids = array_merge($exclude_ids, wp_list_pluck($slider_q->posts, 'ID'));
	}

	// Query for horizontal card posts
	$horizontal_args = [
		'post_type'      => $reviews_post_type,
		'posts_per_page' => $horizontal_count,
		'post__not_in'   => $exclude_ids,
	];
	if (!empty($reviews_cats)) {
		$horizontal_args['category__in'] = $reviews_cats;
	}
	$horizontal_q = new WP_Query($horizontal_args);

	// Update exclusions for News section
	if ($horizontal_q->have_posts()) {
		$exclude_ids = array_merge($exclude_ids, wp_list_pluck($horizontal_q->posts, 'ID'));
	}

	if ($slider_q->have_posts()) :
?>
<section class="hp-section hp-slider-section" aria-label="<?php esc_attr_e('Featured Content','gizmodotech-pro'); ?>">
	<div class="hp-container">
		<div class="hp-slider-grid">

			<!-- Left Column: Slider -->
			<div class="hp-slider-col">
				<h2 class="section-title" style="color:var(--text-primary); margin-bottom:1.5rem;">
					<?php echo esc_html($slider_title); ?>
				</h2>
				<div class="post-slider-container">
					<div class="post-slider-track">
						<?php while ($slider_q->have_posts()) : $slider_q->the_post(); ?>
						<div <?php post_class('post-item-card'); ?>>
							<a href="<?php the_permalink(); ?>" class="post-item-card__thumb" tabindex="-1" aria-hidden="true">
								<?php if (has_post_thumbnail()) {
									the_post_thumbnail('gizmo-card', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]);
								} ?>
							</a>
							<div class="post-item-card__content">
								<h3 class="post-item-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
								<div class="post-item-card__meta">
									<time datetime="<?php echo esc_attr(get_the_date('c')); ?>"><?php echo esc_html(get_the_date()); ?></time>
								</div>
							</div>
						</div>
						<?php endwhile; wp_reset_postdata(); ?>
					</div>
					<button class="slider-button slider-button-prev" aria-label="<?php esc_attr_e('Previous slide','gizmodotech-pro'); ?>">&#x276E;</button>
					<button class="slider-button slider-button-next" aria-label="<?php esc_attr_e('Next slide','gizmodotech-pro'); ?>">&#x276F;</button>
				</div>
			</div>

			<!-- Right Column: Horizontal Cards -->
			<?php if ($horizontal_q->have_posts()) : ?>
			<div class="hp-horizontal-cards-col">
				<h2 class="section-title" style="color:var(--text-primary); margin-bottom:1.5rem;">
					<?php echo esc_html($reviews_title); ?>
				</h2>
				<?php while ($horizontal_q->have_posts()) : $horizontal_q->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="horizontal-card">
					<?php if (has_post_thumbnail()) : ?>
					<div class="horizontal-card__thumb">
						<?php the_post_thumbnail('thumbnail', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]); ?>
					</div>
					<?php endif; ?>
					<div class="horizontal-card__content">
						<h3 class="horizontal-card__title"><?php the_title(); ?></h3>
						<div class="horizontal-card__meta">
							<time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
								<?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp'))); ?> <?php esc_html_e('ago','gizmodotech-pro'); ?>
							</time>
						</div>
					</div>
				</a>
				<?php endwhile; wp_reset_postdata(); ?>
			</div>
			<?php endif; ?>

		</div>
	</div>
</section>
<?php
	endif; // end if ($slider_q->have_posts())
endif; // end if ($slider_enabled)
?>

<!-- ============================================================
     LATEST NEWS (dark strip)
     ============================================================ -->
<?php
// 1. Setup variables - Adjust 'technews' if your ACF slug is different
$news_post_type = get_theme_mod('gizmo_news_post_type', 'technews'); 
$news_count     = 6; // Force 6 cards to fix the layout

// 2. The Query
$news_args = [
    'post_type'           => $news_post_type,
    'post_status'         => 'publish',
    'posts_per_page'      => $news_count,
    'orderby'             => 'date',
    'order'               => 'DESC',
    'ignore_sticky_posts' => 1,
    // We REMOVE 'post__not_in' here so it definitely shows the 6 latest news 
    // even if they appeared in the slider above.
];

$news_q = new WP_Query($news_args);

if ($news_q->have_posts()) : ?>
<section class="hp-section hp-section--dark" aria-label="Latest News">
    <div class="hp-container">

        <div class="section-title-row">
            <h2 class="section-title">
                <?php esc_html_e('Latest','gizmodotech-pro'); ?>
                <span><?php esc_html_e('News','gizmodotech-pro'); ?></span>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link($news_post_type)); ?>" class="section-view-all">
                <?php esc_html_e('View all','gizmodotech-pro'); ?> →
            </a>
        </div>

        <div class="news-row">
            <?php while ($news_q->have_posts()) : $news_q->the_post(); ?>
            <article <?php post_class('news-card'); ?>>
                <?php if (has_post_thumbnail()) : ?>
                <a class="news-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
                    <?php the_post_thumbnail('gizmo-thumb', ['loading' => 'lazy', 'alt' => esc_attr(get_the_title())]); ?>
                </a>
                <?php endif; ?>
                
                <div class="news-card__body">
                    <?php 
                    // Restoration of your exact theme category function
                    if (function_exists('gizmo_the_post_categories')) {
                        gizmo_the_post_categories(get_the_ID(), 'post-cat-badge post-cat-badge--sm');
                    } 
                    ?>
                    
                    <h3 class="news-card__title">
                        <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                    </h3>
                    
                    <div class="news-card__meta">
                        <span><?php the_author(); ?></span>
                        <span>·</span>
                        <time datetime="<?php echo esc_attr(get_the_date('c')); ?>">
                            <?php echo esc_html(human_time_diff(get_the_time('U'), current_time('timestamp'))); ?> <?php esc_html_e('ago','gizmodotech-pro'); ?>
                        </time>
                    </div>
                </div>
            </article>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>

    </div>
</section>
<?php endif; ?>


<!-- ============================================================
     HOW TO'S SECTION
     ============================================================ -->
<?php
$howto_title = get_theme_mod('gizmo_howto_title', "How To's");
$howto_type  = get_theme_mod('gizmo_howto_post_type', 'post');
$howto_cats_str = get_theme_mod('gizmo_howto_categories', '');
$howto_cats     = !empty($howto_cats_str) ? array_map('intval', explode(',', $howto_cats_str)) : [];

$howto_args = [
	'post_type'      => $howto_type,
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'post__not_in'   => $exclude_ids,
];
if (!empty($howto_cats)) { $howto_args['category__in'] = $howto_cats; }

$howto_q = new WP_Query($howto_args);

if ($howto_q->have_posts()) :
	$exclude_ids = array_merge($exclude_ids, wp_list_pluck($howto_q->posts, 'ID'));
?>
<section class="hp-section" aria-label="<?php echo esc_attr($howto_title); ?>">
	<div class="hp-container">
		<div class="section-title-row">
			<h2 class="section-title" style="color:var(--text-primary);">
				<?php echo esc_html($howto_title); ?>
			</h2>
		</div>
		<div class="news-row">
			<?php while ($howto_q->have_posts()) : $howto_q->the_post(); ?>
			<article <?php post_class('news-card'); ?> style="background:var(--bg-surface); border-color:var(--border-color);">
				<?php if (has_post_thumbnail()) : ?>
				<a class="news-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php the_post_thumbnail('gizmo-thumb',['loading'=>'lazy', 'alt' => esc_attr(get_the_title())]); ?>
				</a>
				<?php endif; ?>
				<div class="news-card__body">
					<h3 class="news-card__title">
						<a href="<?php the_permalink(); ?>" style="color:var(--text-primary) !important;"><?php the_title(); ?></a>
					</h3>
					<div class="news-card__meta" style="color:var(--text-muted);">
						<time><?php echo esc_html(get_the_date()); ?></time>
					</div>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     TECH TIPS SECTION
     ============================================================ -->
<?php
$techtips_title = get_theme_mod('gizmo_techtips_title', "Tech Tips");
$techtips_type  = get_theme_mod('gizmo_techtips_post_type', 'post');
$techtips_cats_str = get_theme_mod('gizmo_techtips_categories', '');
$techtips_cats     = !empty($techtips_cats_str) ? array_map('intval', explode(',', $techtips_cats_str)) : [];

$techtips_args = [
	'post_type'      => $techtips_type,
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'post__not_in'   => $exclude_ids,
];
if (!empty($techtips_cats)) { $techtips_args['category__in'] = $techtips_cats; }

$techtips_q = new WP_Query($techtips_args);

if ($techtips_q->have_posts()) :
?>
<section class="hp-section hp-section--dark" aria-label="<?php echo esc_attr($techtips_title); ?>">
	<div class="hp-container">
		<div class="section-title-row">
			<h2 class="section-title">
				<?php echo esc_html($techtips_title); ?>
			</h2>
		</div>
		<div class="news-row">
			<?php while ($techtips_q->have_posts()) : $techtips_q->the_post(); ?>
			<article <?php post_class('news-card'); ?>>
				<?php if (has_post_thumbnail()) : ?>
				<a class="news-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php the_post_thumbnail('gizmo-thumb',['loading'=>'lazy', 'alt' => esc_attr(get_the_title())]); ?>
				</a>
				<?php endif; ?>
				<div class="news-card__body">
					<h3 class="news-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<div class="news-card__meta">
						<time><?php echo esc_html(get_the_date()); ?></time>
					</div>
				</div>
			</article>
			<?php endwhile; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<!-- ============================================================
     MOBILES SECTION
     ============================================================ -->
<?php
$mobiles_title = get_theme_mod('gizmo_mobiles_title', "Mobiles");
$mobiles_type  = get_theme_mod('gizmo_mobiles_post_type', 'post');
$mobiles_count = get_theme_mod('gizmo_mobiles_count', 8);
$mobiles_filter_cats_str = get_theme_mod('gizmo_mobiles_filter_categories', '');
$mobiles_filter_cat_ids  = !empty($mobiles_filter_cats_str) ? array_map('intval', explode(',', $mobiles_filter_cats_str)) : [];

// Fallback: If no categories selected, look for specific mobile brands
if (empty($mobiles_filter_cat_ids)) {
	$brand_slugs = ['samsung', 'vivo', 'oppo', 'nothing', 'xiaomi', 'apple', 'asus', 'google', "asus", "oneplus", "realme", "motorola", "oppo", "iqoo"];
	$brand_terms = get_terms([
		'taxonomy'   => 'category',
		'slug'       => $brand_slugs,
		'hide_empty' => true,
	]);
	if (!is_wp_error($brand_terms) && !empty($brand_terms)) {
		$mobiles_filter_cat_ids = wp_list_pluck($brand_terms, 'term_id');
	}
}

// Get 'mobile' category for intersection (to ensure we only show phones)
$mobile_term    = get_term_by('slug', 'mobile', 'category');
$mobile_term_id = $mobile_term ? $mobile_term->term_id : 0;

$initial_mobiles_args = [
	'post_type'      => $mobiles_type,
	'post_status'    => 'publish',
	'posts_per_page' => $mobiles_count,
	'post__not_in'   => $exclude_ids,
];

$tax_query = [];
if ($mobile_term_id) {
	$tax_query[] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $mobile_term_id];
}
if (!empty($mobiles_filter_cat_ids)) {
	$tax_query[] = ['taxonomy' => 'category', 'field' => 'term_id', 'terms' => $mobiles_filter_cat_ids, 'operator' => 'IN'];
}

if (!empty($tax_query)) {
	$tax_query['relation'] = 'AND';
	$initial_mobiles_args['tax_query'] = $tax_query;
}

$mobiles_q = new WP_Query($initial_mobiles_args);

if ($mobiles_q->have_posts()) :
?>
<section class="hp-section" aria-label="<?php echo esc_attr($mobiles_title); ?>">
	<div class="hp-container">
		<div class="section-title-row">
			<h2 class="section-title" style="color:var(--text-primary);">
				<?php echo esc_html($mobiles_title); ?>
			</h2>
		</div>

        <?php if (!empty($mobiles_filter_cat_ids)) :
            $filter_categories = get_categories(['include' => $mobiles_filter_cat_ids, 'hide_empty' => false]);
        ?>
        <nav class="mobile-filter-nav" aria-label="<?php esc_attr_e('Filter Mobiles', 'gizmodotech-pro'); ?>">
            <button type="button" class="mobile-filter-btn is-active" data-category="0"><?php esc_html_e('All', 'gizmodotech-pro'); ?></button>
            <?php foreach ($filter_categories as $category) : ?>
                <button type="button" class="mobile-filter-btn" data-category="<?php echo esc_attr($category->term_id); ?>"><?php echo esc_html($category->name); ?></button>
            <?php endforeach; ?>
        </nav>
        <?php endif; ?>

		<div class="mobiles-grid" id="mobiles-grid-container">
			<?php
            while ($mobiles_q->have_posts()) :
                $mobiles_q->the_post();
				get_template_part('template-parts/card', 'mobile');
			endwhile;
            wp_reset_postdata();
            ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php get_footer(); ?>
