<?php
/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

global $jr_mt_kwvalsep;
/*	Everything is converted to lower-case, so upper-case letter makes a good keyword-value separator
*/
$jr_mt_kwvalsep = 'A';

/*	Go to Settings page to get Settings checked/fixed/initialized.

	But don't display on Settings page itself.
*/
if ( ( !isset( $_GET['page'] ) || ( 'jr_mt_settings' !== $_GET['page'] ) ) 
	&& ( ( FALSE === ( $internal_settings = get_option( 'jr_mt_internal_settings' ) ) )
		|| ( !isset( $internal_settings['v7init'] ) ) )
	&& ( FALSE !== get_option( 'jr_mt_settings' ) ) ) {
	/*	If (public) Settings do not exist, this is a new install or
		re-install after deleting the plugin.
		No warning is needed because the plugin will not do anything,
		and the admin will naturally go the Settings to set some Theme Selections.
	*/
	add_action( 'all_admin_notices', 'jr_mt_v7init_required' );
	/**
	* Warn that Private Site is turned OFF by default
	* 
	* Put Warning on top of every Admin page (visible to Admins only)
	* until Admin visits plugin's Settings page.
	*
	*/
	function jr_mt_v7init_required() {
		global $jr_mt_plugin_data;
		if ( current_user_can( 'manage_options' ) ) {
			echo '<div class="updated"><p><b>' . $jr_mt_plugin_data['Name'] . ' plugin: Version '. $jr_mt_plugin_data['Version'] . ' update requires visit to <a href="'
				. admin_url( 'options-general.php?page=jr_mt_settings' )
				. '">Settings page</a> to check and update its Settings.</b></p></div>';
		}
	}
}

add_action( 'admin_enqueue_scripts', 'jr_mt_admin_enqueue_scripts' );
function jr_mt_admin_enqueue_scripts() {
	global $jr_mt_plugin_data;
	wp_enqueue_script( 'jr_mt_tabs', plugins_url() . '/' . dirname( jr_mt_plugin_basename() ) . '/js/tabs.js', array(), $jr_mt_plugin_data['Version'] );
}

/**
 * Settings page for plugin
 * 
 * Display and Process Settings page for this plugin.
 *
 */
function jr_mt_settings_page() {
	global $jr_mt_plugin_data, $jr_mt_plugins_cache;
	$jr_mt_plugins_cache = get_plugins();
	add_thickbox();
	echo '<div class="wrap">';
	echo '<h2>' . $jr_mt_plugin_data['Name'] . '</h2>';
	
	/*	Required because it is only called automatically for Admin Pages in the Settings section
	*/
	settings_errors( 'jr_mt_settings' );
	
	/*	Return to Same Tab where button was pushed.
	
		TODO:  This should be converted to use wp_localize_script()
		as described on page 356 of "Professional WordPress Plugin Development" 2011.
	*/
	$name = 'jr_mt_' . get_current_user_id() . '_tab';
	if ( FALSE === ( $tab = get_transient( $name ) ) ) {
		$tab = 1;
	} else {
		delete_transient( $name );
	}
	echo '<script type="text/javascript">window.onload = function() { jrMtTabs('
		. $tab . ', 6 ); }</script>';
	
	$theme_obj = wp_get_theme();
	$theme = $theme_obj->Name;
	$theme_version = $theme_obj->Version;
	global $jr_mt_options_cache;

	$current_wp_version = get_bloginfo( 'version' );
	
	global $jr_mt_plugins_cache;
	
	$compatible = TRUE;
	
	//	Check for incompatible plugins that have been activated:  BuddyPress and Theme Test Drive
	global $jr_mt_incompat_plugins;
	foreach ( $jr_mt_plugins_cache as $rel_path => $plugin_data ) {
		if ( in_array( $plugin_data['Name'], $jr_mt_incompat_plugins, TRUE ) && is_plugin_active( $rel_path ) ) {
			if ( $compatible ) {
				echo '<h3>Plugin Conflict Error Detected</h3>';
				$compatible = FALSE;
			}
			echo '<p>This Plugin (' . $jr_mt_plugin_data['Name'] . ') cannot be used when the <b>' . $plugin_data['Name'] 
				. '</b> plugin is Activated.  If you wish to use the ' . $jr_mt_plugin_data['Name'] 
				. ' plugin, please deactivate the '  . $plugin_data['Name'] 
				. ' plugin (not just when viewing this Settings page, but whenever the ' 
				. $jr_mt_plugin_data['Name'] . ' plugin is activated).</p>';
		}
	}
	
	if ( $compatible ) {
		?>
		<style type="text/css">
		<!--
		ul.jrmtpoints {	margin-left: 1em;
						list-style: disc;}
		-->
		</style>
		<h2 class="nav-tab-wrapper">
		<a href="#" class="nav-tab nav-tab-active" id="jr-mt-tabs1"
		onClick="jrMtTabs( 1, 6 );">Settings</a><a href="#" class="nav-tab" id="jr-mt-tabs2"
		onClick="jrMtTabs( 2, 6 );">Site Aliases</a><a href="#" class="nav-tab" id="jr-mt-tabs3"
		onClick="jrMtTabs( 3, 6 );">Advanced Settings</a><a href="#" class="nav-tab" id="jr-mt-tabs4"
		onClick="jrMtTabs( 4, 6 );">Theme Options</a><a href="#" class="nav-tab" id="jr-mt-tabs5"
		onClick="jrMtTabs( 5, 6 );">System Information</a><a href="#" class="nav-tab" id="jr-mt-tabs6"
		onClick="jrMtTabs( 6, 6 );">Help</a>
		</h2>
		<div id="jr-mt-settings1">
		<h3>Settings</h3>
		<p>
		This is the main Settings tab.
		You should also review the
		<a href="#" onClick="jrMtTabs( 2, 6 );">Site Aliases tab</a>:
		</p>
		<ul class="jrmtpoints">
		<li>
		when first using this plugin,
		</li>
		<li>
		when upgrading from Version 5 (or earlier) of this plugin,
		and 
		</li>
		<li>
		whenever you change the
		<b>
		Site Address (URL)
		</b>
		defined on the
		<a href="options-general.php">
		General Settings</a>
		Admin panel.
		</li>
		</ul>
		<p>
		Additional Settings are available on the
		<a href="#" onClick="jrMtTabs( 3, 6 );">Advanced Settings tab</a>,
		but they can cause problems
		in certain WordPress configurations,
		so should be used with care.
		</p>
		<p>
		Checking Settings...
		</p>
		<ul class="jrmtpoints">
		<?php
		
		unregister_setting( 'jr_mt_settings', 'jr_mt_settings', 'jr_mt_validate_settings' );
		
		global $wp;
		$default_internal_settings = array(
			'version'    => $jr_mt_plugin_data['Version'],
			'permalink'  => get_option( 'permalink_structure' ),
			/*	Store $wp->public_query_vars for when they are needed before 'setup_theme' Action
			
				Note:  $wp is not valid until 'setup_theme' Action.
			*/
			'query_vars' => $wp->public_query_vars
			);
		if ( is_array( $internal_settings_original = get_option( 'jr_mt_internal_settings' ) ) ) {
			$internal_settings = $internal_settings_original;
			/*	When plugin is installed (i.e. - no Settings),
				Previous Version is set to Current Version
				because no Version Conversion is required.
			*/
			if ( ( !isset( $internal_settings['version'] ) ) || ( !is_string( $internal_settings['version'] ) ) ) {
				$internal_settings['version'] = $default_internal_settings['version'];				
			}
			$internal_settings['query_vars'] = $default_internal_settings['query_vars'];	
		} else {
			$internal_settings = $default_internal_settings;
			jr_mt_messages( 'First use (or after plugin deleted): initialize Internal ("invisible") Settings' );
		}
		$previous_version = $internal_settings['version'];
		/*	Update to Current Version (which may be the same as Previous Version)
		*/
		$internal_settings['version'] = $default_internal_settings['version'];
		
		$internal_settings['v7init'] = TRUE;

		if ( $internal_settings !== $internal_settings_original ) {
			if ( update_option( 'jr_mt_internal_settings', $internal_settings ) ) {
				jr_mt_messages( 'Internal ("invisible") Settings have changed and were successfully updated' );
			} else {
				jr_mt_messages( 'Internal ("invisible") Settings have changed but could not be updated' );
			}
		}		

		/*	Check if any Settings were not properly converted from prior versions,
			the Site URL has changed, or the settings are otherwise corrupted.
			Do things in the following order, to avoid issues:
			- check every setting exists and is of the right type
			- check for still being in Version 4 settings; use upgradev5, if so
			- check for still being in Version 5 settings; do my own conversion, if so
			- check for pre-Version 7 format for ['prep']['query'] elements; convert
			- check for "=" in ['prep']['query'] keyword or value
				- remove if first char of value or last char of keyword
				- delete setting if found anywhere else, and report as corrupt
			- be sure ['aliases'][]['home'] is TRUE for correct URL (Site URL); if not:
				- create any missing ['url'*][]['rel_url'] from ['url'*][]['url'] and
					['aliases'][]['url'] from old ['aliases'][]['home']
				- change to FALSE
				- set correct ['aliases'][]['home'] to TRUE, even if it means adding alias entries
				- rebuild all ['url'*][]['prep']
				- re-create all ['url'*][]['url'] from ['url'*][]['rel_url'] and new Site URL
			- ['url'*][]['id'] is integer - convert to string
			- Missing ['url'*][]['rel_url'] - create it from ['url'*][]['url'] and Site URL
			- ['url'*][]['prep'] is not array - rebuild all ['url'*][]['prep']
			- "?" in ['url'*][]['url'] but no query in ['url'*][]['prep'][] - rebuild this ['url'*][]['prep']
			- Deleted Themes
		*/
		if ( is_array( $settings_original = get_option( 'jr_mt_settings' ) ) ) {
			$settings = $settings_original;
			global $jr_mt_url_types;
			$default_settings = jr_mt_default_settings();
			/*	Check for unconverted Version 4 Settings
			*/
			if ( ( !isset( $settings['url'] ) ) 
				|| version_compare( $previous_version, '5', '<' ) ) {
				require_once( jr_mt_path() . 'includes/upgradev5.php' );
				$settings = jr_mt_convert_ids( $settings );
				jr_mt_messages( 'Conversion completed to Version 5 format from Version ' . $previous_version );
			}
			/*	Check for unconverted Version 5 Settings
			*/
			if ( ( !isset( $settings['aliases'] ) ) 
				|| version_compare( $previous_version, '6', '<' ) ) {
				$settings['aliases'] = $default_settings['aliases'];
				require_once( jr_mt_path() . 'includes/upgradev6.php' );
				$settings = jr_mt_convert_url_arrays( $settings );
				jr_mt_messages( 'Conversion completed to Version 6 format from Version ' . $previous_version );
			}
			/*	Check Settings for missing or wrong type entries,
				including Themes that are not currently installed.
			*/
			$jr_mt_all_themes = jr_mt_all_themes();
			/*	Check: 'all_pages', 'all_posts', 'site_home', 'current', 'ajax_all'
			*/
			foreach ( array( 'all_pages', 'all_posts', 'site_home', 'current', 'ajax_all' ) as $key ) {
				if ( ( !isset( $settings[ $key ] ) ) || ( !is_string( $settings[ $key ] ) ) ) {
					$settings[ $key ] = $default_settings[ $key ];
					jr_mt_messages( 'Home, Everything or All Pages/Posts/AJAX Setting corrupt: reset to default (WordPress Active Theme)' );
				} else {
					if ( ( '' !== $settings[ $key ] ) && ( !isset( $jr_mt_all_themes[ $settings[ $key ] ] ) ) ) {
						$settings[ $key ] = $default_settings[ $key ];
						jr_mt_messages( 'Home, Everything or All Pages/Posts/AJAX Setting deleted: specified Theme no longer exists' );
					}
				}
			}
			/*	Check ['query_present']
			*/
			if ( ( !isset( $settings['query_present'] ) ) || ( !is_bool( $settings['query_present'] ) ) ) {
				$settings['query_present'] = $default_settings['query_present'];
				jr_mt_messages( '"When to add Sticky Query to a URL" Setting corrupt: reset to default' );
			}
			/*	Check ['aliases']
			*/
			if ( ( !isset( $settings['aliases'] ) ) || ( !is_array( $settings['aliases'] ) ) ) {
				$settings['aliases'] = $default_settings['aliases'];
				jr_mt_messages( 'No Site Aliases defined; set to Default Aliases, if any' );
				/*	Relies on ['url'*] being valid, so do after it.
				*/
				$rebuild_prep = TRUE;
			} else {
				foreach ( $settings['aliases'] as $alias ) {
					if ( ( !is_string( $alias['url'] ) ) 
						|| ( !is_array( $alias['prep'] ) ) 
						|| !is_bool( $alias['home'] ) ) {
						$settings['aliases'] = $default_settings['aliases'];
						jr_mt_messages( 'Site Aliases settings are corrupt; reset to Default Aliases, if any' );
						$rebuild_prep = TRUE;
						break;
					}
				}
			}
			/*	Check [url*]
			*/
			foreach ( $jr_mt_url_types as $url_type ) {
				if ( !isset( $settings[ $url_type ] ) ) {
					$settings[ $url_type ] = $default_settings[ $url_type ];
					jr_mt_messages( 'URL setting(s) re-initialized' );
				} else {
					if ( !is_array( $settings[ $url_type ] ) ) {
						$settings[ $url_type ] = $default_settings[ $url_type ];
						jr_mt_messages( 'URL setting(s) deleted because they are corrupt' );
					} else {
						foreach ( $settings[ $url_type ] as $index => $url_array ) {
							if ( ( !isset( $url_array['url'] ) ) 
								|| ( !isset( $url_array['prep'] ) ) 
								|| ( !isset( $url_array['theme'] ) )
								|| ( !is_string( $url_array['url'] ) ) 
								|| ( !is_string( $url_array['theme'] ) )
								|| ( !isset( $jr_mt_all_themes[ $url_array['theme'] ] ) ) ) {
								unset( $settings[ $url_type ][ $index ] );
								jr_mt_messages( 'URL setting deleted: either it was corrupt or specified Theme that no longer exists' );
							} else {
								/*	Be sure all ['prep'] are arrays.
									If any are not, rebuild all ['prep'] entries.
								*/
								if ( is_array( $url_array['prep'] ) ) {
									/*	Be sure that all ['prep'] entries have a ['query'] entry when the 
										URL has a Query in it, denoted by a "?".
										If any not found, Rebuild all the ['prep'] entries.
									*/
									if ( FALSE !== strpos( $url_array['url'], '?' ) ) {
										foreach ( $url_array['prep'] as $prep_entry ) {
											if ( isset( $prep_entry['query'] ) && is_array( $prep_entry['query'] ) ) {
												/*	Check for unconverted and corrupt Queries in ['prep']['query'] Settings
							
													A Keyword ending in "=" or a Value beginning with "=" gets converted.
													A "=" anywhere else means Corruption.
												*/											
												foreach ( $prep_entry['query'] as $keyword => $value_array ) {
													if ( is_array( $value_array ) ) {
														foreach ( $value_array as $value => $equalsign ) {
															/*	There must be an Equals Sign if there is a Value
															*/
															if ( is_bool( $equalsign ) && ( $equalsign || ( '' === $value ) ) ) {
																foreach ( array(
																	'=', '?', '&',	' ', '#', '/', '\\', '[', ']'
																	) as $char ) {
																	if ( FALSE !== strpos( $keyword . $value, $char ) ) {
																		unset( $settings[ $url_type ][ $index ] );
																		jr_mt_messages( 'URL setting deleted: illegal character found in Query keyword or value' );
																		break 4;
																	}
																}
															} else {
																$rebuild_prep = TRUE;
																break 3;
															}
														}
													} else {
														$rebuild_prep = TRUE;
														break 2;
													}
												}
											} else {
												$rebuild_prep = TRUE;
												break;
											}
										}
									}
								} else {
									$rebuild_prep = TRUE;
								}
								
								/*	Convert any integer Page/Post/Attachment ID to a string
									so it will match the type of a Query value in PHP/HTTP
								*/
								if ( isset( $url_array['id'] ) && is_int( $url_array['id'] ) ) {
									$settings[ $url_type ][ $index ]['id'] = ( string ) $url_array['id'];
									jr_mt_messages( 'Integer type Page/Post/Attachment ID found: converted to String type' );
								}
								if ( isset( $url_array['id_kw'] ) 
									&& ( ( !is_string( $url_array['id_kw'] ) ) 
										|| ( !in_array( $url_array['id_kw'], array( 'page_id', 'p', 'attachment_id' ), TRUE ) ) ) ) {
									unset( $settings[ $url_type ][ $index ]['id_kw'] );
								}
								if ( isset( $url_array['rel_url'] ) 
									&& ( !is_string( $url_array['rel_url'] ) ) ) {
									unset( $settings[ $url_type ][ $index ]['rel_url'] );
								}
							}
						}
					}
				}
			}
			/*	Check ['query']
			*/
			if ( ( !isset( $settings['query'] ) ) || ( !is_array( $settings['query'] ) ) ) {
				$settings['query'] = $default_settings['query'];
				jr_mt_messages( 'Query setting(s) deleted because they are corrupt' );
			} else {
				foreach ( $settings['query'] as $keyword => $value_array ) {
					if ( is_array( $value_array ) ) {
						foreach ( $value_array as $value => $theme ) {
							if ( ( !is_string( $theme ) )
								|| ( !isset( $jr_mt_all_themes[ $theme ] ) ) ) {
								unset( $settings['query'][ $keyword ][ $value ] );
								jr_mt_messages( 'Query setting deleted: either it was corrupt or specified Theme that no longer exists' );
								/*	All the checking for Empty parents (e.g. - $settings['query'][ $keyword ])
									and matching remember/query elements is done later.
								*/
							}
						}
						if ( empty( $settings['query'][ $keyword ] ) ) {
							unset( $settings['query'][ $keyword ] );
						}								
					} else {
						unset( $settings['query'][ $keyword ] );
						jr_mt_messages( 'Query setting deleted because it is corrupt' );
					}
				}
				/*	Check: 'remember', 'override'
				*/
				foreach ( array( 'remember', 'override' ) as $key ) {
					if ( is_array( $settings[ $key ] ) 
						&& isset( $settings[ $key ]['query'] ) 
						&& is_array( $settings[ $key ]['query'] ) ) {
						foreach ( $settings[ $key ] as $query_constant => $query_array ) {
							if ( 'query' !== $query_constant ) {
								unset( $settings[ $key ][ $query_constant ] );
								jr_mt_messages( 'Sticky/Override setting(s) deleted because they are corrupt' );
							}
						}
						foreach ( $settings[ $key ]['query'] as $keyword => $value_array ) {
							if ( is_array( $value_array ) ) {
								foreach ( $value_array as $value => $bool ) {
									if ( ( !is_bool( $bool ) ) 
										|| ( !isset( $settings['query'][ $keyword ][ $value ] ) ) ) {
										unset( $settings[ $key ]['query'][ $keyword ][ $value ] );
										jr_mt_messages( 'Sticky/Override setting(s) deleted because they are corrupt' );
									}
								}
								if ( empty( $settings[ $key ]['query'][ $keyword ] ) ) {
									unset( $settings[ $key ]['query'][ $keyword ] );
								}
							} else {
								unset( $settings[ $key ]['query'][ $keyword ] );
								jr_mt_messages( 'Sticky/Override setting(s) deleted because they are corrupt' );
							}
						}
					} else {
						$settings[ $key ] = $default_settings[ $key ];
						jr_mt_messages( 'Sticky/Override setting(s) deleted because they are corrupt' );
					}
				}
			}
			
			/*	Check for missing ['rel_url'] and build it
			*/
			$settings = jr_mt_missing_rel_url( $settings, JR_MT_HOME_URL );
			
			/*	Check if Site URL has changed by comparing corresponding ['url'] of ['home']
				setting in Aliases array with current Site URL.
			*/
			$site_url_changed = FALSE;
			if ( is_array( $settings['aliases'] ) ) {
				foreach ( $settings['aliases'] as $index => $alias ) {
					if ( $alias['home'] ) {
						if ( !jr_mt_same_url( $alias['prep'], JR_MT_HOME_URL ) ) {
							/*	Site URL has changed.
							*/
							$site_url_changed = TRUE;
							$old_site_url = $alias['url'];
							break;
						}
					}
				}
				if ( $site_url_changed ) {
					$settings = jr_mt_rebuild_display_url( $settings, $old_site_url );
					
					/*	See if there is an Alias entry for the new Site URL.
						If not, add one, and perhaps another with/without www.
					*/
					$settings = jr_mt_rebuild_alias_home( $settings );
					
					jr_mt_messages( 'Site Address (URL) has changed:  any URLs and Aliases in settings have been updated' );
					
					/*	Rebuild ['prep']
					*/
					$rebuild_prep = TRUE;
				}
			} else {
				$settings['aliases'] = jr_mt_init_aliases();
				jr_mt_messages( 'No Site Aliases defined; set to Default Aliases, if any' );
			}

			/*	Rebuild all ['prep'] entries, if required.
			*/
			if ( isset( $rebuild_prep ) ) {
				$settings = jr_mt_rebuild_prep( $settings );
				jr_mt_messages( 'URL settings, if any, have had their URL matching structures rebuilt' );
			}				
		} else {
			$settings = jr_mt_default_settings();
			jr_mt_messages( 'First use (or after plugin deleted): initialize Plugin Settings', 'immediate' );
		}
		if ( $settings !== $settings_original ) {
			if ( update_option( 'jr_mt_settings', $settings ) ) {
				jr_mt_messages( 'Plugin Settings have changed and were successfully updated', 'immediate' );
			} else {
				jr_mt_messages( 'Plugin Settings have changed but could not be updated', 'immediate' );
			}
		}
		jr_mt_messages( NULL, 'display' );
		jr_mt_messages( 'Check complete', 'immediate' );
		?>
		</ul>
		<p>
		While every attempt has been made,
		in the Settings Check above,
		to ensure that the plugin's
		Setting are valid,
		if you experience any unexpected behaviour,
		you should try to re-create the Settings from scratch.
		First, re-initialize all Settings to their defaults,
		by deactivating, deleting, installing and reactivating this plugin;
		then visit this Settings page again,
		as this is where the Settings are initialized.
		</p>
		<?php
		jr_mt_admin_init();
		
		echo '<form action="options.php" method="POST">';
		$permalink = get_option( 'permalink_structure' );
		if ( isset( $internal_settings['permalink'] ) ) {
			if ( $internal_settings['permalink'] !== $permalink ) {
				/*	Permalink Structure has been changed.
				*/
				if ( empty( $settings['url'] ) && empty( $settings['url_prefix'] ) && empty( $settings['url_asterisk'] ) ) {
					$update = TRUE;
				} else {
					?>
					<p>
					Permalink Structure has been changed.
					In the
					<b>
					Current Theme Selection Entries
					</b>
					Section just below,
					please review all
					URL=,
					URL Prefix=
					and
					URL Prefix*=
					entries,
					as they may need to be changed to reflect the new Permalink Structure.
					<br />
					<input type="checkbox" id="permalink" name="jr_mt_settings[permalink]" value="true" />
					Dismiss Warning
					</p>
					<?php
					$update = FALSE;
				}
			} else {
				$update = FALSE;
			}
		} else {
			/*	Permalink Internal Setting for Plugin not set,
				so initialize it to current Permalink Structure.
			*/
			$update = TRUE;
		}
		if ( $update ) {
			$internal_settings['permalink'] = $permalink;
			update_option( 'jr_mt_internal_settings', $internal_settings );
		}
		?>
		<h3>Overview</h3>
		<p>This Plugin allows you to selectively display Themes on your web site
		other than the Theme shown as
		<b>
		Active
		</b>
		on
		<b>
		Appearance-Themes
		</b>
		in the WordPress Admin panels.
		</p>
		<p>
		Below,
		Theme Selection entries can be created
		where each Entry specifies which of the installed themes shown on the Appearance-Themes Admin panel will be applied to:
		<ul class="jrmtpoints">
		<li>The Site Home</li>
		<li>An exact URL of any non-Admin page on this WordPress Site</li>
		<li>One or more URLs that begin with the partial URL you specify ("URL Prefix")</li>
		<li>One or more URLs that begin with the wildcard URL you specify ("URL Prefix*")</li>
		<li>Any URL containing a Specific Query Keyword (<code>?keyword</code> or <code>&keyword</code>)</li>
		<li>Any URL containing a Specific Query Keyword/Value pair (<code>?keyword=value</code> or <code>&keyword=value</code>)</li>
		<li>For the same site visitor, all non-Admin pages after a <b>Sticky</b> Query Keyword/Value pair is specified in any URL (Advanced Settings tab)</li>
		<li>AJAX URLs containing <code>admin-ajax.php</code> (Advanced Settings tab)</li>
		<li>All Pages (Advanced Settings tab)</li>
		<li>All Posts (Advanced Settings tab)</li>
		<li>Everything else, except what is specified above (Advanced Settings tab)</li>
		</ul>
		</p>
		<h3>Important Notes</h3>
		<?php
		if ( function_exists('is_multisite') && is_multisite() ) {
			echo "In a WordPress Network (AKA Multisite), Themes must be <b>Network Enabled</b> before they will appear as Available Themes on individual sites' Appearance-Themes panel.";
		}
		echo '<p>';
		echo "The Active Theme, defined to WordPress in the Appearance-Themes admin panel, is <b>$theme</b>.";
		if ( trim( $settings['current'] ) ) {
			echo " But it is being overridden by the Theme for Everything setting (see Advanced Settings tab), which set the plugin's default Theme to <b>";
			echo wp_get_theme( $settings['current'] )->Name;
			echo '</b>. You will not normally need to specify this default Theme in any of the other Settings on this page, though you will need to specify the WordPress Active Theme wherever you want it to appear. Or, if you specify, on the Advanced Settings tab, a different Theme for All Pages, All Posts or Everything, and wish to use the default Theme for one or more specific Pages, Posts or other non-Admin pages.';
		} else {
			echo ' You will not normally need to specify it in any of the Settings on this page. The only exception would be if you specify, on the Advanced Settings tab, a different Theme for All Pages, All Posts or Everything, and wish to use the Active Theme for one or more specific Pages, Posts or other non-Admin pages.';
		}
		echo '</p>';

		if ( jr_mt_plugin_update_available() ) {
			echo '<p>A new version of this Plugin (' . $jr_mt_plugin_data['Name'] . ') is available from the WordPress Repository.'
				. ' Updating as quickly as possible is strongly recommend because new versions fix problems that users like you have already reported.'
				. ' <a class="thickbox" title="' . $jr_mt_plugin_data['Name'] . '" href="' . network_admin_url()
				. 'plugin-install.php?tab=plugin-information&plugin=' . $jr_mt_plugin_data['slug']
				. '&section=changelog&TB_iframe=true&width=640&height=768">Click here</a> for more details.</p>';
		}
		?>
		<p>
		If a newly-added Theme Selection does not seem to be working, 
		especially if the associated web page does not display properly, 
		try deactivating any plugins that provide Caching. 
		You may find that you have to flush the plugin's Cache whenever you add or change a Theme Selection setting. 
		Also note that some Caching plugins only cache for visitors who are not logged in, 
		so be sure to check your site after logging out.
		</p>
		<p>
		Need more help?
		Please click on the
		<a href="#" onClick="jrMtTabs( 6, 6 );">Help tab</a>
		above
		for more information.
		</p>
		<hr />
		<?php
		
		//	Plugin Settings are displayed and entered here:
		settings_fields( 'jr_mt_settings' );
		do_settings_sections( 'jr_mt_settings_page' );
		?>
		<p>
		More comprehensive AJAX support is planned for future Versions of this plugin.
		</p>
		<p>
		&nbsp;
		</p>
		<p>
		<input name="jr_mt_settings[tab3]" type="submit" value="Save All Changes" class="button-primary" />
		</p>
		</form>
		<?php
	}

	?>
	</div>
	<div id="jr-mt-settings4" style="display: none;">
	<h3>
	Theme Options and Template Selection
	</h3>
	<p>
	This tab provides information on changing Theme Options
	(Widgets, Sidebars, Menus, Background, Header, etc.) 
	for all the different Themes used on a WordPress site.
	</p>
	<p>	
	Information on changing the Template for each Page or Post
	is found near the bottom of this tab.
	</p>
	<h3>
	Changing Theme Options
	</h3>
	<p>
	For the Active Theme, nothing changes when using the jonradio Multiple Themes plugin.
	Options for the Active Theme, 
	including Widgets, Sidebars, Menus, Background, Header and other Customizations supported by the Theme, 
	can be modified in the Admin panel using the Appearance menu items on the left sidebar.
	Some Themes also provide their own menu items in the left sidebar of the Admin panel,
	and these will still appear for the Active Theme when using this plugin.
	</p>
	<p>	
	It is more difficult to modify Options for installed Themes that are not the WordPress Active Theme.
	Building this functionality into this plugin is in the plans for a future Version, 
	but it is not clear just how practical that is, so the best that can be said is:
	<i>
	Maybe</i>.
	</p>
	<p>	
	For now, there are four approaches that can be used to change Options for an installed Theme that is not the Active Theme.
	The first works best if only one Theme has a lot of Options that need to be changed frequently:
	</p>
	<ol>
	<li>
	Make that Theme the Active Theme defined in the Appearance-Themes WordPress admin panel;
	</li>
	<li>
	If that meant changing the Active Theme,
	the previous Active Theme can be selected on the plugin's
	<b>
	Advanced Settings
	</b>
	tab
	in the
	<b>
	Select Theme for Everything
	</b>
	field 
	and it will be used everywhere except where you have specified
	another Theme in the Theme Selection entries for this plugin.
	</li>
	</ol>
	<p>
	For other situations,
	two multi-step Methods are available,
	and are described in the two Sections below.
	Both Methods work for most Theme Options,
	with the following exceptions:
	</p>
	<ol>
	<li>
	Menus really work well with Method #1, 
	but are severely restricted with Method #2;
	</li>
	<li>
	Widgets normally only work with Method #2;
	</li>
	<li>
	Using both Methods may cause conflicts;
	</li>
	<li>
	No matter which Method you choose,
	you may lose previously-set Theme Options.
	A Backup and Recovery of your WordPress Database
	would be required to avoid such a loss.
	</li>
	</ol>
	<p>
	Finally, there is the Method of Last Resort.
	Although it is the most obvious way to change Theme Options,
	it is also the most risky,
	in terms of loss of Options set for other Themes.
	</p>
	<h4>
	<u>
	Method #1</u>:
	Set the Theme Options with Live Preview.
	</h4>
	<p>
	Note: Widgets cannot be placed using this Method.
	</p>
	<ol>
	<li>
    Go to Appearance-Themes in the WordPress Admin panels.
	</li>
	<li>
	Mouse over the Theme that you wish to change
	and click the Live Preview button that appears.
	</li>
	<li>
    Use the left sidebar to modify the Theme Options. 
	Note that
	<b>
	Navigation
	</b>
	will not appear in the Live Preview sidebar until a Menu has been defined in Appearance-Menus. 
	Navigation is where you would set the custom menu(s) to be used for the Theme you are currently previewing.
	</li>
	<li>
    Click the Save & Activate button.
	</li>
	<li>
    Go immediately to Appearance-Themes in the WordPress Admin panels.
	</li>
	<li>
	Mouse over the Theme that had previously been the Active Theme
	and click the Activate button that appears
	to reactivate the Active Theme.
	</li>
	</ol>
	<h4>
	<u>
	Method #2</u>:
	Use the Theme Test Drive plugin.
	</h4>
	<p>
	Note: this approach only allows Menus to be set for one Theme. Using this method to assign one or more menus to a Theme will unassign menus for all other Themes.
	</p>
	<p>
	The jonradio Multiple Themes plugin (i.e. - this plugin) must be Deactivated, 
	and the Theme Test Drive plugin installed and activated.
	This enables each Theme to be selected with the Theme Test Drive plugin, 
	allowing the Theme's Options to be set 
	<i>
	as if
	</i>
	it were the Active Theme.
	</p>
	<ol>
	<li>
    Deactivate the jonradio Multiple Themes plugin.
	</li>
    <li>
	Install the Theme Test Drive plugin found at
	<a target="_blank" href="http://wordpress.org/plugins/theme-test-drive/">http://wordpress.org/plugins/theme-test-drive/</a>.
	</li>
	<li>
    Activate the Theme Test Drive plugin.
	</li>
	<li>
    Go to 
	<b>
	Appearance-Theme Test Drive
	</b>
	in the WordPress Admin panels.
	</li>
	<li>
    In the Usage section, select a Theme whose Options you wish to change.
	</li>
	<li>
    Push the Enable Theme Drive button at the bottom of the Admin panel.
	</li>
	<li>
	Make your changes to the Theme Options, including Widgets, Sidebars, Menus (see note above about Menus), Background, Header and other Customizations for this alternate Theme
	using the Appearance submenu
	in the WordPress Admin panels,
	just as you would for the Active Theme.
	</li>
	<li>
    If more than one Theme has Options that need changing, repeat Steps 4-8 for each Theme
	(except the Active Theme,
	which should be only changed
	<i>
	without
	</i>
	the Theme Test Drive plugin activated). 
	</li>
	<li>
    Deactivate the Theme Test Drive plugin.
	</li>
	<li>
    Activate this plugin (jonradio Multiple Themes).
	</li>
	<li>
    Changes to the Options for the Active Theme can now be made normally, just as you would without either plugin.
	</li>
	<li>
    Both the alternate and Active Themes should now display all Theme options properly when selected through the jonradio Multiple Themes plugin.
	</li>
	</ol>
	<h4>
	<u>
	Method of Last Resort</u>:
	Activate a Theme to change its Options.
	</h4>
	<p>
	Note:
	this approach is the most likely to cause the loss of Theme Options set in other Themes,
	though the risk does depend on the Theme and the Options that are set.
	</p>	
	<ol>
	<li>
    Go to Appearance-Themes in the WordPress Admin panels.
	</li>
	<li>
	Mouse over the Theme that you wish to change
	and click the Activate button that appears.
	</li>
	<li>
    Make the appropriate changes to Theme Options,
	clicking a Save button, if present
	(some Options are automatically saved; some are not).
	</li>
	<li>
    Go immediately to Appearance-Themes in the WordPress Admin panels.
	</li>
	<li>
	Mouse over the Theme that had previously been the Active Theme
	and click the Activate button that appears
	to reactivate the Active Theme.
	</li>
	</ol>
	<h3>
	Changing Templates
	</h3>	
	<p>
	Many Themes provide more than one Template.
	For each Page or Post, you can select the Template you want to use for that Page or Post.
	</p>
	<p>	
	For the Active Theme, nothing changes when using the jonradio Multiple Themes plugin.
	Select an alternate Template from the drop-down list in the Template field of the Page Attributes section of the Add New Page, Edit Page, Add New Post or Edit Post page of the Admin panels.
	Or the Template field in Quick Edit.
	</p>
	<p>
	It is more difficult to change Templates for Pages or Posts defined with the jonradio Multiple Themes plugin to use Installed Themes that are not the Active Theme.
	Building this functionality into this plugin is in the plans for a future Version.
	</p>
	<p>
	Use the Theme Test Drive plugin. 
	The jonradio Multiple Themes plugin (i.e. - this plugin) must be Deactivated, and the Theme Test Drive plugin installed and activated, 
	so that each Theme can be selected with the Theme Test Drive plugin, 
	allowing the Theme's Template to be set for each Page or Post using that Theme 
	<i>
	as if
	</i>
	it were the Active Theme.
	</p>
	<ol>
	<li>
    Deactivate the jonradio Multiple Themes plugin.
	</li>
    <li>
	Install the Theme Test Drive plugin found at
	<a target="_blank" href="http://wordpress.org/plugins/theme-test-drive/">http://wordpress.org/plugins/theme-test-drive/</a>.
	</li>
	<li>
    Activate the Theme Test Drive plugin.
	</li>
	<li>
    Go to 
	<b>
	Appearance-Theme Test Drive
	</b>
	in the WordPress Admin panels.
	</li>
	<li>
    In the Usage section, select a Theme whose Templates need to be changed for a Post or Page.
	</li>
	<li>
    Push the Enable Theme Drive button at the bottom of the Admin panel.
	</li>
	<li>
	Go to Posts-All Posts or Pages-All Pages in the WordPress Admin panels.
	</li>
	<li>
	For each Page or Post where a Template needs to be changed for this Theme,
	mouse over the Page or Post title and click on Quick Edit.
	</li>
	<li>
	Change the Template field.
	</li>
	<li>
	Click the Update button.
	</li>
	<li>
	Repeat Steps 8-10 for each Page or Post that requires a change to Template for this Theme.
	</li>
	<li>
    If more than one Theme has Pages or Posts with Templates that need to be changed,
	repeat Steps 4-11 for each Theme
	(except the Active Theme,
	where Template changes should only be made
	<i>
	without
	</i>
	the Theme Test Drive plugin activated). 
	</li>
	<li>
    Deactivate the Theme Test Drive plugin.
	</li>
	<li>
    Activate this plugin (jonradio Multiple Themes).
	</li>
	<li>
    Changing Templates for the Active Theme can now be made normally, just as you would without either plugin.
	</li>
	<li>
    Both the alternate and Active Themes should now display the correct Template when selected through the jonradio Multiple Themes plugin.
	</li>
	</ol>
	</div>
	<div id="jr-mt-settings5" style="display: none;">
	<h3>
	System Information
	</h3>
	<p>
	WordPress DEBUG mode is currently turned
	<?php
	if ( TRUE === WP_DEBUG ) {
		echo 'on';
	} else {
		echo 'off';
	}
	echo ". It is controlled by the <code>define('WP_DEBUG', true);</code> statement near the bottom of <code>"
		. ABSPATH
		. 'wp-config.php</code></p>';
	$posix = function_exists( 'posix_uname' );
	echo '<p>You are currently running:<ul class="jrmtpoints">'
		. "<li>The {$jr_mt_plugin_data['Name']} plugin Version {$jr_mt_plugin_data['Version']}</li>"
		. '<ul class="jrmtpoints">' . "<li>The Path to the plugin's directory is <code>" . rtrim( jr_mt_path(), '/' ) . '</code></li>'
		. "<li>The URL to the plugin's directory is <code>" . plugins_url() . "/{$jr_mt_plugin_data['slug']}</code></li></ul>"
		. "<li>The Active Theme is $theme Version $theme_version</li>"
		. '<ul class="jrmtpoints">'
		. "<li>The Path to the Active Theme's stylesheet directory is <code>" . get_stylesheet_directory() . '</code></li>'
		. "<li>The Path to the Active Theme's template directory is <code>" . get_template_directory() . '</code></li></ul>'
		. '<li>Site Address (URL) is <code>' . JR_MT_HOME_URL . '</code></li>'
		. '<li>WordPress Address (URL) is <code>' . site_url() . '</code></li>';
	$permalink = get_option( 'permalink_structure' );
	if ( empty( $permalink ) ) {
		$permalink = 'Default (Query <code>/?p=123</code>)';
	} else {
		$permalink = "<code>$permalink</code>";
	}
	echo "<li>The current Permalink Structure is $permalink";
	echo "<li>WordPress Version $current_wp_version</li>";
	echo '<ul class="jrmtpoints"><li>WordPress language is set to ' , get_bloginfo( 'language' ) . '</li></ul>';
	echo '<li>' . php_uname( 's' ) . ' operating system, Release/Version ' . php_uname( 'r' ) . ' / ' . php_uname( 'v' ) . '</li>';
	if ( $posix ) {
		$array = posix_getpwuid( posix_getuid() );
		$user = $array['name'];
		echo "<li>Real operating system User ID that runs WordPress is $user</li>";
		$array = posix_getpwuid( posix_geteuid() );
		$user = $array['name'];
		echo "<li>Effective operating system User ID that runs WordPress is $user</li>";
	}
	echo '<li>' . php_uname( 'm' ) . ' computer hardware</li>';
	echo '<li>Host name ' . php_uname( 'n' ) . '</li>';
	echo '<li>php Version ' . phpversion() . '</li>';
	echo '<ul class="jrmtpoints"><li>php memory_limit ' . ini_get('memory_limit') . '</li>';
	if ( !$posix ) {
		echo '<li>POSIX functions are not available</li>';
	}
	echo '</ul><li>Zend engine Version ' . zend_version() . '</li>';
	echo '<li>Web Server software is ' . getenv( 'SERVER_SOFTWARE' ) . '</li>';
	if ( function_exists( 'apache_get_version' ) && ( FALSE !== $apache = apache_get_version() ) ) {
		echo '<ul class="jrmtpoints"><li>Apache Version' . "$apache</li></ul>";
	}
	global $wpdb;
	echo '<li>MySQL Version ' . $wpdb->get_var( 'SELECT VERSION();', 0, 0 ) . '</li>';

	echo '</ul></p>';
	
	$paths = array(
		'/..',
		'/',
		'/wp-content/',
		'/wp-content/plugins/',
		'/wp-content/plugins/' . dirname( jr_mt_plugin_basename() ),
		'/wp-content/plugins/' . dirname( jr_mt_plugin_basename() ) . '/readme.txt'
	);
	echo '<h3>File Permissions</h3><p>All of the Paths shown below are relative to the WordPress Site Path <code>'
		. ABSPATH
		. '</code><br />The first ("/..") is the Parent Directory <code>'
		. dirname( ABSPATH )
		. '/</code> and the second ("/") is the WordPress Site Path itself.</p><table class="widefat"><thead><tr><th>Path</th><th>Type</th><th>Read</th><th>Write</th>';
	if ( $posix ) {
		echo '<th>Owner</th><th>Group</th>';
	}
	echo '</tr></thead><tbody>';
	foreach ( $paths as $path ) {
		$full_path = ABSPATH . jr_mt_substr( $path, 1 );
		if ( is_dir( $full_path ) ) {
			$type = 'Directory';
		} else {
			$type = 'File';
		}
		if ( is_readable( $full_path ) ) {
			$read = 'Yes';
		} else {
			$read = 'No';
		}
		if ( is_writeable( $full_path ) ) {
			$write = 'Yes';
		} else {
			$write = 'No';
		}
		if ( $posix ) {
			if ( FALSE === ( $uid = fileowner( $full_path ) ) ) {
				$user = '-';
				$group = '-';
			} else {
				$array = posix_getpwuid( $uid );
				$user = $array['name'];
				$array = posix_getgrgid( filegroup( $full_path ) );
				$group = $array['name'];
			}
		}
		echo "<tr><td>$path</td><td>$type</td><td>$read</td><td>$write</td>";
		if ( $posix ) {
			echo "<td>$user</td><td>$group</td>";
		}
		echo '<tr>';
	}
	echo '</tbody></table>';
	?>
	</div>
	<div id="jr-mt-settings6" style="display: none;">
	<h3>
	If Any Theme Uses AJAX
	</h3>
	<p>
	More and more Themes,
	especially Paid Themes,
	use AJAX,
	which adds impressive-looking dynamic features to a web site.
	</p>
	<p>
	AJAX also adds complexity.
	Instead of a single web page having a single URL,
	AJAX uses additional URLs to dynamically insert content into a web page after it is initially displayed.
	</p>
	<p>
	Each of those URLs must be considered when creating
	Theme Selection settings for this plugin.
	Otherwise,
	this plugin will return the incorrect Theme for some portions of
	a web page,
	which may cause the
	display of incorrect content,
	or no content at all.
	</p>
	<p>
	Version 7.1 of this plugin introduced an AJAX Advanced Setting
	for URLs that include
	<code>admin-ajax.php</code>,
	a common AJAX technique in WordPress.
	Additional AJAX support is planned for future versions of this plugin,
	as well as a Diagnostic Tool for determining URLs being used by AJAX,
	most likely as a separate plugin.
	</p>
	<h3>
	An Alternative to This Plugin
	</h3>
	<p>
	WordPress was not designed with the idea in mind of multiple Themes on a single Site.
	Which is why this plugin struggles to provide full multi-theme capabilities.
	</p>
	<p>
	An alternative to this plugin
	is a
	<a href="http://codex.wordpress.org/Create_A_Network">WordPress Network</a>,
	also known as Multisite.
	Each WordPress Site within a WordPress Network can have a different Theme.
	WordPress was built to fully support Multiple Themes used in this way.
	</p>
	<p>
	What is less obvious,
	is that a WordPress Network of Sites
	can be designed to appear as if it is a single integrated web site.
	For example,
	using the Subdirectory option of a WordPress Network:
	</p>
	<ol>
	<li>
	Site 1 could be
	<code>example.com</code>,
	the web site's home page, 
	and any other web pages with the same Theme;
	</li>
	<li>
	Site 2 could be
	<code>example.com/forum</code>,
	the discussion forum portion of your web site
	with a different Theme;
	</li>
	<li>
	Site 3 could be 
	<code>example.com/news</code>
	for a News section with its own Theme;
	and
	</li>
	<li>
	Site 4 could be
	<code>example.com/store</code>
	with a fourth Theme for a Store.
	</li>
	</ol>
	<p>
	Admittedly,
	extra effort will be required to make a WordPress Network look like a single web site,
	especially if you rely on automatically-created Menus.
	Menu entries will have to be manually created to point to other Sites within the WordPress Network.
	</p>
	<h3>
	Need Help?
	</h3>
	<p>
	Support has moved to the ZATZLabs site and is no longer provided on the WordPress.org forums. Please visit the new <A HREF=“http://zatzlabs.com/forums/“>ZATZLab Forums</a>. If you need a timely reply from the developer, please <a href=“http://zatzlabs.com/submit-ticket/“>open a ticket</a>.
 Because this plugin was adopted and is being supported by a new developer, you may want to refer to the many support questions that the original author answered both online and via email since the plugin was first released in 2012. They can be found at the old <a href="http://wordpress.org/support/plugin/jonradio-multiple-themes">WordPress support forums</a>.
	</p>
	<p>
	For information on other adopted jonradio plugins,
	<a target="_blank" href="http://zatzlabs.com/plugins/">click here</a>.
	</p>
	<h3>
	Want to Help?
	</h3>
	<p>
	You can help by 
	<a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/jonradio-multiple-themes">reviewing this plugin</a> 
	for the WordPress Plugin Directory,
	and telling other people that it works for your particular combination of Plugin version and WordPress version
	in the Compability section of the
	<a target="_blank" href="http://wordpress.org/plugins/jonradio-multiple-themes/">WordPress Directory entry for this plugin</a>.
	</p>
	</div>
	
	</div>
	<?php
	/*	</div> ends the <div class="wrap"> at the beginning
	*/
}

require_once( jr_mt_path() . 'includes/admin-sections.php' );
require_once( jr_mt_path() . 'includes/admin-validate.php' );

?>