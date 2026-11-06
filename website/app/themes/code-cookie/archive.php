<?php

/**
 * Category, tag and date archives.
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

    <h1 class="cc-section__title"><?php echo wp_kses_post(get_the_archive_title()); ?></h1>
    <?php the_archive_description('<p class="cc-card__excerpt">', '</p>'); ?>

    <?php get_template_part('components/category-filters'); ?>

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
        <p><?php esc_html_e('Le four est encore froid : rien dans cette catégorie.', 'code-cookie'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
