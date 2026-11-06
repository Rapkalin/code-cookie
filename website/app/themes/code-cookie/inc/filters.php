<?php

/**
 * Category filter pills.
 *
 * The taxonomy mirrors every subject under both families, so "Backend", "CMS"
 * and "Git" each exist twice. A flat row would show them twice over; the row is
 * therefore contextual — the blog index offers the families, a family archive
 * offers its own subjects.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('get_the_archive_title_prefix', '__return_empty_string');

/**
 * @return array<int, array{label: string, url: string, isCurrent: bool, flavour: Flavour}>
 */
function code_cookie_filters(): array
{
    $current = is_category() ? get_queried_object() : null;
    $current = $current instanceof WP_Term ? $current : null;

    $family = null;
    if ($current !== null) {
        $family = $current->parent !== 0 ? get_term($current->parent, 'category') : $current;
        $family = $family instanceof WP_Term ? $family : null;
    }

    if ($family !== null) {
        $head = [
            'label' => $family->name,
            'url' => (string) get_term_link($family),
            'isCurrent' => $current->term_id === $family->term_id,
            'flavour' => Flavour::forTerm($family),
        ];
        $parentId = $family->term_id;
    } else {
        $blogId = (int) get_option('page_for_posts');
        $head = [
            'label' => __('Tout', 'code-cookie'),
            'url' => $blogId !== 0 ? (string) get_permalink($blogId) : home_url('/'),
            'isCurrent' => is_home(),
            'flavour' => Flavour::Caramel,
        ];
        $parentId = 0;
    }

    $terms = get_terms([
        'taxonomy' => 'category',
        'parent' => $parentId,
        'hide_empty' => true,
    ]);

    if (!is_array($terms)) {
        return [];
    }

    $pills = [$head];
    foreach ($terms as $term) {
        if (code_cookie_is_level($term)) {
            continue;
        }

        $pills[] = [
            'label' => $term->name,
            'url' => (string) get_term_link($term),
            'isCurrent' => $current !== null && $current->term_id === $term->term_id,
            'flavour' => Flavour::forTerm($term),
        ];
    }

    // A single pill filters nothing.
    return count($pills) > 1 ? $pills : [];
}
