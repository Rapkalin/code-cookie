<?php

/**
 * "Les miettes" — the breadcrumb trail.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

function code_cookie_crumbs(): void
{
    if (is_front_page()) {
        return;
    }

    $trail = [['label' => __('Accueil', 'code-cookie'), 'url' => home_url('/')]];

    if (is_singular('post')) {
        $kind = CookieKind::forPost(get_the_ID());
        $trail[] = ['label' => $kind->label(), 'url' => ''];
        $trail[] = ['label' => get_the_title(), 'url' => ''];
    } elseif (is_category() || is_tag()) {
        $trail[] = ['label' => single_term_title('', false), 'url' => ''];
    } elseif (is_search()) {
        $trail[] = ['label' => __('Recherche', 'code-cookie'), 'url' => ''];
    } elseif (is_404()) {
        $trail[] = ['label' => __('Cookie expiré', 'code-cookie'), 'url' => ''];
    } else {
        $trail[] = ['label' => get_the_title(), 'url' => ''];
    }

    $last = array_key_last($trail);
    ?>
    <nav class="cc-crumbs" aria-label="<?php esc_attr_e('Les miettes', 'code-cookie'); ?>">
        <svg width="14" height="14" viewBox="0 0 14 14" aria-hidden="true" focusable="false">
            <circle cx="3" cy="7" r="1.6" fill="currentColor"></circle>
            <circle cx="7.5" cy="5" r="1.1" fill="currentColor"></circle>
            <circle cx="11" cy="8.5" r="1.4" fill="currentColor"></circle>
        </svg>
        <?php foreach ($trail as $index => $crumb) : ?>
            <?php if ($crumb['url'] !== '' && $index !== $last) : ?>
                <a href="<?php echo esc_url($crumb['url']); ?>"><?php echo esc_html($crumb['label']); ?></a>
            <?php else : ?>
                <span class="cc-crumbs__here"><?php echo esc_html($crumb['label']); ?></span>
            <?php endif; ?>
            <?php if ($index !== $last) : ?>
                <span aria-hidden="true">·</span>
            <?php endif; ?>
        <?php endforeach; ?>
    </nav>
    <?php
}
