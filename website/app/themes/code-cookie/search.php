<?php

/**
 * Search results.
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
        <h1>
            <?php
            printf(
                /* translators: %s: the search query. */
                esc_html__('Miettes trouvées pour « %s »', 'code-cookie'),
                esc_html(get_search_query())
            );
            ?>
        </h1>

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
        <h1><?php esc_html_e('Pas une miette.', 'code-cookie'); ?></h1>
        <p>
            <?php
            printf(
                /* translators: %s: the search query. */
                esc_html__('Rien ne correspond à « %s ». Essayez un mot plus court.', 'code-cookie'),
                esc_html(get_search_query())
            );
            ?>
        </p>
        <?php get_search_form(); ?>
    <?php endif; ?>
</div>

<?php
get_footer();
