<?php
  require_once( "../wp-load.php" );

  $id = $args['id'];
  
  // Prepare the page content
  $post = get_post( $id ); 
  $content = wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); 
  echo "<div data-url=\"" . get_permalink( $id ) . "\"></div>" . $content;