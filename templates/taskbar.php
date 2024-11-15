<?php
/** 
* Template Name: Taskbar
*/ 

  wp_enqueue_style( 'wp98-taskbar', get_theme_file_uri() . '/assets/css/taskbar.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
?>

<div id="wp98-taskbar">
  <?php
    get_template_part( './templates/start-button' );
    get_template_part( './templates/open-programs' );
    get_template_part( './templates/system-tray' );
  ?>
</div>