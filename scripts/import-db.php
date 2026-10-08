<?php

require __DIR__ . '/../website/vendor/autoload.php';

use Dotenv\Dotenv;

// Only allowed for cli
if (PHP_SAPI !== 'cli') {
    die('Not allowed');
}

$start = microtime(true);

// Load .env data
// The .env lives in the docroot on the server (a symlink to shared/.env) and at
// the repository root in local development.
$envDir = file_exists(__DIR__ . '/../website/.env') ? __DIR__ . '/../website' : __DIR__ . '/..';
$dotenv = Dotenv::createUnsafeImmutable($envDir, '.env');
$dotenv->safeLoad();

$filename = 'export-' . date('Ymd_his') . '.sql';

// Import db in prod server
exec(
    'ssh ' . getenv('PROD_USER') . '@' . getenv('PROD_HOST') .
    ' "mysqldump --host=' . getenv('DATABASE_PROD_HOST') . ' --user=' . getenv('DATABASE_PROD_USER') .
    ' --password=' . getenv('DATABASE_PROD_PASSWORD') . ' --single-transaction --routines --no-tablespaces ' .
    getenv('DATABASE_PROD_NAME') . '>' . $filename . '"'
);

echo "...SQL file available on remote server. \r\n";

// Copy this file on current machine
exec('scp ' . getenv('PROD_USER') . '@' . getenv('PROD_HOST') . ':' . $filename . ' exports');

if (file_exists('exports/' . $filename)) {
    echo "...SQL file copied locally.\r\n";

    $pdo = new PDO(
        'mysql:host=' . getenv('DATABASE_HOST'),
        getenv('DATABASE_USER'),
        getenv('DATABASE_PASSWORD'),
        [PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES UTF8']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

    // Regenerate database
    $pdo->exec('DROP DATABASE IF EXISTS `' . getenv('DATABASE_NAME') . '`;');
    $pdo->exec('CREATE DATABASE `' . getenv('DATABASE_NAME') . '`;');
    $pdo->exec('USE `' . getenv('DATABASE_NAME') . '`;');

    echo "...database created.\r\n";

    // Load sql file in local database
    $sql = file_get_contents('exports/' . $filename);
    $pdo->exec($sql);

    // Update some values in tables to work on localhost
    try {
        $pdo->beginTransaction();

        $stmt = $pdo->query('SELECT * FROM wp_options WHERE option_name="active_plugins"');
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $active_plugins = unserialize($result["option_value"]);
        unset($active_plugins[array_search("jetpack/jetpack.php", $active_plugins)]);
        unset($active_plugins[array_search("w3-total-cache/w3-total-cache.php", $active_plugins)]);
        $active_plugins = array_values($active_plugins);
        $serialized_array = serialize($active_plugins);

        $stmt = $pdo->prepare('UPDATE wp_options SET option_value = replace(option_value, ?, ?) WHERE option_name = "active_plugins";');
        $stmt->execute([$result["option_value"], $serialized_array]);


        $stmt = $pdo->prepare('UPDATE wp_posts SET guid = replace(guid, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_posts SET post_content = replace(post_content, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_links SET link_url = replace(link_url, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_links SET link_image = replace(link_image, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_postmeta SET meta_value = replace(meta_value, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_usermeta SET meta_value = replace(meta_value, ?, ?);');
        $stmt->execute([getenv('PROD_SITEURL'), getenv('WP_SITEURL')]);

        $stmt = $pdo->prepare('UPDATE wp_options SET option_value = replace(option_value, ?, ?) WHERE option_name = "home" OR option_name = "siteurl";');
        $stmt->execute([rtrim(getenv('PROD_SITEURL'), '/'), rtrim(getenv('WP_SITEURL'), '/')]);

        $pdo->commit();

        echo "...database updated.\r\n";
    } catch (Exception $e) {
        if (isset($pdo)) {
            $pdo->rollBack();
            echo "Error: " . $e->getMessage();
        }
    }
} else {
    echo "Error while communicating with remote server.\r\n";
}

// Delete the file on prod server
exec('ssh ' . getenv('PROD_USER') . '@' . getenv('PROD_HOST') . ' "rm ' . $filename . '"');

echo "...SQL file removed on remote server.\r\n";

echo 'execution time ' . round(microtime(true) - $start, 2) . ' seconds.';
