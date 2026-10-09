<?php

/**
 * Code Cookie — theme entry point.
 *
 * HYBRID theme: structure and components in classic PHP, design constrained by
 * theme.json. This file only includes the `inc/` thematic files: to extend the
 * theme, add an `inc/xxx.php` file and its require_once below.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/enums/CookieKind.php';   // Editorial families: lasting articles vs quick tips.
require_once get_template_directory() . '/inc/enums/Flavour.php';      // The four accent colours, one per topic.
require_once get_template_directory() . '/inc/enums/MenuLocation.php'; // Menu locations the theme registers.
require_once get_template_directory() . '/inc/assets.php';             // Front-end stylesheet and script loading.
require_once get_template_directory() . '/inc/breadcrumb.php';         // "Les miettes" — the breadcrumb trail.
require_once get_template_directory() . '/inc/legacy-filters.php';     // Query rules carried over from the previous theme.
require_once get_template_directory() . '/inc/pagination.php';           // "La fournée suivante" — archive pagination.
require_once get_template_directory() . '/inc/post-meta.php';          // Topic term, flavour and reading time.
require_once get_template_directory() . '/inc/scheme.php';             // Light/dark switch, applied client-side so caching survives.
require_once get_template_directory() . '/inc/security.php';           // Front-end hardening.
require_once get_template_directory() . '/inc/setup.php';              // Supports, translations, menu locations.
