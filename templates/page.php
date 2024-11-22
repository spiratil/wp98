<?php
  require_once("../../../../wp-load.php");
  $id = absint( (int)$_GET[ "id" ] );
  $post = get_post( $id ); 
  $url = get_permalink( $post );
  $title = wp_kses_post( apply_filters( 'the_title', $post->post_title ) ); 
  $content = wp_kses_post( apply_filters( 'the_content', $post->post_content ) ); 
?>

  
  <div class="title-bar">
    <div class="title-bar-text"><?php echo esc_html( $title ); ?></div>
    <div class="title-bar-controls">
      <button aria-label="Minimize"></button>
      <button aria-label="Maximize"></button>
      <button aria-label="Close"></button>
    </div>
  </div>
  <div class="window-body wp98-content">
    <?php echo $content; ?>
  </div>
  <!--
  <iframe src="<?php echo $url; ?>" title="<?php echo $title; ?>" class="window-body wp98-content">
    <?php //echo $content; ?>
  </iframe>
  -->
  <div class="status-bar">
    <p class="status-bar-field">Press F1 for help</p>
    <p class="status-bar-field">Slide 1</p>
    <p class="status-bar-field">CPU Usage: 14%</p>
  </div>
