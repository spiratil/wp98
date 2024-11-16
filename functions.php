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
add_action( 'wp_enqueue_scripts', 'wp98_setup' );
add_action( 'admin_menu', 'wp98_add_custom_admin_menu' );
add_action( 'after_switch_theme', 'wp98_create_theme_database_table' );

// Check if the database tables exist and create them if not
function wp98_create_theme_database_table() {
  global $wpdb, $options_table, $menu_table;
  require_once ABSPATH . 'wp-admin/includes/upgrade.php';
  $charset_collate = $wpdb->get_charset_collate();

  if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $options_table )  ) ) !== $options_table  ) {
    error_log( "Creating $options_table" . ': ' . print_r( $options_table , true ) );
    $sql = "CREATE TABLE $options_table  (
      opt_id mediumint(9) NOT NULL AUTO_INCREMENT,
      opt_name varchar(32) NOT NULL,
      opt_cat varchar(10) NOT NULL,
      opt_lbl varchar(32),
      opt_desc text,
      opt_val text,
      PRIMARY KEY (opt_id)
    ) $charset_collate;";
  
    dbDelta( $sql );

    wp98_fill_empty_database_options_table( $wpdb, $options_table );
  } 

  if ( $wpdb->get_var( $wpdb->prepare( "SHOW TABLES LIKE %s", $wpdb->esc_like( $menu_table ) ) ) !== $menu_table  ) {
    error_log( "Creating $menu_table" . ': ' . print_r( $options_table , true ) );
    $sql = "CREATE TABLE $menu_table  (
      id mediumint(9) NOT NULL AUTO_INCREMENT,
      lbl varchar(255) NOT NULL,
      img varchar(2048),
      link varchar(2048),
      PRIMARY KEY (id)
    ) $charset_collate;";
  
    dbDelta( $sql );
  } 
}

// Create the schema and default options for database tables
function wp98_fill_empty_database_options_table( $wpdb, $table ) {
  $wpdb->query("INSERT INTO $table
                (opt_name, opt_cat, opt_lbl, opt_desc, opt_val)
                VALUES
                ('show-title-bar', 'start-menu', 'Title Bar', NULL, 1),
                ('show-site-logo', 'start-menu', 'Logo', NULL, 1),
                ('show-site-title', 'start-menu', 'Site Title', NULL, 1)
              ");
  /*
  $entry =  array( 'opt_name' => 'show-site-logo', 'opt_cat' => 'start-menu', 'opt_lbl' => 'Show Logo in Start Menu', 'opt_val' => 1 );

  $wpdb->insert( $table, $entry );
  */
}

function wp98_setup() {
  wp98_enqueue_styles();
  wp98_enqueue_scripts();
}

function wp98_enqueue_styles() {
  wp_enqueue_style( 'wp98-fonts', get_theme_file_uri() . '/assets/css/fonts.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98-variables', get_theme_file_uri() . '/assets/css/variables.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
  wp_enqueue_style( 'wp98', get_theme_file_uri() . '/assets/css/98.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
}

function wp98_enqueue_scripts() {
  if ( is_front_page() ) {
    wp_enqueue_script( 'wp98-header', get_parent_theme_file_uri( '/assets/js/98.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
  }
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

	get_template_part( './templates/wp98-options-page' );
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