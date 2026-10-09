<?php

/**
 * One quick tip in the "À grignoter" list: a round time badge and a title.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

$itemId = get_the_ID();
$flavour = code_cookie_flavour($itemId);
$topic = code_cookie_topic($itemId);
?>
<article class="cc-session">
    <span class="cc-session__time cc-badge <?php echo esc_attr($flavour->modifier('cc-badge')); ?>">
        <?php echo esc_html(code_cookie_reading_minutes($itemId)); ?>'
    </span>
    <div class="cc-session__body">
        <h3 class="cc-session__title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>
        <?php if ($topic instanceof WP_Term) : ?>
            <p class="cc-session__topic"><?php echo esc_html($topic->name); ?></p>
        <?php endif; ?>
    </div>
</article>
