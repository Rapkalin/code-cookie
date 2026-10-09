<?php

/**
 * Brand icon, served by the theme.
 *
 * The media library only holds the previous brand's files, so the icon is a
 * theme asset rather than a site_icon attachment.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_head', 'code_cookie_favicon_tags');
add_action('admin_head', 'code_cookie_favicon_tags');

function code_cookie_favicon_tags(): void
{
    if ((int) get_option('site_icon') !== 0) {
        return;
    }

    printf(
        '<link rel="icon" href="%s" type="image/svg+xml">' . "\n",
        esc_url(get_template_directory_uri() . '/assets/icons/cookie.svg')
    );
}
