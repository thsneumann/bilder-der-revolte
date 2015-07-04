<?php
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

// Get all posts
$posts = $wpdb->get_results('SELECT * FROM '. $wpdb->prefix.'posts');

var_dump($posts);

?>
