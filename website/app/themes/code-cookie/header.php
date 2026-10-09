<?php if (!defined('ABSPATH')) { exit; } ?>
<!doctype html>
<html <?php language_attributes(); ?> data-scheme="dark">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="cc-skip-link" href="#cc-main"><?php esc_html_e('Aller au contenu', 'code-cookie'); ?></a>

<header class="cc-header">
    <div class="cc-shell cc-header__inner">
        <a class="cc-header__brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
            <svg width="44" height="44" viewBox="0 0 44 44" aria-hidden="true" focusable="false">
                <circle cx="22" cy="22" r="19" fill="#FBB04D"></circle>
                <circle cx="15" cy="16" r="3.4" fill="currentColor"></circle>
                <circle cx="29" cy="14.5" r="2.6" fill="currentColor"></circle>
                <circle cx="24" cy="27" r="3.6" fill="currentColor"></circle>
                <circle cx="32.5" cy="26" r="2.2" fill="currentColor"></circle>
                <circle cx="14" cy="29" r="2.4" fill="currentColor"></circle>
            </svg>
            <span class="cc-header__wordmark"><?php bloginfo('name'); ?></span>
        </a>

        <nav class="cc-header__nav" aria-label="<?php esc_attr_e('Navigation principale', 'code-cookie'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => MenuLocation::Primary->value,
                'container' => false,
                'depth' => 1,
                'fallback_cb' => false,
            ]);
            get_search_form();
            code_cookie_scheme_toggle();
            ?>
        </nav>
    </div>
</header>

<main id="cc-main">
