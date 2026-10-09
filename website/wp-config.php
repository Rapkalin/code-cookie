<?php

/**
 * WordPress configuration.
 *
 * Every value comes from the .env file: nothing environment-specific and no
 * secret belongs in this file, which is versioned.
 *
 * This file must live next to the docroot: WordPress only looks in its own
 * directory or one directory up.
 */

define('DIR_VENDOR', __DIR__ . '/vendor/');

if (! file_exists(DIR_VENDOR . 'autoload.php')) {
    exit('Composer dependencies are missing. Run: docker compose exec php composer install');
}

require_once DIR_VENDOR . 'autoload.php';

/**
 * The .env lives in the docroot on the server (website/.env, a symlink to
 * shared/.env, which survives deployments) and at the repository root in local
 * development — there is no shared/ on a dev machine.
 */
$env_dir = file_exists(__DIR__ . '/.env') ? __DIR__ : dirname(__DIR__);

$dotenv = Dotenv\Dotenv::createImmutable($env_dir);
$dotenv->load();
$dotenv->required(['WP_HOME', 'DATABASE_NAME', 'DATABASE_USER', 'DATABASE_PASSWORD']);

/**
 * Reads an optional variable, since required() already guards the mandatory
 * ones. Empty strings count as absent: a variable left blank in the .env means
 * "not set", never "set to nothing".
 */
function code_cookie_env(string $name, mixed $default = null): mixed
{
    $value = $_ENV[$name] ?? null;

    if ($value === null || $value === '') {
        return $default;
    }

    return match (strtolower((string) $value)) {
        'true' => true,
        'false' => false,
        default => $value,
    };
}

/**
 * URLs. A single value in the .env drives all three: a trailing slash left in
 * WP_HOME would otherwise produce double slashes in every asset URL.
 */
define('WP_HOME', rtrim((string) code_cookie_env('WP_HOME'), '/'));
define('WP_SITEURL', WP_HOME . '/wordpress-core');

/**
 * Custom content directory (replaces wp-content).
 */
define('WP_CONTENT_DIR', __DIR__ . '/app');
define('WP_CONTENT_URL', WP_HOME . '/app');

/**
 * Database.
 */
if (code_cookie_env('DATABASE_SSL') === true) {
    define('MYSQL_CLIENT_FLAGS', MYSQLI_CLIENT_SSL);
}

define('DB_NAME', code_cookie_env('DATABASE_NAME'));
define('DB_USER', code_cookie_env('DATABASE_USER'));
define('DB_PASSWORD', code_cookie_env('DATABASE_PASSWORD'));
define('DB_HOST', code_cookie_env('DATABASE_HOST', 'localhost'));
define('DB_CHARSET', 'utf8mb4');
define('DB_COLLATE', '');

$table_prefix = (string) code_cookie_env('DATABASE_PREFIX', 'wp_');

/**
 * Authentication keys and salts: one set per environment, never committed.
 * bin/init.sh generates them on first start; in production, generate a set at
 * https://roots.io/salts.html
 */
foreach ([
    'AUTH_KEY',
    'SECURE_AUTH_KEY',
    'LOGGED_IN_KEY',
    'NONCE_KEY',
    'AUTH_SALT',
    'SECURE_AUTH_SALT',
    'LOGGED_IN_SALT',
    'NONCE_SALT',
] as $salt) {
    define($salt, (string) code_cookie_env($salt, 'put-a-real-salt-in-the-env'));
}

/**
 * Active theme of record. The database is what WordPress actually obeys; this
 * constant is the fallback it falls back to, and the value bin/init.sh
 * reconciles an imported dump against — the production dump still names a theme
 * that no longer ships with this repository.
 */
define('WP_DEFAULT_THEME', 'code-cookie');

/**
 * Environment.
 */
define('WP_ENV', (string) code_cookie_env('WP_ENV', 'production'));
define('WP_ENVIRONMENT_TYPE', WP_ENV === 'development' ? 'local' : WP_ENV);

/**
 * Full-page cache (W3 Total Cache). The advanced-cache.php drop-in is only
 * loaded when this constant is true, which is what keeps the plugin out of the
 * way in local development.
 */

define('DISABLE_WP_CRON', code_cookie_env('DISABLE_WP_CRON', false) === true);

/**
 * Hardening. Updates go through Composer and Git, never through the admin: a
 * compromised account must not be able to write PHP to the server.
 *
 * DISALLOW_FILE_MODS is deliberately NOT set: W3 Total Cache writes its own
 * drop-ins and its w3tc-config directory at activation, and would fail
 * silently without write access.
 */
define('DISALLOW_FILE_EDIT', true);
define('AUTOMATIC_UPDATER_DISABLED', true);

/**
 * Debugging. Errors are never displayed: a stack trace tells an attacker the
 * absolute paths of the server.
 */
define('WP_DEBUG', WP_ENV !== 'production' && code_cookie_env('WP_DEBUG', false) === true);
define('WP_DEBUG_DISPLAY', false);
define('WP_DEBUG_LOG', WP_DEBUG);
define('SCRIPT_DEBUG', false);
ini_set('display_errors', '0');

/**
 * Lets WordPress detect HTTPS behind a reverse proxy or load balancer.
 *
 * @see https://developer.wordpress.org/reference/functions/is_ssl/
 */
if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
    $_SERVER['HTTPS'] = 'on';
}

if (! defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/wordpress-core/');
}

require_once ABSPATH . 'wp-settings.php';
