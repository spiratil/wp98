<?php
  require_once("../../../../wp-load.php");
  $id = absint( $_GET[ "id" ] );

  // Boolean indicating if the page container is to be built
  $build_page;
  if ( empty( $_GET[ 'buildpage' ] ) ) $build_page = false;
  else $build_page = absint( $_GET[ "buildpage" ] ) === 1 ? true : false;

  // Page content
  $post = get_post( $id ); 
  $url = get_permalink( $post );
  $title = wp_kses_post( apply_filters( 'the_title', $post->post_title ) ); 
  $content = wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); 

  // Only build the new page if launching from the start menu
  if ( $build_page === true ) : ?>
    <div class="title-bar">
      <div class="title-bar-text"><?php echo esc_html( $title ); ?></div>
      <div class="title-bar-controls">
        <button aria-label="Minimize"></button>
        <button aria-label="Maximize"></button>
        <button aria-label="Close"></button>
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
      <p class="status-bar-field">Press F1 for help</p>
      <p class="status-bar-field">Slide 1</p>
      <p class="status-bar-field">CPU Usage: 14%</p>
    </div>
  <?php endif;



