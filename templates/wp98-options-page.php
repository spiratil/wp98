<?php 
  global $wpdb, $options_table, $menu_table, $start_menu, $nav_menu;
  
  // Option categories sorted into an array
  foreach ( $wpdb->get_results( "SELECT DISTINCT opt_cat FROM $options_table") as $value )
    $categories[] = $value->opt_cat;

  // Database settings
  $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE opt_cat='start-menu'" );
  $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );
  error_log(print_r((array)$start_menu, true));

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    foreach ( $categories as $cat ) {
      $var_name = str_replace( '-', '_', $cat );
      foreach ( ${$var_name} as $option ) {
        $key = $cat . '-' . $option->opt_name;
        $id = wp98_get_database_entry_id((array)${$var_name}, $option->opt_name );
        if ( empty( $_POST[$key] ) ) $wpdb->update( $options_table, array( 'opt_val' => 0 ), array( 'opt_id' => $id ) );
        else $wpdb->update( $options_table, array( 'opt_val' => 1 ), array( 'opt_id' => $id ) );
      }
    }

    $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE opt_cat='start-menu'" );
    $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );
  }
?>

<div class="wrap">
  <h1>WP 98 Options</h1>
  <form name="wp98-options" method="post" action="<?php echo esc_html(site_url() . $_SERVER['PHP_SELF'] . '?page=wp98_theme_options' ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <?php 
          // Insert the options section heading and fill in the options
          foreach ($categories as $cat) {
            $heading = ucwords( str_replace( '-', ' ', $cat ) );
            $var_name = str_replace( '-', '_', $cat );
            $data = ${ $var_name };
            $rows = count((array)$data) + 1
                    + ($var_name === 'start_menu' ? 1 : 0); // Add row count for nav menu entries

            echo '<tr><td colspan="2"><h2>' . $heading . '</h2></td><td rowspan="' . $rows . '">Preview box</td></tr>';
            // Run the function that creates the html for each required setting option
            ('wp98_build_' . $var_name . '_options_html')($var_name, $data);
          }
        ?>
        <tr><td><input type="submit" class="button-primary" value="Save Changes"></td></tr>
      </tbody>
    </table>
  </form>
</div>

<?php
  // Find the id entry in the database for the given name entry
  function wp98_get_database_entry_id( $array, $key ) {
    $keys = array_column( $array, 'opt_name', 'opt_id');
    $index = array_search( $key,$keys );
    return $index;
  }
  function wp98_build_start_menu_options_html( $cat, $data ) {
    global $wpdb;
    //error_log(print_r($data, true));

    foreach ( $data as $option ) {
      if ( strlen(intval( $option->opt_val) ) === 1 && intval( $option->opt_val ) <= 1 ) {
        wp98_build_checkbox_html(
          $option->opt_name,
          $option->opt_cat,
          $option->opt_lbl,
          $option->opt_desc,
          intval( $option->opt_val )
        );
      }
    }

    if ( $cat === 'start_menu' ) wp98_build_nav_menu_html();
  }

  function wp98_build_system_tray_options_html() {
    //wp98_build_checkbox_html( 'Show System Tray', 'show-system-tray', '', 'wp98_system_tray_show' );
  }

  function wp98_build_checkbox_html( $name, $category, $label, $description, $value ) {
    echo "<tr>
      <th scope=\"row\">$label</th>
      <td> <input id=\"$name\" type=\"checkbox\" name=\"$category-$name\" " . ($value === 1 ? "checked>" : ">") . "<label for=\"$name\">$description</label></td>
    </tr>";
  }

  function wp98_build_nav_menu_html() {
    global $nav_menu;
    $entry_count = count( (array)$nav_menu );
    //error_log(print_r($nav_menu, true));
    echo '<tr>
      <th scope="row"> <label for="menu-items">Menu Items</label> </th>
      <td><table id="wp98_nav_menu_options" name="menu-items">
      <tr> <th class="start-menu-icon-col">Icon</th> <th class="start-menu-label-col">Label</th> <th class="start-menu-link-col">Link</th> </tr>';
    
      // Add menu items previous recorded
    foreach ( $nav_menu as $entry ) {
      echo '<tr><td class="start-menu-icon-col"><img src="' . $entry->img . '" name="start-menu-nav-img-' . $entry->id . '"></td>
        <td class="start-menu-label-col"><input type="text" value="' . $entry->lbl . '" name="start-menu-nav-label-' . $entry->id . '"></td>
        <td class="start-menu-link-col"><input type="text" value="' . $entry->link . '" name="start-menu-nav-link-' . $entry->id . '"></td></tr>';
    }
    echo '<tr><td class="start-menu-icon-col"><img src="" name="start-menu-nav-img-' . $entry_count . '"><button>Choose</button></td>
        <td class="start-menu-label-col"><input type="text" name="start-menu-nav-label-' . $entry_count . '"></td>
        <td class="start-menu-link-col"><input type="text" name="start-menu-nav-link-' . $entry_count . '"></td></tr>';
    echo "<tr><td colspan=\"3\"><p class=\"description\">The Start Menu will be created dynamically based on your choices here.</p></td></tr></table></td>
      
    </tr>";
  }