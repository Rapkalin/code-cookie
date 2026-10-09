<?php

/**
 * Front-end hardening and weight trimming.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'rsd_link');

add_action('init', 'code_cookie_trim_head');
add_filter('should_load_separate_core_block_assets', '__return_true');
add_filter('xmlrpc_enabled', '__return_false');
add_filter('pre_ping', 'code_cookie_drop_self_pings');

function code_cookie_trim_head(): void
{
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
}

/**
 * Drop pingbacks the site would send to itself.
 */
function code_cookie_drop_self_pings(array &$links): void
{
    $home = get_option('home');

    foreach ($links as $index => $link) {
        if (str_starts_with($link, $home)) {
            unset($links[$index]);
        }
    }
}
