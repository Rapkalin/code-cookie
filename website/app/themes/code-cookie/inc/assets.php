<?php

/**
 * Front-end asset loading.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', 'code_cookie_enqueue_assets');

function code_cookie_enqueue_assets(): void
{
    // Cache busting follows the release, not a second number kept in the theme
    // header: composer.json is the only place the version is written.
    $version = code_cookie_version() ?: null;

    wp_enqueue_style('code-cookie', get_stylesheet_uri(), [], $version);

    wp_enqueue_script(
        'code-cookie-scheme',
        get_template_directory_uri() . '/assets/js/scheme.js',
        [],
        $version,
        true
    );

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}
