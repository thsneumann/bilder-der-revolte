<?php
/***************************************
/* Revolte68 Faceted Search Widget *****
/**************************************/

class Rev68_Faceted_Search_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	function __construct() {
		parent::__construct(
			'rev68_faceted_search_widget', // Base ID
			__( 'Rev68 Faceted Search', 'text_domain' ), // Name
			array( 'description' => __( 'Faceted Search for Revolte68', 'text_domain' ), ) // Args
		);

	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ). $args['after_title'];
		}

		$this->display();

		echo $args['after_widget'];
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		$title = ! empty( $instance['title'] ) ? $instance['title'] : __( '', 'text_domain' );
		?>
		<p>
		<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
		<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<?php 
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

		return $instance;
	}

	/**
      * Functions
    */

	// List taxonomies
	public function display() {
		global $wp_query;
?>
		<span class="button">
		<?php echo '<a id="rev68_faceted_search_submit" href="'. $_SERVER['PHP_SELF'] . '" onClick="return rev68_facets_redirect()">Filtern</a><br /><br />';	?>
		</span>
		<form id="rev68_faceted_search_form" method="GET">
<?php /*		
		<input id="rev68_faceted_search_submit" type="submit" value="Filtern" onClick="return rev68_facets_redirect()"><br /><br />	
*/ ?>

<?php
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
				$terms_selected[ $tax->name ][] = get_term_by( 'slug', $slug, $tax->name )->name;
			}

		}

		if (!empty( $terms_selected )) {
			echo "<h3>Auswahl</h3>";
			foreach ($taxonomies as $tax) {
				if (empty( $terms_selected[ $tax->name ] )) continue;
				echo $tax->label . ': ' . implode( ', ', $terms_selected[ $tax->name ] ) . '<br />';
			}
			echo "<br /><br />";
		}

		// Order by Date filter 
		if ($_GET['datum_sortieren'] == 'aufsteigend') $orderbydate_checked['asc'] = 'checked';  // *** can this be done in a more elegant way? ***
		if ($_GET['datum_sortieren'] == 'absteigend') $orderbydate_checked['desc'] = 'checked';
?>

<?php
		ob_start();
?>

		<h3>Nach Datum sortieren</h3>	
		<input type="checkbox" id="rev68_orderbydate_asc" name="datum_sortieren" value="aufsteigend" <?php echo $orderbydate_checked['asc']; ?> onclick="rev68_facets_date_checkboxes('asc')"> Aufsteigend<br />
		<input type="checkbox" id="rev68_orderbydate_desc" name="datum_sortieren" value="absteigend" <?php echo $orderbydate_checked['desc']; ?> onclick="rev68_facets_date_checkboxes('desc')"> Absteigend<br /><br />

<?php
		// Taxonomies filter
		// Uses Shortcode Ultimate Plugin

		foreach ($taxonomies as $tax) {
			$terms = get_terms( $tax->name, array( 'orderby' => 'name',   	// Order terms by name
												  'order'	=> 'ASC' 
											)
							  );

			echo "<h3>" . __( $tax->label ) . "</h3>";
			echo "[su_tabs]";

			$firstTerm = $terms[0];			
			$currentLetter = substr( $firstTerm->name, 0, 1 );
			echo '[su_tab title="' . htmlspecialchars(strtoupper($currentLetter)) . '"]';

			foreach ($terms as $term) {
				$firstLetter = substr($term->name, 0, 1);
				if (strcasecmp( $firstLetter, $currentLetter ) ) {   // case insensitive comparison
					echo '[/su_tab]' . 
					     '[su_tab title="' . strtoupper($firstLetter) . '"]';
					$currentLetter = $firstLetter;
				}

				$checked = '';
				if ( @in_array( $term->slug, $terms_selected_slugs[ $tax->name ] ) ) $checked = 'checked';

				if ( $tax->name == 'person' ) $term->name = rev68_format_person_name( $term->name ); // *** to be improved: hook into get_term ***
				echo '<input type="checkbox" name="' . $tax->name . '[]" value="' . $term->slug . '" ' . $checked . ' /> ' . $term->name . '<br />';
			}

			echo "[/su_tab][/su_tabs]";

		}

		$content = ob_get_clean();
		echo do_shortcode( $content );

		echo '</form>';
	}


} // class Rev68_Faceted_Search_Widget

add_action('widgets_init',
     create_function('', 'return register_widget("Rev68_Faceted_Search_Widget");')
);

// Embed Javascript
function rev68_enqueue_scripts() {
	wp_register_script( 'rev68_faceted_search_widget', plugins_url( '/js/revolte68_faceted_search_widget.js', __FILE__ ) );
	wp_enqueue_script( 'rev68_faceted_search_widget' );
}

add_action( 'wp_enqueue_scripts', 'rev68_enqueue_scripts' );

?>
