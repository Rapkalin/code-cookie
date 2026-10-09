<?php

/**
 * The "Tout chaud" hero: the latest post, large, next to the cookie scene.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

$heroId = get_the_ID();
?>
<section class="cc-hero">
    <div class="cc-shell cc-hero__inner">

        <div class="cc-hero__body">
            <p class="cc-hero__flag">
                <svg width="15" height="15" viewBox="0 0 16 16" aria-hidden="true" focusable="false">
                    <path d="M8 1.5c1.6 2 .4 3.2 0 4.4-.5 1.5.6 2.3 1.4 1.5.9-.8.4-2 .4-2 2 1.4 2.7 3.3 2.7 4.6a4.5 4.5 0 1 1-9 0c0-3 2.4-4.6 4.5-8.5Z" fill="currentColor"></path>
                </svg>
                <?php esc_html_e('Tout chaud', 'code-cookie'); ?>
            </p>

            <h1 class="cc-hero__title">
                <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
            </h1>

            <p class="cc-hero__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>

            <p class="cc-hero__actions">
                <a class="cc-button" href="<?php the_permalink(); ?>">
                    <?php esc_html_e('Croquer dedans', 'code-cookie'); ?>
                </a>
                <span class="cc-hero__byline">
                    <?php echo get_avatar(get_the_author_meta('ID'), 34, '', '', ['class' => 'cc-hero__avatar']); ?>
                    <?php
                    printf(
                        /* translators: %1$s: author name, %2$s: "Git · 9 min". */
                        esc_html__('%1$s · %2$s', 'code-cookie'),
                        esc_html(get_the_author()),
                        esc_html(code_cookie_meta_line($heroId))
                    );
                    ?>
                </span>
            </p>
        </div>

        <div class="cc-hero__art">
            <?php get_template_part('components/cookie-scene'); ?>
        </div>

    </div>
</section>
