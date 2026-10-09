<?php

/**
 * "Ce cookie a expiré." — the 404 page.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="cc-shell cc-prose">
    <?php code_cookie_crumbs(); ?>

    <p><code>404 — expired</code></p>
    <h1><?php esc_html_e('Ce cookie a expiré.', 'code-cookie'); ?></h1>
    <p>
        <?php esc_html_e('Quelqu’un l’a mangé, ou la date de péremption est passée. Dans les deux cas il n’en reste que des miettes.', 'code-cookie'); ?>
    </p>

    <p>
        <a class="cc-badge" href="<?php echo esc_url(home_url('/')); ?>">
            <?php esc_html_e('Retourner au bocal', 'code-cookie'); ?>
        </a>
    </p>

    <?php get_search_form(); ?>
</div>

<?php
get_footer();
