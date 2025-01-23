<?php 
  // Database tables
  global $wpdb, $options_table, $menu_table;
  // Option categories in database tables
  global $general_opts, $taskbar_opts, $styling_opts, $danger_opts, $nav_menu;

  // Option categories sorted into an array
  foreach ( $wpdb->get_results( "SELECT DISTINCT cat FROM $options_table") as $value )
  $categories[] = $value->cat;

  // Database settings
  $general_opts = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='general'" );
  $taskbar_opts = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='taskbar'" );
  $styling_opts = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='styling'" );
  $danger_opts = $wpdb->get_results( "SELECT * FROM $options_table WHERE cat='danger'" );
  $nav_menu = $wpdb->get_results( "SELECT * FROM $menu_table" );

  // Check if the form on the page has been submitted
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    error_log( print_r( $_POST, true) );
    error_log( print_r( $nav_menu, true) );

    foreach ( array_keys($_POST) as $key ) {
      // Save General settings
      if ( str_starts_with( $key, 'general' ) === true ) {
        $option_name = substr( $key, 8 );
        // If the option to be saved is a colour, remove the #
        $option_value;
        if ( str_starts_with( $_POST[$key], '#' ) )
          $option_value = substr( sanitize_hex_color( $_POST[$key] ), 1 );
        else $option_value = '';
        foreach ( $general_opts as $setting ) {
          // Save the option if it is found in the database and different from the currently save value
          if ( $setting->name == $option_name && $option_value != $setting->val) {
            $setting->val = $option_value;
            $wpdb->update( $options_table, array( 'val' => $option_value ), array( 'id' => $setting->id ) );
            break;
          }
        }
      }

      // Save Taskbar settings
      if ( str_starts_with( $key, 'taskbar' ) === true ) {
        $option_name = substr( $key, 8 );
        // If the option to be saved is a colour, remove the #
        $option_value = absint( $_POST[$key] );
        foreach ( $taskbar_opts as $setting ) {
          // Save the option if it is found in the database and different from the currently save value
          if ( $setting->name == $option_name && $option_value != $setting->val) {
            $setting->val = $option_value;
            $wpdb->update( $options_table, array( 'val' => $option_value ), array( 'id' => $setting->id ) );
            break;
          }
        }
      }
    }

    // Save the custome CSS settings to file
    saveCssSettings($general_opts);

    $menu_count = 0; $new_menu = [];
    // Save Menu settings
    for ( $i = 0; $i < count( array_keys( $_POST ) ); $i++ ) {
      if ( array_key_exists( 'menu-action-' . $i, $_POST ) && $_POST['menu-action-' . $i] != 'delete' ) {
        $new_menu[$menu_count] = new stdClass();
        $new_menu[$menu_count]->ord = absint( $_POST['menu-order-' . $menu_count] );
        $new_menu[$menu_count]->id = absint( $_POST['menu-page-id-' . $menu_count] );
        $new_menu[$menu_count]->img = sanitize_url( $_POST['menu-img-' . $menu_count] );
        $new_menu[$menu_count]->lbl = sanitize_text_field( $_POST['menu-name-' . $menu_count] );
        $new_menu[$menu_count]->head = sanitize_file_name( $_POST['menu-head-' . $menu_count] );
        $new_menu[$menu_count]->foot = sanitize_file_name( $_POST['menu-foot-' . $menu_count] );
        $menu_count++;
      }  
    }
    $menu_count--;

    error_log( print_r($new_menu, true));

    // Delete the old menu entries
    $wpdb->query("TRUNCATE TABLE $menu_table");

    // Add the menu items
    foreach( $new_menu as $new ) {
      $wpdb->insert( $menu_table,
        array( 'ord' => $new->ord,
          'id' => $new->id,
          'lbl' => $new->lbl,  
          'img' => $new->img,
          'head' => $new->head,
          'foot' => $new->foot ) );
    }

    $nav_menu = $new_menu;
  }

  // Save the custom CSS settings to file
  function saveCssSettings( $general_opts ) {
    // Create new contents to add to the settings.css file
    $fileContent = "/* CSS user selected settings */\n:root {";
    // Save each CSS property
    foreach ( $general_opts as $setting ) {
      switch ($setting->name) {
        case 'colour-main':
          $fileContent .= "--wp98--color--dialog-blue: #$setting->val;";
          break;
        case 'colour-second':
          $fileContent .= "--wp98--color--dialog-blue-light: #$setting->val;";
          break;
        case 'colour-desktop':
          $fileContent .= "--wp98--color--desktop: #$setting->val;";
          break;
      }
    }
    $fileContent .= '}';

    // Save to file
    file_put_contents(
      get_template_directory() . '/assets/css/settings.css',
      $fileContent );
  }
?>

<!-- Create the form -->
<div class="wrap">
  <h1><?php echo esc_html (get_admin_page_title() ); ?></h1>
  <form name="wp98-options" method="post" action="<?php echo esc_html( site_url() . $_SERVER['PHP_SELF'] . '?page=wp98_theme_options' ); ?>">
    <table class="form-table" role="presentation">
      <tbody>
        <?php // Create the General settings section ?>
        <tr>
          <td colspan="2"><h2>General</h2></td>
          <td rowspan="<?php echo esc_html( count((array)$general_opts) + 1 ); ?>">Preview box</td>
        </tr>
        <?php  wp98_build_general_options_html( $categories, $general_opts );
        // Create the Taskbar settings section ?>   
        <tr>
          <td colspan="2"><h2>Taskbar</h2></td>
          <td rowspan="<?php echo esc_html( count((array)$taskbar_opts) + 1 ); ?>">Preview box</td>
        </tr>
        <?php
          wp98_build_taskbar_options_html( $categories, $taskbar_opts );
          wp98_build_nav_menu_html();
          // Create the Custom Styling settings section
        ?>
        <tr>
          <td colspan="2"><h2>Custom Styling</h2></td>
        </tr>
        <?php  wp98_build_styling_options_html( $styling_opts );
        // Create the Danger Zone settings section ?>
        <tr>
          <td colspan="2"><h2>Danger Zone</h2></td>
        </tr>
        <?php  wp98_build_danger_options_html( $danger_opts ); ?>
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
  function wp98_build_general_options_html( $cat, $data ) {
    global $wpdb;

    foreach ( $data as $option ) {
      if ( strlen( $option->val) === 6 ) {
        wp98_build_color_html(
          $option->name,
          $option->cat,
          $option->lbl,
          $option->desc,
          $option->val 
        );
      }
    }
  }

  function wp98_build_taskbar_options_html( $cat, $data ) {
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

  function wp98_build_styling_options_html( $data ) {
    global $wpdb; ?>

    <tr id="wp98-custom-styling">
      <th>
        <select id="wp98-style-selector">
          <option value="general">General</option>
          <span>The general styling options will be applied to all pages that don't have custom styling applied.</span>
          <?php foreach( $data as $page ) : ?>
            <option value="<?php echo sanitize_key( $page->name ); ?>"><?php echo sanitize_key( $page->name ); ?></option>
          <?php endforeach ?>
        </select><br><br>
        <button id="wp98-style-save-button" class="button button-secondary" type="button">Save</button>
      </th>
      <td>
        <span>Pages opened through the Start Menu inside a window can be given separate styles from the rest of the website.<br>This allows the loaded page to appear as though it is being viewed as a site inside Internet Explorer within WP 98.
        </span><br><br>
        <textarea id="wp98-general-styles" class="styles-textarea">
          <?php echo file_get_contents( get_template_directory() . '/page-templates/styles/general.css' ); ?>
        </textarea> 
        <?php foreach( $data as $page ) : ?>
          <input id="wp98-styles-<?php echo sanitize_key( $page->name ); ?>-activate" type="checkbox" name="wp98-styles-<?php echo sanitize_key( $page->name ); ?>-code" <?php echo ($value === 1 ? 'checked' : ''); ?>>
          <textarea id="wp98-<?php echo sanitize_key( $page->name ); ?>-styles" class="styles-textarea" name="<?php echo sanitize_key( $page->name ); ?>-styles" value="<?php echo sanitize_key( $page->val ); ?>"></textarea> 
        <?php endforeach ?>
        <br><span id="wp98-styles-info">General stylings will be applied to all pages that do not have a custom style applied.<br></span>
      </td>
    </tr>
  <?php }

  function wp98_build_danger_options_html( $data ) { ?>
    <input type="hidden" name="uninstall" value="">
    <?php wp98_build_checkbox_html( "warning", "danger", "Uninstall WP 98", "Completely removes WP 98 from your WordPress Database.", 0 );
  }

  function wp98_build_checkbox_html( $name, $category, $label, $description, $value ) {
    ?>
    <tr>
      <th scope="row"><?php echo $label; ?></th>
      <td>
        <input id="<?php echo $name; ?>" type="hidden" name="<?php echo $category . '-' . $name ?>" value="<?php echo $value === 1; ?>">
        <input id="checkbox-<?php echo $name; ?>" type="checkbox" <?php echo ($value === 1 ? 'checked' : ''); ?>>
        <label for="<?php echo $name; ?>"><?php echo $description; ?></label>
      </td>
    </tr>
    <?php
  }

  function wp98_build_button_html( $name, $category, $label, $description ) {
    ?>
    <tr>
      <th scope="row"><?php echo $label; ?></th>
      <td>
        <button id="<?php echo $name; ?>" type="button" name="<?php echo $category . '-' . $name ?>">Confirm</button>
        <label for="<?php echo $name; ?>"><?php echo $description; ?></label>
      </td>
    </tr>
    <?php
  }

  function wp98_build_color_html( $name, $category, $label, $description, $value ) {
    ?>
    <tr>
      <th scope="row"><?php echo $label; ?></th>
      <td>
        <input id="<?php echo $name; ?>" type="color" name="<?php echo $category . '-' . $name ?>" value="#<?php echo $value; ?>">
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

    // Get all page menus created for individual pages
    $page_menus = get_terms( 'nav_menu', array( 'hide_empty' => true ) ); 
    error_log( print_r( $page_menus, true) );

    // Get all page headers created for individual pages
    $template_files = list_files( get_template_directory() . '/page-templates/templates/', 100, array( '.', '..' ) );
    $page_headers = [];
    $page_footers = [];
    foreach( $template_files as $path ) {
      if ( pathinfo( $path, PATHINFO_EXTENSION ) === 'php') {
        $file = basename( $path, '.php' );
        if ( str_ends_with( $file, 'header' ) )
          array_push( $page_headers, basename( $file, '.php' ) );
        else if ( str_ends_with( $file, 'footer' ) )
          array_push( $page_footers, basename( $file, '.php' ) );
      }
    } ?>

    <tr>
      <th scope="row"><label for="menu-items">Menu Items</label></th>
      <td>
        <table id="wp98_menu_options" name="menu-items">
          <?php wp98_build_menu_headers();
          // Add saved menu items
          wp98_build_saved_menu_items( $entry_count, $nav_menu, $page_array, $page_headers, $page_footers );

          // Add a row with options to add new items
          wp98_build_new_menu_item( $entry_count, $page_array, $page_headers, $page_footers ) ?>
        </table>
      </td>
    </tr>
  <?php }

  function wp98_build_saved_menu_items( $entry_count, $nav_menu, $page_array, $page_headers, $page_footers ) {
    for ($row = 0; $row < $entry_count; $row++ ) {
      foreach ( $nav_menu as $entry ) {
        if ( $row === (int)$entry->ord ) { ?>
          <tr class="menu-row">
            <?php wp98_build_hidden_inputs( $row, $entry );
            wp98_build_menu_drag_icon(); ?>
            <td class="menu-icon-col">
              <?php if ( $entry->img === null || $entry->img === '' ) wp98_build_menu_no_image_container();
              else wp98_build_menu_image_container( $entry->img ); ?>
            </td>
            <?php wp98_build_menu_page_dropdown( $row, $entry, $page_array );
            wp98_build_menu_page_header_dropdown( $row, $page_headers );
            wp98_build_menu_page_footer_dropdown( $row, $page_footers );
            wp98_build_menu_delete_icon( true ); ?>
          </tr>
        <?php }
      }
    }
  }

  function wp98_build_new_menu_item( $entry_count, $page_array, $page_headers, $page_footers ) { ?>
    <tr class="menu-row">
      <?php wp98_build_hidden_inputs( $entry_count, null );
      wp98_build_menu_drag_icon(); ?>
      <td class="menu-icon-col">
        <?php wp98_build_menu_no_image_container(); ?>
      </td>
      <?php wp98_build_menu_page_dropdown( $entry_count, NULL, $page_array );
      wp98_build_menu_page_header_dropdown( $entry_count, $page_headers );
      wp98_build_menu_page_footer_dropdown( $entry_count, $page_footers );
      wp98_build_menu_delete_icon(); ?>
    </tr>
  <?php }

  function wp98_build_menu_headers() { ?>
    <tr>
      <th></th>  
      <th>Icon</th>
      <th>Linked Page</th>
      <th>Header on<br>Linked Page</th>
      <th>Footer on<br>Linked Page</th>
      <th></th>
    </tr>
  <?php }

  function wp98_build_hidden_inputs( $row, $entry ) { ?>
    <input type="hidden" name="menu-action-<?php echo $row; ?>" value="<?php echo is_null( $entry ) ? 'delete' : 'no-change'; ?>">
    <input type="hidden" name="menu-order-<?php echo $row; ?>" value="<?php echo $row; ?>">
    <input type="hidden" name="menu-page-id-<?php echo $row; ?>" value="<?php echo is_null( $entry ) ? '' : $entry->id; ?>">
    <input type="hidden" name="menu-img-<?php echo esc_html( $row ); ?>" value="<?php echo is_null( $entry ) ? '' : esc_html( $entry->img ); ?>">
    <input type="hidden" name="menu-name-<?php echo $row; ?>" value="<?php echo is_null( $entry ) ? '' : $entry->lbl; ?>">
    <input type="hidden" name="menu-head-<?php echo $row; ?>" value="<?php echo is_null( $entry ) ? '' : $entry->head; ?>">
    <input type="hidden" name="menu-foot-<?php echo $row; ?>" value="<?php echo is_null( $entry ) ? '' : $entry->foot; ?>">
  <?php }

  function wp98_build_menu_drag_icon() { ?>
    <td class="menu-drag-icon">
      <div>≡</div>
    </td>
  <?php }

  function wp98_build_menu_image_container( $img ) { ?>
    <div class="menu-no-icon" style="display: none;">No<br>Icon</div>
    <img src="<?php echo esc_html( $img ); ?>">
    <div class="menu-btn-flex-container">
      <button type="button" class="mediamanager-btn button button-secondary">Change</button>
      <button type="button" class="icon-remove-btn button button-secondary">Remove</button>
    </div>
  <?php }

  function wp98_build_menu_no_image_container( ) { ?>
    <div class="menu-no-icon">No<br>Icon</div>
    <img src="" style="display: none;">
    <button type="button" class="mediamanager-btn add-button button button-secondary">Icon</button>
  <?php }

  function wp98_build_menu_page_dropdown( $row, $entry, $page_array ) { ?>
    <td class="menu-page-col">
      <select>
        <option value="none" <?php echo is_null( $entry ) ? 'selected="selected"' : '' ?>>--- None ---</option>
        <?php foreach( $page_array as $page => $page_id ) :
          if ( str_contains( $page, 'WP98 Homepage' ) ) continue; ?>
          <option value="<?php echo $page_id; ?>" <?php
            if ( !is_null( $entry) && $page_id === $entry->id ) echo 'selected="selected"'; ?>>
            <?php echo $page; ?></option>
        <?php endforeach ?>
      </select>
    </td>
  <?php }

  function wp98_build_menu_page_header_dropdown( $row, $headers ) { ?>
    <td class="menu-header-col">
      <select>
        <option value="none">--- No Header ---</option>
        <?php foreach( $headers as $header ) : ?>
          <?php $header_title = ucwords( str_replace( '-',' ', $header ) ); ?>
          <option value="<?php echo $header; ?>"><?php echo $header_title; ?></option>
        <?php endforeach ?>
      </select>
    </td>
  <?php }

  function wp98_build_menu_page_footer_dropdown( $row, $footers ) { ?>
   <td class="menu-footer-col">
      <select>
        <option value="none">--- No Footer ---</option>
        <?php foreach( $footers as $footer ) : ?>
          <?php $footer_title = ucwords( str_replace( '-',' ', $footer ) ); ?>
          <option value="<?php echo $footer; ?>"><?php echo $footer_title; ?></option>
        <?php endforeach ?>
      </select>
    </td>
  <?php }

  function wp98_build_menu_delete_icon( $add_icon = false ) { ?>
    <td class="menu-delete-icon">
      <?php if ( $add_icon ) : ?>
        <img src="<?php echo get_theme_file_uri() . '/assets/images/main/delete-button.svg'; ?>" alt="Delete row">
      <?php endif; ?>
    </td>
  <?php }