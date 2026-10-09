<?php

/**
 * Release version, shown in the footer on every environment.
 *
 * The source of truth is the `version` field of composer.json, which sits above
 * the docroot: PHP reads it, the web server never serves it. `bin/release.sh`
 * keeps it in step with the theme header and the Git tag.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Environments where the environment name is not worth advertising.
 */
const CODE_COOKIE_SILENT_ENVIRONMENTS = ['production', 'prod'];

function code_cookie_version(): string
{
    static $version = null;

    if ($version !== null) {
        return $version;
    }

    $version = '';
    $path = dirname(ABSPATH, 2) . '/composer.json';

    if (is_readable($path)) {
        $data = json_decode((string) file_get_contents($path), true);
        if (is_array($data) && isset($data['version']) && is_string($data['version'])) {
            $version = $data['version'];
        }
    }

    return $version;
}

/**
 * The environment name, or an empty string on production where it says nothing
 * a visitor needs.
 */
function code_cookie_environment(): string
{
    $environment = defined('WP_ENV') ? (string) WP_ENV : '';

    return in_array($environment, CODE_COOKIE_SILENT_ENVIRONMENTS, true) ? '' : $environment;
}

function code_cookie_version_tag(): void
{
    $version = code_cookie_version();
    if ($version === '') {
        return;
    }

    $environment = code_cookie_environment();
    ?>
    <span class="cc-version">
        <span class="cc-version__number">v<?php echo esc_html($version); ?></span>
        <?php if ($environment !== '') : ?>
            <span class="cc-version__env"><?php echo esc_html($environment); ?></span>
        <?php endif; ?>
    </span>
    <?php
}
