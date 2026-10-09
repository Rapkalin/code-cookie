<?php

/**
 * The homepage: hero, fresh batch, the two jars, quick tips, the newsletter band.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$hero = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 1,
    'ignore_sticky_posts' => true,
]);

$heroId = $hero->have_posts() ? $hero->posts[0]->ID : 0;

if ($hero->have_posts()) {
    while ($hero->have_posts()) {
        $hero->the_post();
        get_template_part('components/hero');
    }
    wp_reset_postdata();
}

$fresh = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 3,
    'post__not_in' => array_filter([$heroId]),
    'ignore_sticky_posts' => true,
]);
?>

<?php if ($fresh->have_posts()) : ?>
    <section class="cc-shell cc-section">
        <div class="cc-section__head">
            <h2 class="cc-section__title"><?php esc_html_e('Sorti du four', 'code-cookie'); ?></h2>
            <a class="cc-pill cc-pill--link" href="<?php echo esc_url(get_permalink(get_option('page_for_posts')) ?: home_url('/')); ?>">
                <?php esc_html_e('Tout le bocal →', 'code-cookie'); ?>
            </a>
        </div>

        <div class="cc-grid">
            <?php
            while ($fresh->have_posts()) {
                $fresh->the_post();
                get_template_part('components/card');
            }
            wp_reset_postdata();
            ?>
        </div>
    </section>
<?php endif; ?>

<?php get_template_part('components/jars'); ?>

<?php
$sessions = new WP_Query([
    'post_type' => 'post',
    'posts_per_page' => 4,
    'category_name' => CookieKind::Session->value,
    'ignore_sticky_posts' => true,
]);
?>

<?php if ($sessions->have_posts()) : ?>
    <section class="cc-shell cc-section">
        <h2 class="cc-section__title"><?php esc_html_e('À grignoter en deux minutes', 'code-cookie'); ?></h2>

        <div class="cc-sessions">
            <?php
            while ($sessions->have_posts()) {
                $sessions->the_post();
                get_template_part('components/session-item');
            }
            wp_reset_postdata();
            ?>
        </div>

        <p class="cc-section__more">
            <a class="cc-button cc-button--ghost" href="<?php echo esc_url(get_category_link(get_category_by_slug(CookieKind::Session->value))); ?>">
                <?php esc_html_e('La fournée suivante', 'code-cookie'); ?>
            </a>
        </p>
    </section>
<?php endif; ?>

<?php
get_template_part('components/newsletter');

get_footer();
