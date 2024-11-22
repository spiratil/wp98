<!DOCTYPE html>
<html>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <!-- Loads a global JS constant that allows other scripts access to server file paths -->
    <script type="text/javascript">const templateUrl = "<?php echo esc_html( get_bloginfo( "template_url" ) ); ?>" </script>
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" type="text/css" />
    <?php wp_head(); ?>
  </head>
  <body>
    <div id="wp98-desktop">
      <?php
        if ( is_front_page() ) {
          get_template_part( './templates/taskbar' );
        }
      ?>
      
      <?php wp_footer(); ?>
    </div>
  </body>
</html>