<?php

/**
 * The site brand: the custom logo when one is set, the drawn cookie otherwise.
 * Sizing belongs to the stylesheet, so this takes no arguments.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<a class="cc-brand" href="<?php echo esc_url(home_url('/')); ?>" rel="home">
    <?php if (has_custom_logo()) : ?>
        <?php the_custom_logo(); ?>
    <?php else : ?>
        <svg class="cc-brand__mark" viewBox="0 0 44 44" aria-hidden="true" focusable="false">
            <circle cx="22" cy="22" r="19" class="cc-brand__dough"></circle>
            <circle cx="15" cy="16" r="3.4" class="cc-brand__chip"></circle>
            <circle cx="29" cy="14.5" r="2.6" class="cc-brand__chip"></circle>
            <circle cx="24" cy="27" r="3.6" class="cc-brand__chip"></circle>
            <circle cx="32.5" cy="26" r="2.2" class="cc-brand__chip"></circle>
            <circle cx="14" cy="29" r="2.4" class="cc-brand__chip"></circle>
        </svg>
    <?php endif; ?>

    <span class="cc-brand__wordmark"><?php bloginfo('name'); ?></span>
</a>
