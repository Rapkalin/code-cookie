<?php

/**
 * Post metadata the listings need: the topic term, its flavour, and how long the
 * thing takes to eat.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

const CODE_COOKIE_WORDS_PER_MINUTE = 200;

/**
 * The most specific category of a post: the deepest one, so "Git" wins over
 * "Articles".
 */
function code_cookie_topic(int $postId): ?WP_Term
{
    $terms = get_the_terms($postId, 'category');
    if (!is_array($terms) || $terms === []) {
        return null;
    }

    $best = null;
    foreach ($terms as $term) {
        if ($term->parent !== 0 && ($best === null || $term->count > $best->count)) {
            $best = $term;
        }
    }

    return $best ?? $terms[0];
}

function code_cookie_flavour(int $postId): Flavour
{
    return Flavour::forTerm(code_cookie_topic($postId));
}

/**
 * Reading time in whole minutes, never zero.
 */
function code_cookie_reading_minutes(int $postId): int
{
    $post = get_post($postId);
    if (!$post instanceof WP_Post) {
        return 1;
    }

    // str_word_count() splits accented words ("bibliothèque" counts as two),
    // which inflates French text by about 10%. Split on whitespace instead.
    $text = trim(wp_strip_all_tags(strip_shortcodes($post->post_content)));
    $words = $text === '' ? 0 : count(preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY));

    return max(1, (int) ceil($words / CODE_COOKIE_WORDS_PER_MINUTE));
}

/**
 * "Git · 9 min" — the line under a badge.
 */
function code_cookie_meta_line(int $postId): string
{
    $topic = code_cookie_topic($postId);
    $minutes = code_cookie_reading_minutes($postId);

    $duration = sprintf(
        /* translators: %d: reading time in minutes. */
        _n('%d min', '%d min', $minutes, 'code-cookie'),
        $minutes
    );

    return $topic instanceof WP_Term
        ? $topic->name . ' · ' . $duration
        : $duration;
}
