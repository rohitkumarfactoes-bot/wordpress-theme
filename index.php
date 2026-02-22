<?php
/**
 * Gizmodotech Pro — Index / Homepage
 * Bento Grid + Latest News + Stories in Focus sections
 *
 * @package gizmodotech-pro
 */

get_header();
?>

<?php if ( is_home() && ! is_paged() ) : ?>

<!-- ============================================================
     BENTO HERO SECTION
     ============================================================ -->
<section class="homepage-section" aria-label="<?php esc_attr_e( 'Featured Posts', 'gizmodotech-pro' ); ?>">
	<div class="container" style="padding-top: var(--space-8);">
		<div class="section-header">
			<h2 class="section-header__title">
				<?php esc_html_e( 'Latest', 'gizmodotech-pro' ); ?>
				<span><?php esc_html_e( 'Stories', 'gizmodotech-pro' ); ?></span>
			</h2>
			<a href="<?php echo esc_url( get_permalink( get_option( 'page_for_posts' ) ) ?: home_url( '/blog/' ) ); ?>" class="section-header__link">
				<?php esc_html_e( 'View all', 'gizmodotech-pro' ); ?>
				<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="9 18 15 12 9 6"/></svg>
			</a>
		</div>
	</div>

	<?php
	$bento_query = new WP_Query( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 6,
		'ignore_sticky_posts' => false,
	] );
	?>

	<?php if ( $bento_query->have_posts() ) : ?>
	<div class="bento-grid" role="list">

		<?php
		$card_classes = [
			0 => 'bento-card--hero',
			1 => 'bento-card--half',
			2 => 'bento-card--half',
			3 => 'bento-card--third',
			4 => 'bento-card--third',
			5 => 'bento-card--third',
		];
		$counter = 0;

		while ( $bento_query->have_posts() ) :
			$bento_query->the_post();
			$size_class = $card_classes[ $counter ] ?? 'bento-card--third';
			$is_hero    = $counter === 0;
			$categories = get_the_category();
			$cat        = $categories ? $categories[0] : null;
			$read       = gizmo_get_reading_time( get_the_ID() );
			?>

			<article class="bento-card <?php echo esc_attr( $size_class ); ?>" role="listitem">

				<?php if ( has_post_thumbnail() ) : ?>
				<div class="bento-card__image">
					<?php
					the_post_thumbnail(
						$is_hero ? 'gizmo-hero' : 'gizmo-card',
						[
							'loading' => 'lazy',
							'alt'     => esc_attr( get_the_title() ),
						]
					);
					?>
					<?php if ( $is_hero && $counter === 0 ) : ?>
						<span class="bento-card__badge"><?php esc_html_e( '🔥 Trending', 'gizmodotech-pro' ); ?></span>
					<?php endif; ?>
				</div>
				<?php endif; ?>

				<div class="bento-card__content">

					<?php if ( $cat ) : ?>
					<a class="bento-card__category" href="<?php echo esc_url( get_category_link( $cat->term_id ) ); ?>">
						<?php echo esc_html( $cat->name ); ?>
					</a>
					<?php endif; ?>

					<h3 class="bento-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>

					<?php if ( $is_hero ) : ?>
					<p class="bento-card__excerpt"><?php echo esc_html( wp_trim_words( get_the_excerpt(), 20 ) ); ?></p>
					<?php endif; ?>

					<div class="bento-card__meta">
						<?php
						$author_avatar = get_avatar( get_the_author_meta( 'email' ), 56, '', '', [ 'class' => 'bento-card__avatar' ] );
						?>
						<div class="bento-card__author">
							<?php echo $author_avatar; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							<span><?php the_author(); ?></span>
						</div>

						<div class="bento-card__date">
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
							<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
								<?php echo esc_html( get_the_date( "M j, 'y" ) ); ?>
							</time>
						</div>

						<div class="bento-card__reading-time">
							<svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
							<?php echo esc_html( $read['label'] ); ?>
						</div>
					</div>
				</div>

			</article>

			<?php $counter++; ?>
		<?php endwhile; wp_reset_postdata(); ?>

	</div><!-- /.bento-grid -->
	<?php endif; ?>
</section>

<!-- ============================================================
     STORIES IN FOCUS (Beebom-style widget)
     ============================================================ -->
<?php
$focus_query = new WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 4,
	'tag'            => 'featured,stories-in-focus',
	'paged'          => 1,
] );

// Fallback if tag not found
if ( ! $focus_query->have_posts() ) {
	$focus_query = new WP_Query( [
		'post_type'      => 'post',
		'post_status'    => 'publish',
		'posts_per_page' => 4,
		'offset'         => 6,
	] );
}

if ( $focus_query->have_posts() ) : ?>
<section class="homepage-section" aria-label="<?php esc_attr_e( 'Stories in Focus', 'gizmodotech-pro' ); ?>" style="margin-top: var(--space-12);">
	<div class="container">
		<div class="section-header">
			<h2 class="section-header__title">
				<?php esc_html_e( 'Stories in', 'gizmodotech-pro' ); ?>
				<span><?php esc_html_e( 'Focus', 'gizmodotech-pro' ); ?></span>
			</h2>
		</div>

		<div class="stories-grid">
			<?php while ( $focus_query->have_posts() ) : $focus_query->the_post(); ?>
			<article class="story-card">
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="story-card__image">
					<?php the_post_thumbnail( 'gizmo-card', [ 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ] ); ?>
				</div>
				<?php endif; ?>
				<div class="story-card__body">
					<h3 class="story-card__title">
						<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
					</h3>
					<div class="story-card__meta">
						<span>
							<?php
							/* translators: %s: author name */
							printf( esc_html__( 'By %s', 'gizmodotech-pro' ), esc_html( get_the_author() ) );
							?>
						</span>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( get_the_date( "M j, 'y" ) ); ?>
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
     LATEST NEWS (Beebom-style with sidebar news)
     ============================================================ -->
<?php
$news_query = new WP_Query( [
	'post_type'      => 'post',
	'post_status'    => 'publish',
	'posts_per_page' => 6,
	'offset'         => 10,
] );

if ( $news_query->have_posts() ) : ?>
<section class="homepage-section" aria-label="<?php esc_attr_e( 'Latest News', 'gizmodotech-pro' ); ?>" style="margin-top: var(--space-12); padding-block: var(--space-12); background: linear-gradient(135deg, var(--color-dark) 0%, var(--color-dark-2) 100%);">
	<div class="container">
		<div class="section-header" style="margin-bottom: var(--space-6);">
			<h2 class="section-header__title" style="color: white;">
				<?php esc_html_e( 'Latest', 'gizmodotech-pro' ); ?>
				<span><?php esc_html_e( 'News', 'gizmodotech-pro' ); ?></span>
			</h2>
			<a href="<?php echo esc_url( home_url( '/news/' ) ); ?>" class="section-header__link" style="border-color: rgba(255,255,255,.3); color: rgba(255,255,255,.8);">
				<?php esc_html_e( 'View all →', 'gizmodotech-pro' ); ?>
			</a>
		</div>

		<div class="news-grid">
			<?php while ( $news_query->have_posts() ) : $news_query->the_post(); ?>
			<article class="news-card" style="background: rgba(255,255,255,.05); border-color: rgba(255,255,255,.08);">
				<?php if ( has_post_thumbnail() ) : ?>
				<div class="news-card__image">
					<?php the_post_thumbnail( 'gizmo-thumb', [ 'loading' => 'lazy', 'alt' => esc_attr( get_the_title() ) ] ); ?>
				</div>
				<?php endif; ?>
				<div class="news-card__content">
					<h3 class="news-card__title" style="color: rgba(255,255,255,.9);">
						<a href="<?php the_permalink(); ?>" style="color: inherit;"><?php the_title(); ?></a>
					</h3>
					<div class="news-card__meta">
						<span><?php the_author(); ?></span>
						<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>">
							<?php echo esc_html( human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) ); ?>
							<?php esc_html_e( 'ago', 'gizmodotech-pro' ); ?>
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
     UPCOMING PHONES (Price row — like screenshot)
     ============================================================ -->
<?php
$upcoming = get_posts( [
	'post_type'   => 'post',
	'numberposts' => 5,
	'category_name' => 'upcoming',
] );

if ( $upcoming ) : ?>
<section class="homepage-section" style="margin-top: var(--space-12);">
	<div class="container">
		<div class="section-header">
			<h2 class="section-header__title">
				<?php esc_html_e( 'Upcoming', 'gizmodotech-pro' ); ?>
				<span><?php esc_html_e( 'Phones', 'gizmodotech-pro' ); ?></span>
			</h2>
		</div>
		<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:var(--space-4);">
			<?php foreach ( $upcoming as $post ) : setup_postdata( $post ); ?>
			<a href="<?php the_permalink(); ?>" style="text-decoration:none;">
				<div class="bento-card" style="padding: var(--space-4); text-align: center; align-items: center;">
					<?php if ( has_post_thumbnail() ) : ?>
					<div style="height:120px;display:flex;align-items:center;justify-content:center;">
						<?php the_post_thumbnail( 'gizmo-thumb', [ 'loading' => 'lazy', 'style' => 'max-height:120px;width:auto;' ] ); ?>
					</div>
					<?php endif; ?>
					<h4 style="font-size:var(--font-size-sm);margin-top:var(--space-3);color:var(--text-primary);"><?php the_title(); ?></h4>
					<p style="font-size:var(--font-size-lg);font-weight:800;color:var(--color-primary);margin-top:var(--space-2);">
						<?php echo esc_html( get_post_meta( get_the_ID(), '_price', true ) ?: '—' ); ?>
					</p>
				</div>
			</a>
			<?php endforeach; wp_reset_postdata(); ?>
		</div>
	</div>
</section>
<?php endif; ?>

<?php else : ?>

<!-- ============================================================
     STANDARD BLOG LOOP (paginated / archive)
     ============================================================ -->
<div class="container" style="padding-top: var(--space-8);">
	<?php if ( have_posts() ) : ?>

		<div class="news-grid">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'template-parts/card', 'post' ); ?>
			<?php endwhile; ?>
		</div>

		<div class="pagination">
			<?php
			echo paginate_links( [
				'prev_text' => '← ' . __( 'Prev', 'gizmodotech-pro' ),
				'next_text' => __( 'Next', 'gizmodotech-pro' ) . ' →',
				'type'      => 'list',
			] );
			?>
		</div>

	<?php else : ?>
		<p><?php esc_html_e( 'No posts found.', 'gizmodotech-pro' ); ?></p>
	<?php endif; ?>
</div>

<?php endif; ?>

<?php get_footer(); ?>
