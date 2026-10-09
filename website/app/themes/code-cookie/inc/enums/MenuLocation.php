<?php

/**
 * Menu locations registered by the theme. Add a location = add a case.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

enum MenuLocation: string
{
    case Primary = 'cc-primary';
    case Footer = 'cc-footer';

    public function label(): string
    {
        return match ($this) {
            self::Primary => __('Navigation principale', 'code-cookie'),
            self::Footer => __('Pied de page (colonnes)', 'code-cookie'),
        };
    }

    /** @return array<string, string> */
    public static function registry(): array
    {
        $locations = [];
        foreach (self::cases() as $case) {
            $locations[$case->value] = $case->label();
        }

        return $locations;
    }
}
