<?php

/**
 * Plugin Name: Code Cookie — local mail sender
 * Description: Gives outgoing mail a deliverable From address outside production.
 *
 * WordPress builds its default sender from the site host: on a local stack that
 * host is "localhost", so the address is wordpress@localhost, which PHPMailer
 * rejects as invalid. Every wp_mail() call then returns false before the mail
 * ever reaches the SMTP relay — a failure that looks like a broken feature.
 *
 * Production is left untouched: the site has a real domain there, and the
 * sender must stay whatever the site was configured with.
 */

if (! defined('ABSPATH')) {
    exit;
}

if (defined('WP_ENV') && WP_ENV === 'production') {
    return;
}

add_filter('wp_mail_from', static function (string $from): string {
    return $from === 'wordpress@localhost' ? 'no-reply@code-cookie.local' : $from;
});
