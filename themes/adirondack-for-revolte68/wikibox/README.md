# wikibox
create useful infoboxes based on GND-IDs and enriched with data from Wikipedia!

See the demo at [www.bilder-der-revolte.de](http://bilder-der-revolte.de/photo/rainer-langhans-flugblaetter-verteilend-2/)

![A simple Infobx for Rainer Langhans at bilder-der-revolte.de][screenshot]

[screenshot]: screenshot.png "A simple Infobx for Rainer Langhans at bilder-der-revolte.de" width="1054px" height="841px"

## How it works
After page load wikibox looks for img-elements wrapped by an `a`-Element. It attaches a css and a data attribute:
	
	<a href="http://d-nb.info/gnd/4058171-8" class="wikibox-gndlink" data-gnd="4058171-8"> 
	 <img src="http://bilder-der-revolte.de/wp-content/plugins/revolte68/images/icon_gnd.gif">
	</a>
	
If the user clicks on the `icon_gnd.gif` wikibox.js makes an AJAX request to a gndProxy instance that gathers data from DNB, Wikipedia and Wikidata. The results are parsed and according html-elements are created for your wikibox.


## Requirements
 - jQuery 
 - a running [gndProxy](https://github.com/jhercher/gndProxy) instance

## Installation 

 - download the sources
 - embedd wikibox.css into the `html-header`
 - embedd wikibox.js into your `html-footer`
 - you need to configure wikibox.js to let jQuery search at the correct location in your html-page.
 
## Notes & Meta
This script was developed with ♥ as showcase for [Coding da Vinci](http://codingdavinci.de/) cultural hackathon. Unless it works there is much to improve here to make it more generic and stable. Pull requests are welcome!


