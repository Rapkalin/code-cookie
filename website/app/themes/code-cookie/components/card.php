<?php

/**
 * One post card: illustrated cover, flavour badge, title, excerpt.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

$cardId = get_the_ID();
$flavour = code_cookie_flavour($cardId);
?>
<article <?php post_class('cc-card'); ?>>
    <a class="cc-card__cover <?php echo esc_attr($flavour->modifier('cc-card__cover')); ?>"
       href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
        <?php if (code_cookie_has_usable_thumbnail($cardId)) : ?>
            <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'alt' => '']); ?>
        <?php else : ?>
            <?php get_template_part('components/cover-fallback'); ?>
        <?php endif; ?>
    </a>

    <div class="cc-card__body">
        <span class="cc-badge <?php echo esc_attr($flavour->modifier('cc-badge')); ?>">
            <?php echo esc_html(code_cookie_meta_line($cardId)); ?>
        </span>

        <h3 class="cc-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="cc-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
    </div>
</article>
