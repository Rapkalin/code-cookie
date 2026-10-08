<?php

use Newsmatic\CustomizerDefault as ND;

/**
 * Custom template tags for this child theme.
 */

require get_stylesheet_directory() . '/inc/template-tags-child.php';
require_once get_stylesheet_directory() . '/options/banner-event.php';

add_action ( 'newsmatic_before_main_content', 'newsmatic_child_author_hook');

/* Load styles and scripts */
add_action( 'wp_enqueue_scripts', 'newsmatic_enqueue_styles' );
add_action( 'wp_enqueue_scripts', 'newsmatic_child_register_style', 11 );

/* Load internationalisation / translations */
add_action( 'after_setup_theme', 'newsmatic_child_theme_locale' );

/* Retrieve mods from parent to child theme */
add_action( 'after_switch_theme', 'set_newsmatic_child_theme_mods' );

/* Remove self pings */
add_action( 'pre_ping', 'no_self_ping' );

/* Override header for 404 */
add_action( 'newsmatic_header__menu_section_hook', 'newsmatic_child_header_menu_404_hook', 11 );

/* Override header */
add_action( 'newsmatic_header__site_branding_section_hook', 'newsmatic_child_header_branding_hook', 4 );

/* Override bottom footer */
add_action( 'newsmatic_botttom_footer_hook', 'newsmatic_child_bottom_footer_hook', 11 );

/* Update parent theme */
add_action( 'newsmatic_child_404_header__menu_section_hook', 'newsmatic_header_menu_part' );
add_action( 'newsmatic_child_404_header_icon__menu_section_hook', 'newsmatic_header_theme_mode_icon_part' );
add_action( 'newsmatic_child_404_header__section_hook', 'newsmatic_header_search_part' );

add_action( 'init', 'newsmatic_child_init_hook' );

add_filter( 'site_icon_meta_tags', 'newsmatic_child_custom_favicon', 10, 1 );

/* Performance optimization */
add_filter( 'should_load_separate_core_block_assets', '__return_true' );
add_action( 'wp_enqueue_scripts', 'wpdocs_dequeue_libraries' );
add_filter( 'jetpack_sharing_counts', '__return_false', 99 );
add_filter( 'jetpack_implode_frontend_css', '__return_false', 99 );
add_action( 'wp_default_scripts', 'remove_jquery_migrate' );

/* Override post queries */
add_action( 'pre_get_posts', 'custom_filter_posts_by_tags' );

/**
 * Filter unwanted tags on selected pages
 * @param $query
 * @return void
 */
function custom_filter_posts_by_tags($query): void
{
    $excludedTags = ['tags-forum-php'];

    $tagIds = [];
    foreach ($excludedTags as $excludedTag) {
        $tag = get_term_by('slug', $excludedTag, 'post_tag');
        if ($tag) {
            $tagIds[] = $tag->term_id;
        }
    }

    // if tag is not empty and is main query and is not admin and is home
    if (!empty($tagIds) && $query->is_main_query() && !is_admin() && is_home()) {
        $query->set('tag__not_in', $tagIds);
    }
}


/**
 * @return void
 */
function wpdocs_dequeue_libraries(): void
{
    // unregister style if user not connected (bacause adminbar uses dashicons
    if (!is_user_logged_in()) {
        wp_deregister_style('dashicons');
    }
    wp_deregister_style('classic-theme-styles');
    wp_deregister_script('wp-mediaelement');
    wp_deregister_style('wp-mediaelement');
}

/**
 * @param $scripts
 *
 * @return void
 */
function remove_jquery_migrate($scripts): void
{
    if (!is_admin() && isset($scripts->registered['jquery'])) {
        $script = $scripts->registered['jquery'];

        if ($script->deps) {
            $script->deps = array_diff($script->deps, ['jquery-migrate', 'jquery-waypoint']);
        }
    }
}

/**
 * @param $meta_tags
 *
 * @return array
 */
function newsmatic_child_custom_favicon($meta_tags): array
{
    // using the next filter we can decide which elements of the array of meta tags want to treat.
    $lines_to_change = apply_filters('lines_of_favicon_metas_to_change', [0, 1]);

    foreach ($lines_to_change as $i) {
        // extracting the URL of the image
        preg_match_all('/href=\"([^\"]*)\"{1}/', $meta_tags[$i], $matches);

        // get the mime image type and creating "image/type" or whatever associated with its extension
        $type = get_favicon_image_extension_type_attribute($matches[1][0]);

        // Introducing type="image/type" the HTML code
        $meta_tags[$i] = str_replace('"icon" href', '"icon" type="' . $type . '" href', $meta_tags[$i]);
    }

    return $meta_tags;
}

/**
 * @param $t
 *
 * @return string
 */
// For expanding and simplify the code we create a separate function for treating the URL of the images
function get_favicon_image_extension_type_attribute($t): string
{
    $base = trim(strtok(pathinfo($t, PATHINFO_EXTENSION), '?'));
    $type = match ($base) {
        'ico' => 'image/x-icon',
        'jpg', 'jpeg' => 'image/jpeg',
        default => 'image/' . $base,
    };
    return apply_filters('get_favicon_image_extension_type_attribute', $type, $t, $base);

}

// Override header title to remove the separator
add_filter( 'pre_get_document_title', 'newsmatic_child_title', 999, 1 );

/**
 * @param $title
 *
 * @return string
 */
function newsmatic_child_title($title): string
{
    return rtrim($title, " -");
}

/**
 * @return void
 */
function newsmatic_child_init_hook(): void
{
    // Dark mode as default
    if (!isset($_COOKIE['themeMode'])) {
        setcookie('themeMode', 'dark', 0, "/");
        $_COOKIE['themeMode'] = 'dark';
    }
    wp_deregister_script('heartbeat');
    # Update pagination button
    if (function_exists('newsmatic_pagination_fnc')) {
        remove_action('newsmatic_pagination_link_hook', 'newsmatic_pagination_fnc');
        add_action('newsmatic_pagination_link_hook', 'newsmatic_child_pagination_fnc');
    }

}

/**
 * Add the parent theme style
 *
 * @return void
 */
function newsmatic_enqueue_styles(): void
{
    $parenthandle = 'newsmatic-style'; // This is 'twentyfifteen-style' for the Twenty Fifteen theme.
    $theme = wp_get_theme();
    wp_enqueue_style( $parenthandle, get_template_directory_uri() . '/style.css',
        [],  // if the parent theme code has a dependency, copy it to here
        $theme->parent()->get('Version')
    );
}

/**
 * Register and add child theme style
 *
 * @return void
 */
function newsmatic_child_register_style(): void
{
    wp_register_style( 'newsmatic-child-style', get_stylesheet_uri());
    wp_enqueue_style( 'newsmatic-child-style');

    wp_register_style( 'newsmatic-child-style-author', get_stylesheet_directory_uri() . '/styles/author.css' );
    wp_enqueue_style( 'newsmatic-child-style-author');

    wp_register_style( 'newsmatic-child-style-articles', get_stylesheet_directory_uri() . '/styles/articles.css' );
    wp_enqueue_style( 'newsmatic-child-style-articles');

    wp_register_style( 'newsmatic-child-style-footer', get_stylesheet_directory_uri() . '/styles/footer.css' );
    wp_enqueue_style( 'newsmatic-child-style-footer');

    wp_register_style( 'newsmatic-child-style-header', get_stylesheet_directory_uri() . '/styles/header.css' );
    wp_enqueue_style( 'newsmatic-child-style-header');

    wp_register_style( 'newsmatic-child-style-homepage', get_stylesheet_directory_uri() . '/styles/homepage.css' );
    wp_enqueue_style( 'newsmatic-child-style-homepage');

    wp_register_style( 'newsmatic-child-style-main-child', get_stylesheet_directory_uri() . '/styles/main-child.css' );
    wp_enqueue_style( 'newsmatic-child-style-main-child');

    wp_register_style( 'newsmatic-child-style-404', get_stylesheet_directory_uri() . '/styles/404.css' );
    wp_enqueue_style( 'newsmatic-child-style-404');

    wp_register_style( 'newsmatic-child-style-search', get_stylesheet_directory_uri() . '/styles/search.css' );
    wp_enqueue_style( 'newsmatic-child-style-search');

    wp_register_style( 'newsmatic-child-style-theme-options', get_stylesheet_directory_uri() . '/styles/customizer-controls.css');
    wp_enqueue_style( 'newsmatic-child-style-theme-options');

    wp_dequeue_script('newsmatic-theme');
    wp_enqueue_script('newsmatic-child-theme', get_stylesheet_directory_uri() . '/assets/js/theme.js', ['jquery'], NEWSMATIC_VERSION, true );

    $scriptVars['_wpnonce'] = wp_create_nonce( 'newsmatic-nonce' );
    $scriptVars['ajaxUrl'] 	= admin_url('admin-ajax.php');
    $scriptVars['uploadsFolder'] = wp_get_upload_dir()['baseurl'];
    $scriptVars['customLogoDark'] = get_custom_logo();
    $scriptVars['customLogoLight'] = sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
        esc_url(get_home_url()),
        wp_get_attachment_image(1720, 'full', false, [
            'class'    => 'custom-logo',
        ])
    );
    $scriptVars['stt']	= ND\newsmatic_get_multiselect_tab_option('stt_responsive_option');
    $scriptVars['stickey_header']= ND\newsmatic_get_customizer_option('theme_header_sticky');
    $scriptVars['livesearch']= ND\newsmatic_get_customizer_option('theme_header_live_search_option');
    // newsmatic scripts
    wp_localize_script( 'newsmatic-child-theme', 'newsmaticObject' , $scriptVars);
}

/**
 * Retrieve parent theme mods and set them for child theme
 *
 * @return void
 */
function set_newsmatic_child_theme_mods(): void
{
    $parent_theme = get_template_directory();
    $theme_parent_slug = basename($parent_theme);
    $theme_parent_mods = get_option( "theme_mods_{$theme_parent_slug}");
    $theme_child_mods = get_theme_mods();

    if (!array_key_exists('newsmatic_site_logo_width', $theme_child_mods) && !empty($theme_parent_mods) && is_array($theme_parent_mods)) {
        foreach ($theme_parent_mods as $key => $mod) {
            set_theme_mod($key, $mod);
        }
    }
}

/**
 * Remove parent functions to override them
 *
 * @return void
 */
function newsmatic_child_author_hook(): void
{
    /* Update author template */
    if (function_exists('newsmatic_category_archive_author_html')) {
        remove_action( 'newsmatic_before_main_content', 'newsmatic_category_archive_author_html', 20 );
        add_action( 'newsmatic_before_main_content', 'newsmatic_child_category_archive_author_html', 20 );
    }
}

/**
 * @return void
 */
function newsmatic_child_header_menu_404_hook(): void
{
    # Remove native menu for 404
    if(is_404() && function_exists('newsmatic_header_menu_part')) {
        remove_action( 'newsmatic_header__menu_section_hook', 'newsmatic_header_search_part', 50 );
        remove_action( 'newsmatic_header__menu_section_hook', 'newsmatic_header_menu_part', 40 );
        remove_action( 'newsmatic_header__menu_section_hook', 'newsmatic_header_theme_mode_icon_part', 60 );
    }
}

/**
 * @return void
 */
function newsmatic_child_header_branding_hook(): void
{
    # Remove social media part to center logo properly
    if (function_exists('newsmatic_top_header_social_part')) {
        remove_action('newsmatic_header__site_branding_section_hook', 'newsmatic_top_header_social_part', 5);
    }

    # Update search and theme buttons
    if (function_exists('newsmatic_header_search_part')) {
        remove_action('newsmatic_header__menu_section_hook', 'newsmatic_header_search_part', 50);
        add_action('newsmatic_header__menu_section_hook', 'newsmatic_child_header_search_part', 50);
    }

    if (function_exists('newsmatic_header_theme_mode_icon_part')) {
        remove_action('newsmatic_header__menu_section_hook', 'newsmatic_header_theme_mode_icon_part', 60);
        add_action('newsmatic_header__menu_section_hook', 'newsmatic_child_header_theme_mode_icon_part', 60);
    }

    if (function_exists('newsmatic_header_theme_mode_icon_part')) {
        remove_action('newsmatic_header__site_branding_section_hook', 'newsmatic_header_site_branding_part', 10);
        add_action('newsmatic_header__site_branding_section_hook', 'newsmatic_child_header_site_branding_part', 10);
    }
}

/**
 * @return void
 */
function newsmatic_child_bottom_footer_hook(): void
{
    # Remove the newsmatic's theme footer function to overide it
    if (function_exists('newsmatic_bottom_footer_copyright_part')) {
        remove_action('newsmatic_botttom_footer_hook', 'newsmatic_bottom_footer_copyright_part', 20);
        add_action('newsmatic_botttom_footer_hook', 'newsmatic_child_bottom_footer_copyright_part', 20);
    }
}

/**
 * Add details on search button in header
 * @return void
 */
function newsmatic_child_header_search_part(): void
{
    if(!ND\newsmatic_get_customizer_option( 'header_search_option')) return;
    if(!is_404()) {?>
        <div class="search-wrap">
            <button class="search-trigger" aria-label="Rechercher du contenu" name="Rechercher du contenu">
                <i class="fas fa-search"></i>
            </button>
            <div class="search-form-wrap hide">
                <?php echo get_search_form(); ?>
            </div>
        </div>
    <?php
    }
}

/**
 * @return void
 */
function newsmatic_child_header_theme_mode_icon_part(): void
{
    if(!ND\newsmatic_get_customizer_option( 'header_theme_mode_toggle_option')) return;
    $theme_mode_dark = ( isset( $_COOKIE['themeMode'] ) && $_COOKIE['themeMode'] == 'dark' );
    if(!is_404()) {?>
        <div class="mode_toggle_wrap">
            <input aria-label="Changer de mode d'affichage" class="mode_toggle" type="checkbox" name="mode_toggle"
                <?php echo checked(true, $theme_mode_dark); ?>
            >
        </div>
    <?php
    }
}

/**
 * @return void
 */
function newsmatic_child_header_site_branding_part(): void
{
    ?>
    <div class="site-branding">
        <?php
        if (isset($_COOKIE['themeMode']) && $_COOKIE['themeMode'] === 'dark') {
            the_custom_logo();
        } else {
            echo sprintf( '<a href="%1$s" class="custom-logo-link" rel="home" itemprop="url">%2$s</a>',
                esc_url(get_home_url()),
                wp_get_attachment_image(1720, 'full', false, [
                    'class'    => 'custom-logo',
                ])
            );
        }
        if ( is_front_page() && is_home() ) :
            ?>
            <h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
        <?php
        else :
            ?>
            <p class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></p>
        <?php
        endif;
        $newsmatic_description = get_bloginfo( 'description', 'display' );
        if ( $newsmatic_description || is_customize_preview() ) :
            ?>
            <p class="site-description"><?php echo apply_filters( 'newsmatic_bloginfo_description', esc_html( $newsmatic_description ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></p>
        <?php endif; ?>
    </div><!-- .site-branding -->
    <?php
}

/**
 * Add a copyright in the footer
 *
 * @return void
 */
function newsmatic_child_bottom_footer_copyright_part(): void
{
    ?>
    <div class="site-info">
        <?php echo  '© ' . date('Y') . ' - ' . 'Powered By <a href="https://github.com/Rapkalin/">Rapkalin</a> and <a href="https://github.com/noweh/">Noweh</a>. <br />' .
            __('Discover the full project on GitHub', 'explain')
        ?>
    </div>
    <?php
}

/**
 * @return void
 *
 * Load translation files from child and parent themes
 */
function newsmatic_child_theme_locale(): void
{
    load_child_theme_textdomain( 'newsmatic', get_stylesheet_directory() . '/languages' );
    load_child_theme_textdomain( 'explain', get_stylesheet_directory() . '/languages' );
}

/**
 * @return void
 */
function newsmatic_child_category_archive_author_html(): void
{
    if(!is_author()) return;
    $author_id =  get_query_var('author');

    $facebook = get_the_author_meta( 'facebook', $author_id );
    $twitter = get_the_author_meta( 'twitter', $author_id );
    $instagram = get_the_author_meta( 'instagram', $author_id );
    $pinterest = get_the_author_meta( 'pinterest', $author_id );
    $youtube = get_the_author_meta( 'youtube', $author_id );
    $linkedin = get_the_author_meta( 'linkedin', $author_id );
    ?>
    <div class="newsmatic-container newsmatic-author-section">
        <div class="row">
            <?php echo get_avatar($author_id, 125, 'mystery', __('Profile picture', 'explain')) ?>
            <div class="author-content">
                <h2 class="author-name"><?php echo esc_html(get_the_author_meta('display_name', $author_id)); ?></h2>

                <?php
                    // Display social media for the current author

                    if(!empty($facebook)) {
                    echo '<a href="'. $facebook .'" rel="nofollow" target="_blank" alt="Facebook"><i class="author-social-media-child fab fa-facebook"></i></a>';
                    }

                    if(!empty($instagram)) {
                    echo '<a href="'. $instagram .'" rel="nofollow" target="_blank" alt="Instagram"><i class="author-social-media-child fab fa-instagram"></i></a>';
                    }

                    if(!empty($pinterest)) {
                    echo '<a href="'. $pinterest .'" rel="nofollow" target="_blank" alt="Pinterest"><i class="author-social-media-child fab fa-pinterest"></i></a>';
                    }

                    if(!empty($twitter)) {
                    echo '<a href="https://twitter.com/' . $twitter .'" rel="nofollow" target="_blank" alt="Twitter"><i class="author-social-media-child fab fa-twitter"></i></a>';
                    }

                    if(!empty($youtube)) {
                    echo '<a href="'. $youtube .'" rel="nofollow" target="_blank" alt="YouTube"><i class="author-social-media-child fab fa-youtube"></i></a>';
                    }

                    if(!empty($linkedin)) {
                    echo '<a href="'. $linkedin .'" rel="nofollow" target="_blank" alt="Linkedin"><i class="author-social-media-child fab fa-linkedin"></i></a>';
                    }
                ?>

                <p class="author-desc"><?php echo nl2br(wp_kses_post(get_the_author_meta('description', $author_id))); ?></p>
            </div>
        </div>
    </div>
    <?php
}

/**
 * @return void
 */
function newsmatic_child_pagination_fnc(): void
{
    if(is_null(paginate_links())) {
        return;
    }
    ?>
    <nav class="pagination" aria-label="<?php esc_attr_e('Pagination', 'explain'); ?>">
      <?php echo paginate_links([
          'type' => 'list',
          'prev_text' => '<span class="screen-reader-text">' . __('Previous page', 'explain') . '</span><i class="fas fa-chevron-left"></i>',
          'next_text' => '<span class="screen-reader-text">' . __('Next page', 'explain') . '</span><i class="fas fa-chevron-right"></i>',
          'before_page_number' => '<span class="screen-reader-text">' . __( 'Page', 'explain' ) . '</span> '
      ]); ?>
    </nav>
    <?php
}

/**
 * @param $links
 *
 * @return void
 *
 * Deactivate self pingbacks
 */
function no_self_ping(&$links): void
{

    remove_action('admin_head', 'wp_site_icon');
    $home = get_option('home');
    foreach ($links as $l => $link) {
        if (0 === strpos($link, $home)) {
            unset($links[$l]);
        }
    }
}

/**
 * @param $content
 *
 * @return false|string
 */
function filter_content_without_plugins($content) {
    // Supprime les contenus des plugins
    $classes_to_remove = array('wpulike', 'sharedaddy');
    $dom = new DOMDocument();
    libxml_use_internal_errors(true);
    $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'), LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    libxml_clear_errors();

    $xpath = new DOMXPath($dom);

    foreach ($classes_to_remove as $class) {
        $elements = $xpath->query("//*[contains(@class, '$class')]");

        foreach ($elements as $element) {
            $element->parentNode->removeChild($element);
        }
    }
    return $dom->saveHTML();
}

/**
 * @param $content
 *
 * @return array|void
 */
function get_plugins_content($content = null) {
    if ($content) {
        $plugin_classes_to_get = array('wpulike', 'sharedaddy');
        $dom = new DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML(mb_convert_encoding($content, 'HTML-ENTITIES', 'UTF-8'));
        libxml_clear_errors();

        $xpath = new DOMXPath($dom);

        $removed_elements = array();

        foreach ($plugin_classes_to_get as $class) {
            $elements = $xpath->query("//*[contains(@class, '$class')]");
            foreach ($elements as $element) {
                if ($element->tagName !== 'svg') {
                    $removed_elements[] = $dom->saveHTML($element);
                    $element->parentNode->removeChild($element);
                }
            }
        }

        return $removed_elements;
    }
}
