<?php

/**
 * The row of category pills above a listing.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

$filters = code_cookie_filters();
if ($filters === []) {
    return;
}
?>
<nav class="cc-filters" aria-label="<?php esc_attr_e('Filtrer par catégorie', 'code-cookie'); ?>">
    <?php foreach ($filters as $filter) : ?>
        <a class="cc-filter <?php echo esc_attr($filter['flavour']->modifier('cc-filter')); ?>"
           href="<?php echo esc_url($filter['url']); ?>"
            <?php echo $filter['isCurrent'] ? ' aria-current="page"' : ''; ?>>
            <?php echo esc_html($filter['label']); ?>
        </a>
    <?php endforeach; ?>
</nav>
