<?php

/**
 * Names of the two editorial families.
 *
 * The enum carries the defaults; the settings page may override them. The
 * override is resolved here so CookieKind stays unaware of ACF, and so the
 * breadcrumb, the single post and the jars all say the same word.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

function code_cookie_kind_label(CookieKind $kind): string
{
    $field = match ($kind) {
        CookieKind::Persistent => 'home_persistent_label',
        CookieKind::Session => 'home_session_label',
    };

    return code_cookie_option($field, $kind->label());
}

function code_cookie_kind_tagline(CookieKind $kind): string
{
    $field = match ($kind) {
        CookieKind::Persistent => 'home_persistent_tagline',
        CookieKind::Session => 'home_session_tagline',
    };

    return code_cookie_option($field, $kind->tagline());
}
