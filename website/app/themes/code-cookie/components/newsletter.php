<?php

/**
 * "Reçois la fournée" — the full-width caramel band.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}
?>
<section class="cc-batch">
    <div class="cc-shell cc-batch__inner">
        <div class="cc-batch__pitch">
            <span class="cc-batch__icon" aria-hidden="true">
                <svg viewBox="0 0 70 70" focusable="false">
                    <rect x="6" y="20" width="58" height="40" rx="9" class="cc-batch__envelope"></rect>
                    <path d="M6 26l29 20 29-20" fill="none" class="cc-batch__flap" stroke-width="5" stroke-linejoin="round"></path>
                    <circle cx="56" cy="16" r="10" class="cc-batch__stamp"></circle>
                </svg>
            </span>
            <div>
                <h2 class="cc-batch__title"><?php esc_html_e('Reçois la fournée', 'code-cookie'); ?></h2>
                <p class="cc-batch__text">
                    <?php esc_html_e('Un courriel quand ça sort du four. Rien d’autre, promis.', 'code-cookie'); ?>
                </p>
            </div>
        </div>

        <form class="cc-batch__form" method="post" action="#">
            <?php wp_nonce_field('code_cookie_batch', 'code_cookie_batch_nonce'); ?>
            <label class="screen-reader-text" for="cc-batch-mail">
                <?php esc_html_e('Votre adresse de courriel', 'code-cookie'); ?>
            </label>
            <input id="cc-batch-mail" type="email" name="cc_batch_mail" required
                   placeholder="<?php esc_attr_e('vous@exemple.fr', 'code-cookie'); ?>">
            <button type="submit"><?php esc_html_e('Je m’abonne', 'code-cookie'); ?></button>
        </form>
    </div>
</section>
