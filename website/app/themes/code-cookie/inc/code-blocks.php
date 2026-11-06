<?php

/**
 * Code block markup clean-up.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('render_block_core/code', 'code_cookie_drop_code_lang');

/**
 * Saved code blocks carry lang="php", "bash"…: `lang` expects a human language
 * tag, so assistive tech would try to read the code as "php". The language
 * class that drives highlighting is left alone.
 */
function code_cookie_drop_code_lang(string $content): string
{
    $processor = new WP_HTML_Tag_Processor($content);

    while ($processor->next_tag('code')) {
        $processor->remove_attribute('lang');
    }

    return $processor->get_updated_html();
}
