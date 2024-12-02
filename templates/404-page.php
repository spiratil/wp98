<?php 
  require_once("../../../../wp-load.php");
  $status = absint( $_GET[ "status" ] );
  $message = sanitize_text_field( $_GET[ "message" ] );
  
  // Check if the request was an AJAX request
  if(strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest') : ?>
    <div class="wp98-error-window full-screen-wrapper">
      <div class="window">
        <div class="title-bar">
          <div class="title-bar-text"><?php echo $status; ?> Error</div>
          <div class="title-bar-controls">
            <button aria-label="Close" disabled></button>
          </div>
        </div>
        <div class="window-body">
          <img class="error-icon" src="<?php echo get_stylesheet_directory_uri() . '/assets/images/icons/msg_error-0.png'; ?>">
          <p><?php if ( (int)$status === 404 ) echo $message; ?></p>
          <div class="flex-break"></div>
          <section class="field-row">
            <button class="close-button">OK</button>
          </section>
        </div>
      </div>
    </div>
  
  
  <?php endif;