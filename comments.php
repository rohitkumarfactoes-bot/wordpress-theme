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
					'avatar_size' => 48,
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