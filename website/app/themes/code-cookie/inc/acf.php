<?php

/**
 * ACF Pro integration.
 *
 * Registers the theme settings page. Field groups live as Local JSON in
 * `acf-json/`, versioned and still editable from the ACF screen.
 *
 * Every read goes through code_cookie_option(): it is the one place that knows
 * ACF might be missing, so templates never guard for it themselves.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

const CODE_COOKIE_OPTIONS_SLUG = 'code-cookie-settings';

add_action('acf/init', 'code_cookie_register_options_page');

function code_cookie_register_options_page(): void
{
    if (!function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title' => __('Réglages Code Cookie', 'code-cookie'),
        'menu_title' => __('Réglages Code Cookie', 'code-cookie'),
        'parent_slug' => 'options-general.php',
        'menu_slug' => CODE_COOKIE_OPTIONS_SLUG,
        'capability' => 'manage_options',
    ]);
}

/**
 * Read a theme option, falling back when ACF is absent or the field is empty.
 */
function code_cookie_option(string $name, string $fallback = ''): string
{
    if (!function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field($name, 'option');

    return is_string($value) && trim($value) !== '' ? $value : $fallback;
}
