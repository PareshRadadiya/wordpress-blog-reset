<?php
$_SERVER['HTTP_HOST'] = 'rtvoxxi.au';
$_SERVER['SERVER_NAME'] = 'rtvoxxi.au';

if ( ! defined( 'WP_LOAD_PATH' ) ) {
        $path ="../";
  if ( file_exists( $path . 'wp-load.php' ) )
    define( 'WP_LOAD_PATH', $path );
  else
    exit( "Could not find wp-load.php" );
}

require_once( WP_LOAD_PATH . 'wp-load.php');

/**
 * Detect plugin. For use on Front End only.
 */
include_once(ABSPATH . 'wp-admin/includes/plugin.php');
require_once( ABSPATH . '/wp-admin/includes/upgrade.php' );

//// check for plugin using plugin name
//$plugin_dir = basename(__DIR__);

// name of blog to be reset
$blog_id = intval( get_blog_id_from_url("apple.rtvoxxi.au") );
global $wpdb;
switch_to_blog( $blog_id  );

$wpdb->set_blog_id( $blog_id );

//$reactivate_wp_reset_additional = get_option('active_plugins');

// list of all activate plugin

$row = $wpdb->get_row( $wpdb->prepare( "SELECT option_value FROM $wpdb->options WHERE option_name = %s LIMIT 1", 'active_plugins' ) );

$reactivate_wp_reset_additional =  ( unserialize( $row->option_value )  );

// current theme stylesheet
$theme = wp_get_theme();
$reset_current_theme = $theme->get_stylesheet();

@wpmu_delete_blog( $blog_id, true );

$prefix = str_replace( '_', '\_', $wpdb->prefix );
$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
foreach ( $tables as $table ) {
  $wpdb->query( "DROP TABLE $table" );
}


$blog_id = @wpmu_create_blog( "apple.rtvoxxi.au", "/", "Apple", 1, array( 'public' ) );

if( $blog_id ) {

  switch_to_blog( $blog_id  );

  $wpdb->set_blog_id( $blog_id );

  if ( ! empty( $reactivate_wp_reset_additional ) ) {
    foreach ( $reactivate_wp_reset_additional as $plugin ) {
      $plugin = plugin_basename( $plugin );

      $test = @activate_plugin( $plugin );
      var_dump( $test );
    }
  }

  switch_theme( $reset_current_theme );
}




exit();
