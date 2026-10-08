<?php
// Template content-single.php

use Newsmatic\CustomizerDefault as ND;

// Récupérer le contenu principal de l'article
ob_start(); // Commencer à mettre en mémoire tampon la sortie
the_content(
        sprintf(
            wp_kses(
            /* translators: %s: Name of current post. Only visible to screen readers */
                __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'newsmatic' ),
                array(
                    'span' => array(
                        'class' => array(),
                    ),
                )
            ),
            wp_kses_post( get_the_title() )
        )
); // Afficher le contenu
$content = ob_get_clean(); // Récupérer la sortie mise en mémoire tampon

// Classes à retirer
$classes_to_remove = array('wpulike', 'sharedaddy'); // Ajoute les classes des plugins à retirer

// Extraire les éléments retirés
$removed_elements = get_plugins_content($content);

$single_post_element_order = ND\newsmatic_get_customizer_option( 'single_post_element_order' );
$single_post_meta_order = ND\newsmatic_get_customizer_option( 'single_post_meta_order' );
?>

<article <?php newsmatic_schema_article_attributes(); ?> id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
    <div class="post-inner">
        <header class="entry-header">
            <?php
            foreach( $single_post_element_order as $element_order ) :
                if( $element_order['option'] ) {
                    switch( $element_order['value'] ) {
                        case 'title': the_title( '<h1 class="entry-title"' .newsmatic_schema_article_name_attributes(). '>', '</h1>' );
                            break;
                        case 'meta':	if ( 'post' === get_post_type() ) :
                            ?>
                            <div class="entry-meta">
                                <?php
                                foreach( $single_post_meta_order as $meta_order ) :
                                    if( $meta_order['option'] ) {
                                        switch( $meta_order['value'] ) {
                                            case 'author': newsmatic_posted_by();
                                                break;
                                            case 'date': newsmatic_posted_on();
                                                break;
                                            case 'comments': newsmatic_comments_number();
                                                break;
                                            case 'read-time': echo '<span class="read-time">' .newsmatic_post_read_time( get_the_content() ). ' ' .esc_html__( 'mins', 'newsmatic' ). '</span>';
                                                break;
                                            default: '';
                                        }
                                    }
                                endforeach;

                                newsmatic_child_tags_list();
                                echo '<div class="article-published-date article-before-content-text">' . __('Article published on: ', 'explain') . get_the_date() . '</div>';
                                ?>
                            </div><!-- .entry-meta -->

                        <?php endif;
                            break;
                        default: '';
                    }
                }
            endforeach;
            ?>
        </header><!-- .entry-header -->

        <div <?php newsmatic_schema_article_body_attributes(); ?> class="entry-content">
            <?php
            // Afficher le contenu principal
            echo filter_content_without_plugins($content);
            ?>
        </div><!-- .entry-content -->

        <!-- Afficher les éléments retirés dans une div séparée -->
        <?php if (!empty($removed_elements)) : ?>
            <div class="plugin-content">
                <?php foreach ($removed_elements as $element) : ?>
                    <?php echo $element; ?>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <footer class="entry-footer">
            <?php newsmatic_entry_footer(); ?>
        </footer><!-- .entry-footer -->
        <?php
        the_post_navigation(
            array(
                'prev_text' => '<span class="nav-subtitle"><i class="fas fa-angle-double-left"></i>' . esc_html__( 'Previous:', 'newsmatic' ) . '</span> <span class="nav-title">%title</span>',
                'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next:', 'newsmatic' ) . '<i class="fas fa-angle-double-right"></i></span> <span class="nav-title">%title</span>',
            )
        );
        ?>
    </div>
    <?php
    // Si les commentaires sont ouverts ou s'il y a au moins un commentaire, charger le template des commentaires
    if (comments_open() || get_comments_number()) :
        comments_template();
    endif;
    ?>
</article><!-- #post-<?php the_ID(); ?> -->

<?php
/**
 * hook - newsmatic_single_post_append_hook
 *
 */
do_action('newsmatic_single_post_append_hook');
?>