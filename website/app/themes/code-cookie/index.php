<?php

/**
 * Fallback template: home listing and any archive without a closer match.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="cc-shell">
    <?php code_cookie_crumbs(); ?>

    <?php if (have_posts()) : ?>
        <div class="cc-grid">
            <?php
            while (have_posts()) {
                the_post();
                get_template_part('components/card');
            }
            ?>
        </div>

        <?php code_cookie_pagination(); ?>
    <?php else : ?>
        <h1><?php esc_html_e('Le four est encore froid.', 'code-cookie'); ?></h1>
        <p><?php esc_html_e('Aucun article par ici pour l’instant. Ça ne saurait tarder.', 'code-cookie'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
