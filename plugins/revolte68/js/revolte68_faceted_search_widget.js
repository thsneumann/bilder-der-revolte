function rev68_facets_redirect() {
	
	var url = document.getElementById( "rev68_faceted_search_submit" ).href + '?';

	// Order by date
	var orderbydate = '';
	if (document.getElementById( "rev68_orderbydate_asc" ).checked) 
		orderbydate = 'aufsteigend';
	if (document.getElementById( "rev68_orderbydate_desc" ).checked) 
		orderbydate = 'absteigend';
		

	// Tags  *** to be improved: loop through array of taxonomies ***
	var input_tags = document.getElementsByName( "post_tag[]" );
	var tags_checked = [];
	for ( var i = 0; input_tags[i]; i++ ) {
		if ( input_tags[i].checked ) {
			tags_checked.push( input_tags[i].value );
		}
	}

	// Persons
	var input_persons = document.getElementsByName( "person[]" );
	var persons_checked = [];
	for ( var i = 0; input_persons[i]; i++ ) {
		if ( input_persons[i].checked ) {
			persons_checked.push( input_persons[i].value );
		}
	}
	
	if (tags_checked.length > 0) url += 'tag=' + tags_checked.join( ',' ) + '&';
	if (persons_checked.length > 0) url += 'person=' + persons_checked.join( ',' ) + '&';    // OR 
	if (orderbydate != '') url += 'datum_sortieren=' + orderbydate; 

	window.location.href = url;
	return false;
}

// Make checkboxes for order by date filter mutually exclusive
function rev68_facets_date_checkboxes( orderbydate ) {
	
	if (orderbydate == 'asc') 
		document.getElementById( "rev68_orderbydate_desc" ).checked = false;
	else
		document.getElementById( "rev68_orderbydate_asc"  ).checked = false;
	
}
