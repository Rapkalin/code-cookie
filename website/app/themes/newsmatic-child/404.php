<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package Newsmatic
 */
get_header();
?>
    <div id="theme-content">
        <main id="primary" class="site-main">
            <div class="newsmatic-container">
                <section id="error-404-child">
                    <div id="post-inner-wrapper-404">

                        <div id="page-header-404-child" class="site-header layout--default layout--on">
                            <h1 id="page-title-404"><?php echo esc_html__( '404 Not Found', 'explain' ); ?></h1>
                            <div id="error-image-404" >
                                <img id="lost-404-gif" src="/app/themes/newsmatic-child/images/explain-code-to-me-404.gif" alt="gif for 404 page">
                            </div>

                            <div id="description-toggle">
                                <p><?php echo esc_html__( 'Wouuupsy, looks like you got lost!', 'explain' ); ?></p>
                                <!-- Toggle icon dark/light mode -->
                                <?php
                                    do_action( 'newsmatic_child_404_header_icon__menu_section_hook' );
                                ?>
                            </div>

                            <div id="page-content-404">
                                <!-- Menu -->
                                <?php
                                    do_action( 'newsmatic_child_404_header__menu_section_hook' );
                                ?>

                                <!-- Search form -->
                                <div id="search-form-404">
                                    <?php echo get_search_form(); ?>
                                </div>
                            </div>
                        </div><!-- .page-header -->

                    </div><!-- .post-inner-wrapper -->
                </section><!-- .error-404 -->
            </div>
        </main><!-- #main -->
    </div><!-- #theme-content -->


    <!-- News carrousel: Discover also -->
    <?php
        do_action( 'newsmatic_bottom_full_width_blocks_hook' );
    ?>
<?php
get_footer();
