<?php

/**
 * Comments. Kept: the blog has an open comment thread on every post.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

if (post_password_required()) {
    return;
}
?>

<section id="comments" class="cc-comments">
    <?php if (have_comments()) : ?>
        <h2>
            <?php
            printf(
                /* translators: %s: comment count. */
                esc_html(_n('%s miette laissée', '%s miettes laissées', get_comments_number(), 'code-cookie')),
                esc_html(number_format_i18n(get_comments_number()))
            );
            ?>
        </h2>

        <ol class="comment-list">
            <?php
            wp_list_comments([
                'style' => 'ol',
                'short_ping' => true,
                'avatar_size' => 48,
            ]);
            ?>
        </ol>

        <?php the_comments_pagination(); ?>
    <?php endif; ?>

    <?php
    comment_form([
        'title_reply' => __('Laisser une miette', 'code-cookie'),
        'class_submit' => 'cc-badge',
    ]);
    ?>
</section>
