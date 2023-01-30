<?php

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function jr_mt_messages( $message, $action = 'count' ) {
	global $jr_mt_messages;
	switch ( $action ) {
		case 'display':
			if ( isset( $jr_mt_messages ) ) {
				foreach ( $jr_mt_messages as $echo => $count ) {
					echo '<li>' . $echo;
					if ( $count > 1 ) {
						echo ' ('. $count . ' times)';
					}
					echo '</li>';
				}
				unset( $jr_mt_messages );
			}
			if ( empty( $message ) ) {
				break;
			}
			/*	Purposely let it fall through to output message,
				after stored messages are output.
			*/
		case 'immediate':
			echo '<li>' . $message . '</li>';
			break;
		case 'count':
			if ( isset( $jr_mt_messages[ $message ] ) ) {
				++$jr_mt_messages[ $message ];
			} else {
				$jr_mt_messages[ $message ] = 1;
			}
			break;
	}
}

function jr_mt_theme_entry( $type, $theme = '', $display1 = NULL, $display2 = NULL ) {
	$three_dots = '&#133;';
	$before = '<li>Delete <input type="checkbox" id="del_entry" name="jr_mt_settings[del_entry][]" value="';
	$after = '" /> &nbsp; ';
	/*	Fortunately, wp_get_theme() creates an Object if $theme does not exist,
		complete with a Name entry that matches the folder name.
		
		TODO:	flag the situation of a non-existent Theme (i.e. - was deleted after entry was created)
	*/
	$theme_equals = 'Theme=' . wp_get_theme( $theme )->Name . '; ';
	switch ( $type ) {
		case 'Query':
			echo $before
				. 'query'
				. '='
				. $display1
				. '='
				. $display2
				. $after
				. $theme_equals;
			if ( '*' !== $display2 ) {
				$settings = get_option( 'jr_mt_settings' );
				$sticky = isset( $settings['remember']['query'][ $display1 ][ $display2 ] );
				$override = isset( $settings['override']['query'][ $display1 ][ $display2 ] );
				if ( $sticky ) {
					if ( $override ) {
						echo 'Sticky/Override ';
					} else {
						echo 'Sticky ';
					}
				} else {
					if ( $override ) {
						echo 'Override ';
					}
				}
			}
			echo 'Query='
				. '<code>'
				. JR_MT_HOME_URL 
				. "/</code>$three_dots<code>/?"
				. '<b><input type="text" readonly="readonly" disable="disabled" name="jr_mt_delkw" value="'
				. $display1
				. '" size="'
				. jr_mt_strlen( $display1 )
				. '" /></b>'
				. '=';
			if ( '*' === $display2 ) {	
				echo '</code>' . $three_dots;
			} else {
				echo '<b><input type="text" readonly="readonly" disable="disabled" name="jr_mt_delkwval" value="'
					. $display2
					. '" size="'
					. jr_mt_strlen( $display2 )
					. '" /></b></code>';
			}
			break;
		case 'url':
		case 'url_prefix':
		case 'url_asterisk':
			echo $before
				. $type
				. '='
				. 'url'
				. '='
				. $display1
				. $after
				. $theme_equals
				. $display2
				. '=<code>' . $display1 . '</code>';
			break;
		case 'wordpress':
			echo '<li><a href="'
				. get_admin_url()
				. 'themes.php" class="button-primary">Change</a> &nbsp; '
				. 'Theme='
				. wp_get_theme()->Name
				. ', the Theme chosen as Active from Appearance-Themes in the WordPress Admin panels';
			break;
		default:
			echo $before
				. $type
				. $after
				. $theme_equals
				. $display1;
			if ( 'site_home' === $type ) {
				echo ' (<code>' . JR_MT_HOME_URL . '</code>) setting';
			} else {
				echo ' setting (see Advanced Settings tab)';
			}
			break;
	}
	echo '</li>';
}

//	$theme_name is the name of the Theme's folder within the Theme directory
function jr_mt_themes_field( $field_name, $theme_name, $setting, $excl_current_theme ) {
	echo "<select id='$field_name' name='$setting" . "[$field_name]' size='1'>";
	if ( empty( $theme_name ) ) {
		$selected = 'selected="selected"';
	} else {
		$selected = '';
	}
	echo "<option value='' $selected></option>";
	foreach ( jr_mt_all_themes() as $folder => $theme_obj ) {
		if ( $excl_current_theme ) {
			if ( ( jr_mt_current_theme( 'stylesheet' ) == $theme_obj['stylesheet'] ) && ( jr_mt_current_theme( 'template' ) == $theme_obj['template'] ) ) {
				//	Skip the Current Theme
				continue;
			}
		}
		if ( $theme_name === $folder ) {
			$selected = 'selected="selected"';
		} else {
			$selected = '';
		}
		$name = $theme_obj->Name;
		echo "<option value='$folder' $selected>$name</option>";
	}
	echo '</select>' . PHP_EOL;
}

/**
 * Update available for Plugin?
 *
 * @return bool - TRUE if an update is available in the WordPress Repository,
 *	FALSE if no update is available or if the update_plugins transient is not available
 *	(which also results in an error message). 
 **/
function jr_mt_plugin_update_available() {
	global $jr_mt_update_plugins;
	if ( !isset( $jr_mt_update_plugins ) ) {
		$transient = get_site_transient( 'update_plugins' );
		if ( FALSE === $transient ) {
			//	Error
			return FALSE;
		} else {
			$jr_mt_update_plugins = $transient;
		}
	}
	if ( empty( $jr_mt_update_plugins->response ) ) {
		return FALSE;
	}
	return array_key_exists( jr_mt_plugin_basename(), $jr_mt_update_plugins->response );
}

/**
 * Prepare URL Query Value
 * 
 * Sanitize and standardize a URL Query Value for storage in a database.
 * Does not support ?keyword[]=value, i.e. - $value cannot be an Array. 
 *
 * @param    string  $value		URL Query Value to be sanitized and standardized; will fail if array 
 * @return   string             URL Query Value after being sanitized and standardized
 */
function jr_mt_prep_query_value( $value ) {
	return str_ireplace( '%e2%80%8e', '', jr_mt_strtolower( trim( $value ) ) );
}
function jr_mt_prep_query_keyword( $keyword ) {
	return jr_mt_prep_query_value( $keyword );
}

/**
 * Sanitize a URL from a Text Form field intended for database storage
 */
function jr_mt_sanitize_url( $url ) {
	/*	Handle troublesome %E2%80%8E UTF Left-to-right Mark (LRM) suffix first.
	*/
	if ( FALSE === stripos( $url, '%E2%80%8E' ) ) {
		if ( FALSE === stripos( rawurlencode( $url ), '%E2%80%8E' ) ) {
			$clean_url = $url;
		} else {
			$clean_url = rawurldecode( str_ireplace( '%E2%80%8E', '', rawurlencode( $url ) ) );
		}
	} else {
		$clean_url = str_ireplace( '%E2%80%8E', '', $url );
	}
	$clean_url = rawurldecode( trim( $clean_url ) );

	return $clean_url;
}

/**
 * Make URL Relative to Site URL
 * 
 */
function jr_mt_relative_url( $url, $site_url ) {
	$url_path_array = parse_url( $url );
	$url_path = $url_path_array['path'];
	if ( !empty( $url_path_array['query'] ) ) {
		$url_path .= '?' . $url_path_array['query'];
	}
	$site_url_path = parse_url( $site_url, PHP_URL_PATH );
	return trim( jr_mt_substr( $url_path, jr_mt_strlen( $site_url_path ) ), '/\\' );
}

function jr_mt_missing_rel_url( $settings, $relative_to_url ) {
	global $jr_mt_url_types;
	foreach ( $jr_mt_url_types as $url_type ) {
		if ( isset( $settings[ $url_type ] ) && is_array( $settings[ $url_type ] ) ) {
			foreach ( $settings[ $url_type ] as $index => $url_array ) {
				if ( !isset( $url_array['rel_url'] ) ) {
					$settings[ $url_type ][ $index ]['rel_url'] = jr_mt_relative_url( $url_array['url'], $relative_to_url );
					jr_mt_messages( 'Missing Relative URL added to URL setting' );
				}
			}
		}
	}
	return $settings;
}

function jr_mt_rebuild_display_url( $settings, $old_site_url ) {
	global $jr_mt_url_types;
	foreach ( $jr_mt_url_types as $url_type ) {
		if ( isset( $settings[ $url_type ] ) && is_array( $settings[ $url_type ] ) ) {
			foreach ( $settings[ $url_type ] as $index => $url_array ) {
				if ( !isset( $url_array['rel_url'] ) ) {
					$settings[ $url_type ][ $index ]['rel_url'] = jr_mt_relative_url( $url_array['url'], $old_site_url );
				}
				$settings[ $url_type ][ $index ]['url'] = JR_MT_HOME_URL . '/' . $settings[ $url_type ][ $index ]['rel_url'];
			}	
		} else {
			$settings[ $url_type ] = array();
		}
	}
	return $settings;
}

function jr_mt_rebuild_alias_home( $settings ) {
	/*	See if there is an Alias entry for the new Site URL.
		If not, add one, and perhaps another with/without www.
	*/
	$no_home = TRUE;
	foreach ( $settings['aliases'] as $index => $alias ) {
		if ( $settings['aliases'][ $index ]['home'] = jr_mt_same_url( $alias['prep'], JR_MT_HOME_URL ) ) {
			$no_home = FALSE;
		}
	}
	if ( $no_home ) {
		$settings['aliases'] = array_merge( $settings['aliases'], jr_mt_init_aliases() );
	}
	return $settings;
}

function jr_mt_rebuild_prep( $settings ) {
	/*	Assumes that ['url'*] and ['aliases'] Settings have been checked,
		so doesn't check if they exist and are array.
		Be sure to do that if wrappering this function with get_ and update_option().
	*/
	global $jr_mt_url_types;
	foreach ( $jr_mt_url_types as $url_type ) {
		foreach ( $settings[ $url_type ] as $url_key => $url_array ) {
			$settings[ $url_type ][ $url_key ]['prep'] = array();
			$rel_url = $url_array['rel_url'];
			foreach ( $settings['aliases'] as $index => $alias ) {
				$settings[ $url_type ][ $url_key ]['prep'][ $index ] = jr_mt_prep_url( $alias['url'] . '/' . $rel_url );
			}
		}
	}
	return $settings;
}

?>