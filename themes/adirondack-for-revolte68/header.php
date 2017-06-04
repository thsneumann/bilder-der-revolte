<?php
/**
 * The header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="content">
 *
 * @package Adirondack
 */
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="profile" href="http://gmpg.org/xfn/11">
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">

<?php wp_head(); ?>
<!-- my content -->
<link rel="stylesheet" id="wikibox-style-css" href="<?php echo get_template_directory_uri() . '/wikibox/wikibox.css'; ?>" type="text/css" media="all">
</head>

<body <?php body_class(); ?>>
<?php include_once( dirname(__FILE__) . "/inc/analyticstracking.php") ?>
<?php do_action( 'adirondack_load_svg' ); ?>

<a class="skip-link screen-reader-text" href="#content"><?php _e( 'Skip to content', 'adirondack' ); ?></a>

<div id="page" class="hfeed site">

	<header id="masthead" class="site-header" role="banner">
		<div class="site-branding">
			<h1 class="site-title"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1>
		</div>

		<div class="nav-container">
		<?php 
		/* *** Hide ***		
		$button = '';
		if ( is_active_sidebar( 'sidebar-1' ) ) {
			$button = '<button><svg class="ellipsis"><use xlink:href="#icon-ellipsis" /></svg><svg class="x"><use xlink:href="#icon-x" /></svg></button>';
		}
		*/
		?>
		<nav id="site-navigation" class="main-navigation <?php echo $button? 'has-widgets': 'no-widgets'; ?>" role="navigation">
			<?php /* *** Hide ***
			<button class="menu-toggle"><?php _ex( 'Menu', 'primary menu label', 'adirondack' ); ?></button>
			<div class="small-widgets-toggle widgets-toggle"><?php echo $button; ?></div>
			*/ ?>

			<?php 
			/* *** Backup ***
			wp_nav_menu( array(
				'theme_location' => 'primary',
				'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s<li class="widgets-toggle">' . $button . '</li></ul>'
			) ); 
			*/

			//$options_button = ''; //'<a id="sidebar-xx-toggle" href="#"><i class="fa fa-bars"></i></a>';
			//if (is_home() || is_archive()) {
				$options_button = '<a id="sidebar-1-toggle" href="#"><i class="fa fa-search"></i></a>';
			//}

			wp_nav_menu( array(
				'theme_location' => 'primary',
				'items_wrap' => '<ul id="%1$s" class="%2$s">%3$s<li class="search-toggle">' . $options_button . '</li></ul>'
			) );

			?>

		</nav><!-- #site-navigation -->
		</div>



	</header><!-- #masthead -->


	<div id="content" class="site-content">

		<?php get_sidebar(); ?>
