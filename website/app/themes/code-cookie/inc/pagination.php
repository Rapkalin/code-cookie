<?php

/**
 * "La fournée suivante" — archive pagination.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

function code_cookie_pagination(): void
{
    $links = paginate_links([
        'type' => 'list',
        'prev_text' => __('Fournée précédente', 'code-cookie'),
        'next_text' => __('La fournée suivante', 'code-cookie'),
        'before_page_number' => '<span class="screen-reader-text">' . __('Page', 'code-cookie') . '</span> ',
    ]);

    if ($links === null) {
        return;
    }
    ?>
    <nav class="cc-pagination" aria-label="<?php esc_attr_e('Pagination', 'code-cookie'); ?>">
        <?php echo wp_kses_post($links); ?>
    </nav>
    <?php
}
