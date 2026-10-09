<?php if (!defined('ABSPATH')) { exit; } ?>
</main>

<footer class="cc-footer">
    <div class="cc-shell cc-footer__inner">

        <div class="cc-footer__brand">
            <?php get_template_part('components/brand'); ?>
            <p class="cc-footer__tagline">
                <?php
                echo esc_html(code_cookie_option(
                    'footer_tagline',
                    get_bloginfo('description')
                ));
                ?>
            </p>
        </div>

        <?php foreach (code_cookie_footer_columns() as $column) : ?>
            <div class="cc-footer__col <?php echo esc_attr($column['flavour']->modifier('cc-footer__col')); ?>">
                <h2 class="cc-footer__col-title"><?php echo esc_html($column['title']); ?></h2>
                <ul class="cc-footer__links">
                    <?php foreach ($column['links'] as $link) : ?>
                        <li><a href="<?php echo esc_url($link['url']); ?>"><?php echo esc_html($link['label']); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endforeach; ?>

    </div>

    <div class="cc-footer__legal">
        <div class="cc-shell">
            <p>
                <?php echo esc_html(code_cookie_footer_legal()); ?>
                <?php code_cookie_version_tag(); ?>
            </p>
        </div>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
