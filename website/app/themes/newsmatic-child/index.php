<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package Newsmatic
 */
use Newsmatic\CustomizerDefault as ND;
get_header();
/**
 * hook - newsmatic_main_banner_hook
 *
 * hooked - newsmatic_main_banner_part - 10
 */
if( is_home() && is_front_page() )

    $banner_title = nl2br(get_theme_mod('newsmatic_child_banner_title')) ?: 'Titre de la bannière';
    $banner_url = get_theme_mod('newsmatic_child_banner_url') ?: '/';
    $banner_is_active = get_theme_mod('newsmatic_child_banner_active');

if ($banner_is_active) :
?>

    <!-- Banner event -->
    <a
            class="newsmatic-child-event-banner slide-item slick-clone"
            href="<?php echo $banner_url ?>"
    >
        <?php echo $banner_title ?>
    </a>
<?php
endif;

do_action( 'newsmatic_main_banner_hook' );

$homepage_content_order = ND\newsmatic_get_customizer_option( 'homepage_content_order' );

foreach( $homepage_content_order as $content_order_key => $content_order ) :
    if( $content_order['value'] == 'latest_posts' && is_home() && ! is_front_page()  ) $content_order['option'] = true;
    if( $content_order['option'] ) :
        switch( $content_order['value'] ) {
            case "full_width_section":
                /**
                 * hook - newsmatic_full_width_blocks_hook
                 *
                 * hooked- newsmatic_full_width_blocks_part
                 * @since 1.0.0
                 *
                 */
                if( is_home() && is_front_page() ) do_action( 'newsmatic_full_width_blocks_hook' );
                break;
            case "leftc_rights_section":
                /**
                 * hook - newsmatic_leftc_rights_blocks_hook
                 *
                 * hooked- newsmatic_leftc_rights_blocks_part
                 * @since 1.0.0
                 *
                 */
                if( is_home() && is_front_page() ) do_action( 'newsmatic_leftc_rights_blocks_hook' );
                break;
            case "lefts_rightc_section":
                /**
                 * hook - newsmatic_lefts_rightc_blocks_hook
                 *
                 * hooked- newsmatic_lefts_rightc_blocks_part
                 * @since 1.0.0
                 *
                 */
                if( is_home() && is_front_page() ) do_action( 'newsmatic_lefts_rightc_blocks_hook' );
                break;
            case "bottom_full_width_section":
                /**
                 * hook - newsmatic_bottom_full_width_blocks_hook
                 *
                 * hooked- newsmatic_bottom_full_width_blocks_part
                 * @since 1.0.0
                 *
                 */
                if( is_home() && is_front_page() ) do_action( 'newsmatic_bottom_full_width_blocks_hook' );
                break;
            default: ?>
                <div id="theme-content">
                    <main id="primary" class="site-main">
                        <div class="newsmatic-container">
                            <div class="row">
                                <div class="secondary-left-sidebar">
                                    <?php get_sidebar('left'); ?>
                                </div>
                                <div class="primary-content">
                                    <?php
                                    if ( have_posts() ) :
                                        if ( is_home() && ! is_front_page() ) :
                                            ?>
                                            <header>
                                                <h1 class="page-title newsmatic-block-title screen-reader-text"><?php single_post_title(); ?></h1>
                                            </header>
                                        <?php
                                        endif;
                                        $args['archive_post_element_order'] = ND\newsmatic_get_customizer_option( 'archive_post_element_order' );
                                        $args['archive_post_meta_order'] = ND\newsmatic_get_customizer_option( 'archive_post_meta_order' );
                                        echo '<div class="news-list-wrap">';
                                        /* Start the Loop */
                                        while ( have_posts() ) :
                                            /*
                                            * Include the Post-Type-specific template for the content.
                                            * If you want to override this in a child theme, then include a file
                                            * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                                            */
                                            the_post();
                                            get_template_part( 'template-parts/content', get_post_type(), $args );
                                        endwhile;
                                        echo '</div>';
                                        /**
                                         * hook - newsmatic_pagination_link_hook
                                         *
                                         * @package Newsmatic
                                         * @since 1.0.0
                                         */
                                        do_action( 'newsmatic_pagination_link_hook' );
                                    else :
                                        get_template_part( 'template-parts/content', 'none' );
                                    endif;
                                    ?>
                                </div>
                                <div class="secondary-sidebar">
                                    <?php
                                    get_sidebar();
                                    ?>
                                </div>
                            </div>
                        </div> <!-- newsmatic-container end -->
                    </main><!-- #main -->
                </div><!-- #theme-content -->
            <?php
        }
    endif;
endforeach;
get_footer();