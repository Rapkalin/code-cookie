<?php

/**
 * Menu locations registered by the theme.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

enum MenuLocation: string
{
    case Primary = 'cc-primary';

    public function label(): string
    {
        return match ($this) {
            self::Primary => __('Navigation principale', 'code-cookie'),
        };
    }
}
