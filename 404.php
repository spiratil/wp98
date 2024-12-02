<?php 
/**
 * The template for displaying 404 pages (Not Found) when the user enters an incorrect URL in the address bar
 *
 */

 nocache_headers();
 // Set a cookie to trigger a 404 error message on reload
 setcookie( '404', 1, time() + 2, "/" );
 wp_safe_redirect( '/' );
 exit();
 //get_template_part( './index' );
?>