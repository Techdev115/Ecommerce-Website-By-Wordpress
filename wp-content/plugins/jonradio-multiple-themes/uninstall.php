<?php
//	Ensure call comes from WordPress, not a hacker or anyone else trying direct access.
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) )
	exit ();

	
/*  Remove any tables, options, and such created by this Plugin  */
$setting_names = array(
	'jr_mt_settings',
	'jr_mt_internal_settings',
	'jr_mt_all_themes'
	);
if ( function_exists( 'is_multisite' ) && is_multisite() ) {
	global $wpdb, $site_id;
	$blogs = $wpdb->get_results( "SELECT blog_id FROM {$wpdb->blogs} WHERE site_id = $site_id" );
	foreach ( $blogs as $blog_obj ) {
		foreach ( $setting_names as $name ) {
			delete_blog_option( $blog_obj->blog_id, $name );
		}
	}
} else {
	foreach ( $setting_names as $name ) {
		delete_option( $name );
	}
}

?>