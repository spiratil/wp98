<?php
/** 
* Template Name: Open Programs
*/ 
wp_enqueue_style( 'wp98-open-programs', get_theme_file_uri() . '/assets/css/open-programs.css', array(), wp_get_theme()->get( 'Version' ), 'all' );
wp_enqueue_script( 'wp98-open-programs', get_parent_theme_file_uri( '/assets/js/open-programs.js' ), array(), wp_get_theme()->get( 'Version' ), array( 'defer', true ) );
?>

<div id="wp98-open-programs"></div>
