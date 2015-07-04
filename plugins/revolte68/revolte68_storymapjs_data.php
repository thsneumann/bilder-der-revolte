<?php
/*
Output JSON data for StorymapJS
Parameters: category
*/

// Get Wordpress installation root path
// *** http://stackoverflow.com/a/19626451 ***
function get_wp_installation()
{
    $full_path = getcwd();
    $ar = explode("wp-", $full_path);
    return $ar[0];
}

// Include WordPress
// *** http://www.wprecipes.com/how-to-run-the-loop-outside-of-wordpress ***
define( 'WP_USE_THEMES', false );
require( get_wp_installation() . '/wp-blog-header.php' );


// Set up query
$excluded_categories = array( get_cat_ID( 'aussortiert' ), get_cat_ID( 'Datum unklar' ) );
$args = array( 'post_type' 			=> 'photo',
			   'post_status' 		=> 'publish',
			   'posts_per_page'		=> -1,
			   'category__not_in' 	=> $excluded_categories,
			   'meta_key'			=> 'date', // Sort by date
			   'orderby'			=> 'meta_value_num',
			   'order'				=> 'ASC', );

$first_slide_headline = 'Ludwig Binder fotografiert die 68er Bewegung: ein Stadtspaziergang';

if ( ($cat_slug = $_GET['spaziergang']) != '') {
	$args['category_name'] = $cat_slug;
	$first_slide_headline = 'Spaziergang: ' . get_category_by_slug($cat_slug)->name;
}

query_posts( $args );

// Set up Storymap data as array, later convert to JSON
$storymap_data = array( 'storymap' => array( 
							'language'	=> 'de',
							'slides' => array( 
								array( 
									'type' => 'overview',
									'date' => '1967-1969',
									'text' => array(
										'headline' => $first_slide_headline,
										'text'	   => '...'
									),
									'media' => array(
										'url' 	   => 'http://www.hdg.de/lemo/img_hd/bestand/objekte/biografien/binder-ludwig_foto_2012-11-0001_hdg.jpg',
										'credit'   => 'Stiftung Haus der Geschichte; EB-Nr. 2012/11/0001',
										'caption'  => 'Porträt des Fotojournalisten Ludwig Binder mit Kamera'
									)
								)
							)
						 )
					);


while (have_posts()): the_post(); 

	$post_id = get_the_ID();
	$location_id = $wpdb->get_var( "SELECT location_id FROM " . $wpdb->prefix . "geo_mashup_location_relationships WHERE object_id=$post_id" );
	// Only include posts with geolocation
	if (!$location_id) continue;
	$location = array_pop($wpdb->get_results( "SELECT address, lat, lng FROM " . $wpdb->prefix . "geo_mashup_locations WHERE id=$location_id" ));
	$meta = get_post_meta( $post_id );		
	$date = rev68_format_date( $meta['date'][0], 'show' );
	// Get attached image		
	// *** https://codex.wordpress.org/Function_Reference/get_children#Show_the_first_image_associated_with_the_post ***
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );

	$storymap_data['storymap']['slides'][] = array(
		'date'	=> $date,
		'text'	=> array(
			'headline' 	=> '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . get_the_title() . '</a>',
			'text'		=> get_the_content()
		),
		'location' => array(
			'name'	=> $location->address,
			'lat'	=> $location->lat,
			'lon'	=> $location->lng,
			'zoom'	=> 10,
			'line'	=> true
		),
		'media' => array(
			'url' 	   => $thumbnail[0],
			'credit'   => '',
			'caption'  => ''
		)
	);

endwhile; 

wp_reset_postdata();

header('Content-Type: application/json');
echo json_encode( $storymap_data );



?>



