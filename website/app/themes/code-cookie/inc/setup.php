<?php

/**
 * Theme supports, translations and menu locations.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', 'code_cookie_setup');

function code_cookie_setup(): void
{
    load_theme_textdomain('code-cookie', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('automatic-feed-links');
    add_theme_support('custom-logo', [
        'height' => 60,
        'width' => 240,
        'flex-height' => true,
        'flex-width' => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');

    register_nav_menus([
        MenuLocation::Primary->value => MenuLocation::Primary->label(),
    ]);
}
