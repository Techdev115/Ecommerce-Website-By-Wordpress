<?php
/*
Plugin Name: Multiple Themes
Plugin URI: http://zatzlabs.com/plugins/
Description: Select different Themes for one or more WordPress Pages, Posts or other non-Admin pages.  Or Site Home.
Version: 7.1.1
Author: David Gewirtz
Author URI: http://zatzlabs.com/plugins/
License: GPLv2
*/

/*  Copyright 2015  jonradio  (email : info@zatz.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

DEFINE( 'JR_MT_HOME_URL', home_url() );
DEFINE( 'JR_MT_FILE', __FILE__ );

/*	For Hooks, when it needs to run first or last.
*/
DEFINE( 'JR_MT_RUN_FIRST', 1 );
DEFINE( 'JR_MT_RUN_SECOND', JR_MT_RUN_FIRST + 1 );
DEFINE( 'JR_MT_RUN_LAST', 999 );

DEFINE( 'JR_MT_WP_GET_THEMES_ACTION', 'plugins_loaded' );

global $jr_mt_url_types;
$jr_mt_url_types = array( 'url', 'url_prefix', 'url_asterisk' );

function jr_mt_default_settings() {
	return array(
		/*	Settings structure:
			code - get_option( 'jr_mt_settings' )
			['all_pages'] => zero length string or folder in Themes directory containing theme to use for All Pages
			['all_posts'] => zero length string or folder in Themes directory containing theme to use for All Posts
			['site_home'] => zero length string or folder in Themes directory containing theme to use for Home Page
			['current'] => zero length string or folder in Themes directory containing theme to override WordPress Current Theme
			['query']
				[keyword]
					[value] or ['*'] => folder in Themes directory containing theme to use
			['remember']
				['query']
					[keyword]
						[value] => TRUE
			['override']
				['query']
					[keyword]
						[value] => TRUE
			['query_present'] => TRUE or FALSE
			
			Added in Version 5.0:
			['url'], ['url_prefix'] and ['url_asterisk'] - array with each entry:
				['url'] => URL
				['prep'][] => array of URL arrays created by jr_mt_prep_url(), with array index matching the array index of ['aliases']
				['rel_url'] => Relative URL based on Site Address (URL) that admin entered the URL
				['id'] => Post ID (Page, Post or Attachment), if known and if relevant
				['id_kw'] => 'page_id', 'p' or 'attachment_id'
				['theme'] => folder in Themes directory containing theme to use
			
			Added in Version 6.0:
			['aliases'][] - array of Alias URLs that could replace 'home' in URL of this site,
					with each entry:
				['url'] => URL
				['prep'] => URL array created by jr_mt_prep_url()
				['home'] => TRUE if this is Site Address (URL) field value from WordPress General Settings,
					which is stored here to determine when the WordPress General Setting is changed				
			
			Added in Version 7.1:
			['ajax_all'] => zero length string or folder in Themes directory containing theme to use for /wp-admin/admin-ajax.php
			
			Prior to Version 5.0:
			['ids']
				[id] - zero length string or WordPress ID of Page, Post, etc.
					['type'] => 'page' or 'post' or 'admin' or 'cat' or 'archive' or 'prefix' or other
					['theme'] => folder in Themes directory containing theme to use
					['id'] => FALSE or WordPress ID of Page, Post, etc.
					['page_url'] => relative URL WordPress page, post, admin, etc. or FALSE
					['rel_url'] => URL relative to WordPress home
					['url'] => original full URL, from Settings page entry by user	
		*/
		'aliases'       => jr_mt_init_aliases(),
		'all_pages'     => '',
		'all_posts'     => '',
		'site_home'     => '',
		'current'       => '',
		'ajax_all'      => '',
		'query'         => array(),
		'remember'      => array( 'query' => array() ),
		'override'      => array( 'query' => array() ),
		'query_present' => FALSE,
		'url'           => array(),
		'url_prefix'    => array(),
		'url_asterisk'  => array()
		);
}

/*	Catch old unsupported version of WordPress before any damage can be done.
*/
if ( version_compare( get_bloginfo( 'version' ), '3.4', '<' ) ) {
	require_once( plugin_dir_path( JR_MT_FILE ) . 'includes/old-wp.php' );
} else {
	/*	Use $plugin_data['Name'] for the array of incompatible plugins
	*/
	global $jr_mt_incompat_plugins;
	$jr_mt_incompat_plugins = array( 'Theme Test Drive' );  // removed for V5: 'BuddyPress', 'Polylang'
	
	require_once( plugin_dir_path( JR_MT_FILE ) . 'includes/functions.php' );
	
	if ( is_admin() ) {
		/* 	Add Link to the plugin's entry on the Admin "Plugins" Page, for easy access
			
			Placed here to avoid the confusion of not displaying it during a Version conversion of Settings
		*/
		add_filter( 'plugin_action_links_' . jr_mt_plugin_basename(), 'jr_mt_plugin_action_links', 10, 1 );
		
		/**
		* Creates Settings entry right on the Plugins Page entry.
		*
		* Helps the user understand where to go immediately upon Activation of the Plugin
		* by creating entries on the Plugins page, right beside Deactivate and Edit.
		*
		* @param	array	$links	Existing links for our Plugin, supplied by WordPress
		* @param	string	$file	Name of Plugin currently being processed
		* @return	string	$links	Updated set of links for our Plugin
		*/
		function jr_mt_plugin_action_links( $links ) {
			/*	The "page=" query string value must be equal to the slug
				of the Settings admin page.
			*/
			array_unshift( $links, '<a href="' . get_bloginfo('wpurl') . '/wp-admin/admin.php?page=jr_mt_settings' . '">Settings</a>' );
			return $links;
		}
		
		//	Admin Page

		add_action( 'admin_menu', 'jr_mt_admin_hook' );
		//	Runs just before admin_init (in admin.php file)
		
		/**
		* Add Admin Menu item for plugin
		* 
		* Plugin needs its own Page in the Settings section of the Admin menu.
		*
		*/
		function jr_mt_admin_hook() {
			//  Add Settings Page for this Plugin
			global $jr_mt_plugin_data;
			add_theme_page( $jr_mt_plugin_data['Name'], 'Multiple Themes plugin', 'switch_themes', 'jr_mt_settings', 'jr_mt_settings_page' );
			add_options_page( $jr_mt_plugin_data['Name'], 'Multiple Themes plugin', 'switch_themes', 'jr_mt_settings', 'jr_mt_settings_page' );
		}
		
		add_action( 'admin_init', 'jr_mt_register_settings' );
		function jr_mt_register_settings() {
			register_setting( 'jr_mt_settings', 'jr_mt_settings', 'jr_mt_validate_settings' );
		}
	}
	
	if ( is_admin() ) {
		/*	&& isset( $_GET['page'] ) && ( 'jr_mt_settings' === $_GET['page'] )
			should work, but redirects Save Changes to options.php
		*/
		require_once( jr_mt_path() . 'includes/admin-functions.php' );
		/*	Admin panel
		*/
		require_once( jr_mt_path() . 'includes/admin.php' );
	} else {
		require_once( jr_mt_path() . 'includes/select-theme.php' );
	}
}

?>