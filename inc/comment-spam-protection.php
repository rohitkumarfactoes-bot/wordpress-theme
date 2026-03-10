<?php
/**
 * Comment Spam Protection for Gizmodotech Theme
 * 
 * @package gizmodotech-pro
 */

if (!defined('ABSPATH')) exit;

/* ============================================================
   COMMENT SPAM PROTECTION
   ============================================================ */

/**
 * 1. Honeypot Field for Comment Form
 * Adds a hidden field that bots will fill but humans won't see
 */
add_filter('comment_form_default_fields', 'gizmo_add_comment_honeypot');
function gizmo_add_comment_honeypot($fields) {
    $fields['city'] = '<p class="comment-form-city" style="display:none !important;"><label for="city">City</label><input id="city" name="city" type="text" size="30" autocomplete="off" /></p>';
    return $fields;
}

/**
 * 2. Validate Honeypot Field
 * Reject comments if honeypot field is filled
 */
add_filter('preprocess_comment', 'gizmo_check_comment_honeypot');
function gizmo_check_comment_honeypot($comment_data) {
    if (!empty($_POST['city'])) {
        wp_die('<p>Spam detected! If you are human, please leave the City field blank.</p><p><a href="javascript:history.back()">&laquo; Go back and try again</a></p>', 'Comment Submission Rejected', array('response' => 403));
    }
    return $comment_data;
}

/**
 * 3. Time-based Spam Protection
 * Reject comments submitted too quickly (less than 5 seconds)
 */
add_action('comment_form', 'gizmo_add_comment_timestamp');
function gizmo_add_comment_timestamp() {
    echo '<input type="hidden" name="comment_timestamp" value="' . time() . '" />';
}

add_filter('preprocess_comment', 'gizmo_check_comment_timing');
function gizmo_check_comment_timing($comment_data) {
    if (isset($_POST['comment_timestamp'])) {
        $submission_time = time();
        $form_load_time = intval($_POST['comment_timestamp']);
        $time_diff = $submission_time - $form_load_time;
        
        if ($time_diff < 5) {
            wp_die('<p>Please take more time to compose your comment.</p><p><a href="javascript:history.back()">&laquo; Go back and try again</a></p>', 'Comment Submitted Too Quickly', array('response' => 403));
        }
    }
    return $comment_data;
}

/**
 * 4. Keyword-based Spam Detection
 * Block comments with suspicious keywords
 */
add_filter('preprocess_comment', 'gizmo_check_comment_keywords');
function gizmo_check_comment_keywords($comment_data) {
    $spam_keywords = array('viagra', 'cialis', 'levitra', 'casino', 'poker', 'slots', 'backlink', 'SEO services', 'digital marketing', '[url=', 'href=', '.ru', '.cn', '.tk', 'dating site', 'weight loss', 'make money', 'best porn', 'free sex', 'xxx');
    
    $content = $comment_data['comment_content'];
    $author = $comment_data['comment_author'];
    $email = $comment_data['comment_author_email'];
    $url = $comment_data['comment_author_url'];
    
    $check_text = strtolower($content . ' ' . $author . ' ' . $email . ' ' . $url);
    
    foreach ($spam_keywords as $keyword) {
        if (strpos($check_text, strtolower($keyword)) !== false) {
            wp_die('<p>Your comment contains blocked content.</p><p><a href="javascript:history.back()">&laquo; Go back and revise your comment</a></p>', 'Comment Blocked', array('response' => 403));
        }
    }
    
    return $comment_data;
}

/**
 * 5. Limit Links in Comments
 * Prevent comment spam with too many links
 */
add_filter('preprocess_comment', 'gizmo_limit_comment_links');
function gizmo_limit_comment_links($comment_data) {
    $content = $comment_data['comment_content'];
    $link_count = substr_count(strtolower($content), 'http');
    
    if ($link_count > 2) {
        wp_die('<p>Comments with too many links are not allowed.</p><p><a href="javascript:history.back()">&laquo; Go back and reduce links</a></p>', 'Too Many Links', array('response' => 403));
    }
    
    return $comment_data;
}

/**
 * 6. Require User-Agent Header
 * Most legitimate browsers send User-Agent
 */
add_filter('preprocess_comment', 'gizmo_require_user_agent');
function gizmo_require_user_agent($comment_data) {
    if (empty($_SERVER['HTTP_USER_AGENT'])) {
        wp_die('<p>Automated submissions are not accepted.</p>', 'Submission Rejected', array('response' => 403));
    }
    return $comment_data;
}

/**
 * 7. Moderate First-Time Commenters
 * Hold first comments for moderation
 */
add_filter('pre_comment_approved', 'gizmo_moderate_new_commenters', 10, 2);
function gizmo_moderate_new_commenters($approved, $comment_data) {
    if ($approved === 1) {
        $author_email = $comment_data['comment_author_email'];
        $previous_comments = get_comments(array('author_email' => $author_email, 'status' => 'approve', 'number' => 1));
        if (empty($previous_comments)) {
            return 0;
        }
    }
    return $approved;
}

/**
 * 8. Enhanced Comment Form Security
 */
add_filter('comment_form_defaults', function($defaults) {
    $defaults['comment_notes_before'] = '';
    $defaults['comment_notes_after'] = '<p class="comment-form-privacy">Your comment will be held for moderation if it\'s your first submission.</p>';
    return $defaults;
});