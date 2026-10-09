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
        <?php if (has_post_thumbnail()) : ?>
            <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'alt' => '']); ?>
        <?php else : ?>
            <span class="cc-cover__fallback" aria-hidden="true">
                <svg viewBox="0 0 320 160" focusable="false">
                    <circle cx="160" cy="80" r="46" class="cc-cover__cookie"></circle>
                    <circle cx="143" cy="66" r="8" class="cc-cover__chip"></circle>
                    <circle cx="176" cy="71" r="6" class="cc-cover__chip"></circle>
                    <circle cx="160" cy="97" r="9" class="cc-cover__chip"></circle>
                    <circle cx="261" cy="46" r="9" class="cc-cover__crumb"></circle>
                    <circle cx="56" cy="112" r="7" class="cc-cover__crumb"></circle>
                </svg>
            </span>
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
