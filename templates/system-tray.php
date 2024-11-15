<?php
/** 
* Template Name: System Tram
*/ 
wp_enqueue_style( 'wp98-system-tray', get_theme_file_uri() . '/assets/css/system-tray.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
?>

<div class="copyright">Copyright &copy; <?php echo date(format: 'Y');?></div>
