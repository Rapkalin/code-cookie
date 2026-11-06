<?php

/**
 * The posts page: every article, with the family filters.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>

<div class="cc-shell cc-section">
    <h1 class="cc-section__title"><?php echo esc_html(get_the_title(get_option('page_for_posts'))); ?></h1>

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
        <p><?php esc_html_e('Le four est encore froid.', 'code-cookie'); ?></p>
    <?php endif; ?>
</div>

<?php
get_footer();
