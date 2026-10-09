<?php if (!defined('ABSPATH')) { exit; } ?>
<form role="search" method="get" class="cc-search" action="<?php echo esc_url(home_url('/')); ?>">
    <label class="screen-reader-text" for="cc-search-field">
        <?php esc_html_e('Chercher une miette', 'code-cookie'); ?>
    </label>
    <input id="cc-search-field" type="search" name="s"
           value="<?php echo esc_attr(get_search_query()); ?>"
           placeholder="<?php esc_attr_e('Une miette ?', 'code-cookie'); ?>">
</form>
