<?php

/**
 * A static page.
 *
 * @package code-cookie
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) :
    the_post();
    ?>
    <div class="cc-shell">
        <?php code_cookie_crumbs(); ?>
    </div>

    <article <?php post_class('cc-shell cc-prose'); ?>>
        <h1><?php the_title(); ?></h1>
        <div class="entry-content">
            <?php the_content(); ?>
        </div>
    </article>
    <?php
endwhile;

get_footer();
