<?php
/**
 * The sidebar containing the main widget area.
 *
 * @package Adirondack
 */

if ( ! is_active_sidebar( 'sidebar-1' ) ) {
	return;
}
?>

<div id="sidebar-1" role="complementary" <?php adirondack_widgets_class(); ?>>
	<div class="wrapper">
		<?php dynamic_sidebar(); ?>
	</div>
</div><!-- #secondary -->
