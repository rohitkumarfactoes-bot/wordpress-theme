<?php
/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package gizmodotech-pro
 */

if ( post_password_required() ) {
	return;
}
?>

<?php 
$comment_n = get_comments_number();
?>

<div class="comments-section" id="comments-section">

	<!-- Toggle Button -->
	<button class="comments-toggle" type="button" aria-expanded="false" aria-controls="comments-body" id="comments-toggle-btn">
		<div class="comments-toggle__left">
			<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
			<?php printf( _n( '%s Comment', '%s Comments', $comment_n, 'gizmodotech-pro' ), '<span class="comments-toggle__count">' . number_format_i18n( $comment_n ) . '</span>' ); ?>
		</div>
		<svg class="comments-toggle__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
	</button>

	<div class="comments-body" id="comments-body" aria-live="polite">

<div class="comments-area">

	<?php if ( have_comments() ) : ?>
		<h2 class="comments-title">
			<?php
			$comment_count = get_comments_number();
			if ( '1' === $comment_count ) {
				esc_html_e( '1 Comment', 'gizmodotech-pro' );
			} else {
				printf(
					/* translators: %s: Number of comments. */
					esc_html__( '%s Comments', 'gizmodotech-pro' ),
					number_format_i18n( $comment_count )
				);
			}
			?>
		</h2>

		<ol class="comment-list">
			<?php
			wp_list_comments(
				[
					'style'       => 'ol',
					'short_ping'  => true,
					'avatar_size' => 80,
					'callback'    => 'gizmo_comment_callback',
				]
			);
			?>
		</ol>

		<?php
		the_comments_navigation(
			[
				'prev_text' => '<span>&larr;</span> ' . __( 'Older Comments', 'gizmodotech-pro' ),
				'next_text' => __( 'Newer Comments', 'gizmodotech-pro' ) . ' <span>&rarr;</span>',
			]
		);

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'gizmodotech-pro' ); ?></p>
			<?php
		endif;

	endif;

	$comment_field = '<p class="comment-form-comment">' .
					 '<label for="comment">' . _x( 'Comment', 'noun', 'gizmodotech-pro' ) . '</label>' .
					 '<textarea id="comment" name="comment" cols="45" rows="6" maxlength="65525" required="required"></textarea></p>';

	comment_form(
		[
			'comment_field'        => $comment_field,
			'class_form'           => 'comment-form',
			'comment_notes_after'  => '',
			'logged_in_as'         => '<p class="logged-in-as">' . sprintf(
				/* translators: 1: user name, 2: logout url */
				__( 'Logged in as %1$s. <a href="%2$s">Log out?</a>', 'gizmodotech-pro' ),
				wp_get_current_user()->display_name,
				wp_logout_url( apply_filters( 'the_permalink', get_permalink() ) )
			) . '</p>',
		]
	);
	?>

</div>

	</div><!-- /.comments-body -->
</div><!-- /.comments-section -->