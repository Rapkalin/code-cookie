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
 * The taxonomy mixes subjects (Git, Backend) with reading levels under the same
 * two parents, so a level would otherwise win the badge on sheer post count.
 */
const CODE_COOKIE_LEVEL_SLUGS = ['debutant', 'intermediaire', 'avance', 'notions-base'];

function code_cookie_is_level(WP_Term $term): bool
{
    foreach (CODE_COOKIE_LEVEL_SLUGS as $slug) {
        if (str_contains($term->slug, $slug)) {
            return true;
        }
    }

    return false;
}

/**
 * The subject of a post: the most specific category that is not a reading level,
 * so "Backend" wins over both "Astuces" and "Débutant".
 */
function code_cookie_topic(int $postId): ?WP_Term
{
    $terms = get_the_terms($postId, 'category');
    if (!is_array($terms) || $terms === []) {
        return null;
    }

    $subjects = array_values(array_filter($terms, static fn (WP_Term $term): bool => !code_cookie_is_level($term)));
    if ($subjects === []) {
        return null;
    }

    $best = null;
    foreach ($subjects as $term) {
        if ($term->parent !== 0 && ($best === null || $term->count > $best->count)) {
            $best = $term;
        }
    }

    return $best ?? $subjects[0];
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
