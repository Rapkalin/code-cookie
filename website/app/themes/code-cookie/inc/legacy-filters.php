<?php

/**
 * Query rules carried over from the previous theme.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('pre_get_posts', 'code_cookie_hide_tags_from_home');

/**
 * Keep a set of tags out of the home listing. Carried over from
 * newsmatic-child's custom_filter_posts_by_tags().
 */
function code_cookie_hide_tags_from_home(WP_Query $query): void
{
    if (is_admin() || !$query->is_main_query() || !$query->is_home()) {
        return;
    }

    $slugs = apply_filters('code_cookie_tags_hidden_from_home', ['tags-forum-php']);
    $termIds = [];

    foreach ($slugs as $slug) {
        $term = get_term_by('slug', $slug, 'post_tag');
        if ($term instanceof WP_Term) {
            $termIds[] = $term->term_id;
        }
    }

    if ($termIds !== []) {
        $query->set('tag__not_in', $termIds);
    }
}
