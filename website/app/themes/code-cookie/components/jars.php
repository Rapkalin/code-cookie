<?php

/**
 * The two jars: lasting articles on one side, quick tips on the other.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="cc-shell cc-jars">
    <?php foreach (CookieKind::cases() as $kind) : ?>
        <?php
        $term = get_category_by_slug($kind->value);
        if (!$term instanceof WP_Term) {
            continue;
        }
        $isSession = $kind === CookieKind::Session;
        ?>
        <div class="cc-jar <?php echo esc_attr($kind->flavour()->modifier('cc-jar')); ?>">
            <span class="cc-jar__icon" aria-hidden="true">
                <?php if ($isSession) : ?>
                    <svg viewBox="0 0 76 76" focusable="false">
                        <circle cx="38" cy="40" r="26" class="cc-jar__fill"></circle>
                        <circle cx="62" cy="20" r="13" class="cc-jar__hole"></circle>
                        <circle cx="30" cy="34" r="4.5" class="cc-jar__hole"></circle>
                        <circle cx="42" cy="50" r="5" class="cc-jar__hole"></circle>
                        <circle cx="28" cy="50" r="3" class="cc-jar__hole"></circle>
                    </svg>
                <?php else : ?>
                    <svg viewBox="0 0 76 76" focusable="false">
                        <rect x="14" y="20" width="48" height="46" rx="12" class="cc-jar__fill"></rect>
                        <rect x="24" y="10" width="28" height="12" rx="6" class="cc-jar__fill"></rect>
                        <circle cx="29" cy="38" r="5" class="cc-jar__hole"></circle>
                        <circle cx="45" cy="34" r="4" class="cc-jar__hole"></circle>
                        <circle cx="38" cy="52" r="5.5" class="cc-jar__hole"></circle>
                        <circle cx="50" cy="51" r="3.5" class="cc-jar__hole"></circle>
                    </svg>
                <?php endif; ?>
            </span>

            <div class="cc-jar__body">
                <h2 class="cc-jar__title"><?php echo esc_html($kind->label()); ?></h2>
                <p class="cc-jar__tagline"><?php echo esc_html($kind->tagline()); ?></p>
                <p class="cc-jar__actions">
                    <span class="cc-pill">
                        <?php
                        printf(
                            /* translators: %d: number of posts in the jar. */
                            esc_html(_n('%d article', '%d articles', $term->count, 'code-cookie')),
                            (int) $term->count
                        );
                        ?>
                    </span>
                    <a class="cc-button cc-button--small" href="<?php echo esc_url(get_category_link($term)); ?>">
                        <?php esc_html_e('Ouvrir le bocal', 'code-cookie'); ?>
                    </a>
                </p>
            </div>
        </div>
    <?php endforeach; ?>
</section>
