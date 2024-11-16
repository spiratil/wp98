<?php
/** 
* Template Name: Taskbar
*/ 

wp_enqueue_style( 'wp98-start-button', get_theme_file_uri() . '/assets/css/start-button.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
wp_enqueue_script( 'wp98-start-button', get_parent_theme_file_uri( '/assets/js/start-button.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );

global $wpdb, $options_table, $menu_table, $start_menu, $nav_menu;

?>

<button id="wp98-start-button"><?php echo '<img src="' . get_stylesheet_directory_uri() . '/assets/images/main/start-button.png">' ?></button>
    
<div id="wp98-start-menu" class="window">
  <?php 
    if ( intval( $wpdb->get_var( "SELECT opt_val FROM $options_table WHERE opt_name='show-title-bar'" ) ) === 1 ) : ?>
      <div class="title-bar">
        <div class="title-bar-text">
          <?php
            if ( intval( $wpdb->get_var( "SELECT opt_val FROM $options_table WHERE opt_name='show-site-logo'" ) ) === 1 ) :
              if ( has_site_icon() ) {
                echo '<img src="' . get_site_icon_url(32) . '" alt="' . get_bloginfo('name') . ' Logo">';
              }
            endif;
            if ( intval( $wpdb->get_var( "SELECT opt_val FROM $options_table WHERE opt_name='show-site-title'" ) ) === 1 )
              echo '<h1>' . get_bloginfo('name') . '</h1>';
          ?>
        </div>
      </div>
    <?php endif; ?>
  <?php 
  add_filter( 'wp_nav_menu_objects', 'update_menu_link', 10, 2 );

  function update_menu_link($items){
    var_dump( $items );
  
    //look through the menu for items with Label "Link Title"
        foreach($items as $item){
  
            if($item->title === "Blog"){ // this is the link label your searching for
                $item->url = "http://newlink.com"; //this is the new link
            }
        }
        return $items;
    }
    wp_nav_menu( array ( 
      'menu' => 'wp98-menu',
      'container' => 'nav',
      'menu_class' => 'window-body',
      'theme_location' => 'start-menu',
      'link_before' => '<img src="https://wordpress.ddev.site/wp-content/themes/wp98/assets/images/icons/camera3-4.png">'
    ) );
  ?>
</div>