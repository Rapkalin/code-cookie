<?php

/**
 * One post card.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

$kind = CookieKind::forPost(get_the_ID());
$category = get_the_category();
$categoryName = $category === [] ? '' : $category[0]->name;
?>
<article <?php post_class('cc-card'); ?>>
    <?php if (has_post_thumbnail()) : ?>
        <a class="cc-card__cover" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
            <?php the_post_thumbnail('medium_large', ['loading' => 'lazy', 'alt' => '']); ?>
        </a>
    <?php endif; ?>

    <div class="cc-card__body">
        <span class="cc-badge<?php echo esc_attr($kind->badgeModifier()); ?>">
            <?php echo esc_html($categoryName !== '' ? $categoryName : $kind->label()); ?>
        </span>

        <h3 class="cc-card__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <p class="cc-card__excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
    </div>
</article>
