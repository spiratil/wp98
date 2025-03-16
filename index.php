<?php
/**
 * The template for displaying the main desktop with the taskbar, start menu and required scripts and styling
 *
 */

  // If there is a query string attached to the main url, reload the page without this query
  if ( empty( $_SERVER['QUERY_STRING'] ) === false ) {
    nocache_headers();
    wp_safe_redirect( '/' );
    exit();
  }

  // If there is a specific page request in the URL, reload the page without this part of the address
  if ( $_SERVER['REQUEST_URI'] !== '/' ) {
    echo 'NOOOOOOO';
    setcookie( 'page', "$_SERVER[REQUEST_URI]", time() + 2, '/' );
    wp_safe_redirect( '/' );
    exit();
  }
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <title><?php wp_title( '|', true, 'right' ); ?></title>
    <link rel="stylesheet" href="<?php echo esc_url( get_stylesheet_uri() ); ?>" type="text/css" />
    <?php wp_head(); ?>
  </head>
  <body>
    <div id="wp98-desktop">
      <?php get_template_part( './templates/taskbar' ); ?>
      <?php wp_footer(); ?>
    </div>
  </body>
</html>

<?php // Display the 404 error message prompt on desktop load 
  if ( isset( $_COOKIE['404'] ) ) { ?>
    <script>
      (function($) {
        $(document).ready(function() {
          pm.loadPageError(404, 'The requested page was not found.');
      });
      })(jQuery);
    </script>
  <?php }

  // Load the page inside a page window when the user enters the address directly into the browser address bar
  if ( isset( $_COOKIE['page'] ) ) { ?>
    <script>
      (function($) {
        $(document).ready(function() {
          const id = '<?php echo url_to_postid( "https://$_SERVER[HTTP_HOST]$_COOKIE[page]" ) ?>'; 

          // Create the page container
          const page = $('<div/>',{
            id: `wp98-page-${id}`,
            class: 'wp98-page window',
            'data-id': id
          }).appendTo('body');

          // Fetch the page content
          $.ajax({
            url: `/wp-content/themes/wp98/templates/window.php?id=${id}`,
            success: content => {
              page.html(content);
              pm.addPage(id, 'page');
              op.addTab(id);
            }
          });
        });
      })(jQuery);
  </script>
<?php }
