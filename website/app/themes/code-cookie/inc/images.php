<?php

/**
 * Featured image helpers.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

add_filter('image_editor_output_format', 'code_cookie_webp_sub_sizes');

/**
 * Resized copies of uploaded JPEGs are written as WebP.
 *
 * @param array<string, string> $formats
 * @return array<string, string>
 */
function code_cookie_webp_sub_sizes(array $formats): array
{
    $formats['image/jpeg'] = 'image/webp';

    return $formats;
}

/**
 * Whether a post has a featured image that will actually render.
 *
 * has_post_thumbnail() only checks the meta row. A media file deleted from disk
 * leaves that row behind, so the card would emit an <img> pointing at a 404 and
 * collapse the layout — which is exactly what happened to the lost illustration
 * of "Astuce PHP // Traiter correctement la validation des booléens".
 */
function code_cookie_has_usable_thumbnail(int $postId): bool
{
    $thumbnailId = (int) get_post_thumbnail_id($postId);
    if ($thumbnailId === 0) {
        return false;
    }

    $file = get_attached_file($thumbnailId);

    return is_string($file) && $file !== '' && file_exists($file);
}
