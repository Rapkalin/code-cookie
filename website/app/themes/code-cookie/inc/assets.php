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
    $version = code_cookie_asset_version();

    wp_enqueue_style('code-cookie', get_stylesheet_uri(), [], $version);

    wp_enqueue_script(
        'code-cookie-scheme',
        get_template_directory_uri() . '/assets/js/scheme.js',
        [],
        $version,
        true
    );

    if (is_singular() && has_block('core/code')) {
        wp_enqueue_script(
            'code-cookie-prism',
            get_template_directory_uri() . '/assets/js/prism.js',
            [],
            $version,
            ['in_footer' => true, 'strategy' => 'defer']
        );
    }

    if (is_singular() && comments_open() && get_option('thread_comments')) {
        wp_enqueue_script('comment-reply');
    }
}

/**
 * Cache-busting token.
 *
 * In production it is the release: composer.json is the only place the version
 * is written, and the stylesheet changes only when the release does. Elsewhere
 * the release number stands still between two builds, so the browser would
 * serve a stale stylesheet for the whole sprint — the file's own timestamp is
 * what moves.
 */
function code_cookie_asset_version(): string
{
    if (!code_cookie_is_production()) {
        $stylesheet = get_template_directory() . '/style.css';
        $modified = file_exists($stylesheet) ? filemtime($stylesheet) : false;

        if ($modified !== false) {
            return (string) $modified;
        }
    }

    return code_cookie_version();
}
