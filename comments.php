<?php
/**
 * The template for displaying comments
 *
 * @package Gizmodotech
 * @since 1.0.0
 */

if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area" style="margin-top: 60px; padding-top: 40px; border-top: 2px solid var(--border-color);">

	<?php
	if ( have_comments() ) :
		?>
		<h2 class="comments-title">
			<?php
			$gizmodotech_comment_count = get_comments_number();
			if ( '1' === $gizmodotech_comment_count ) {
				printf(
					/* translators: 1: title. */
					esc_html__( 'One thought on &ldquo;%1$s&rdquo;', 'gizmodotech' ),
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			} else {
				printf(
					/* translators: 1: comment count number, 2: title. */
					esc_html( _nx( '%1$s thought on &ldquo;%2$s&rdquo;', '%1$s thoughts on &ldquo;%2$s&rdquo;', $gizmodotech_comment_count, 'comments title', 'gizmodotech' ) ),
					number_format_i18n( $gizmodotech_comment_count ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					'<span>' . wp_kses_post( get_the_title() ) . '</span>'
				);
			}
			?>
		</h2>

		<?php the_comments_navigation(); ?>

		<ol class="comment-list" style="list-style: none; padding: 0; margin: 30px 0;">
			<?php
			wp_list_comments(
				array(
					'style'      => 'ol',
					'short_ping' => true,
					'avatar_size' => 60,
					'callback'   => 'gizmodotech_custom_comment',
				)
			);
			?>
		</ol>

		<?php
		the_comments_navigation();

		if ( ! comments_open() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'gizmodotech' ); ?></p>
			<?php
		endif;

	endif;

	comment_form(
		array(
			'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
			'title_reply_after'  => '</h3>',
			'class_form'         => 'comment-form',
			'comment_field'      => '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Comment', 'gizmodotech' ) . ' <span class="required">*</span></label><textarea id="comment" name="comment" cols="45" rows="8" maxlength="65525" required="required" style="width: 100%; padding: 12px; border: 1px solid var(--border-color); border-radius: var(--radius-md); font-family: inherit; font-size: 15px;"></textarea></p>',
			'submit_button'      => '<button type="submit" class="btn btn-primary">%4$s</button>',
		)
	);
	?>

</div>

<?php
/**
 * Custom comment display callback
 */
function gizmodotech_custom_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( 'comment-item', $comment ); ?> style="margin-bottom: 30px; padding: 20px; background: var(--bg-secondary); border-radius: var(--radius-lg);">
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<footer class="comment-meta" style="display: flex; align-items: center; gap: 15px; margin-bottom: 15px;">
				<div class="comment-author vcard">
					<?php
					if ( 0 != $args['avatar_size'] ) {
						echo get_avatar( $comment, $args['avatar_size'], '', '', array( 'style' => 'border-radius: 50%;' ) );
					}
					?>
				</div>
				<div class="comment-metadata" style="flex: 1;">
					<b class="fn" style="display: block; color: var(--text-primary); font-size: 16px;">
						<?php echo get_comment_author_link( $comment ); ?>
					</b>
					<div class="comment-date" style="font-size: 13px; color: var(--text-tertiary);">
						<a href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>" style="color: var(--text-tertiary);">
							<time datetime="<?php comment_time( 'c' ); ?>">
								<?php
								/* translators: 1: comment date, 2: comment time */
								printf( esc_html__( '%1$s at %2$s', 'gizmodotech' ), get_comment_date( '', $comment ), get_comment_time() );
								?>
							</time>
						</a>
						<?php edit_comment_link( esc_html__( '(Edit)', 'gizmodotech' ), '<span class="edit-link">', '</span>' ); ?>
					</div>
				</div>
			</footer>

			<?php if ( '0' == $comment->comment_approved ) : ?>
				<em class="comment-awaiting-moderation" style="display: block; padding: 10px; background: var(--color-warning); color: white; border-radius: var(--radius-md); margin-bottom: 15px;">
					<?php esc_html_e( 'Your comment is awaiting moderation.', 'gizmodotech' ); ?>
				</em>
			<?php endif; ?>

			<div class="comment-content" style="color: var(--text-secondary); line-height: 1.7;">
				<?php comment_text(); ?>
			</div>

			<?php
			comment_reply_link(
				array_merge(
					$args,
					array(
						'add_below' => 'div-comment',
						'depth'     => $depth,
						'max_depth' => $args['max_depth'],
						'before'    => '<div class="reply" style="margin-top: 15px;">',
						'after'     => '</div>',
					)
				)
			);
			?>
		</article>
	<?php
}
