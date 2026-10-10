<?php

/**
 * Homepage helpers.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * The post chosen for the "Tout chaud" slot, or 0 to fall back to the latest.
 *
 * A post that has since been unpublished or deleted returns 0 too: the hero
 * must never be empty because someone moved an article to draft.
 */
function code_cookie_featured_post_id(): int
{
    $postId = code_cookie_option_id('home_featured_post');
    if ($postId === 0) {
        return 0;
    }

    $post = get_post($postId);

    return ($post instanceof WP_Post && $post->post_type === 'post' && $post->post_status === 'publish')
        ? $postId
        : 0;
}
