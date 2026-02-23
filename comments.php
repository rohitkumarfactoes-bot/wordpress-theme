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

// Get the number of comments. The $comment_count variable was previously undefined, causing a fatal error.
$comment_count = get_comments_number();
?>
<!-- ============================================================
		     COMMENTS SECTION WITH TOGGLE
		     ============================================================ -->
		<?php if ( comments_open() || get_comments_number() ) : ?>
		<div class="comments-section" id="comments-section">

			<!-- Toggle Button -->
			<button class="comments-toggle"
			        type="button"
			        aria-expanded="false"
			        aria-controls="comments-body"
			        id="comments-toggle-btn">
				<div class="comments-toggle__left">
					<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
					<?php
					printf(
						/* translators: %s is the number of comments */
						_n( '%s Comment', '%s Comments', $comment_count, 'gizmodotech-pro' ),
						'<span class="comments-toggle__count">' . number_format_i18n( $comment_count ) . '</span>'
					);
					?>
				</div>
				<svg class="comments-toggle__icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><polyline points="6 9 12 15 18 9"/></svg>
			</button>

			<!-- Comments Body (hidden until toggled) -->
			<div class="comments-body" id="comments-body" aria-live="polite">
				<div class="comment-form-area">
					<?php
					comment_form( [
						'title_reply'          => __( 'Leave a Comment', 'gizmodotech-pro' ),
						'title_reply_to'       => __( 'Reply to %s', 'gizmodotech-pro' ),
						'cancel_reply_link'    => __( 'Cancel Reply', 'gizmodotech-pro' ),
						'label_submit'         => __( 'Post Comment', 'gizmodotech-pro' ),
						'comment_notes_before' => '',
						'class_submit'         => 'submit',
					] );
					?>
				</div>

				<?php if ( have_comments() ) : ?>
				<div class="comments-list" style="margin-top: var(--space-6);">
					<h3 style="font-size:var(--font-size-lg);font-weight:700;margin-bottom:var(--space-5);">
						<?php
						comments_number(
							__( 'No comments yet', 'gizmodotech-pro' ),
							__( '1 Comment', 'gizmodotech-pro' ),
							/* translators: %s: number of comments */
							__( '% Comments', 'gizmodotech-pro' )
						);
						?>
					</h3>
					<ol class="comment-list" style="list-style:none;">
						<?php
						wp_list_comments( [
							'style'       => 'ol',
							'short_ping'  => true,
							'avatar_size' => 80,
							'callback'    => 'gizmo_comment_callback', // Use the standard callback from functions.php
						] );
						?>
					</ol>
					<?php the_comments_pagination(); ?>
				</div>
				<?php endif; ?>

				<?php if ( ! have_comments() && comments_open() ) : ?>
					<p style="text-align:center; color:var(--text-muted); margin-top:1.5rem;">
						<?php esc_html_e('No comments yet. Be the first to share your thoughts!', 'gizmodotech-pro'); ?>
					</p>
				<?php endif; ?>

			</div><!-- /.comments-body -->

		</div><!-- /.comments-section -->
		<?php endif; ?>