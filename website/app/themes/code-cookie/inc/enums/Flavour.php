<?php

/**
 * The four accent colours. Each topic gets one, so a listing reads as a shelf of
 * flavours rather than a wall of identical cards.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

enum Flavour: string
{
    case Caramel = 'caramel';
    case Orange = 'orange';
    case Earth = 'earth';
    case Mint = 'mint';

    /**
     * Topic keyword => flavour. Matched against the term slug, so both branches
     * of the duplicated taxonomy (articles-git / astuces-git) land on the same
     * colour.
     */
    private const KEYWORDS = [
        'git' => self::Caramel,
        'github' => self::Caramel,
        'basiques' => self::Caramel,
        'notions' => self::Caramel,
        'backend' => self::Orange,
        'php' => self::Orange,
        'cms' => self::Orange,
        'framework' => self::Orange,
        'ide' => self::Orange,
        'cache' => self::Mint,
        'devops' => self::Mint,
        'securite' => self::Mint,
        'frontend' => self::Mint,
        'ecoresponsable' => self::Earth,
        'vie-de-dev' => self::Earth,
        'actualites' => self::Earth,
        'interviews' => self::Earth,
        'forum' => self::Earth,
    ];

    /** CSS modifier appended to a .cc-badge or .cc-cover class. */
    public function modifier(): string
    {
        return match ($this) {
            self::Caramel => '',
            self::Orange => ' cc-badge--orange',
            self::Earth => ' cc-badge--earth',
            self::Mint => ' cc-badge--mint',
        };
    }

    public function coverModifier(): string
    {
        return match ($this) {
            self::Caramel => '',
            self::Orange => ' cc-cover--orange',
            self::Earth => ' cc-cover--earth',
            self::Mint => ' cc-cover--mint',
        };
    }

    public static function forTerm(?WP_Term $term): self
    {
        if (!$term instanceof WP_Term) {
            return self::Caramel;
        }

        foreach (self::KEYWORDS as $keyword => $flavour) {
            if (str_contains($term->slug, $keyword)) {
                return $flavour;
            }
        }

        // Stable fallback: the same term always gets the same colour.
        return [self::Caramel, self::Orange, self::Earth, self::Mint][$term->term_id % 4];
    }
}
