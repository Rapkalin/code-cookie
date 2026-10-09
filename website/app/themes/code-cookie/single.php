<?php

/**
 * A single post.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    $kind = CookieKind::forPost(get_the_ID());
    ?>

    <div class="cc-shell">
        <?php code_cookie_crumbs(); ?>
    </div>

    <article <?php post_class('cc-shell cc-prose'); ?>>
        <header>
            <p>
                <span class="cc-badge<?php echo esc_attr($kind->badgeModifier()); ?>">
                    <?php echo esc_html($kind->label()); ?>
                </span>
            </p>
            <h1><?php the_title(); ?></h1>
            <p class="cc-card__excerpt">
                <?php
                printf(
                    /* translators: %1$s: author name, %2$s: publication date. */
                    esc_html__('Par %1$s · %2$s', 'code-cookie'),
                    esc_html(get_the_author()),
                    esc_html(get_the_date())
                );
                ?>
            </p>
        </header>

        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('large'); ?>
        <?php endif; ?>

        <div class="entry-content">
            <?php the_content(); ?>
        </div>

        <footer>
            <?php the_tags('<p class="cc-card__excerpt">' . esc_html__('Pépites :', 'code-cookie') . ' ', ', ', '</p>'); ?>
        </footer>
    </article>

    <div class="cc-shell cc-prose">
        <?php
        if (comments_open() || get_comments_number()) {
            comments_template();
        }
        ?>
    </div>

    <?php
endwhile;

get_footer();
