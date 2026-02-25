<?php
/**
 * Gizmodotech Pro — single.php
 *
 * Layout:
 *  - Full-width hero image (above everything)
 *  - Content area: 2-column (post content LEFT | sidebar RIGHT)
 *  - Sidebar: "You may also like" + "Related Posts"
 *  - Share bar, Author box, Comment toggle below content
 *  - Yoast SEO compatible (no duplicate schema conflicts)
 *
 * @package gizmodotech-pro
 */

get_header();

while (have_posts()) : the_post();

	$reading   = gizmo_get_reading_time(get_the_ID());
	$cats      = get_the_category();
	$cat       = $cats ? $cats[0] : null;
	$tags      = get_the_tags();
	$post_type = get_post_type();

	// Related posts by category
	$related = null;
	if ($cat) {
		$related = new WP_Query([
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'post__not_in'   => [get_the_ID()],
			'cat'            => $cat->term_id,
			'orderby'        => 'rand',
		]);
	} else {
		// Fallback: Random posts from same post type
		$related = new WP_Query([
			'post_type'      => $post_type,
			'post_status'    => 'publish',
			'posts_per_page' => 4,
			'post__not_in'   => [get_the_ID()],
			'orderby'        => 'rand',
		]);
	}

	// "You may also like" — from same category, different set
	$also_like_args = [
		'post_type'      => $post_type,
		'post_status'    => 'publish',
		'posts_per_page' => 5,
		'post__not_in'   => [get_the_ID()],
		'orderby'        => 'date',
		'order'          => 'DESC',
	];
	if ($cat) {
		$also_like_args['cat'] = $cat->term_id;
	}
	$also_like = new WP_Query($also_like_args);

	// Check if this is a "Mobile" post (Category 'mobile' OR Post Type 'mobile')
	$is_mobile_gallery = ( has_category('mobile') || get_post_type() === 'mobile' );
	$gallery_images    = $is_mobile_gallery ? gizmo_get_gallery_images(get_the_ID()) : [];
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('single-post'); ?>
         itemscope itemtype="https://schema.org/Article">

	

	<!-- ── Two-column layout: content + sidebar ── -->
	<div class="single-layout">
		<div class="single-wrap">
	
		
		<!-- ── LEFT: Post Content ── -->
		<div class="single-content">

			<!-- Breadcrumbs -->
			<?php gizmodotech_breadcrumbs(); ?>

	<!-- Mobile Gallery OR Full-width hero image -->
	<?php if ( $is_mobile_gallery && ! empty( $gallery_images ) ) : ?>
		<div class="mobile-wrap">
			<div class="extracted-images">
				<?php foreach ( $gallery_images as $img_src ) : ?>
				<div class="thumbnail">
					<img src="<?php echo esc_url( $img_src ); ?>" alt="Thumbnail" data-full-image="<?php echo esc_url( $img_src ); ?>">
				</div>
				<?php endforeach; ?>
			</div>
			<div class="image-display" id="image-display">
				<img src="<?php echo esc_url( $gallery_images[0] ); ?>" alt="<?php the_title_attribute(); ?>">
			</div>
		</div>

	<?php elseif (has_post_thumbnail()) : ?>
	<div class="single-hero">
		<?php the_post_thumbnail('gizmo-hero', [
			'loading' => 'eager',
			'alt'     => esc_attr(get_the_title()),
			'fetchpriority' => 'high',
			'itemprop'=> 'image',
		]); ?>
	</div>
	<?php endif; ?>
			<!-- Post Header -->
			<header class="single-header">

				<?php gizmo_the_post_categories( get_the_ID(), 'post-cat-badge' ); ?>

				<h1 class="single-title" itemprop="headline"><?php the_title(); ?></h1>

				<div class="single-meta">
					<div class="single-meta__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
						<?php echo get_avatar(get_the_author_meta('email'), 44, '', '', ['class'=>'single-meta__avatar']); // phpcs:ignore ?>
						<div>
							<a class="single-meta__name" itemprop="url"
							   href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
								<span itemprop="name"><?php the_author(); ?></span>
							</a>
							<time class="single-meta__date" itemprop="datePublished"
							      datetime="<?php echo esc_attr(get_the_date('c')); ?>">
								<?php echo esc_html(get_the_date()); ?>
							</time>
						</div>
					</div>
					<div class="single-meta__extras">
						<span class="single-meta__read">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php echo esc_html($reading['label']); ?>
						</span>
						<?php
						// Show calendar icon for last modified date
						$modified = get_the_modified_date();
						$published = get_the_date();
						if ($modified !== $published) : ?>
						<span class="single-meta__updated" title="<?php esc_attr_e('Last updated','gizmodotech-pro'); ?>">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
							<time itemprop="dateModified" datetime="<?php echo esc_attr(get_the_modified_date('c')); ?>">
								<?php echo esc_html($modified); ?>
							</time>
						</span>
						<?php endif; ?>
					</div>
				</div>

			</header><!-- /.single-header -->

			<!-- Table of Contents -->
			<div class="toc">
				<h3 class="toc__title">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
					<span><?php esc_html_e('In this article', 'gizmodotech-pro'); ?></span>
				</h3>
				<ul class="toc__list"></ul>
			</div>

			<!-- Post Body -->
			<div class="single-body" itemprop="articleBody">
				<?php the_content(); ?>
			</div>

			<?php
			wp_link_pages([
				'before' => '<div class="post-pages">',
				'after'  => '</div>',
			]);
			?>

			<!-- Share Bar -->
			<div class="share-bar" aria-label="<?php esc_attr_e('Share this article','gizmodotech-pro'); ?>">
				<span class="share-bar__label"><?php esc_html_e('Share:','gizmodotech-pro'); ?></span>

				<button class="share-btn share-btn--twitter" type="button" aria-label="<?php esc_attr_e('Share on X','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.741l7.73-8.835L1.254 2.25H8.08l4.26 5.633L18.244 2.25Zm-1.161 17.52h1.833L7.084 4.126H5.117L17.083 19.77Z"/></svg>
					<span><?php esc_html_e('Twitter','gizmodotech-pro'); ?></span>
				</button>

				<button class="share-btn share-btn--facebook" type="button" aria-label="<?php esc_attr_e('Share on Facebook','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
					<span><?php esc_html_e('Facebook','gizmodotech-pro'); ?></span>
				</button>

				<button class="share-btn share-btn--whatsapp" type="button" aria-label="<?php esc_attr_e('Share on WhatsApp','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
					<span><?php esc_html_e('WhatsApp','gizmodotech-pro'); ?></span>
				</button>

				<button class="share-btn share-btn--linkedin" type="button" aria-label="<?php esc_attr_e('Share on LinkedIn','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 01-2.063-2.065 2.064 2.064 0 112.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
					<span><?php esc_html_e('LinkedIn','gizmodotech-pro'); ?></span>
				</button>

				<button class="share-btn share-btn--copy" type="button" aria-label="<?php esc_attr_e('Copy link','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"/></svg>
					<span><?php esc_html_e('Copy Link','gizmodotech-pro'); ?></span>
				</button>

				<?php if ( $is_mobile_gallery ) : 
					$device_slug = get_post_field( 'post_name', get_the_ID() );
				?>
				<a href="<?php echo esc_url( home_url( '/compare/?device1=' . $device_slug ) ); ?>" class="share-btn share-btn--compare" aria-label="<?php esc_attr_e('Compare Device','gizmodotech-pro'); ?>">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M8 3 4 7l4 4"/><path d="M4 7h16"/><path d="m16 21 4-4-4-4"/><path d="M20 17H4"/></svg>
					<span><?php esc_html_e('Compare','gizmodotech-pro'); ?></span>
				</a>
				<?php endif; ?>
			</div><!-- /.share-bar -->

			<!-- Author Box -->
			<div class="author-box">
				<?php echo get_avatar(get_the_author_meta('email'), 80, '', '', ['class'=>'author-box__avatar']); // phpcs:ignore ?>
				<div class="author-box__info">
					<h3 class="author-box__name"><?php the_author(); ?></h3>
					<p class="author-box__bio">
						<?php echo esc_html(get_the_author_meta('description') ?: __('Tech journalist and gadget enthusiast covering the latest in smartphones, laptops, and emerging technology.','gizmodotech-pro')); ?>
					</p>
					<a class="author-box__link" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
						<?php esc_html_e('View all posts →','gizmodotech-pro'); ?>
					</a>
				</div>
			</div>

		

		</div><!-- /.single-content -->
		
		<!-- Comments -->
		<?php if ( comments_open() || get_comments_number() ) : ?>
			<?php comments_template(); ?>
		<?php endif; ?>
</div>
		<!-- ── RIGHT: Sidebar ── -->
		<aside class="single-sidebar" aria-label="<?php esc_attr_e('Sidebar','gizmodotech-pro'); ?>">

			<!-- Manual Widgets (Appearance > Widgets > Sidebar) -->
			<?php if ( is_active_sidebar('sidebar-1') ) : ?>
				<?php dynamic_sidebar('sidebar-1'); ?>
			<?php endif; ?>

			<!-- You May Also Like -->
			<?php
			$latest_posts_q = new WP_Query([
				'post_type'      => 'post',
				'posts_per_page' => 5,
				'post_status'    => 'publish',
				'orderby'        => 'date',
				'order'          => 'DESC',
				'post__not_in'   => [get_the_ID()],
			]);
			if ($latest_posts_q->have_posts()) :
			?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('You may also like', 'gizmodotech-pro'); ?></h3>
				<ul class="sidebar-also-like">
					<?php while ($latest_posts_q->have_posts()) : $latest_posts_q->the_post(); ?>
					<li>
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</li>
					<?php endwhile; wp_reset_postdata(); ?>
				</ul>
			</div>
			<?php endif; ?>
			
			<!-- Amazon Products (Async) -->
			<?php if (get_theme_mod('gizmo_amazon_enabled', false)) : ?>
			<div id="gizmo-amazon-sidebar" data-keyword="<?php echo esc_attr(get_the_title()); ?>"></div>
			<?php endif; ?>

			
			

			<!-- Related Posts -->
			<?php if ($related && $related->have_posts()) : ?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('Related Post','gizmodotech-pro'); ?></h3>
				<div class="sidebar-related">
					<?php while ($related->have_posts()) : $related->the_post(); 
						$is_mob = has_category('mobile') ? 'type-mobile' : '';
					?>
					<a class="sidebar-related__item <?php echo esc_attr($is_mob); ?>" href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail()) : ?>
						<div class="sidebar-related__thumb">
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=> esc_attr(get_the_title())]); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Latest Tech News -->
			<?php
			$technews_q = new WP_Query([
				'post_type'      => 'technews',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'post__not_in'   => [get_the_ID()],
				'orderby'        => 'date',
				'order'          => 'DESC',
			]);
			if ($technews_q->have_posts()) :
			?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('Latest Tech News','gizmodotech-pro'); ?></h3>
				<div class="sidebar-related">
					<?php while ($technews_q->have_posts()) : $technews_q->the_post(); ?>
					<a class="sidebar-related__item" href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail()) : ?>
						<div class="sidebar-related__thumb">
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=> esc_attr(get_the_title())]); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Latest Reviews (Post Type: Review) -->
			<?php
			$custom_reviews_q = new WP_Query([
				'post_type'      => 'review',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'post__not_in'   => [get_the_ID()],
				'orderby'        => 'date',
				'order'          => 'DESC',
			]);
			if ($custom_reviews_q->have_posts()) :
			?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('Latest Reviews','gizmodotech-pro'); ?></h3>
				<div class="sidebar-related">
					<?php while ($custom_reviews_q->have_posts()) : $custom_reviews_q->the_post(); ?>
					<a class="sidebar-related__item" href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail()) : ?>
						<div class="sidebar-related__thumb">
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=> esc_attr(get_the_title())]); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

			<!-- Compare (Category: Compare) -->
			<?php
			$compare_q = new WP_Query([
				'post_type'      => 'post',
				'category_name'  => 'compare',
				'post_status'    => 'publish',
				'posts_per_page' => 4,
				'post__not_in'   => [get_the_ID()],
				'orderby'        => 'date',
				'order'          => 'DESC',
			]);
			if ($compare_q->have_posts()) :
			?>
			<div class="sidebar-widget">
				<h3 class="sidebar-widget__title"><?php esc_html_e('Compare','gizmodotech-pro'); ?></h3>
				<div class="sidebar-related">
					<?php while ($compare_q->have_posts()) : $compare_q->the_post(); 
						$is_mob = has_category('mobile') ? 'type-mobile' : '';
					?>
					<a class="sidebar-related__item <?php echo esc_attr($is_mob); ?>" href="<?php the_permalink(); ?>">
						<?php if (has_post_thumbnail()) : ?>
						<div class="sidebar-related__thumb">
							<?php the_post_thumbnail('thumbnail',['loading'=>'lazy','alt'=> esc_attr(get_the_title())]); ?>
						</div>
						<?php endif; ?>
						<span class="sidebar-related__title"><?php the_title(); ?></span>
					</a>
					<?php endwhile; wp_reset_postdata(); ?>
				</div>
			</div>
			<?php endif; ?>

		</aside><!-- /.single-sidebar -->

	</div><!-- /.single-layout -->

</article>

<?php endwhile; ?>

<?php get_footer(); ?>
