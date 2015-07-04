<?php
/**
 * @package Adirondack
 */
?>

<?php
$post_id = get_the_ID();
$meta = get_post_meta( $post_id );
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<?php if ( has_post_thumbnail() ) : ?>
		<header class="entry-header">
			<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
		</header><!-- .entry-header -->
	<?php endif; ?>

	<div class="entry-content">
		<div class="wrapper">
			<?php
			$large_image_url = wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' );
			echo '<a href="' . $large_image_url[0] . '" title="' . the_title_attribute( 'echo=0' ) . '">';
			the_post_thumbnail();
			echo '</a>';
			?>

			<?php the_content(); ?>

			<h4>Kommentar</h4>
			<p>
			<?php echo $meta['commentary'][0]; ?>
			</p>

			<?php //echo rev68_scraper_medienarchiv68( rev68_format_date( $meta['date'][0], 'show' ) ); ?>

			<?php
				wp_link_pages( array(
					'before' => '<div class="page-links">' . __( 'Pages:', 'adirondack' ),
					'after'  => '</div>',
				) );
			?>
		</div>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
			/* translators: used between list items, there is a space after the comma */
			$category_list = get_the_category_list( __( ', ', 'adirondack' ) );

			/* translators: used between list items, there is a space after the comma */
			$tag_list = rev68_get_term_list ($post_id, 'post_tag');  //get_the_tag_list( '', __( ', ', 'adirondack' ) );

			$person_list = rev68_get_term_list( $post_id, 'person' ); //get_the_term_list( '', 'person' );

			$map_link = GeoMashup::show_on_map_link_url();
		?>

		<?php if ( '' != $map_link ) : ?>
		<div class="meta-item geomashup">
			<i class="fa fa-map-marker"></i>
			<a href="<?php echo $map_link; ?>">Auf Karte anzeigen</a>
		</div>
		<?php endif; ?>

		<?php if ( '' != $tag_list ) : ?>
			<div class="meta-item tags">
				<h4 class="meta-title"><?php _e( 'Tags' ); ?></h4>
				<?php echo $tag_list; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' != $person_list ) : ?>
			<div class="meta-item persons">
				<h4 class="meta-title"><?php echo 'Personen'; ?></h4>
				<?php echo $person_list; ?>
			</div>
		<?php endif; ?>

		<div class="meta-item medienarchiv68">
			<?php rev68_scraper_medienarchiv68( rev68_format_date( $meta['date'][0], 'show' ) ); ?>
		</div>

	</footer><!-- .entry-footer -->
</article><!-- #post-## -->
