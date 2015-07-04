<?php
/**
 * Template Name: Full Width GeoMashup
 *
 * @package Adirondack
 */
?>

<?php get_header(); ?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main full-width" role="main">

		<?php if ( have_posts() ) : ?>

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>


			<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<?php if ( has_post_thumbnail() ) : ?>
					<header class="entry-header">
						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
					</header><!-- .entry-header -->
				<?php endif; ?>

				<div class="entry-content">
					<div class="wrapper">
						<?php the_content(); ?>
						
						<?php 
						// Exclude categories 'aussortiert' (394) and 'Datum unklar' (395)
						echo GeoMashup::Map(array( 'map_cat' => '-394,-395' )); ?>
					</div>
				</div><!-- .entry-content -->

				<footer class="entry-footer">
	
				</footer><!-- .entry-footer -->
			</article><!-- #post-## -->

			<?php endwhile; ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
