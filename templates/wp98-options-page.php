<?php 
  global $wpdb, $options_table, $menu_table, $start_menu, $nav_menu;
  
  // Option categories sorted into an array
  foreach ( $wpdb->get_results( "SELECT DISTINCT opt_cat FROM $options_table") as $value )
    $categories[] = $value->opt_cat;

  // Database settings
  $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE opt_cat='start-menu'" );
  $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );

  // Check if the form on the page has been submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Update items from the options table on the database
    foreach ( $categories as $cat ) {
      $data_ref = str_replace( '-', '_', $cat );
      foreach ( ${$data_ref} as $option ) {
        $key = $cat . '-' . $option->opt_name;
        $id = wp98_get_database_entry_id((array)${$data_ref}, $option->opt_name );

        // Currently, only checkbox options are updated
        if ( empty( $_POST[$key] ) ) $wpdb->update( $options_table, array( 'opt_val' => 0 ), array( 'opt_id' => $id ) );
        else $wpdb->update( $options_table, array( 'opt_val' => 1 ), array( 'opt_id' => $id ) );
      }
    }

    // Update menu items on the database
    error_log( print_r( $_POST, true ) );


    // Fetch the database settings again
    //*** Change this later to update this data as the database is updated for each variable without having to download everything again */
    $start_menu = $wpdb->get_results( "SELECT * FROM $options_table WHERE opt_cat='start-menu'" );
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
    $keys = array_column( $array, 'opt_name', 'opt_id');
    $index = array_search( $key,$keys );
    return $index;
  }

  // Iterate through all required options saved on the database and prepare the required HTML
  function wp98_build_start_menu_options_html( $cat, $data ) {
    global $wpdb;

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
    $entry_count = count( (array)$nav_menu ) + 1;
    error_log( print_r('nav id count: ' . $entry_count, true) );
    //error_log(print_r($nav_menu, true));
    ?>
    <tr>
      <th scope="row">
        <label for="menu-items">Menu Items</label>
      </th>
      <td>
        <table id="wp98_nav_menu_options" name="menu-items">
        <!--
        <tr>
          <th class="start-menu-icon-col">Icon</th>
          <th class="start-menu-label-col">Label</th>
          <th class="start-menu-link-col">Link</th>
        </tr>
        -->
    <?php
    // Add menu items previous recorded
    foreach ( $nav_menu as $entry ) {
      ?>
      <tr class="start-menu-row" data-row="<?php echo esc_html($entry->id); ?>">
        <td class="start-menu-drag-icon">
          <div>≡</div>
        </td>
        <td class="start-menu-icon-col">
            <div class="start-menu-no-icon" style="display: none;">No<br>Icon</div>
            <img src="<?php echo esc_html($entry->img); ?>" name="start-menu-img-<?php echo esc_html($entry->id); ?>">
            <div class="start-menu-btn-flex-container">
              <button type="button" class="mediamanager-btn button button-secondary">Change</button>
              <button type="button" class="icon-remove-btn button button-secondary">Remove</button>
            </div>
        </td>
        <td class="start-menu-label-col">
          <input type="text" value="<?php echo esc_html($entry->lbl); ?>" name="start-menu-label-<?php echo esc_html($entry->id); ?>">
        </td>
        <td class="start-menu-link-col">
          <input type="text" value="<?php echo esc_html($entry->link); ?>" name="start-menu-link-<?php echo esc_html($entry->id); ?>">
        </td>
      </tr>
      <?php
    }

    // Add a row with options to add new items
    ?>
          <tr class="start-menu-row" data-row="<?php echo esc_html($entry_count); ?>">
            <td class="start-menu-drag-icon">
              <div>≡</div>
            </td>
            <td class="start-menu-icon-col">
                <div class="start-menu-no-icon">No<br>Icon</div>
                <img src="" name="start-menu-img-<?php echo esc_html($entry_count); ?>" style="display: none;">
                <button type="button" class="mediamanager-btn add-button button button-secondary">Choose<br>Icon</button>
            </td>
            <td class="start-menu-label-col">
              <input type="text" name="start-menu-label-<?php echo esc_html($entry_count); ?>" placeholder="Blog">
            </td>
            <td class="start-menu-link-col">
              <input type="text" name="start-menu-link-<?php echo esc_html($entry_count); ?>" placeholder="www.sitename.com/blog/">
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