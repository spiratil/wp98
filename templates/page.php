<?php
  require_once("../../../../wp-load.php");
  $id = absint( $_GET[ "id" ] );

  // Boolean indicating if the page container is to be built
  $build_page;
  if ( empty( $_GET[ 'buildpage' ] ) ) $build_page = false;
  else $build_page = absint( $_GET[ "buildpage" ] ) === 1 ? true : false;

  // Page content
  global $menu_table;
  $post = get_post( $id ); 
  $url = get_permalink( $post );
  $imgUrl = $wpdb->get_var( "SELECT img FROM $menu_table WHERE id=$id" );
  $title = wp_kses_post( apply_filters( 'the_title', $post->post_title ) ); 
  $content = wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); 

  // Only build the new page if launching from the start menu
  if ( $build_page === true ) : ?>
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
  <?php endif;

  // Prepare the page content
  echo $content;

  // Finish building the page container if required
  if ( $build_page === true ) : ?>
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
  <?php endif;



