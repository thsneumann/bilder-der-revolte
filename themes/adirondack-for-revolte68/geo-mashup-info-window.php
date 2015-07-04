<?php
/**
 * This is the default template for the info window in Geo Mashup maps. 
 *
 * Don't modify this file! It will be overwritten by upgrades.
 *
 * Instead, copy this file to "geo-mashup-info-window.php" in your theme directory, 
 * or info-window.php in the Geo Mashup Custom plugin directory, if you have that 
 * installed. Those files take precedence over this one.
 *
 * For styling of the info window, see map-style-default.css.
 *
 * @package GeoMashup
 * 
 * Notes: jh: moved content into .mapwrap div to style it 1.jul.15
 */
// A potentially heavy-handed way to remove shortcode-like content
add_filter('the_excerpt', array('GeoMashupQuery', 'strip_brackets'));
?>
<div class="locationinfo post-location-info">
<?php if (have_posts()) : ?>

        <?php while (have_posts()) : the_post(); ?>
            
                <h2><a href="<?php the_permalink() ?>" target="_blank" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h2>
                <div class="mapwrap">
                <?php if (function_exists('has_post_thumbnail') and has_post_thumbnail()) : ?>
                    <?php the_post_thumbnail( 'medium' ); ?>
                <?php endif; ?>
                <?php /* <p class="meta"><span class="blogdate"><?php echo get_the_date(); ?></span> <?php the_category(', ') ?></p> */ ?>
				<div class="storycontent">
                <?php 
				if ($wp_query->post_count == 1)
					the_content();
				else
					the_excerpt();
                ?>
                </div>
            </div>
            <?php endwhile; ?>

    <?php else : ?>

        <h2 class="center">Not Found</h2>
        <p class="center">Sorry, but you are looking for something that isn't here.</p>

<?php endif; ?>

</div>
