<?php
/** 
* Template Name: System Tram
*/ 
wp_enqueue_style( 'wp98-system-tray', get_theme_file_uri() . '/assets/css/system-tray.css', array(), wp_get_theme()->get( 'Version' ), 'all' );

global $wpdb, $options_table;

if ( intval( $wpdb->get_var( "SELECT val FROM $options_table WHERE name='copyright'" ) ) === 1 ) : ?>
  <div class="copyright">Copyright &copy; <?php echo date(format: 'Y');?></div>
<?php endif; 
