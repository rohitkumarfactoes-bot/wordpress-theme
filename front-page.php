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

// Get category selections from Customizer
$bento_cat_ids_str = get_theme_mod('gizmo_bento_categories', '');
$bento_cat_ids     = !empty($bento_cat_ids_str) ? array_map('intval', explode(',', $bento_cat_ids_str)) : [];

// --- Bento Query ---
$bento_args = [
	'post_type'           => 'post',
	'post_status'         => 'publish',
	'posts_per_page'      => 6,
	'ignore_sticky_posts' => false,
];
if (!empty($bento_cat_ids)) {
	$bento_args['category__in'] = $bento_cat_ids;
}
$bento_q = new WP_Query($bento_args);

// Array to store post IDs from the bento grid to exclude them from the next query
$bento_post_ids = [];

?>

<!-- ============================================================
     BENTO GRID SECTION
     ============================================================ -->
<section class="hp-section" aria-label="<?php esc_attr_e('Featured Stories','gizmodotech-pro'); ?>">
	<div class="hp-container">

		<?php if ($bento_q->have_posts()) : ?>

		<div class="bento-grid">
			<?php
			$i = 0;
			// Card type map: 0=hero, 1-2=half, 3-5=third
			$types = ['hero','half','half','third','third','third'];

			while ($bento_q->have_posts()) : $bento_q->the_post();
				$type   = $types[$i] ?? 'third';
				$cats   = get_the_category();
				$bento_post_ids[] = get_the_ID(); // Collect the ID to exclude from the next query
				$cat    = $cats ? $cats[0] : null;
				$read   = gizmo_get_reading_time(get_the_ID());
				$is_hero= ($i === 0);
			?>

			<article id="post-<?php the_ID(); ?>" <?php post_class('bento-card bento-card--'.$type); ?>>

				<?php if (has_post_thumbnail()) : ?>
				<div class="bento-card__img">
					<a href="<?php the_permalink(); ?>" tabindex="-1">
						<?php the_post_thumbnail($is_hero ? 'gizmo-hero' : 'gizmo-card',[
							'loading' => $is_hero ? 'eager' : 'lazy',
							'alt'     => esc_attr( get_the_title() ),
						]); ?>
					</a>
					<?php if ($is_hero) : ?>
					<span class="trending-badge">🔥 <?php esc_html_e('Trending','gizmodotech-pro'); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="bento-card__body">

					<?php if ($cat) : ?>
					<a class="post-cat-badge" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
						<?php echo esc_html($cat->name); ?>
					</a>
					<?php endif; ?>

					<h2 class="bento-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h2>

					<?php if ($is_hero) : ?>
					<p class="bento-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(),20)); ?></p>
					<?php endif; ?>

					<div class="post-card__meta">
						<?php echo get_avatar(get_the_author_meta('email'),28,'','',['class'=>'post-card__avatar']); // phpcs:ignore ?>
						<span class="post-card__author"><?php the_author(); ?></span>
						<span class="post-card__sep" aria-hidden="true">·</span>
						<time class="post-card__date" datetime="<?php echo esc_attr(get_the_date('c')); ?>">
							<?php echo esc_html(get_the_date("M j, 'y")); ?>
						</time>
						<span class="post-card__sep" aria-hidden="true">·</span>
						<span class="post-card__read">
							<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php echo esc_html($read['label']); ?>
						</span>
					</div>

				</div>

			</article>

			<?php $i++; endwhile; wp_reset_postdata(); ?>
		</div><!-- /.bento-grid -->

		<?php endif; ?>

	</div>
</section>

<!-- ============================================================
     LATEST NEWS (dark strip)
     ============================================================ -->
<?php
// Now build and run the news query
$news_cat_ids_str = get_theme_mod('gizmo_news_categories', '');
$news_cat_ids     = !empty($news_cat_ids_str) ? array_map('intval', explode(',', $news_cat_ids_str)) : [];

$news_args = [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'post__not_in'   => $bento_post_ids, // Exclude posts already shown in the bento grid
];
if (!empty($news_cat_ids)) {
	$news_args['category__in'] = $news_cat_ids;
}
$news_q = new WP_Query($news_args);

if ($news_q->have_posts()) : ?>
<section class="hp-section hp-section--dark" aria-label="<?php esc_attr_e('Latest News','gizmodotech-pro'); ?>">
	<div class="hp-container">

		<div class="section-title-row">
			<h2 class="section-title">
				<?php esc_html_e('Latest','gizmodotech-pro'); ?>
				<span><?php esc_html_e('News','gizmodotech-pro'); ?></span>
			</h2>
			<a href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/blog/')); ?>"
			   class="section-view-all">
				<?php esc_html_e('View all','gizmodotech-pro'); ?> →
			</a>
		</div>

		<div class="news-row">
			<?php while ($news_q->have_posts()) : $news_q->the_post();
				$cats = get_the_category();
				$cat  = $cats ? $cats[0] : null;
			?>
			<article <?php post_class('news-card'); ?>>
				<?php if (has_post_thumbnail()) : ?>
				<a class="news-card__thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
					<?php the_post_thumbnail('gizmo-thumb',['loading'=>'lazy','alt'=>'']); ?>
				</a>
				<?php endif; ?>
				<div class="news-card__body">
					<?php if ($cat) : ?>
					<a class="post-cat-badge post-cat-badge--sm" href="<?php echo esc_url(get_category_link($cat->term_id)); ?>">
						<?php echo esc_html($cat->name); ?>
					</a>
					<?php endif; ?>
					<h3 class="news-card__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
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
     FEATURED SLIDER SECTION
     ============================================================ -->
<?php
$slider_enabled = get_theme_mod('gizmo_slider_section_enabled', false);

if ($slider_enabled) :
	$slider_post_type = get_theme_mod('gizmo_slider_post_type', 'post');
	$slider_count     = get_theme_mod('gizmo_slider_posts_count', 6);
	$horizontal_count = get_theme_mod('gizmo_horizontal_posts_count', 3);

	// Exclude all posts already shown on the page
	$exclude_ids = array_merge($bento_post_ids, wp_list_pluck($news_q->posts, 'ID'));

	// Query for slider posts
	$slider_q = new WP_Query([
		'post_type'      => $slider_post_type,
		'posts_per_page' => $slider_count,
		'post__not_in'   => $exclude_ids,
	]);

	// Add slider posts to exclusion list for next query
	$exclude_ids = array_merge($exclude_ids, wp_list_pluck($slider_q->posts, 'ID'));

	// Query for horizontal card posts
	$horizontal_q = new WP_Query([
		'post_type'      => $slider_post_type, // Using same post type for simplicity, can be changed
		'posts_per_page' => $horizontal_count,
		'post__not_in'   => $exclude_ids,
	]);

	if ($slider_q->have_posts()) :
?>
<section class="hp-section hp-slider-section" aria-label="<?php esc_attr_e('Featured Content','gizmodotech-pro'); ?>">
	<div class="hp-container">
		<div class="hp-slider-grid">

			<!-- Left Column: Slider -->
			<div class="hp-slider-col">
				<div class="post-slider-container">
					<div class="post-slider-track">
						<?php while ($slider_q->have_posts()) : $slider_q->the_post(); ?>
						<div class="post-item-card">
							<a href="<?php the_permalink(); ?>" class="post-item-card__thumb">
								<?php if (has_post_thumbnail()) : ?>
									<?php the_post_thumbnail('gizmo-card', ['loading' => 'lazy']); ?>
								<?php endif; ?>
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
				<?php while ($horizontal_q->have_posts()) : $horizontal_q->the_post(); ?>
				<a href="<?php the_permalink(); ?>" class="horizontal-card">
					<?php if (has_post_thumbnail()) : ?>
					<div class="horizontal-card__thumb">
						<?php the_post_thumbnail('thumbnail', ['loading' => 'lazy']); ?>
					</div>
					<?php endif; ?>
					<div class="horizontal-card__content">
						<h4 class="horizontal-card__title"><?php the_title(); ?></h4>
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

<?php get_footer();
