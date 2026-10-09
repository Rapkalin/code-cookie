<?php

/**
 * Footer rendering helpers.
 *
 * The link columns come from the footer menu: a top-level item is a column
 * heading, its children are the links. Navigation stays in WordPress menus;
 * ACF carries the prose.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<int, array{title: string, flavour: Flavour, links: array<int, array{label: string, url: string}>}>
 */
function code_cookie_footer_columns(): array
{
    $location = MenuLocation::Footer->value;
    if (!has_nav_menu($location)) {
        return [];
    }

    $assigned = get_nav_menu_locations()[$location] ?? 0;
    $menu = wp_get_nav_menu_object($assigned);
    if (!$menu instanceof WP_Term) {
        return [];
    }

    $items = wp_get_nav_menu_items($menu->term_id);
    if (!is_array($items)) {
        return [];
    }

    $columns = [];
    foreach ($items as $item) {
        if ((int) $item->menu_item_parent === 0) {
            $columns[(int) $item->ID] = ['title' => $item->title, 'links' => []];
        }
    }

    foreach ($items as $item) {
        $parent = (int) $item->menu_item_parent;
        if ($parent !== 0 && isset($columns[$parent])) {
            $columns[$parent]['links'][] = ['label' => $item->title, 'url' => $item->url];
        }
    }

    // The colour is assigned here, not by a CSS :nth-child that also counts the
    // brand block sitting in the same row.
    // Order taken from the mockup: the first column is caramel, the second mint.
    $palette = [Flavour::Caramel, Flavour::Mint, Flavour::Earth, Flavour::Orange];
    $ordered = [];
    foreach (array_values($columns) as $index => $column) {
        $column['flavour'] = $palette[$index % count($palette)];
        $ordered[] = $column;
    }

    return $ordered;
}

/**
 * "© 2026 Code Cookie — mitonné par…" : the year and the site name stay
 * automatic so the line cannot go stale; only the credit is contributed.
 */
function code_cookie_footer_legal(): string
{
    $credit = code_cookie_option(
        'footer_credit',
        __('mitonné par Rapkalin et Noweh.', 'code-cookie')
    );

    return sprintf(
        /* translators: %1$s: year, %2$s: site name, %3$s: contributed credit line. */
        __('© %1$s %2$s — %3$s', 'code-cookie'),
        date_i18n('Y'),
        get_bloginfo('name'),
        $credit
    );
}
