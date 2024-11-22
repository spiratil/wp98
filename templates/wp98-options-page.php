<?php 
  global $wpdb, $options_table, $menu_table, $start_menu, $nav_menu;

  // Option categories sorted into an array
  foreach ( $wpdb->get_results( "SELECT DISTINCT cat FROM $options_table") as $value )
    $categories[] = $value->cat;

  // Database settings
  $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='start-menu'" );
  $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );

  // Check if the form on the page has been submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log( print_r( "---------------------------------------------------------------\n", true) );
    error_log( print_r( $nav_menu, true) );
    error_log( print_r( $_POST, true) );
    // Update items from the options table on the database
    foreach ( $categories as $cat ) {
      $data_ref = str_replace( '-', '_', $cat );
      foreach ( ${$data_ref} as $option ) {
        $key = $cat . '-' . $option->name;
        $id = wp98_get_database_entry_id((array)${$data_ref}, $option->name );

        // Currently, only checkbox options are updated
        if ( empty( $_POST[$key] ) === true ) $wpdb->update( $options_table, array( 'val' => 0 ), array( 'id' => $id ) );
        else $wpdb->update( $options_table, array( 'val' => 1 ), array( 'id' => $id ) );
      }
    }

    // Update menu items on the database
    foreach ( array_keys($_POST) as $key ) {
      if ( str_contains( $key, 'start-menu-action' ) === true ) {
        $string_array = preg_split( '/-/', $key );
        $menu_id = $string_array[ count( $string_array ) - 1 ];

        if ( $_POST["start-menu-action-$menu_id"] === 'no-change' ) continue;

        /*
        $id;
        if ( $_POST["start-menu-dropdown-$menu_id"] === 'choose' )
        */
          $id = (int)( $_POST["start-menu-page-id-$menu_id"] );
        //else $id = (int)( $_POST["start-menu-dropdown-$menu_id"] );
        var_dump($id);
        if ( $_POST["start-menu-action-$menu_id"] === 'delete' ) {
          error_log( print_r( "DELETE: $id", true ) );
          $wpdb->delete( $menu_table, array( 'id' => $id ) );
          continue;
        }

        if ( $_POST["start-menu-action-$menu_id"] === 'update' ) {
          $ord = (int)( $_POST["start-menu-order-$menu_id"] );
          $label = get_the_title( $id );
          if ( $label === '' ) continue;
          $image = empty( $_POST["start-menu-img-$menu_id"] )
            ? NULL
            : rtrim( sanitize_url( $_POST["start-menu-img-$ord"], array( 'https' ) ), '/' );

          error_log( print_r( "ID: $id ORD: $ord LABEL: $label IMAGE: $image", true ) );

          // Iterate through the current menus stored on the database and check if duplicates exist before entering
          $is_match = false;
          foreach ( $nav_menu as $entry ) {
            if ( $id === (int)$entry->id ) {
              error_log( print_r( "UPDATE: $id", true ) );
              $is_match = true;
              $wpdb->update( $menu_table, array( 'ord' => $ord, 'lbl' => $label, 'img' => $image ), array( 'id' => $id ) );
              break;
            }
          }

          // Add a new entry to the database if the menu item doesn't exist
          if ( $is_match === false ) {
            error_log( print_r( "INSERT: $id", true ) );
            $wpdb->insert( $menu_table, array( 'id' => $id, 'ord' => $ord, 'lbl' => $label, 'img' => $image ) );
          }
          
        }
      }
    }

    // Fetch the database settings again
    //*** Change this later to update this data as the database is updated for each variable without having to download everything again */
    $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='start-menu'" );
    $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );
  }
?>

<!-- Create the form -->
 
<script type="text/javascript">const templateUrl = "<?php echo esc_html( get_template_directory_uri() ); ?>"; </script>
<!-- Create the form -->

<div class="wrap">
  <h1><?php echo esc_html (get_admin_page_title() ); ?></h1>
  <form name="wp98-options" method="post" action="<?php echo esc_html( site_url() . $_SERVER['PHP_SELF'] . '?page=wp98_theme_options' ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <?php 
          // Insert the options section heading and fill in the options
          foreach ($categories as $cat) :
            $heading = ucwords( str_replace( '-', ' ', $cat ) );
            $data_ref = str_replace( '-', '_', $cat );
            $data = ${ $data_ref };
            $rows = count((array)$data) + 1
                    + ($data_ref === 'start_menu' ? 1 : 0); // Add row count for nav menu entries
            ?>

            <tr>
              <td colspan="2">
                <h2><?php echo esc_html( $heading ); ?></h2>
              </td>
              <td rowspan="<?php echo esc_html( $rows ); ?>">Preview box</td>
            </tr>

            <?php
            // Run the function that creates the html for each required setting option
            ('wp98_build_' . $data_ref . '_options_html')($data_ref, $data);
          endforeach;
        ?>
        <tr><td><?php submit_button( 'Save Options' ); ?></td></tr>
      </tbody>
    </table>
  </form>
</div>

<?php
  // Find the id entry in the database for the given name entry
  function wp98_get_database_entry_id( $array, $key ) {
    $keys = array_column( $array, 'name', 'id');
    $index = array_search( $key,$keys );
    return $index;
  }

  // Iterate through all required options saved on the database and prepare the required HTML
  function wp98_build_start_menu_options_html( $cat, $data ) {
    global $wpdb;

    foreach ( $data as $option ) {
      if ( strlen(intval( $option->val) ) === 1 && intval( $option->val ) <= 1 ) {
        wp98_build_checkbox_html(
          $option->name,
          $option->cat,
          $option->lbl,
          $option->desc,
          intval( $option->val )
        );
      }
    }

    if ( $cat === 'start_menu' ) wp98_build_nav_menu_html();
  }

  function wp98_build_system_tray_options_html() {
    
  }

  function wp98_build_checkbox_html( $name, $category, $label, $description, $value ) {
    ?>
    <tr>
      <th scope="row"><?php echo $label; ?></th>
      <td>
        <input id="<?php echo $name; ?>" type="checkbox" name="<?php echo $category . '-' . $name ?>" <?php echo ($value === 1 ? 'checked' : ''); ?>>
        <label for="<?php echo $name; ?>"><?php echo $description; ?></label>
      </td>
    </tr>
    <?php
  }

  function wp98_build_nav_menu_html() {
    global $nav_menu;
    $entry_count = count( (array)$nav_menu );

    // Create an array of all pages available on the site    
    $page_ids = get_all_page_ids();
    $page_array = [];
    foreach ( $page_ids as $page_id ) {
      $key = get_the_title( $page_id );
      $page_array[ $key ] = $page_id;
    }
    ksort( $page_array );
    ?>

    <tr>
      <th scope="row">
        <label for="menu-items">Menu Items</label>
      </th>
      <td>
        <table id="wp98_nav_menu_options" name="menu-items">
    <?php
    // Add menu items previous recorded
    for ($i = 0; $i < $entry_count; $i++ ) {
      foreach ( $nav_menu as $entry ) {
        if ( $i === (int)$entry->ord ) {
          ?>
          <tr class="start-menu-row">
            <input type="hidden" name="start-menu-action-<?php echo $i; ?>" value="no-change">
            <input type="hidden" name="start-menu-order-<?php echo $i; ?>" value="<?php echo $i; ?>">
            <input type="hidden" name="start-menu-page-id-<?php echo $i; ?>" value="<?php echo $entry->id; ?>">
            <input type="hidden" name="start-menu-img-<?php echo esc_html( $i ); ?>" value="<?php echo esc_html( $entry->img ); ?>">
            <td class="start-menu-drag-icon">
              <div>≡</div>
            </td>
            <td class="start-menu-icon-col">
              <?php
                if ( $entry->img === null || $entry->img === '' ) wp98_build_menu_no_image_container();
                else wp98_build_menu_image_container( $entry->img );
              ?>
            </td>
            <td class="start-menu-page-col">
              <select name="start-menu-dropdown-<?php echo $i; ?>">
                <option value="choose"></option>
                <?php foreach( $page_array as $page => $page_id ) : ?>
                  <option value="<?php echo $page_id; ?>" <?php if ( $page_id === $entry->id ) echo 'selected="selected"'; ?>><?php echo $page; ?></option>
                <?php endforeach ?>
              </select>
            </td>
          </tr>
          <?php
        }
      }
    }

    // Add a row with options to add new items
    ?>
          <tr class="start-menu-row">
            <input type="hidden" name="start-menu-action-<?php echo esc_html( $entry_count ); ?>" value="delete">
            <input type="hidden" name="start-menu-order-<?php echo esc_html( $entry_count ); ?>" value="<?php echo esc_html( $entry_count ); ?>">
            <input type="hidden" name="start-menu-page-id-<?php echo esc_html( $entry_count ); ?>" value="">
            <input type="hidden" name="start-menu-img-<?php echo esc_html( $entry_count ); ?>" value="">
            <td class="start-menu-drag-icon">
              <div>≡</div>
            </td>
            <td class="start-menu-icon-col">
              <?php
                wp98_build_menu_no_image_container();
              ?>
            </td>
            <td class="start-menu-page-col">
              <select name="start-menu-dropdown-<?php echo esc_html( $entry_count ); ?>">
                <option value="choose"></option>
                <?php foreach( $page_array as $page => $page_id ) : ?>
                  <option value="<?php echo $page_id; ?>"><?php echo $page; ?></option>
                <?php endforeach ?>
              </select>
            </td>
          </tr>
          <tr>
            <td colspan="4">
              <p class="description">The Start Menu will be created dynamically based on your choices here.</p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
    <?php
  }

  function wp98_build_menu_image_container( $img ) {
    ?>
    <div class="start-menu-no-icon" style="display: none;">No<br>Icon</div>
    <img src="<?php echo esc_html( $img ); ?>">
    <div class="start-menu-btn-flex-container">
      <button type="button" class="mediamanager-btn button button-secondary">Change</button>
      <button type="button" class="icon-remove-btn button button-secondary">Remove</button>
    </div>
    <?php
  }

  function wp98_build_menu_no_image_container( ) {
    ?>
    <div class="start-menu-no-icon">No<br>Icon</div>
    <img src="" style="display: none;">
    <button type="button" class="mediamanager-btn add-button button button-secondary">Choose<br>Icon</button>
    <?php
  }