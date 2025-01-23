<?php
/** 
* Template Name: Taskbar
*/ 

  wp_enqueue_style( 'wp98-taskbar', get_theme_file_uri() . '/assets/css/taskbar.css', array(), wp_get_theme()->get( 'Version' ), 'all' );

  global $wpdb, $options_table;
?>

<div id="wp98-taskbar">
  <?php
    get_template_part( './templates/start-button' );
    if ( intval( $wpdb->get_var( "SELECT val FROM $options_table WHERE name='open-windows'" ) ) === 1 )
      get_template_part( './templates/open-programs' );
    get_template_part( './templates/system-tray' );
  ?>
</div>