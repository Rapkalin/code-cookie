<?php if (!defined('ABSPATH')) { exit; } ?>
</main>

<footer class="cc-footer">
    <div class="cc-shell cc-footer__inner">
        <div class="cc-footer__col" style="flex: 1 1 300px">
            <h2><?php bloginfo('name'); ?></h2>
            <p><?php bloginfo('description'); ?></p>
        </div>

        <div class="cc-footer__col">
            <h2><?php esc_html_e('Le bocal', 'code-cookie'); ?></h2>
            <?php
            wp_nav_menu([
                'theme_location' => MenuLocation::Primary->value,
                'container' => false,
                'depth' => 1,
                'fallback_cb' => false,
            ]);
            ?>
        </div>
    </div>

    <div class="cc-footer__legal">
        <div class="cc-shell">
            <p>
                <?php
                printf(
                    /* translators: %1$s: current year, %2$s: site name. */
                    esc_html__('© %1$s %2$s — mitonné par Rapkalin et Noweh.', 'code-cookie'),
                    esc_html(date_i18n('Y')),
                    esc_html(get_bloginfo('name'))
                );
                ?>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
