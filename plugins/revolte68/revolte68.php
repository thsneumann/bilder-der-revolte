<?php
/*
Plugin Name: Revolte68 
Version: 0.5
Author: Thomas Neumann
Description: Plugin für Projekt 'Bilder der Revolte' im Rahmen von Coding da Vinci 2015
License: GPL2
*/

/*****************************************************************************************
** INCLUDES ******************************************************************************
******************************************************************************************/

require_once( plugin_dir_path( __FILE__ ) . 'revolte68_faceted_search_widget.php' );



/*****************************************************************************************
** SETUP PLUGIN **************************************************************************
******************************************************************************************/

// Admin Options
function rev68_create_menu() {

	add_menu_page( 'Revolte XML Import Optionen', 'Rev68 Admin', 'manage_options', 'rev68-admin', 'rev68_settings_page' );

}

add_action( 'admin_menu', 'rev68_create_menu' );

function rev68_admin_init() {

	register_setting( 'rev68-settings-group', 'rev68_options', 'rev68_sanitize_options' );

	// jQuery Datepicker
	wp_enqueue_script('jquery-ui-datepicker');
	wp_enqueue_style('jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css');

}

add_action( 'admin_init', 'rev68_admin_init' );


/*****************************************************************************************
** CUSTOM POST TYPES AND TAXONOMIES*******************************************************
******************************************************************************************/

// Create Custom Post Type 'photo' 
function rev68_create_custom_post_type_photo() {

	$labels = array(
		'name'				=> 'Photos',		// *** to be improved: plugin internationalization ***
		'singular_name' 	=> 'Photo',
		'add_new'			=> 'Neu',
		'add_new_item'		=> 'Photo hinzufügen',
		'edit_item'			=> 'Photo bearbeiten',
		'new_item'			=> 'Neues Photo',
		'all_items'			=> 'Alle Photos',
		'view_item'			=> 'Photo anzeigen',
		'parent_item_colon' => '',
		'menu_name'			=> 'Photos',
	);
	$args = array(
		'labels'			=> $labels,
		'description'		=> 'Custom Post Type für Photos und Metadaten',
		'public'			=> true,
		'menu_position'		=> 5,
		'supports'			=> array( 'title', 'editor', 'thumbnail', 'comments' ),
		'has_archive'		=> true,
		'taxonomies'		=> array( 'category', 'post_tag' ),
	);
	register_post_type( 'photo', $args );

}

add_action( 'init', 'rev68_create_custom_post_type_photo' );


// Create Custom Taxonomy 
// *** http://generatewp.com/taxonomy/ ***

function rev68_custom_taxonomies() {

	$labels = array(
		'name'                       => 'Personen',  // *** to be improved: plugin internationalization ***
		'singular_name'              => 'Person',
		'menu_name'                  => 'Personen',
		'all_items'                  => 'Alle Personen',
		'parent_item'                => 'Eltern-Element',
		'parent_item_colon'          => 'Eltern-Element',
		'new_item_name'              => 'Name der Person',
		'add_new_item'               => 'Neue Person hinzufügen',
		'edit_item'                  => 'Person bearbeiten',
		'update_item'                => 'Person aktualisieren',
		'view_item'                  => 'Person anzeigen',
		'separate_items_with_commas' => 'Personen mit Komma trennen (wichtig: Personenname in Format NACHNAME; VORNAME)',
		'add_or_remove_items'        => 'Personen hinzufügen / löschen',
		'choose_from_most_used'      => 'Aus den meistbenutzten wählen',
		'popular_items'              => 'Häufig verwendete Personen',
		'search_items'               => 'Nach Personen suchen',
		'not_found'                  => 'Person nicht gefunden',
	);
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => true,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => true,
		'show_tagcloud'              => true,
	);
	register_taxonomy( 'person', array( 'photo' ), $args );

}

add_action( 'init', 'rev68_custom_taxonomies', 0 );


/*****************************************************************************************
** ADMIN AREA ****************************************************************************
******************************************************************************************/


// Plugin Settings Page 
function rev68_settings_page() {
?>

	<div class="wrap">
	<h1>Revolte68 Admin</h1>

	<h2>XML Datei Upload</h2>

	<form method="post" enctype="multipart/form-data">
		<input type='file' id='rev68_upload' name='rev68_upload'></input>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Hochladen" disabled> <?php // disabled: not currently needed ?>
		</p>
	</form>

	<h2>GND Daten Upload (CSV-Datei)</h2>
	<form method="post" enctype="multipart/form-data">
		<input type='file' id='rev68_gndid_upload' name='rev68_gndid_upload'></input>
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Hochladen" disabled> <?php // *** deprecated (see below) *** ?>
		</p>
	</form>

	<h2>Alle Fotos löschen</h2>
	<form method="post">
		<input type="hidden" name="delete_all" value="1">
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="Alles löschen" disabled>
		</p>
	</form>

	<h2>TimelineJS JSON erzeugen</h2>
	<form method="post">
		<input type="hidden" name="make_timeline" value="1">
		<p class="submit">
			<input type="submit" name="submit" id="submit" class="button button-primary" value="JSON erzeugen">
		</p>
	</form>


<?php 
	rev68_handle_upload(); 
	if ($_POST['delete_all']) rev68_delete_all(); 
	if ($_POST['make_timeline']) rev68_make_timeline_json(); 
?>
	</div>
<?php
}

// XML File Upload Form Handler
function rev68_handle_upload() {

	// First check if the file appears on the _FILES array  *** XML Upload 
	if(isset($_FILES['rev68_upload'])){
		$file = $_FILES['rev68_upload'];
		$upload_overrides = array( 'test_form' => false );
		// Use the wordpress function to upload
		$uploaded = wp_handle_upload($file, $upload_overrides);
		
		// Error checking using WP functions
		if ($uploaded && !isset($uploaded['error'])) {
			echo ('Upload erfolgreich');

			echo "<p><pre>"; 
			rev68_process_xml( $uploaded['file'] );
			echo "</pre></p>";

			// Delete file after processing
			unlink($uploaded['file']);
		} else {
			echo 'Fehler: ' . $uploaded['error'];
		}

	} 
	/* *** deprecated: should be rewritten ***
	else if (isset($_FILES['rev68_gndid_upload'])) {   // *** GND ID CSV File Upload

		$file = $_FILES['rev68_gndid_upload'];
		$upload_overrides = array( 'test_form' => false );
		// Use the wordpress function to upload
		$uploaded = wp_handle_upload($file, $upload_overrides);
		
		// Error checking using WP functions
		if ($uploaded && !isset($uploaded['error'])) {
			_e('Upload successful!');

			echo "<p><pre>"; 
			rev68_process_gndid( $uploaded['file'] );
			echo "</pre></p>";

			// Delete file after processing
			unlink($uploaded['file']);
		} else {
			echo __('Error') . ': ' . $uploaded['error'];
		}

	}

	*** */

}

// Process XML File
// Used for Bilder-der-Revolte: *** https://offenedaten.de/dataset/ludwig-binder-1968-stiftung-haus-der-geschichte ***
function rev68_process_xml($file) {
	$xml = simplexml_load_file($file);
	
	// Insert xml data into custom post type "photo"
	$data_records = $xml->children();

	foreach ($data_records as $data_record) {
		echo "\n\n";
		
		$new_post = array(
			'post_title'		=> $data_record->title,
			'post_content'		=> $data_record->formal_description,
			'post_status'		=> 'publish',
			'post_type'			=> 'photo',
			'tags_input'		=> (string) $data_record->keywords, 
		);

		$post_id = wp_insert_post( $new_post );
		echo __('Created new photo') . "\n" . 'ID:' . $post_id . '; ' . __('Title') . ': ' . $data_record->title;

		// Build photo url *** to be improved: import photos directly from flickr ***
		// Photos downloaded from Flickr with Evelyn Flickr Downloader *** https://github.com/linpc/Evelyn-Flickr-Downloader ***
		$filename_base = substr($data_record->filename, -12, 11);
		$photo_url = 'http://localhost/fotos-revolte/' . $filename_base . '.jpg'; // imported from local WP install, needs to be adapted

		// Import photo
		rev68_import_photo( $post_id, $photo_url );

		// Store metadata		
		update_post_meta( $post_id, '_signature', (string) $data_record->signature );
		update_post_meta( $post_id, '_filename', (string) $data_record->filename );

		$custom_fields = array( 'formal_description', 'commentary' );
		foreach ($custom_fields as $field) {
			update_post_meta( $post_id, $field, (string) $data_record->{$field} );
		}

		// Add persons to custom taxonomy
		$persons_str = (string) $data_record->persons;		
		
		if (!empty($persons_str)) {
			$persons_arr = explode( ';', $persons_str );
			$term_ids = array();
			foreach ( $persons_arr as $person ) {
				$person = trim( str_replace( ',', ';', $person ) );
				$term = term_exists( $person, 'person' );
				if (empty($term))
					$term = wp_insert_term( $person, 'person' );
				echo "\nPerson: $person\n"; print_r($term);
				$term_ids[] = $term['term_id'];
			}
			echo "\nTerm_IDs: "; print_r($term_ids);			

			$term_ids = array_map( 'intval', $term_ids );
			$term_ids = array_unique( $term_ids );
			wp_set_object_terms( $post_id, $term_ids, 'person' );
		}
		
		// Convert date 
		$date_original = (string) $data_record->date;
		$date_formatted = '19000101';
		$result = preg_match( '/^(\d\d?)\.(\d\d?)\.(\d{4})/', $date_original, $matches );
		if ( $result )
			$date_formatted = $matches[3] . sprintf( "%02d", $matches[2] ) . sprintf( "%02d", $matches[1] );

		update_post_meta( $post_id, 'date', $date_formatted );
		update_post_meta( $post_id, 'date_original', $date_original );

	}

}

// Import photos from given URL 
function rev68_import_photo( $post_id, $photo_url ) {
	$post = get_post( $post_id );
	if( empty( $post ) )
		return false;

	if( !class_exists( 'WP_Http' ) )
	  include_once( ABSPATH . WPINC. '/class-http.php' );

	$photo = new WP_Http();
	$photo = $photo->request( $photo_url );
	if( $photo['response']['code'] != 200 )
		return false;

	$attachment = wp_upload_bits( basename($photo_url), null, $photo['body'], date("Y-m", strtotime( $photo['headers']['last-modified'] ) ) );
	if( !empty( $attachment['error'] ) )
		return false;

	$filetype = wp_check_filetype( basename( $attachment['file'] ), null );

	$postinfo = array(
		'post_mime_type'	=> $filetype['type'],
		'post_title'		=> 'Foto für: ' . $post->post_title,
		'post_content'		=> '',
		'post_status'		=> 'inherit',
	);
	$filename = $attachment['file'];
	$attach_id = wp_insert_attachment( $postinfo, $filename, $post_id );

	if( !function_exists( 'wp_generate_attachment_data' ) )
		require_once(ABSPATH . "wp-admin" . '/includes/image.php');
	$attach_data = wp_generate_attachment_metadata( $attach_id, $filename );
	wp_update_attachment_metadata( $attach_id,  $attach_data );
	// Set featured image for post
	update_post_meta( $post_id, '_thumbnail_id', $attach_id );

	return $attach_id;
}


// Import GND-IDs from CSV file and attach them to photos
/* 
*** deprecated: no taxonomy gnd-id anymore, should be rewritten to use tags instead ***

function rev68_process_gndid($file) {
	global $wpdb;

	$csv = file_get_contents($file);
	$rows = explode("\n", $csv);
	array_shift($rows); // Delete first and last row
	array_pop($rows);
	foreach($rows as $row) {
		$values = explode(',', $row);
		$signature = '2001/03/0275.' . sprintf('%04d', $values[0]);
		$post_id = $wpdb->get_var( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='_signature' AND meta_value='$signature'" );
		if (!$post_id) continue;		
		$gnd_id = $values[1];
		$gnd_name = $values[2];


		$term = term_exists( $gnd_name, 'gnd_id' );
		if (empty($term)) {
			$term = wp_insert_term( $gnd_name, 'gnd_id', array( 'description' => $gnd_id ) );
			echo "Taxonomie: [GND-ID: $gnd_id - Name: $gnd_name] hinzugefügt.\n";
		}
		
		$term_ids = wp_get_object_terms( $post_id, 'gnd_id', array( 'fields' => 'ids' ) );
		if (!term_exists( $gnd_name, 'post_tag' ))    // Check if gnd name is already a tag name		
			$term_ids[] = $term['term_id'];
		$term_ids = array_map( 'intval', $term_ids );
		$term_ids = array_unique( $term_ids );
		wp_set_object_terms( $post_id, $term_ids, 'gnd_id' );
		

		$term = term_exists( $gnd_name, 'post_tag' );
		if (empty($term)) {
			$term = wp_insert_term( $gnd_name, 'post_tag', array( 'description' => $gnd_id ) );
			echo "[GND-ID: $gnd_id - Name: $gnd_name] hinzugefügt.\n";
		} else {
			
		}
		
		$term_ids = wp_get_object_terms( $post_id, 'gnd_id', array( 'fields' => 'ids' ) );
		if (!term_exists( $gnd_name, 'post_tag' ))    // Check if gnd name is already a tag name		
			$term_ids[] = $term['term_id'];
		$term_ids = array_map( 'intval', $term_ids );
		$term_ids = array_unique( $term_ids );
		wp_set_object_terms( $post_id, $term_ids, 'gnd_id' );


		echo "GND-ID $gnd_id ($gnd_name) wurde zum Post $post_id hinzugefügt.\n";
		echo "Post $post_id hat folgende GND-IDs: " . implode(', ', $term_ids) . "\n";

	}

	?><pre><?php print_r($gnd_arr);?></pre>

<?php
}

*** */


// Delete all photos, their metadata and attachments
function rev68_delete_all() {
	global $wpdb;	

	// Get all IDs for post type 'photo'
	$photo_arr = $wpdb->get_results( "SELECT ID FROM $wpdb->posts WHERE post_type='photo'" );
	//echo '<pre>'; print_r($photo_arr); echo '</pre>';
	//return;

	echo '<pre>';
	foreach ($photo_arr as $photo) {
		// Delete attachments
		$attachment_id = get_post_thumbnail_id( $photo->ID );
		echo 'Deleting attachment ' . $attachment_id . ' for post ' . $photo->ID . "\n";
		wp_delete_attachment( $attachment_id );
		// Delete metadata
		$res = $wpdb->delete( 'wp_postmeta', array( 'post_id' => $photo->ID ) );
	}
	// Delete posts
	$result = $wpdb->delete( 'wp_posts', array( 'post_type' => 'photo' ) );

	echo '</pre>';
}


// Make JSON for timeline.js
function rev68_make_timeline_json() {

	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'] . '/timeline.jsonp';
	$content = file_get_contents( plugin_dir_url( __FILE__ ) . 'revolte68_timelinejs_data.php' );
	if (file_put_contents( $file, $content ) ) {
		echo "<p>Datei $file erfolgreich geschrieben.</p>\n" . 
			 "<pre>$content</pre>";
	} else {		
		echo "<p>Fehler beim Schreiben der Datei $file</p>";
	}

}

// Make JSON for timeline.js (silent) ** called when posts are saved **
function rev68_make_timeline_json_silent() {

	$upload_dir = wp_upload_dir();
	$file = $upload_dir['basedir'] . '/timeline.jsonp';
	$content = file_get_contents( plugin_dir_url( __FILE__ ) . 'revolte68_timelinejs_data.php' );
	file_put_contents( $file, $content ); 

}

add_action( 'save_post', 'rev68_make_timeline_json_silent' );


/************************************************************/
/* ADMIN EDIT PAGES *****************************************/
/* **********************************************************/
/************************************************************/

// META BOXES
// *** http://themefoundation.com/wordpress-meta-boxes-guide/ ***

// Adds a meta box to the post editing screen
function rev68_custom_meta() {
    add_meta_box( 'rev68_meta', 'Metadaten', 'rev68_meta_callback', 'photo', 'normal', 'high' );
}

add_action( 'add_meta_boxes', 'rev68_custom_meta' );

function rev68_meta_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'rev68_nonce' );

    $post_meta = get_post_meta( $post->ID );
    ?>
 
	<script type="text/javascript">
	jQuery(document).ready(function($) {
		$('.rev68_datepicker').datepicker({
			dateFormat : 'dd.mm.yy'
		});
	});
	</script>

	<table>
	<tr>
	<td valign="top">
        <label for="meta-date">Datum: </label>
	</td>
	<td>
        <input type="text" class="rev68_datepicker" name="meta-date" id="meta-date" value="<?php echo rev68_format_date( $post_meta['date'][0], 'show' ); ?>" />
    </td>
 	</tr>

	<tr>
	<td valign="top">
		<label for="meta-editor-comment">redaktioneller Kommentar: </label>
	</td>
	<td>
		<textarea name="meta-editor-comment" id="meta-editor-comment" rows="4" cols="80"><?php echo $post_meta['editor-comment'][0]; ?></textarea> 
	</td>
	</tr>	

	<tr>
		<th colspan="2" align="left"><br />Originaldaten aus XML-Datei:</th>
	</tr>
	
	<tr>
		<td>
			<label for="date_original">Datum:</label>
		</td>
		<td>
			<input type="text" readonly name="meta-date_original" id="meta-date_original" value="<?php echo $post_meta['date_original'][0]; ?>" />
		</td>
	</tr>

	<tr>
	<td valign="top">
		<label for="meta-formal_description">Beschreibung: </label>
	</td>
	<td>
		<textarea readonly name="meta-formal_description" id="meta-formal_description" rows="6" cols="80"><?php echo $post_meta['formal_description'][0]; ?></textarea>
	</td>
	</tr>

	<tr>
	<td valign="top">
		<label for="meta-commentary">Kommentar: </label>
	</td>
	<td>
		<textarea readonly name="meta-commentary" id="meta-commentary" rows="6" cols="80"><?php echo $post_meta['commentary'][0]; ?></textarea>
	</td>
	</tr>

	<tr>
	<td valign="top">
		<label for="meta-signature">Signatur: </label>
	</td>
	<td>
		<input type="text" readonly name="meta-signature" id="meta-signature" value="<?php echo $post_meta['_signature'][0]; ?>" />
	</td>
	</tr>

	<tr>
	<td valign="top">
		<label for="meta-filename">Flickr-Link: </label>
	</td>
	<td>
		<span><a href="<?php echo $post_meta['_filename'][0]; ?>" target="_blank"><?php echo $post_meta['_filename'][0]; ?></span>
	</td>
	</tr>
	</table>	

    <?php
}

// Save the custom meta input
function rev68_meta_save( $post_id ) {
 
    // Checks save status
    $is_autosave = wp_is_post_autosave( $post_id );
    $is_revision = wp_is_post_revision( $post_id );
    $is_valid_nonce = ( isset( $_POST[ 'rev68_nonce' ] ) && wp_verify_nonce( $_POST[ 'rev68_nonce' ], basename( __FILE__ ) ) ) ? 'true' : 'false';
 
    // Exits script depending on save status
    if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
        return;
    }
 
    // Checks for input and sanitizes/saves if needed
    if( isset( $_POST[ 'meta-date' ] ) ) {
        update_post_meta( $post_id, 'date', rev68_format_date( $_POST['meta-date'], 'save' ) );
    }
	if( isset( $_POST[ 'meta-editor-comment' ] ) ) {
		update_post_meta( $post_id, 'editor-comment', $_POST['meta-editor-comment'] );
	}
 
}

add_action( 'save_post', 'rev68_meta_save' );


/*****************************************************************************************
** BACKEND FILTERS ***********************************************************************
******************************************************************************************/


// Manage Photo Posts Edit Columns
function rev68_posts_columns($columns){
	return array(
		'cb' 				=> $columns['cb'],
        'title' 			=> $columns['title'],
		'rev68_date'		=> $columns['date'],
		'categories'		=> $columns['categories'],
		'tags'				=> $columns['tags'],
		'taxonomy-person' 	=> $columns['taxonomy-person'],
		'rev68_location'	=> 'Geodaten?',
		'comments'			=> $columns['comments'],
		'rev68_post_thumbs' => __('Thumbnail') // Add featured thumbnail to admin post columns *** http://wpsnipp.com/index.php/functions-php/add-featured-thumbnail-to-admin-post-columns/ ***
	);
}

add_filter('manage_edit-photo_columns', 'rev68_posts_columns', 5);

// Populate photo posts edit custom columns with data
function rev68_posts_custom_columns($column_name, $post_id) {
	
	global $wpdb;

	if ($column_name === 'rev68_post_thumbs') {
        echo the_post_thumbnail( 'thumbnail' );
    } elseif ($column_name === 'rev68_date') {
		$meta = get_post_meta( $post_id );
		echo rev68_format_date( $meta['date'][0], 'show' );
	} elseif ($column_name === 'rev68_location') {
		$has_location = $wpdb->get_var( "SELECT location_id FROM $wpdb->prefix" . "geo_mashup_location_relationships WHERE object_id=$post_id" );
		if ($has_location) echo "ja"; else echo "nein";
	}
}

add_action('manage_photo_posts_custom_column', 'rev68_posts_custom_columns', 5, 2);

// Make custom date field sortable
// *** http://code.tutsplus.com/articles/quick-tip-make-your-custom-column-sortable--wp-25095 ***

// Add date column to list of sortable columns
function rev68_sortable_columns( $columns ) {
	$columns['rev68_date'] = 'date';
	return $columns;
}

add_filter( 'manage_edit-photo_sortable_columns', 'rev68_sortable_columns' );

// Alter $query to order posts by custom date field
function rev68_orderby_date( $query ) {
    if( ! is_admin() )
        return;
 
    $orderby = $query->get( 'orderby' );
 
    if( 'date' == $orderby ) {
        $query->set('meta_key','date');
        $query->set('orderby','meta_value_num');
    }
}

add_action( 'pre_get_posts', 'rev68_orderby_date' );



/*****************************************************************************************
** FRONTEND FILTERS *********************************************************************
******************************************************************************************/


// Filter post queries
function rev68_filter_query( $query ) {

	if (is_admin()) return $query;

	// Show Custom Post Type 'photo' on Front + Tag + Category Page
	/* *** backup ***	
	if ( ( is_home() || is_tag() || is_category ) && $query->is_main_query() && !isset( $_GET['post_type'] ) && !is_page() ) {
		$query->set( 'post_type', array( 'post', 'photo' ) );
		$excluded_categories = array( get_cat_ID( 'aussortiert' ), get_cat_ID( 'Datum unklar' ) ); // Exclude certain categories from main query
		$query->set( 'category__not_in', $excluded_categories );
	}
	*** */

	if ( $query->is_main_query() ) {
		if (!is_page()) $query->set( 'post_type', array( 'post', 'photo' ) );
		$excluded_categories = array( get_cat_ID( 'aussortiert' ), get_cat_ID( 'Datum unklar' ) ); // Exclude certain categories from main query
		$query->set( 'category__not_in', $excluded_categories );

		if ( isset($_GET['datum_sortieren']) ) {
			$query->set( 'meta_key', 'date' );
	 		$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'order', ($_GET['datum_sortieren'] == 'aufsteigend') ? 'asc' : 'desc' );	
		}


	}

    if ($query->is_search()) {
		$query->set('post_type', array('post', 'photo'));
    }

}

add_filter( 'pre_get_posts', 'rev68_filter_query' );


// Convert urls in content to hyperlinks
add_filter( 'the_content', 'make_clickable' );


// Show date before post title
function rev68_filter_title($title) {

	if (get_post_type() != 'photo' || !in_the_loop())
		return $title;

	$title = rev68_format_date( get_post_meta( get_the_ID(), 'date', true ), 'show' ) . ': ' . $title;
	return $title;

}

add_filter( 'the_title', 'rev68_filter_title', 10, 2 );


// Exclude private posts from logged in users in frontend 
// *** http://stackoverflow.com/questions/999933/wordpress-displays-private-posts-to-logged-in-users-how-to-turn-this-function ***

function rev68_no_privates($where) {
    if( is_admin() ) return $where;

    global $wpdb;
    return " $where AND {$wpdb->posts}.post_status != 'private' ";
}

add_filter('posts_where', 'rev68_no_privates');


// Format person names for get_the_terms (for theme functions)
function rev68_get_the_terms_person( $terms ) {
	if ($terms[0]->taxonomy != 'person' || is_admin()) return $terms;

	for ($i=0; $i < count($terms); $i++) {
		$terms[$i]->name = rev68_format_person_name( $terms[$i]->name );
	}

	return $terms;
}

add_filter( 'get_the_terms', 'rev68_get_the_terms_person');


// Format person names for get_term_by (for theme functions)
function rev68_get_term_by_person( $term ) {
	if (is_admin()) return $term;
	$term->name = rev68_format_person_name( $term->name );
	return $term;
}

add_filter( 'get_person', 'rev68_get_term_by_person' );



/*****************************************************************************************/
/* FRONTEND FUNCTIONS ********************************************************************/
/*****************************************************************************************/


function rev68_get_term_list( $post_id, $taxonomy ) {

	$terms = get_the_terms( $post_id, $taxonomy );
	if (!empty($terms)) {
		foreach ($terms as $term) 
			$term_links[] = '<a href="' . esc_url( get_term_link( $term, $taxonomy ) ) . '">' . $term->name . '</a>' . rev68_gnd_link($term);
		$content = implode( ', ', $term_links );
	}

	return $content;
}



/*****************************************************************************************
** SHORTCODES ****************************************************************************
******************************************************************************************/


// [rev68_storymapjs] shortcode
// *** https://storymap.knightlab.com/advanced/ ***
function rev68_storymapjs() {

	$file = plugin_dir_url( __FILE__ ) . 'revolte68_storymapjs_data.php'; // Default dataset: all photos
	if (isset( $_GET['spaziergang'] ) && $_GET['spaziergang'] != 'Alle Fotos') 
		$file .= '?spaziergang=' . $_GET['spaziergang'];

	ob_start();
?>

	<!-- The StoryMap container can go anywhere on the page. Be sure to 
		specify a width and height.  The width can be absolute (in pixels) or 
		relative (in percentage), but the height must be an absolute value.  
		Of course, you can specify width and height with CSS instead -->
	<div id="mapdiv" style="width: 100%; height: 600px;"></div> 

	<!-- Your script tags should be placed before the closing body tag. -->
	<link rel="stylesheet" href="https://cdn.knightlab.com/libs/storymapjs/latest/css/storymap.css">
	<script type="text/javascript" src="https://cdn.knightlab.com/libs/storymapjs/latest/js/storymap-min.js"></script>

	<script>
	// storymap_data can be an URL or a Javascript object
	var storymap_data = '<?php echo $file; ?>'; 

	// certain settings must be passed within a separate options object
	var storymap_options = {
		language:"de"
	};

	var storymap = new VCO.StoryMap('mapdiv', storymap_data, storymap_options);
	window.onresize = function(event) {
		storymap.updateDisplay(); // this isn't automatic
	}          
	</script>

<?php
	return ob_get_clean();

}

add_shortcode( 'rev68_storymapjs', 'rev68_storymapjs' );


// [rev68_walks_list] shortcode
function rev68_walks_list() {
	
	//wp_list_categories( array( 'child_of' => get_cat_ID( 'Spaziergang' ) ) );

	$selected = $_GET['spaziergang'];
	$categories = get_categories( array( 'child_of' => get_cat_ID( 'Spaziergang' ) ) );
	//echo "<pre>"; print_r($categories); echo "</pre>";

	ob_start();

?>
	<form id="rev68_walks" method="get">
	Spaziergang auswählen: 
	<select id="rev68_walks_select" name="spaziergang">
		<option>Alle Fotos</option>
		<?php foreach ($categories as $cat) { ?>
		<option <?php if ($cat->name == $selected) echo 'selected'; ?>><?php echo $cat->name; ?></option> 
		<?php } ?>
	</select>
	<input type="submit" value="Anzeigen">
	</form>
	<br />
<?php						
	return ob_get_clean();
}

add_shortcode( 'rev68_walks_list', 'rev68_walks_list' );


/*****************************************************************************************
** HELPER FUNCTIONS***********************************************************************
******************************************************************************************/


// Format date
function rev68_format_date( $datestr, $action ) {

	if ($action == 'show') {
		$year  = substr( $datestr, 0, 4 );
		$month = substr( $datestr, 4, 2 );
		$day   = substr( $datestr, 6, 2 );
		return "$day.$month.$year";
	} elseif ($action == 'save') {
		$year  = substr( $datestr, 6, 4 );
		$month = substr( $datestr, 3, 2 );
		$day   = substr( $datestr, 0, 2 );
		return "$year$month$day";
	} elseif ($action == 'json') {
		$year  = substr( $datestr, 0, 4 );
		$month = substr( $datestr, 4, 2 );
		$day   = substr( $datestr, 6, 2 );
		return "$year,$month,$day";
	}

}

// Show GND-Link after Tag Title on tag archive page
function rev68_gnd_link($term) {

	$icon_gnd = plugin_dir_url( __FILE__ ) . 'images/icon_gnd.gif';
	// Check for gnd-link
	if ($term->description != '') {
		$link = '<a href="http://d-nb.info/gnd/' . $term->description . '" target="_blank"> <img src="' . $icon_gnd . '" /></a>';
	}

	return $link;
}

// Format Person name (<surname>; <name>) --> (<name> <surname>)
function rev68_format_person_name ($person) {

	list($surname, $name) = explode('; ', $person);
	return "$name $surname";

}

/*
// Convert GND-IDs to tags
function rev68_convert_gndids() {
	global $wpdb;
	// Get all GND-Ids
	$gnd_ids = get_terms( 'gnd_id', array( 'number' => 200, 'hide_empty' => false ) );
?>
	<pre>

	<?php //print_r($gnd_ids); ?>

<?php
	// Loop through them
	foreach ($gnd_ids as $gnd_id) {
		$i++;		
		// Check if current item exists in taxonomies 'person' or 'post_tag'; if so, update, if not insert as post_tag

		echo "$i: $gnd_id->name ($gnd_id->count)\n";

		if ($tag = term_exists( $gnd_id->name, 'post_tag' )) {
			wp_update_term( $tag['term_id'], 'post_tag', array( 'description' => $gnd_id->description ) );
			echo "Term '$gnd_id->name' existiert als Schlagwort: Update der Beschreibung: $gnd_id->description\n";
		} else if ($person = term_exists ( $gnd_id->name, 'person' )) {
			wp_update_term( $person['term_id'], 'person', array( 'description' => $gnd_id->description ) );
			echo "Term '$gnd_id->name' existiert als Person: Update der Beschreibung: $gnd_id->description\n";
		} else {
			$new_tag = wp_insert_term( $gnd_id->name, 'post_tag', array( 'description' => $gnd_id->description ) );
			echo "Term '$gnd_id->name' als Schlagwort neu angelegt. Term-ID: " . $new_tag['term_id'] . " Term-Taxonomy-ID: " . $new_tag['term_taxonomy_id'] . "\n";
			$results = $wpdb->get_results( "SELECT object_id FROM $wpdb->prefix" . "term_relationships WHERE term_taxonomy_id=$gnd_id->term_taxonomy_id", ARRAY_A );
			$func = function($value) { return $value['object_id']; };
			$term_posts = array_map($func, $results);
			$term_posts = array_map('intval', $term_posts);
			echo "Zugeordnete Posts: " . implode(', ', $term_posts) . "\n";
			foreach ($term_posts as $post_id) {
				$post_tag_ids = wp_get_object_terms( $post_id, 'post_tag', array( 'fields' => 'ids' ) );
				$post_tag_ids[] = $new_tag['term_taxonomy_id'];
				$post_tag_ids = array_unique( $post_tag_ids );
				$updated_post_tag_ids = wp_set_object_terms( $post_id, $post_tag_ids, 'post_tag' );
				echo "Post $post_id hat Schlagworte: " . implode( ', ', $post_tag_ids ) . "\n";
			}
		}
	}
?>

	</pre>
<?php
}
*/



/*****************************************************************************************
** DATA EXCTRACTION FUNCTIONS ************************************************************
*****************************************************************************************/

// Display results from medienarchiv68.de for given date
function rev68_scraper_medienarchiv68( $date ) {

	$file = "http://www.medienarchiv68.de/suche.html?suchformular=erweiterte_suche&s_datum_von=$date&s_datum_bis=$date";
	$doc = new DOMDocument();
	@$doc->loadHTMLFile($file);

	// GetElementsByClassName Equivalent in PHP *** http://www.the-art-of-web.com/php/html-xpath-query/ ***
	$xpath = new DOMXpath($doc);
	$resultlist_ul = $xpath->query('//ul[@class="m68-resultlist"]');
	if ($resultlist_ul->length == 0) return;
	$resultlist = $resultlist_ul->item(0)->getElementsByTagName( 'li' );
	?>	
	
	<h4 class="meta-title"><?php echo $resultlist->length; ?> Artikel vom <?php echo $date; ?> auf medienarchiv68.de</h4>

	<ul id="medienarchiv68-resultlist">
	<?php
	$i = 0;
	foreach( $resultlist as $li ) {
		$i++;
		$title = $li->getElementsByTagName( 'h1' )->item(0)->nodeValue;
		$imgsrc = $li->getElementsByTagName( 'img' )->item(0)->attributes->getNamedItem('src')->value;
		$url = $li->getElementsByTagName( 'a' )->item(0)->attributes->getNamedItem('href')->value;
		?>
		<li><a href="http://medienarchiv68.de<?php echo $url; ?>" target="_blank"><?php echo $title; ?></a></li>
	<?php		
	}
	?>
	</ul>
<?php
}





/* Spielereien ***********************************************************************************************************************/

/* 68er Spruch in Admin-Bereich anzeigen - inspired by Hello Dolly Plugin */
function rev68_get_lyric() {

	$lyrics = "Wer zwei Mal mit derselben pennt, gehört schon zum Establishment.
Unter den Talaren herrscht der Muff von 1000 Jahren.
Trau keinem über 30.
Ho ho ho Chi Minh!
He, kommt runter vom Balkon, unterstützt den Vietcong!
Revolution ist machbar, Herr Nachbar.
Alle die jetzt aufgestanden sind sollen sich widersetzen.
Ich geh kaputt – gehst du mit?";

	// Here we split it into lines
	$lyrics = explode( "\n", $lyrics );

	// And then randomly choose a line
	return wptexturize( $lyrics[ mt_rand( 0, count( $lyrics ) - 1 ) ] );
}

function rev68_admin_notice() {
	echo '<p style="float:right; padding-right: 15px; padding-top: 5px; margin:0">' . rev68_get_lyric() . '</p>';
}

add_action( 'admin_notices', 'rev68_admin_notice' );


function rev68_test() {

	global $wpdb, $wp_query;

	if ( isset( $_GET['filter_tag'] ) ) {
		$tags = $_GET['filter_tag'];
	}

	?>

	<pre>
	<?php
		print_r($wp_query);

		

	?>
	</pre>

<?php
}

add_shortcode( 'rev68_test', 'rev68_test' );



/* EOF */
