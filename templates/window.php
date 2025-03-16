<?php
  require_once( "../../../../wp-load.php" );
  $id = absint( $_GET[ "id" ] );

  // Page content
  global $menu_table;
  $post = get_post( $id ); 
  $content = wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); 
  $url = get_permalink( $post );
  $imgUrl = $wpdb->get_var( "SELECT img FROM $menu_table WHERE id=$id" );
  $title = wp_kses_post( apply_filters( 'the_title', $post->post_title ) );
  ?>
  
  <div class="title-bar" data-id="<?php echo $id; ?>">
    <div class="title-bar-text">
      <img src="<?php echo $imgUrl; ?>">
      <span><?php echo esc_html( $title ); ?></span>
    </div>
    <div class="title-bar-controls">
      <button aria-label="Minimize"></button>
      <button aria-label="Maximize"></button>
      <button aria-label="Close"></button>
    </div>
  </div>
  <div class="navigation-bar">
    <button class="page-navigation navigation-back navigation-disabled">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/back-greyscale.png'; ?>" alt="Back">
    </button>
    <button class="page-navigation navigation-forward navigation-disabled">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/forward-greyscale.png'; ?>" alt="Forward">
    </button>
    <button class="page-navigation navigation-cancel navigation-disabled">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/cancel-greyscale.png'; ?>" alt="Stop">
    </button>
    <button class="page-navigation navigation-refresh">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/refresh.png'; ?>" alt="Refresh">
    </button>
    <button class="page-navigation navigation-home">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/home.png'; ?>" alt="Home">
    </button>
    <div class="address-bar">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/icons/html2-1.png'; ?>">
      <span><?php echo $url; ?></span>
    </div>
  </div>
  <div class="window-body wp98-content">
    <?php // Prepare the page content
    echo $content; ?>
  </div>
  <div class="status-bar">
    <div id="url-display-<?php echo $id; ?>" class="status-bar-field url-display">
      <img src="">
      <span></span>
    </div>
    <div class="status-bar-field"></div>
    <div class="status-bar-field"></div>
    <div class="status-bar-field">
      <img src="<?php echo get_stylesheet_directory_uri() . '/assets/images/icons/world-0.png' ?>">
      <span>Internet</span>
      <img id="corner-grabber-<?php echo $id; ?>" class="corner-grabber" src="<?php echo get_stylesheet_directory_uri() . '/assets/images/navigation/corner-grab.png'; ?>" data-id="<?php echo $id; ?>">
    </div>
  </div>


