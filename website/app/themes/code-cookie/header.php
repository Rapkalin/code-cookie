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
        <?php get_template_part('components/brand'); ?>

        <nav class="cc-header__nav" aria-label="<?php echo esc_attr(MenuLocation::Primary->label()); ?>">
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
