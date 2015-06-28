<?php
/**
 * The template for displaying archive pages.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package Adirondack
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main" role="main">

		<?php if ( have_posts() ) : ?>

		<?php 
			$tags = $wp_query->query['tags'];
			$persons = $wp_query->query['person'];
		?>

			<header class="page-header">
				<h1 class="page-title">


<?php
// copied from revolte68_faceted_search_widget.php
$taxonomies_to_show = array( 'post_tag', 'person' ); // *hardcoded* change to selectable later
foreach ($taxonomies_to_show as $tax) {
	$taxonomies[] = get_taxonomy( $tax );
}

$terms_selected_slug = array();
$terms_selected = array();
$query_var = $wp_query->query;

// Show selected terms
foreach ($taxonomies as $tax) {
	if (empty( $query_var[ $tax->query_var ] )) continue;
	$terms_selected_slugs[ $tax->name ] = explode( ',', $query_var[ $tax->query_var ] );
	foreach ($terms_selected_slugs[ $tax->name ] as $slug) {
		$term = get_term_by( 'slug', $slug, $tax->name );
		$terms_selected[ $tax->name ][] = $term->name . ' ' . rev68_gnd_link( $term );
	}

}
?>

					<?php
						if ( !empty( $terms_selected )) {
							foreach ($taxonomies as $tax) {
								if (empty( $terms_selected[ $tax->name ] )) continue;
								echo $tax->label . ': ' . implode( ', ', $terms_selected[ $tax->name ] ) . '<br />';
							}
						} else {
							_e( 'Archives', 'adirondack' );
						}
					?>
				</h1>
				<?php
				/*
					// Show an optional term description.
					$term_description = term_description();
					if ( ! empty( $term_description ) ) :
						printf( '<div class="taxonomy-description">%s</div>', $term_description );
					endif;
				*/
				?>
			</header><!-- .page-header -->

			<?php /* Start the Loop */ ?>
			<?php while ( have_posts() ) : the_post(); ?>

				<?php
					/* Include the Post-Format-specific template for the content.
					 * If you want to override this in a child theme, then include a file
					 * called content-___.php (where ___ is the Post Format name) and that will be used instead.
					 */
					get_template_part( 'content', get_post_format() );
				?>

			<?php endwhile; ?>

			<?php adirondack_paging_nav(); ?>

		<?php else : ?>

			<?php get_template_part( 'content', 'none' ); ?>

		<?php endif; ?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>
