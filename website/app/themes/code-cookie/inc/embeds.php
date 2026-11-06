<?php

/**
 * Third-party video embeds rendered as plain links.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('render_block_core/embed', 'code_cookie_embed_as_link', 10, 2);

/**
 * A YouTube iframe drops tracking cookies before any click, which would need a
 * consent banner. A link sends the reader to YouTube only when they ask for it.
 *
 * @param array<string, mixed> $block
 */
function code_cookie_embed_as_link(string $content, array $block): string
{
    $provider = $block['attrs']['providerNameSlug'] ?? '';
    $url = $block['attrs']['url'] ?? '';

    if ($provider !== 'youtube' || $url === '') {
        return $content;
    }

    $processor = new WP_HTML_Tag_Processor($content);
    $title = $processor->next_tag('iframe') ? (string) $processor->get_attribute('title') : '';

    $label = $title !== ''
        /* translators: %s: video or playlist title. */
        ? sprintf(__('Regarder « %s » sur YouTube', 'code-cookie'), $title)
        : __('Regarder la vidéo sur YouTube', 'code-cookie');

    return sprintf(
        '<p class="cc-embed-link"><a class="cc-button" href="%s" target="_blank" rel="noopener">%s</a></p>',
        esc_url($url),
        esc_html($label)
    );
}
