<?php
/*
Output JSONP data for TimelineJS
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


// Create JSON data from Lemo Chronik *** http://www.hdg.de/lemo/jahreschronik/1967.html ** 
function rev68_scraper_lemochronik() {
	$query_startdate = '1967,05,27';
	$startyear = 1967;
	$query_enddate   = '1969,11,15';
	$endyear   = 1969;
	$data = array();

	for ($year = $startyear; $year <= $endyear; $year++ ) {

		$file = "http://www.hdg.de/lemo/jahreschronik/$year.html";
		$doc = new DOMDocument();
		@$doc->loadHTMLFile($file);

		$resultlist_ul = $doc->getElementById( 'chronicle-year' );
		$resultlist = $resultlist_ul->getElementsByTagName( 'li' );

		foreach( $resultlist as $li ) { 

			$line = str_replace( array( "\t", "\n" ), "", $li->nodeValue);

			if (preg_match( '/(\d\d)\.(\d\d)\.(.*)/', $line, $results)) {     // Datum Format <TAG>.<MONAT>.
				$startdate = $enddate = "$year,$results[2],$results[1]";
				//$headline = substr( $line, 0, 6 );
				$text = $results[3];
				//echo "Tag: $results[1] Monat: $results[2] Text: $results[3]\n";
			} 
			if (preg_match( '/(\d\d)\. - (\d\d)\.(\d\d)\.(.*)/', $line, $results)) {		// Datum Format <TAG_VON>. - <TAG_BIS>.<MONAT>.
				$startdate = "$year,$results[3],$results[1]";
				$enddate = "$year,$results[3],$results[2]";
				//$headline = substr( $line, 0, 12 );
				$text = $results[4];
				//echo "Von: $results[1].$results[3]. - $results[2].$results[3]. Text: $results[4]\n";
			}
			if (preg_match( '/(\d\d)\.(\d\d)\. - (\d\d)\.(\d\d)\.(.*)/', $line, $results)) {	// Datum Format <TAG_VON>.<MONAT_VON>. - <TAG_BIS>.<MONAT_BIS>.
				$startdate = "$year,$results[2],$results[1]";
				$enddate = "$year,$results[4],$results[3]";
				//$headline = substr( $line, 0, 15 );
				$text = $results[5];
			}

			$words = explode( " ", $text );
			$headline = implode( " ", array_splice( $words, 0, 5 ) ) . ' ...';
			$text = implode( " ", $words );

			if ( ($startdate < $query_startdate) || ($startdate > $query_enddate) ) continue;

			$source = 'Quelle: <a href="http://www.hdg.de/lemo/jahreschronik/' . $year . '.html">LeMO Jahreschronik ' . $year . '</a>';

			$data[] = array( 
						'startDate' 	=> $startdate,
						'endDate'		=> $enddate,
						'headline'		=> $headline,
						'text'			=> '<p>' . $text . '</p><p><br />' . $source . '</p>', 
						'tag'			=> 'Chronik'
			);

		} // end foreach

	} // end for

	return $data;
}



// Build data array, then convert to JSON
$timeline_data = array ( 'timeline' => array(
							'headline' => 'Chronik der Revolte',
							'type'	   => 'default',
							'text'	   => 'Ludwig Binder fotografiert die 68er-Bewegung'
							)
						);

// photos
$excluded_categories = array( get_cat_ID( 'aussortiert' ), get_cat_ID( 'Datum unklar' ) );
$args = array( 'post_type' 			=> 'photo',
			   'post_status' 		=> 'publish',
			   'posts_per_page'		=> '-1',
			   'category__not_in' 	=> $excluded_categories );
$the_query = new WP_Query( $args );

while ( $the_query->have_posts() ) {
	$the_query->the_post(); 
	$post_id = get_the_ID();
	$meta = get_post_meta( get_the_ID() );		
	$date_json = rev68_format_date( $meta['date'][0], 'json' );
	// Get attached image		
	// *** https://codex.wordpress.org/Function_Reference/get_children#Show_the_first_image_associated_with_the_post ***
	$thumbnail = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
	
	$timeline_data['timeline']['date'][] = array(
			'startDate' => $date_json,
			'endDate'   => $date_json,
			'headline'	=> '<a href="' . get_permalink( $post_id ) . '" target="_blank">' . get_the_title() . '</a>',
			'text'		=> '<p>' . get_the_content() . '</p>',
			'tag'		=> 'Photo',
			'asset'		=> array( 
							'media'		=> $thumbnail[0],
							'credit'	=> '',
							'caption'	=> ''
							)
			);
}			

/* Restore original Post Data */
wp_reset_postdata();


// Add LeMO Chronik data
$timeline_data['timeline']['date'] = array_merge( $timeline_data['timeline']['date'], rev68_scraper_lemochronik() );

// Convert to JSONP
$timeline_data_jsonp = 'storyjs_jsonp_data = ' . json_encode( $timeline_data );

// Correct mysterious umlaut encoding error *** to be improved ***
$timeline_data_jsonp = str_replace( "\u00c3\u00bc", "\u00fc", $timeline_data_jsonp );	// ü
$timeline_data_jsonp = str_replace( "\u00c3\u00a4", "\u00e4", $timeline_data_jsonp );	// ä
$timeline_data_jsonp = str_replace( "\u00c3\u00b6", "\u00f6", $timeline_data_jsonp );	// ö
$timeline_data_jsonp = str_replace( "\u00c3\u009f", "\u00df", $timeline_data_jsonp );	// ß
$timeline_data_jsonp = str_replace( "\u00c2\u00be", "\u00be", $timeline_data_jsonp );	// ¾


// Output as Javascript
header( 'Content-Type: application/javascript');
echo $timeline_data_jsonp;


