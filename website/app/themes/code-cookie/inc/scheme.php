<?php

/**
 * Light/dark switch.
 *
 * The preference is read and applied in the browser, never in PHP: a page served
 * from the full-page cache carries the default markup, and this script corrects
 * it before paint. Setting the attribute server-side would bake one visitor's
 * choice into the cached copy for everyone.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

const CODE_COOKIE_SCHEME_COOKIE = 'cc_scheme';

add_action('wp_head', 'code_cookie_scheme_boot', 1);

function code_cookie_scheme_boot(): void
{
    ?>
    <script>
    (function () {
        try {
            var match = document.cookie.match(/(?:^|;\s*)<?php echo esc_js(CODE_COOKIE_SCHEME_COOKIE); ?>=(light|dark)/);
            if (match) {
                document.documentElement.setAttribute('data-scheme', match[1]);
            }
        } catch (error) {}
    })();
    </script>
    <?php
}

function code_cookie_scheme_toggle(): void
{
    ?>
    <button type="button" class="cc-scheme-toggle" data-cc-scheme-toggle
            aria-label="<?php esc_attr_e('Changer de mode d’affichage', 'code-cookie'); ?>">
        <svg width="26" height="26" viewBox="0 0 44 44" aria-hidden="true" focusable="false">
            <circle cx="22" cy="22" r="19" fill="currentColor"></circle>
            <circle class="cc-bite" cx="38" cy="11" r="10"></circle>
            <circle class="cc-bite" cx="15" cy="16" r="3.4"></circle>
            <circle class="cc-bite" cx="24" cy="27" r="3.6"></circle>
            <circle class="cc-bite" cx="14" cy="29" r="2.4"></circle>
        </svg>
    </button>
    <?php
}
