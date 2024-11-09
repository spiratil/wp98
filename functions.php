<?php
/**
 * Functions and definitions for WP 98
 *
 * @link https://github.com/spiratil/wp98/
 * @package wp98
 * @since 0.1.0
 */

 add_action( 'after_setup_theme', 'wp98_setup' );

function wp98_setup() {
  wp98_enqueue_styles();
  wp98_enqueue_scripts();
  add_theme_support( 'wp-block-styles' );
}

function wp98_enqueue_styles() {
  wp_enqueue_style(
    'wp98-css',
    get_parent_theme_file_uri( 'assets/css/98.css' ),
    array(),
    wp_get_theme()->get( 'Version' ),
    'all'
  );

  add_editor_style( 'assets/css/editor.css' );
}

function wp98_enqueue_scripts() {
  wp_enqueue_script( 
    'wp98-header', 
    get_parent_theme_file_uri( 'assets/js/header.js' ),
    array(),
    wp_get_theme()->get( 'Version' ),
    array( 'defer', true )
  );
}