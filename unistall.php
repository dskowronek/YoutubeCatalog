<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit;
}

global $wpdb;
$table_name = $wpdb->prefix . 'youtube_videos';

$wpdb->query( "DROP TABLE IF EXISTS $table_name" );