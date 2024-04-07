<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Newsmatic
 */
use Newsmatic\CustomizerDefault as ND;
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<meta name="description" content="Explorez notre blog de programmation pour simplifier et populariser les concepts de codage. Trouvez des articles informatifs, des tutoriels et des astuces pour améliorer vos compétences en programmation.">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php newsmatic_schema_body_attributes(); ?>>
<?php wp_body_open(); ?>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#primary"><?php esc_html_e( 'Skip to content', 'newsmatic' ); ?></a>
	<?php
		if( class_exists( 'Nekit_Render_Templates_Html' ) ) :
			$Nekit_render_templates_html = new Nekit_Render_Templates_Html();
			if( $Nekit_render_templates_html->is_template_available('header') ) {
				$header_rendered = true;
				echo $Nekit_render_templates_html->current_builder_template();
			} else {
				$header_rendered = false;
			}
		else :
			$header_rendered = false;
		endif;

		if( ! $header_rendered ) :
		?>
			<div class="newsmatic_ovelay_div"></div>
			<?php
				/**
				 * hook - newsmatic_page_prepend_hook
				 * 
				 * @package Newsmatic
				 * @since 1.0.0
				 */
				do_action( "newsmatic_page_prepend_hook" );
			?>
			
			<header id="masthead" class="site-header layout--default layout--one">
				<?php
					/**
					 * Function - newsmatic_top_header_html
					 * 
					 * @since 1.0.0
					 * 
					 */
					newsmatic_top_header_html();

					/**
					 * Function - newsmatic_header_html
					 * 
					 * @since 1.0.0
					 * 
					 */
					newsmatic_header_html();
				?>
			</header><!-- #masthead -->
			
			<?php
			/**
			 * function - newsmatic_after_header_html
			 * 
			 * @since 1.0.0
			 */
			newsmatic_after_header_html();
		endif;