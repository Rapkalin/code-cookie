<?php

/**
 * The two editorial families of the blog, named after the two kinds of web
 * cookie: a lasting article outlives the session, a quick tip does not.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

enum CookieKind: string
{
    case Persistent = 'articles';
    case Session = 'astuces';

    public function label(): string
    {
        return match ($this) {
            self::Persistent => __('Les persistants', 'code-cookie'),
            self::Session => __('Les sessions', 'code-cookie'),
        };
    }

    public function tagline(): string
    {
        return match ($this) {
            self::Persistent => __('Les articles de fond. Ils restent après la fermeture de l’onglet.', 'code-cookie'),
            self::Session => __('Les astuces. Lues en deux minutes, croquées tant que c’est chaud.', 'code-cookie'),
        };
    }

    public function badgeModifier(): string
    {
        return match ($this) {
            self::Persistent => '',
            self::Session => ' cc-badge--mint',
        };
    }

    /**
     * Resolve the family a post belongs to from its category slugs. The previous
     * taxonomy mirrors every topic under both branches (articles-git /
     * astuces-git), so the prefix is what carries the family.
     */
    public static function forPost(int $postId): self
    {
        $terms = get_the_terms($postId, 'category');
        if (!is_array($terms)) {
            return self::Persistent;
        }

        foreach ($terms as $term) {
            if (str_starts_with($term->slug, 'astuce')) {
                return self::Session;
            }
        }

        return self::Persistent;
    }
}
