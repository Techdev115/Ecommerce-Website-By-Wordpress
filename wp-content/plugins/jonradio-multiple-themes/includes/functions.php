<?php

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

global $jr_mt_path;
$jr_mt_path = plugin_dir_path( JR_MT_FILE );
/**
* Return Plugin's full directory path with trailing slash
* 
* Local XAMPP install might return:
*	C:\xampp\htdocs\wpbeta\wp-content\plugins\jonradio-multiple-themes/
*
*/
function jr_mt_path() {
	global $jr_mt_path;
	return $jr_mt_path;
}

global $jr_mt_plugin_basename;
$jr_mt_plugin_basename = plugin_basename( JR_MT_FILE );
/**
* Return Plugin's Basename
* 
* For this plugin, it would be:
*	jonradio-multiple-themes/jonradio-multiple-themes.php
*
*/
function jr_mt_plugin_basename() {
	global $jr_mt_plugin_basename;
	return $jr_mt_plugin_basename;
}

if ( !function_exists( 'get_plugin_data' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
}

global $jr_mt_plugin_data;
$jr_mt_plugin_data = get_plugin_data( JR_MT_FILE );
$jr_mt_plugin_data['slug'] = basename( dirname( JR_MT_FILE ) );

global $jr_mt_options_cache;
$all_options = wp_load_alloptions();
$jr_mt_options_cache['stylesheet'] = $all_options['stylesheet'];
$jr_mt_options_cache['template'] = $all_options['template'];

/*	Handle this Odd Situation:
	For WordPress 4.0 and all previous versions,
	wp_get_themes() returns array() when this plugin is Network Activated.
	Waiting until Action 'plugins_loaded' overcomes this problem,
	but potentially creates other problems.
*/	
if ( is_plugin_active_for_network( $jr_mt_plugin_basename ) ) {
	add_action( JR_MT_WP_GET_THEMES_ACTION, 'jr_mt_wp_get_themes_fix', JR_MT_RUN_FIRST );
	function jr_mt_wp_get_themes_fix() {
		DEFINE( 'JR_MT_WP_GET_THEMES_WORKS', TRUE );
		/*	Force the next request for Stylesheet or Template
			to process the Theme Selection logic,
			even if previous requests have cached the Theme.
		*/
		unset( $GLOBALS['jr_mt_theme'] );
		if ( is_admin() ) {
			global $jr_mt_all_themes_cache;
			$jr_mt_all_themes_cache = wp_get_themes();
			update_option( 'jr_mt_all_themes', $jr_mt_all_themes_cache );
		}
	}
} else {
	DEFINE( 'JR_MT_WP_GET_THEMES_WORKS', TRUE );
}
function jr_mt_all_themes() {
	if ( defined( 'JR_MT_WP_GET_THEMES_WORKS' ) ) {
		global $jr_mt_all_themes_cache;
		if ( isset( $jr_mt_all_themes_cache ) ) {
			$return = $jr_mt_all_themes_cache;
		} else {
			$return = wp_get_themes();
		}
	} else {
		/*	Probably not valid,
			typically empty array.
			Better to store and retrieve from Settings myself.
		*/
		if ( FALSE === ( $return = get_option( 'jr_mt_all_themes' ) ) ) {
			$return = wp_get_themes();
		}
	}
	return $return;
}

/**
 * Check for missing Settings and set them to defaults
 * 
 * Ensures that the Named Setting exists, and populates it with defaults for any missing values.
 * Safe to use on every execution of a plugin because it only does an expensive Database Write
 * when it finds missing Settings.
 *
 * Does not delete any key not found in $defaults.
 *
 * @param	string	$name		Name of Settings as looked up with get_option()
 * @param	array	$defaults	Each default Settings value in [key] => value format
 * @return  bool/Null			Return value from update_option(), or NULL if update_option() not called
 */
function jr_mt_missing_settings( $name, $defaults ) {
	$updated = FALSE;
	if ( FALSE === ( $settings = get_option( $name ) ) ) {
		$settings = $defaults;
		$updated = TRUE;
	} else {
		foreach ( $defaults as $key => $value ) {
			if ( !isset( $settings[$key] ) ) {
				$settings[$key] = $value;
				$updated = TRUE;
			}
		}
	}
	if ( $updated ) {
		$return = update_option( $name, $settings );
	} else {
		$return = NULL;
	}
	return $return;
}

/*	As well as dealing with the low probability that a single mb_ function has been disabled in a php.ini,
	this also supports older versions of PHP as mb_ functions were introduced one by one over a number of php versions.
*/
if ( function_exists( 'mb_substr' ) ) {
	function jr_mt_substr() {
		$args = func_get_args();
		if ( isset( $args[2] ) ) {
			return mb_substr( $args[0], $args[1], $args[2] );
		} else {
			return mb_substr( $args[0], $args[1] );
		}
	}
} else {
	function jr_mt_substr() {
		$args = func_get_args();
		if ( isset( $args[2] ) ) {
			return substr( $args[0], $args[1], $args[2] );
		} else {
			return substr( $args[0], $args[1] );
		}
	}
}
if ( function_exists( 'mb_strlen' ) ) {
	function jr_mt_strlen( $string ) {
		return mb_strlen( $string );
	}
} else {
	function jr_mt_strlen( $string ) {
		return strlen( $string );
	}
}
if ( function_exists( 'mb_strtolower' ) ) {
	function jr_mt_strtolower( $string ) {
		return mb_strtolower( $string );
	}
} else {
	function jr_mt_strtolower( $string ) {
		return strtolower( $string );
	}
}

/**
 * Return WordPress Current Theme, as defined in Appearance Admin panels
 *
 * Obtains Folder Name of Current Theme, from 'template' option of wp_load_alloptions().
 *
 * @param	string		$option		parameter to select current template or stylesheet
 * @return	string		type		Folder Name of Current Theme
 */
function jr_mt_current_theme( $option ) {
	global $jr_mt_options_cache;
	return $jr_mt_options_cache[$option];
}

/**
 * What Themes are defined to Plugin?
 *
 * @return arr - a list of Themes (folder names) defined in Settings of Plugin, plus Active WordPress Theme
 **/
function jr_mt_themes_defined() {
	$themes = array( jr_mt_current_theme( 'stylesheet' ), jr_mt_current_theme( 'template' ) );
	$settings = get_option( 'jr_mt_settings' );
	foreach ( $settings as $key => $value ) {
		switch ( $key ) {
			case 'url':
			case 'url_prefix':
			case 'url_asterisk':
				foreach ( $value as $arr ) {
					$themes[] = $arr['theme'];
				}
				break;
			case 'query':
				foreach ( $value as $keyword => $arr1 ) {
					foreach ( $arr1 as $value => $theme ) {
						$themes[] = $theme;
					}
				}
				break;
			case 'all_pages':
			case 'all_posts':
			case 'site_home':
			case 'current':
				if ( !empty( $value ) ) {
					$themes[] = $value;
				}
				break;
		}
	}
	return array_unique( $themes );
}

/**
 * Do two URLs point at the same location on a web site?
 * 
 * Preps URL, if string
 *
 * @param    string/array  $url1	URL to compare, a string, or an array in special format created by companion function
 * @param    string/array  $url2	URL to compare, a string, or an array in special format created by companion function
 * @return   bool					bool TRUE if URL matches prefix; FALSE otherwise
 */
function jr_mt_same_url( $url1, $url2 ) {
	if ( is_string( $url1 ) ) {
		$url1 = jr_mt_prep_url( $url1 );
	}
	if ( is_string( $url2 ) ) {
		$url2 = jr_mt_prep_url( $url2 );
	}
	return ( $url1 == $url2 );
}

/**
 * Does a specified Prefix URL match the given URL?
 * 
 * Preps URL, if string.
 * Note:  parameters MUST be in the right order
 *
 * @param    string/array  $prefix	front part of a URL to compare, a string, or an array in special format created by companion function
 * @param    string/array  $url		full URL to compare, a string, or an array in special format created by companion function
 * @return   bool					bool TRUE if Prefix matches first part of URL; FALSE otherwise
 */
function jr_mt_same_prefix_url( $prefix, $url ) {
	if ( is_string( $prefix ) ) {
		$prefix = jr_mt_prep_url( $prefix );
	}
	if ( is_string( $url ) ) {
		$url = jr_mt_prep_url( $url );
	}
	if ( $url['host'] === $prefix['host'] ) {
		if ( $url['port'] === $prefix['port'] ) {
			if ( $url['path'] === $prefix['path'] ) {
				/*	Host and Path both exactly match for URL and Prefix specified.
				*/
				if ( array() === $prefix['query'] ) {
					$match = TRUE;
				} else {
					/*	Now the hard part:  determining a legitimate prefix match for Query
					*/
					foreach ( $prefix['query'] as $prefix_keyword => $prefix_value_array ) {
						foreach ( $prefix_value_array as $prefix_value => $prefix_equalsign ) {
							$one_match = FALSE;
							foreach ( $url['query'] as $url_keyword => $url_value_array ) {
								foreach( $url_value_array as $url_value => $url_equalsign ) {
									if ( jr_mt_substr( $url_keyword, 0, jr_mt_strlen( $prefix_keyword ) ) === ( string ) $prefix_keyword ) {
										if ( $prefix_equalsign ) {
											if ( $url_equalsign ) {
												if ( jr_mt_substr( $url_value, 0, jr_mt_strlen( $prefix_value ) ) === ( string ) $prefix_value ) {
													$one_match = TRUE;
												}
											}
										} else {
											$one_match = TRUE;
										}
									}
								}
							}
						}
						/*	All Prefix Queries must match.
						*/
						if ( FALSE === $one_match ) {
							return FALSE;
						}
					}
					$match = TRUE;
				}
			} else {
				/*	Paths must exactly match if Prefix specifies Query
				*/
				if ( array() === $prefix['query'] ) {
					/*	No Query in Prefix, so check Path for Prefix match
					*/
					$match = ( $prefix['path'] === jr_mt_substr( $url['path'], 0, jr_mt_strlen( $prefix['path'] ) ) );				
				} else {
					$match = FALSE;
				}
			}
		} else {
			/*	Perhaps unnecessary restriction:
				If Hosts match, then so much Ports.
			*/
			$match = FALSE;
		}
	} else {
		if ( ( 0 === $prefix['port'] ) && ( '' === $prefix['path'] ) && ( array() === $prefix['query'] ) ) {
			/*	No Path or Query in Prefix, so check Host for Prefix match
			*/
			$match = ( $prefix['host'] === jr_mt_substr( $url['host'], 0, jr_mt_strlen( $prefix['host'] ) ) );
		} else {
			/*	Hosts must exactly match if Prefix specifies Path or Query
			*/
			$match = FALSE;
		}
	}
	return $match;
}

function jr_mt_same_prefix_url_asterisk( $prefix, $url ) {
	if ( is_string( $prefix ) ) {
		$prefix = jr_mt_prep_url( $prefix );
	}
	if ( is_string( $url ) ) {
		$url = jr_mt_prep_url( $url );
	}
	$path_prefix = explode( '/', $prefix['path'] );
	$path_url = explode( '/', $url['path'] );
	foreach ( $path_prefix as $i => $directory ) {
		if ( '*' === $directory ) {
			$path_url[ $i ] = '*';
		}
	}
	$url['path'] = implode( '/', $path_url );
	return jr_mt_same_prefix_url( $prefix, $url );
}	

/**
 * Standardize a URL into an array of values that can be accurately compared with another
 * 
 * Preps URL, by removing any UTF Left-to-right Mark (LRM), usually found as a suffix, 
 * translating the URL to lower-case, removing prefix http[s]//:, 
 * any embedded index.php and any trailing slash or #bookmark,
 * and breaks up ?keyword=value queries into array elements.
 *
 * Structure/Elements of Array returned:
 *	[host] - domain.com - all subdomains, including www., are included
 *	[path] - dir/file.ext
 *	[query] - any Queries (e.g. - "?kw=val&kw2=val2") broken up as follows:
 *		[$keyword] => [$value] => $equalsign
 *			$value - blank if not present
 *			$equalsign - bool indicating if = was present in URL
 *		Format prior to Version 7.0:
 *			[$keyword] => $value with preceding equals sign, only if equals sign was present
 * To simplify processing of this Array, zero length strings and empty arrays are used,
 * rather than NULL entries or missing array elements.
 *
 * @param    string  $url	URL to create an array from, in special format for accurate comparison
 * @return   array			array of standardized attributes of the URL (see structure above)
 */
function jr_mt_prep_url( $url ) {
	/*	Handle troublesome %E2%80%8E UTF Left-to-right Mark (LRM) suffix first.
	*/
	if ( FALSE === strpos( $url, '%E2%80%8E' ) ) {
		if ( FALSE === strpos( rawurlencode( $url ), '%E2%80%8E' ) ) {
			$url_clean = $url;
		} else {
			$url_clean = rawurldecode( str_replace( '%E2%80%8E', '', rawurlencode( $url ) ) );
			/*	mb_str_replace() does not exist because str_replace() is binary-safe.
			*/
		}
	} else {
		$url_clean = str_replace( '%E2%80%8E', '', $url );
	}
	$url_clean = str_replace( '\\', '/', trim( $url_clean ) );
	
	/*	parse_url(), especially before php Version 5.4.7,
		has a history of problems when Scheme is not present,
		especially for LocalHost as a Host,
		so add a prefix of http:// if :// is not found
	*/
	if ( FALSE === strpos( $url_clean, '://' ) ) {
		$url_clean = "http://$url_clean";
	}
	
	$parse_array = parse_url( jr_mt_strtolower( $url_clean ) );
	/*	Get rid of URL components that do not matter to us in our comparison of URLs
	*/
	foreach ( array( 'scheme', 'user', 'pass', 'fragment' ) as $component ) {
		unset ( $parse_array[ $component ] );
	}
	/*	Remove standard HTTP Port 80 and HTTPS Port 443, if present.
	*/
	$parse_array['port'] = jr_mt_non_default_port( $parse_array, 'port' );
	if ( isset( $parse_array['path'] ) ) {
		/*	Remove any index.php occurences in path, since these can be spurious in IIS
			and perhaps other environments.
		*/
		$parse_array['path'] = str_replace( 'index.php', '', $parse_array['path'] );
		/*	Remove leading and trailing slashes from path
		*/
		$parse_array['path'] = rtrim( $parse_array['path'], "/\\" );
	} else {
		$parse_array['path'] = '';
	}
	/*	Take /?keyword=value&keyword=value URL query parameters
		and break them up into array( keyword => value => equals?, keyword => value => equals? )
	*/
	if ( isset( $parse_array['query'] ) ) {
		$parms = explode( '&', $parse_array['query'] );
		$parse_array['query'] = array();
		foreach( $parms as $parm ) {
			if ( FALSE === strpos( $parm, '=' ) ) {
				$parse_array['query'][ $parm ][''] = FALSE;
			} else {
				$kwv = explode( '=', $parm );
				$parse_array['query'][ $kwv[0] ][ $kwv[1] ] = TRUE;
			}
			/*	Remember the presence of the Equals Sign ("=") in each Query
				to differentiate between a URL Prefix with a Query Keyword followed by 
				an Equals Sign, and one without.  For example, "address" would match
				address2=abc, while "address=" would not.
			*/
		}
	} else {
		$parse_array['query'] = array();
	}
	return $parse_array;
}

global $jr_mt_default_ports;
$jr_mt_default_ports = array( 80, 443 );
function jr_mt_non_default_port( $array, $key ) {
	/*	Remove standard HTTP Port 80 and HTTPS Port 443, if present.
	*/
	global $jr_mt_default_ports;
	if ( empty( $array[ $key ] ) || in_array( $array[ $key ], $jr_mt_default_ports ) ) {
		$port = 0;
	} else {
		$port = $array[ $key ];
	}
	return $port;
}

/**	Build Query Array

	$array[keyword] = array( value, value, ... )
	Sets both keyword and value to lower-case as
	that is how they are stored in Settings.
	
	Supports only & separator, not proposed semi-colon separator.
	
	Handles duplicate keywords in all four of these forms:
	kw=val1&kw=val2 kw[]=val1&kw[]=val2 kw=val1&kw=val1 kw[]=val1&kw[]=val1
	but nothing else, e.g. - kw=val1,val2 is not valid;
	it returns "val1,val2" as the Value.
	Also handles kw1&kw2
	
	Tests of parse_str() in PHP 5.5.9 proved that semi-colon and comma
	are not supported.  But, neither is kw=val1,kw=val2 which is why
	this function is written without the use of parse_str.
*/
function jr_mt_query_array() {
	/*	Remove array entry indicators ("[]") as we properly handle duplicate keywords,
		and covert to lower-case for comparison purposes.
	*/
	$queries = array();
	if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
		$query = explode( '&', jr_mt_strtolower( str_replace( '[]', '', $_SERVER['QUERY_STRING'] ) ) );
		foreach ( $query as $kwval ) {
			$query_entry = explode( '=', $kwval );
			if ( !isset( $query_entry[1] ) ) {
				$query_entry[1] = '';
			}
			$queries[ $query_entry[0] ][] = $query_entry[1];
		}
	}
	return $queries;
}

/*	Check for numeric IP Addresse, both IPv4 and IPv6
*/
function jr_mt_is_ip( $domain_name ) {
	if ( FALSE !== strpos( $domain_name, ':' ) ) {
		/*	IPv6
		*/
		$return = TRUE;
	} else {
		if ( 4 === count( $ip_array = explode( '.', $domain_name ) ) ) {
			foreach ( $ip_array as $ip ) {
				if ( ctype_digit( $ip ) ) {
					/*	IPv4: maximum = 255
					*/
					if ( $ip > 255 ) {
						$return = FALSE;
						break;
					}
				} else {
					$return = FALSE;
					break;
				}
			}
			$return = !isset( $return );
		} else {
			$return = FALSE;
		}
	}
	return $return;
}

/*	Initialize the Aliases array in the Settings.
	
	First entry will be the Site Address (URL) field value from WordPress General Settings.
	Most of the time, Create an Alias of that URL,
	based on adding or removing www. in the domain name.
	Including Subdomains, which do not normally have www. defined
	as an Alias, but you never know!
	But not for numeric IP addresses, or localhost.
	
	Returns an array containing one or two arrays, each in this format:
		['url'] => URL
		['prep'] => URL array created by jr_mt_prep_url()
		['home'] => TRUE if this is Site Address (URL) field value from WordPress General Settings,
			which is stored here to determine when the WordPress General Setting is changed.
*/	
function jr_mt_init_aliases() {
	$return = array(
				array(
					'url'  => JR_MT_HOME_URL,
					'prep' => jr_mt_prep_url( JR_MT_HOME_URL ),
					'home' => TRUE
					)
				);

	if ( FALSE !== ( $url_parsed = parse_url( JR_MT_HOME_URL ) ) ) {
		$host = $url_parsed['host'];
		if ( 0 !== strcasecmp( $host, 'localhost' ) ) {
			/*	Check for numeric IP Addresse, both IPv4 and IPv6
			*/
			if ( !jr_mt_is_ip( $host ) ) {
				if ( 0 === strncasecmp( $host, 'www.', 4 ) ) {
					$url_parsed['host'] = jr_mt_substr( $host, 4 );
				} else {
					$url_parsed['host'] = 'www.' . $host;
				}
				$url = jr_mt_unparse_url( $url_parsed );
				$return[] = array(
					'url'  => $url,
					'prep' => jr_mt_prep_url( $url ),
					'home' => FALSE
					);
			}
		}
	}
	return $return;
}

function jr_mt_unparse_url( $parse_array ) {
	if ( function_exists( 'http_build_url' ) ) {
		$url = http_build_url( '', $parse_array );
	} else {
		/*	From: https://github.com/jakeasmith/http_build_url
			Version: 0.1.3
			Version created about July 2014, retrieved November 4, 2014
		*/
		$url = $parse_array['scheme'] . '://';

		if ( isset( $parse_array['user'] ) ) {
			$url .= $parse_array['user'];

			if ( isset( $parse_array['pass'] ) ) {
				$url .= ':' . $parse_array['pass'];
			}

			$url .= '@';
		}

		if ( isset( $parse_array['host'] ) ) {
			$url .= $parse_array['host'];
		}

		if ( isset( $parse_array['port'] ) ) {
			$url .= ':' . $parse_array['port'];
		}

		if ( !empty( $parse_array['path'] ) ) {
			$url .= $parse_array['path'];
		}

		if ( isset( $parse_array['query'] ) ) {
			$url .= '?' . $parse_array['query'];
		}

		if ( isset( $parse_array['fragment'] ) ) {
			$url .= '#' . $parse_array['fragment'];
		}
	}
	return $url;
}

/*	Given a URL and a list of Site Aliases,
	either as strings or in "prep format" (arrays),
	return either:
	- (string) the first Site Alias that matches;
	- (string) the relative URL, relative to the Site Alias; and
	- (array) the relative URL in "prep format"
	OR
	- FALSE on failure.
*/
function jr_mt_make_relative( $url, $aliases ) {
	if ( is_string( $url ) ) {
		$url = jr_mt_prep_url( $url );
	}
	$return = FALSE;
	foreach ( $aliases as $alias ) {
		if ( jr_mt_same_prefix_url( $alias, $url ) ) {
			$return = array(
				'alias' => $alias,
				'rel_url' => $rel_url,
				'rel_url_prep' => $rel_url_prep
			);
			break;
		}
	}
	return $return;
}

/*	Which Site Alias is the "Best Match"?

	We already know that more than one Site Alias matches
	all of the current URL for the full length of the Site Alias.
	For example, if example.com and example.com/wp both matched,
	example.com/wp would be the "best" match, in the sense of longest match.
	
	$alias_array - in settings['alias'] format
	$matches - array of keys to $alias_array, of matching Aliases
	return - key of best match
*/
function jr_mt_best_match_alias( $alias_array, $matches ) {
	/*	For each component, if they differ,
		Best is determined as follows, in the following order:
		Host - longest string is Best
		Port - non-zero is Best
		Path - longest string is Best.
	*/
	$best = $matches;
	$max = -1;
	foreach ( $best as $key ) {
		$len = strlen( $alias_array[ $key ]['prep']['host'] );
		if ( $len > $max ) {
			$max = $len;
		}
	}
	foreach ( $best as $index => $key ) {
		if ( $max > strlen( $alias_array[ $key ]['prep']['host'] ) ) {
			unset ( $best[ $index ] );
		}
	}
	foreach ( $best as $key ) {
		if ( 0 !== $alias_array[ $key ]['prep']['port'] ) {
			foreach ( $best as $index => $key ) {
				if ( 0 === $alias_array[ $key ]['prep']['port'] ) {
					unset( $best[ $index ] );
				}
			}
			break;
		}
	}
	$max = -1;
	foreach ( $best as $key ) {
		if ( empty( $alias_array[ $key ]['prep']['path'] ) ) {
			$len = 0;
		} else {
			$len = strlen( $alias_array[ $key ]['prep']['path'] );
		}
		if ( $len > $max ) {
			$max = $len;
		}
	}
	foreach ( $best as $index => $key ) {
		if ( empty( $alias_array[ $key ]['prep']['path'] ) ) {
			$len = 0;
		} else {
			$len = strlen( $alias_array[ $key ]['prep']['path'] );
		}
		if ( $max > $len ) {
			unset ( $best[ $index ] );
		}
	}			
	/*	If there is more than one Site Alias left
		in the $best array, then it should mean
		there are duplicate entries,
		but that makes no sense.
		So, just return the first one in the array.
		
		reset() returns the first array element,
		not the key.
	*/
	return reset( $best );
}

?>