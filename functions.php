<?php
/**
 * Functions and definitions for WP 98
 *
 * @link https://github.com/spiratil/wp98/
 * @package wp98
 * @since 0.1.0
 */

// Global variables
$options_table = $wpdb->prefix . 'wp98_options';
$menu_table = $wpdb->prefix . 'wp98_menus';

//add_action( 'switch_theme', 'wp98_remove_theme_options_menu' );
add_action( 'admin_enqueue_scripts', 'wp98_setup_admin' );
add_action( 'wp_enqueue_scripts', 'wp98_setup_files' );
add_action( 'admin_menu', 'wp98_add_custom_admin_menu' );
add_action( 'after_switch_theme', 'wp98_theme_initialisation' );
add_action( 'admin_enqueue_scripts', 'wp98_add_custom_disabled_fields_js' );
add_action('wp_ajax_load_page', 'ajax_action_callback_load_page');
add_action('wp_ajax_nopriv_load_page', 'ajax_action_callback_load_page');
// Tag and category hooks
add_action('init', 'tags_categories_support_all');
add_action('pre_get_posts', 'tags_categories_support_query');


function wp98_add_custom_disabled_fields_js() {
    if ( 'options-reading' == get_current_screen()->id && ! current_user_can( 'manage_network_options' ) ) {
        wp_enqueue_script( 'wp98_custom_disable_fields', get_parent_theme_file_uri( '/assets/js/disable-homepage-setting.js' ), array( 'jquery' ), false, true );
    }
}

// Initialise the theme on activation
function wp98_theme_initialisation() {
  wp98_set_static_homepage();
  wp98_create_theme_database_table();
}

// Ensure a static page is set to load for the website
function wp98_set_static_homepage() {
  // Check if the WP 98 Homepage exists
  $pages = get_pages();
  $homepage_id = 0;
  foreach ( $pages as $page ) {
    if ( $page->post_title === 'WP98 Homepage - DO NOT MODIFY' ) {
      $homepage_id = absint( $page->ID );
      break;
    };
  }

  // Create a new homepage if it doesn't exist
  if ( $homepage_id === 0 ) {
    global $user_ID;
    $new_post = array(
      'post_title' => 'WP98 Homepage - DO NOT MODIFY',
      'post_status' => 'publish',
      'post_date' => date('Y-m-d H:i:s'),
      'post_author' => $user_ID,
      'post_type' => 'page',
      'post_category' => []
    );
    $homepage_id = wp_insert_post($new_post);
  }
  
  // Set the homepage for the website
  update_option( 'show_on_front', 'page' );
  update_option( 'page_on_front', $homepage_id );
}


// Check if the database tables exist and create them if not
function wp98_create_theme_database_table() {
  // Prepare the database
  global $wpdb, $options_table, $menu_table;
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  $charset_collate = $wpdb->get_charset_collate();

  if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $options_table )  ) ) !== $options_table  ) {
    error_log( "Creating $options_table" . ': ' . print_r( $options_table , true ) );
    $sql = "CREATE TABLE $options_table  (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      `name` varchar(32) NOT NULL,
      cat varchar(12) NOT NULL,
      lbl varchar(32),
      `desc` text,
      val text,
      PRIMARY KEY (id)
    ) $charset_collate;";
  
    dbDelta( $sql );

    wp98_fill_empty_database_options_table( $wpdb, $options_table );
  } 


  if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $menu_table ) ) ) !== $menu_table  ) {
    error_log( "Creating $menu_table" . ': ' . print_r( $options_table , true ) );
    $sql = "CREATE TABLE $menu_table  (
      ord mediumint(9) NOT NULL,
      id mediumint(9) NOT NULL,
      lbl varchar(255) NOT NULL,
      img varchar(2048),
      head varchar(2048),
      foot varchar(2048),
      style int,
      PRIMARY KEY (ord)
    ) $charset_collate;";
  
    dbDelta( $sql );
  } 
}

// Create the schema and default options for database tables
function wp98_fill_empty_database_options_table( $wpdb, $table ) {
  $wpdb->query("INSERT INTO $table
    (`name`, cat, lbl, `desc`, val)
    VALUES
    ('colour-main', 'general', 'Main Colour', 'The main colour for windows/menus.', '000080'),
    ('colour-second', 'general', 'Second Colour', 'The second colour used mostly in gradients', '1084d0'),
    ('colour-desktop', 'general', 'Desktop Colour', 'The colour of the desktop.', '008080'),
    ('start-button', 'taskbar', 'Start Menu', 'Allow the user to click on the Start Button and access the Start Menu.', '1'),
    ('open-windows', 'taskbar', 'Open Programs', 'Show open windows as tabs in the Taskbar.', 1),
    ('copyright', 'taskbar', 'System Tray', 'Show a copyright in the System Tray.', 1)
  ");
  /*
  $entry =  array( 'name' => 'show-site-logo', 'cat' => 'start-menu', 'lbl' => 'Show Logo in Start Menu', 'val' => 1 );

  $wpdb->insert( $table, $entry );
  */
}

function wp98_setup_files() {
  wp98_enqueue_styles();
  wp98_enqueue_scripts();
}

function wp98_enqueue_styles() {
  wp_enqueue_style( 'wp98-fonts', get_theme_file_uri() . '/assets/css/fonts.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98-variables', get_theme_file_uri() . '/assets/css/variables.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98-settings', get_theme_file_uri() . '/assets/css/settings.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98', get_theme_file_uri() . '/assets/css/98.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98-page', get_theme_file_uri() . '/assets/css/page.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
}

function wp98_enqueue_scripts() {
  wp_enqueue_script( 'jquery' );
  wp_enqueue_script( 'wp98', get_parent_theme_file_uri( '/assets/js/page-manager.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
}

function wp98_add_custom_admin_menu() {
	add_theme_page( 'WP 98 Options', 'WP 98 Options', 'edit_theme_options', 'wp98_theme_options', 'wp98_option_page' );
  /*
  remove_submenu_page( 'themes.php', 'nav-menus.php' );
  $customize_page_url = add_query_arg( 'return', urlencode( remove_query_arg( wp_removable_query_args(), wp_unslash( $_SERVER['REQUEST_URI'] ) ) ), 'customize.php' );
  remove_submenu_page( 'themes.php', $customize_page_url );
  */
}

function wp98_option_page() {
  // Must check that the user has the required capability 
  if ( !current_user_can( 'manage_options' ) ) wp_die( __('You do not have sufficient permissions to access this page.') );

	get_template_part( './templates/options-page' );
}

function wp98_setup_admin( $hook ) {
  if ( $hook === 'appearance_page_wp98_theme_options' ) {
    wp_enqueue_style( 'wp98-options-css', get_theme_file_uri() . '/assets/css/options.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
    wp_enqueue_script( 'wp98-options-js', get_parent_theme_file_uri( '/assets/js/options.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
    // mediamanager script and styling
    wp_enqueue_style( 'dropzone-css', get_theme_file_uri() . '/assets/mediamanager/dropzone.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
    wp_enqueue_script( 'dropzone-js', get_parent_theme_file_uri( '/assets/mediamanager/dropzone.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
    wp_enqueue_style( 'mediamanager-css', get_theme_file_uri() . '/assets/mediamanager/mediamanager.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
    wp_enqueue_script( 'mediamanager-js', get_parent_theme_file_uri( '/assets/mediamanager/mediamanager.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
  }
}

// Handler for determining what content to load within the page windows on AJAX requests
function ajax_action_callback_wp98_load_page() {
  if ( isset( $_POST['id'] ) ) {
    $id = absint( $_POST['id'] );
    $type = get_post_type( $id );

    // Check if the post exists
    if ( get_post_status( $id ) === 'publish' ) {
      // Build the page content
      if ( $type = 'page' ) get_template_part( './templates/page', null, array( 'id' => $id ) );
    }
    else echo '404';
  }
  else echo 'err';
  die();
}

// Add tag and category support to pages
function tags_categories_support_all() {
  register_taxonomy_for_object_type('post_tag', 'page');
  register_taxonomy_for_object_type('category', 'page');  
}

// Ensure all tags and categories are included in queries
function tags_categories_support_query($wp_query) {
  if ($wp_query->get('tag')) $wp_query->set('post_type', 'any');
  if ($wp_query->get('category_name')) $wp_query->set('post_type', 'any');
}