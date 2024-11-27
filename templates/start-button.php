<?php
/** 
* Template Name: Start Button and Start Menu
*/ 

wp_enqueue_style( 'wp98-start-button', get_theme_file_uri() . '/assets/css/start-button.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
wp_enqueue_script( 'wp98-start-button', get_parent_theme_file_uri( '/assets/js/start-button.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );

global $wpdb, $options_table, $menu_table, $start_menu, $nav_menu;

?>

<button id="wp98-start-button"><?php echo '<img src="' . get_stylesheet_directory_uri() . '/assets/images/main/start-button.png">' ?></button>
    
<div id="wp98-start-menu" class="window">
  <?php 
    if ( intval( $wpdb->get_var( "SELECT val FROM $options_table WHERE name='show-title-bar'" ) ) === 1 ) : ?>
      <div class="title-bar">
        <div class="title-bar-text">
          <?php
            if ( intval( $wpdb->get_var( "SELECT val FROM $options_table WHERE name='show-site-logo'" ) ) === 1 ) :
              if ( has_site_icon() ) {
                echo '<img src="' . get_site_icon_url(32) . '" alt="' . get_bloginfo('name') . ' Logo">';
              }
            endif;
            if ( intval( $wpdb->get_var( "SELECT val FROM $options_table WHERE name='show-site-title'" ) ) === 1 )
              echo '<h1>' . get_bloginfo('name') . '</h1>';
          ?>
        </div>
      </div>
    <?php endif; ?>
  <?php 
  wp98_insert_menu();

  function wp98_insert_menu(){
    global $wpdb, $menu_table;
    $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );
    ?>
    <nav class="window-body">
      <ul class="nav-list">
        <?php
        foreach ( $nav_menu as $entry ) : ?>
          <li id="wp98-menu-<?php echo $entry->id ?>" class="page-item" data-id="<?php echo $entry->id ?>">
            <img src="<?php echo sanitize_url( $entry->img ); ?>">
            <span><?php echo sanitize_text_field( $entry->lbl ); ?></span>
          </li>
        <?php endforeach; ?>
      </ul>
    </nav>
  <?php } ?>
</div>