<?php
/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/*	Disable until old Version Settings conversion done properly,
	typically by displaying the plugin's Settings page in Admin panels.
*/
if ( ( FALSE === ( $settings = get_option( 'jr_mt_settings' ) ) ) || ( !is_array( $settings ) ) ) {
	return;
}
foreach ( array( 'all_pages', 'all_posts', 'site_home', 'current',
		'url', 'url_prefix', 'url_asterisk', 
		'query', 'remember', 'override', 
		'query_present', 'aliases' ) as $key ) {
	if ( !isset( $settings[ $key ] ) ) {
		return;
	}
}
foreach ( array( 'url', 'url_prefix', 'url_asterisk', 'query', 'remember', 'override', 'aliases' ) as $key ) {
	if ( !is_array( $settings[ $key ] ) ) {
		return;
	}
}
foreach ( array( 'all_pages', 'all_posts', 'site_home', 'current' ) as $key ) {
	if ( !is_string( $settings[ $key ] ) ) {
		return;
	}
}
if ( ( FALSE === ( $internal_settings = get_option( 'jr_mt_internal_settings' ) ) ) 
	|| ( !is_array( $internal_settings ) ) ) {
	return;
}
	

/*	Select the relevant Theme
	These hooks must be available immediately
	as some Themes check them very early.
	Also must be available in Admin for p2.
*/
add_filter( 'pre_option_stylesheet', 'jr_mt_stylesheet' );
add_filter( 'pre_option_template', 'jr_mt_template' );

if ( !is_admin() ) {	
	/*	Be sure Plugins (non-internal) Settings are present in some shape or form
		for public web site.
		Admin is handled on plugin's Settings page.
	*/
	if ( !is_array( get_option( 'jr_mt_settings' ) ) ) {
		update_option( 'jr_mt_settings', jr_mt_default_settings() );
	}
	
	/*	Hooks below shown in order of execution */
	
	/*	Only do this if All Posts or All Pages setting is present.
	*/
	if ( get_option( 'permalink_structure' ) && jr_mt_all_posts_pages() ) {
		/*	'setup_theme' is the earliest Action that I could find where url_to_postid( $url )
			is valid.  get_post() works by then, too.
		*/
		add_action( 'setup_theme', 'jr_mt_page_conditional', JR_MT_RUN_FIRST );
		function jr_mt_page_conditional() {		
			/*	In case any requests for Theme came before this hook,
				make sure that Theme Selection is repeated the next time
				it is needed.
				Because url_to_postid() and possibly get_post() don't work until now.
				
				Note:  in PHP, you cannot directly unset a global variable,
				hence the cryptic code below.
			*/
			unset( $GLOBALS['jr_mt_theme'] );
			DEFINE( 'JR_MT_PAGE_CONDITIONAL', TRUE );
		}
	}
	
	add_action( 'wp_loaded', 'jr_mt_wp_loaded', JR_MT_RUN_LAST );
	function jr_mt_wp_loaded() {
		/*	Purpose of this hook is to output any required Cookie before it is too late
			(after the <html> or any other HTML is generated).
			There is no performance impact because this effectively pre-caches values
			for use later.
			This timing is also used to enqueue JavaScript related to the Sticky feature.
		*/
		global $jr_mt_theme;
		if ( !isset( $jr_mt_theme ) ) {
			$settings = get_option( 'jr_mt_settings' );
			if ( !empty( $settings['remember']['query'] ) ) {
				jr_mt_template();
			}
		}

		DEFINE( 'JR_MT_TOO_LATE_FOR_COOKIES', TRUE );
	}
}

function jr_mt_stylesheet() {
	return jr_mt_theme( 'stylesheet' );
}

function jr_mt_template() {
	return jr_mt_theme( 'template' );
}

function jr_mt_theme( $option ) {
	/*	The hooks that (indirectly) call this function are called repeatedly by WordPress, 
		so do the checking once and store the values in a global array.
		$jt_mt_theme['stylesheet'] - Stylesheet Name of Theme chosen
		$jt_mt_theme['template'] - Template Name of Theme chosen
		
		Very important note:
			- get_option( 'jr_mt_settings' ) ['ids']['theme'] is the Theme Subdirectory Name,
			as opposed to the Template or Stylesheet Name for the Theme.
			- likewise, the variable local variable $theme
		These three different values for each Theme must be clearly separated, as all three usually
		match, but do not have to, e.g. - Child Themes.
	*/
	global $jr_mt_theme;
	if ( !isset( $jr_mt_theme ) ) {
		$jr_mt_theme = array();
	}
	if ( !isset( $jr_mt_theme[$option] ) ) {
		$theme = jr_mt_chosen();
		$jr_mt_all_themes = jr_mt_all_themes();
		/*	Check to be sure that Theme is still installed.
			If not:
				return Everywhere theme if set and it exists,
					otherwise, FALSE to indicate WordPress Active Theme.
		*/
		if ( ( FALSE !== $theme ) && ( !isset( $jr_mt_all_themes[ $theme ] ) ) ) {
			$settings = get_option( 'jr_mt_settings' );
			$everything = $settings['current'];
			if ( ( '' !== $everything ) && ( isset( $jr_mt_all_themes[ $everything ] ) ) ) {
				$theme = $everything;
			} else {
				$theme = FALSE;
			}
		}
		if ( FALSE === $theme ) {
			//	Get both at once, to save a repeat of this logic later:
			$jr_mt_theme['stylesheet'] = jr_mt_current_theme( 'stylesheet' );
			$jr_mt_theme['template'] = jr_mt_current_theme( 'template' );
		} else {
			$jr_mt_theme['stylesheet'] = $jr_mt_all_themes[ $theme ]->stylesheet;
			$jr_mt_theme['template'] = $jr_mt_all_themes[ $theme ]->template;
		}
		if ( !is_admin() ) {
			jr_mt_cookie( 'all', 'clean' );
		}
	}
	$theme = $jr_mt_theme[$option];
	return $theme;
}
	
/**
 * Returns FALSE for Current Theme
 * 
 */
function jr_mt_chosen() {
	$settings = get_option( 'jr_mt_settings' );
	
	/*	$queries - array of [keyword] => array( value, value, ... )
			in the current URL.
	*/
	$queries = jr_mt_query_array(); 
	
	/*	KnowHow ThemeForest Paid Theme special processing:
		if s= is present, and 'knowhow' is either the active WordPress Theme
		or is specified in any Settings, then automatically select the KnowHow theme.
	*/
	if ( isset( $queries['s'] ) && in_array( 'knowhow', jr_mt_themes_defined(), TRUE ) ) {
		return 'knowhow';
	}

	/*	Non-Admin page, i.e. - Public Site, etc.
	
		Begin by checking for any Query keywords specified by the Admin in Settings,
		complicated by the fact that Override entries take precedence.
	*/
	if ( !empty( $settings['query'] ) ) {
		if ( !empty( $_SERVER['QUERY_STRING'] ) ) {
			/*	Check Override entries
			*/
			foreach ( $settings['override']['query'] as $override_keyword => $override_value_array ) {
				if ( isset( $queries[ $override_keyword ] ) ) {
					foreach ( $override_value_array as $override_value_untyped => $bool ) {
						$override_value = ( string ) $override_value_untyped;
						if ( in_array( $override_value, $queries[ $override_keyword ], TRUE ) ) {
							$override_found[] = array( $override_keyword, $override_value );
						}
					}
				}
			}
			if ( !isset( $overrides_found ) ) {
				/*	Look for both keyword=value settings and keyword=* settings,
					with keyword=value taking precedence (sorted out later).
				*/
				foreach ( $settings['query'] as $query_settings_keyword => $value_array ) {
					if ( isset( $queries[ $query_settings_keyword ] ) ) {
						foreach ( $value_array as $query_settings_value_untyped => $theme ) {
							$query_settings_value = ( string ) $query_settings_value_untyped;
							if ( in_array( $query_settings_value, $queries[ $query_settings_keyword ], TRUE ) ) {
								$query_found[] = array( $query_settings_keyword, $query_settings_value );
							}
						}
						if ( isset( $value_array['*'] ) ) {
							$keyword_found[] = $query_settings_keyword;
						}
					}
				}
			}
		}
	}
	
	/*	Handle Overrides:
		First, for Override keyword=value query in URL.
		Second, for previous Override detected by PHP cookie.
	*/
	if ( isset( $override_found ) ) {
		/*	If sticky, create JavaScript Sticky Cookie,
			and PHP Sticky Cookie.
			No matter what:
			return Theme from the first Override found.
		*/
		$keyword = $override_found[0][0];
		$value = $override_found[0][1];
		if ( isset( $settings['remember']['query'][ $keyword ][ $value ] ) ) {
			jr_mt_js_sticky_query( $keyword, $value );
			jr_mt_cookie( 'php', 'put', "$keyword=$value" );
		}
		return $settings['query'][ $keyword ][ $value ];
	} else {
		/*	Is there a previous Override Query for this Site Visitor?
			If so, use it, but only if it is still valid.
		*/
		if ( FALSE !== ( $cookie = jr_mt_cookie( 'php', 'get' ) ) ) {
			list( $keyword, $value ) = explode( '=', $cookie );
			if ( isset( $settings['override']['query'][ $keyword ][ $value ] ) ) {
				/*	If sticky, create JavaScript Sticky Cookie,
					and renew PHP Sticky Cookie.
					No matter what:
					Return Theme
				*/
				if ( isset( $settings['remember']['query'][ $keyword ][ $value ] ) ) {
					jr_mt_js_sticky_query( $keyword, $value );
					jr_mt_cookie( 'php', 'put', "$keyword=$value" );
				}
				return $settings['query'][ $keyword ][ $value ];
			}
		}
	}

	/*	Handle Non-Overrides:
		keyword=value query in URL with matching setting entry.
	*/
	if ( isset( $query_found ) ) {
		$query_keyword_found = $query_found[0][0];
		$query_value_found = $query_found[0][1];
		/*	Probably makes sense to give preference to the Sticky ones
		*/
		foreach ( $query_found as $query_kwval_array ) {
			if ( isset( $settings['remember']['query'][ $query_kwval_array[0] ][ $query_kwval_array[1] ] ) ) {
				$query_keyword_found = $query_kwval_array[0];
				$query_value_found = $query_kwval_array[1];
				/*	Create JavaScript Sticky Cookie,
					and PHP Sticky Cookie.
				*/
				jr_mt_js_sticky_query( $query_keyword_found, $query_value_found );
				jr_mt_cookie( 'php', 'put', "$query_keyword_found=$query_value_found" );
				break;
			}
		}
		/*	Return Theme
		*/
		return $settings['query'][ $query_keyword_found ][ $query_value_found ];
	}
	
	/*	Handle Keyword wildcards:
		keyword=* setting entry that matches keyword in URL query.
	*/
	if ( isset( $keyword_found ) ) {
		return $settings['query'][ $keyword_found[0] ]['*'];
	}
	
	/*	Now look at URL entries: $settings['url'] and ['url_prefix']
		
		Version 6.0 Logic Design to maximize performance on high traffic sites without Caching:
			For current URL, determine Site Alias in use
				- Best Match - from an array of matching Site Aliases, determine "Best", some measure of Longest
			Prep current URL for matching
			Check for match in "URL" plugin entries that have been pre-prepped with this Site Alias
			Check for match in "URL Prefix" plugin entries that have been pre-prepped with this Site Alias
			Check for match in "URL Prefix with Asterisk" plugin entries that have been pre-prepped with this Site Alias
	*/
	
	if ( 0 === ( $port = jr_mt_non_default_port( $_SERVER, 'SERVER_PORT' ) ) ) {
		$url_port = '';
	} else {
		$url_port = ':' . $port;
	}
	$prep_url = jr_mt_prep_url( $current_url = parse_url( JR_MT_HOME_URL, PHP_URL_SCHEME ) 
		. '://' 
		. $_SERVER['SERVER_NAME'] 
		. $url_port
		. $_SERVER['REQUEST_URI'] );
	$match = array();
	foreach ( $settings['aliases'] as $key => $alias_array ) {
		if ( jr_mt_same_prefix_url( $alias_array['prep'], $prep_url ) ) {
			$match[] = $key;
		}
	}
	if ( empty( $match ) ) {
		/*	Maybe not the best thing to do,
			but if Site Alias is not defined,
			always use Current Theme.
		*/
		return FALSE;
	}
	$site_alias_key = jr_mt_best_match_alias( $settings['aliases'], $match );
	foreach ( $settings['url'] as $settings_array ) {
		if ( jr_mt_same_url( $settings_array['prep'][ $site_alias_key ], $prep_url ) ) {
			return $settings_array['theme'];
		}
	}
	foreach ( $settings['url_prefix'] as $settings_array ) {
		if ( jr_mt_same_prefix_url( $settings_array['prep'][ $site_alias_key ], $prep_url ) ) {
			return $settings_array['theme'];
		}
	}
	foreach ( $settings['url_asterisk'] as $settings_array ) {
		if ( jr_mt_same_prefix_url_asterisk( $settings_array['prep'][ $site_alias_key ], $prep_url ) ) {
			return $settings_array['theme'];
		}
	}

	/*	Theme to use for All /wp-admin/admin-ajax.php usage.
		Selected near the end to allow Queries to take precedence.
	*/
	if ( !empty( $settings['ajax_all'] )
		&& ( FALSE !== strpos( $_SERVER['REQUEST_URI'], 'admin-ajax.php' ) ) ) {
		return $settings['ajax_all'];
	}

	/*	Must check for Home near the end as queries override
	
		Home is determined in an odd way:
		(1) Remove all Queries
		(2) Match against Site Address (URL) specified in Admin General Settings
		(3) Check if any non-Permalink keywords are present, such as p= or page_id=
			and cause a non-match if present
	*/
	if ( '' !== $settings['site_home'] ) {
		/*	Check for Home Page,
			with or without Query.
		*/
		$prep_url_no_query = $prep_url;
		$prep_url_no_query['query'] = array();
		if ( jr_mt_same_url( JR_MT_HOME_URL, $prep_url_no_query ) ) {
			$home = TRUE;
			$internal_settings = get_option( 'jr_mt_internal_settings' );
			if ( ( isset( $internal_settings['query_vars'] ) )
				&& ( is_array( $internal_settings['query_vars'] ) ) ) {
				foreach ( $prep_url['query'] as $keyword => $value ) {
					/*	Check for any non-Permalink Query Keyword
					*/
					if ( in_array( $keyword, $internal_settings['query_vars'], TRUE ) ) {
						$home = FALSE;
						break;
					}
				}
			}
			if ( $home ) {
				return $settings['site_home'];
			} else {
				/*	Check for Settings specifying the current Page, Post or Attachment
					specified with kw=val Query default Permalinks.
				*/
				foreach ( $settings['url'] as $settings_array ) {
					if ( isset( $settings_array['id_kw'] ) 
						&& ( isset( $prep_url['query'][ $settings_array['id_kw'] ] ) ) 
						&& ( $prep_url['query'][ $settings_array['id_kw'] ] === $settings_array['id'] )
					) {
						return $settings_array['theme'];
					}
				}
			}
		}
	}
	/*	All Pages and All Posts settings are checked second to last, 
		just before Everything Else.
		
		url_to_postid() only works after JR_MT_PAGE_CONDITIONAL is set.
		But alternate means can be used with default Permalinks.
		
		First, see if any All Pages or All Posts setting exists.
	*/
	if ( jr_mt_all_posts_pages() ) {
		if ( defined( 'JR_MT_PAGE_CONDITIONAL' ) ) {
			if ( 0 !== ( $id = url_to_postid( $current_url ) ) ) {
				if ( NULL !== ( $post = get_post( $id ) ) ) {
					$type = $post->post_type;
					if ( 'post' === $type ) {
						if ( '' !== $settings['all_posts'] ) {
							return $settings['all_posts'];
						}
					} else {
						if ( 'page' === $type ) {
							if ( '' !== $settings['all_pages'] ) {
								return $settings['all_pages'];
							}
						}
					}
				}
			}
		} else {
			$permalink = get_option( 'permalink_structure' );
			if ( empty( $permalink ) ) {
				if ( '' !== $settings['all_posts'] ) {
					if ( isset( $queries['p'] ) ) {
						return $settings['all_posts'];
					}
				}
				
				if ( '' !== $settings['all_pages'] ) {
					if ( isset( $queries['page_id'] ) ) {
						return $settings['all_pages'];
					}
				}
			}
		}
	}
	/*	This is the Theme for Everything Advanced Setting.
		A Setting of Blank uses WordPress Current Theme value,
		i.e. - the Setting is not set.
	*/
	if ( '' === $settings['current'] ) {
		return FALSE;
	} else {
		return $settings['current'];
	}
}

/**	Cookie to JavaScript with Sticky Query and related info.

	Replace Existing or Create New (if no existing) Cookie
	to remember what Sticky Keyword=Value to use on this Browser on this Visitor Computer.
	Cookie is an encoding of this array:
	- keyword=value query to append to URL
	- FALSE if Setting "Append if no question mark ("?") found in URL", or
		TRUE if Setting "Append if no Override keyword=value found in URL"
	- an array of all sticky or override queries (empty array if FALSE)
	
	Version 6.0 - this code has not been upgraded to support Site Aliases!
*/
function jr_mt_js_sticky_query( $keyword, $value ) {
	add_action( 'wp_enqueue_scripts', 'jr_mt_wp_enqueue_scripts' );
	function jr_mt_wp_enqueue_scripts() {
		global $jr_mt_plugin_data;
		wp_enqueue_script( 'jr_mt_sticky', plugins_url() . '/' . dirname( jr_mt_plugin_basename() ) . '/js/sticky.js', array(), $jr_mt_plugin_data['Version'] );
		/*	JavaScript needs some values passed in HTML,
			so add that hook here, too.
		*/
		add_action( 'wp_footer', 'jr_mt_wp_footer' );
	}
	function jr_mt_wp_footer() {
		echo '<div style="display: none;"><div id="jr-mt-home-url" title="'
			. jr_mt_prep_comp_url( JR_MT_HOME_URL )
			. '"></div><div id="jr-mt-site-admin" title="'
			. jr_mt_prep_comp_url( admin_url() )
			. '"></div></div>';
	}
	/**	Prepare URL for JavaScript compares
	
		Remove http[s]//: from beginning
		Convert rest of URL to lower-case
		Remove www. from beginning, if present
		Convert any backslashes to forward slashes
		Remove any trailing slash(es).
	*/
	function jr_mt_prep_comp_url( $url ) {
		$comp_url = jr_mt_strtolower( jr_mt_substr( $url, 3 + strpos( $url, '://' ) ) );
		if ( 'www.' === jr_mt_substr( $comp_url, 0, 4 ) ) {
			$comp_url = jr_mt_substr( $comp_url, 4 );
		}
		return rtrim( str_replace( '\\', '/', $comp_url ), '/' );
	}
			
	$settings = get_option( 'jr_mt_settings' );

	if ( $settings['query_present'] ) {
		foreach ( $settings['override']['query'] as $override_keyword => $override_value_array ) {
			foreach ( $override_value_array as $override_value => $theme ) {
				$override[] = "$override_keyword=$override_value";
			}
		}
	} else {
		$override = array();
	}
	
	jr_mt_cookie( 'js', 'put', strtr( rawurlencode( json_encode(
			array( "$keyword=$value", $settings['query_present'], $override ) ) ), 
		array( '%21' => '!', '%2A' => '*', '%27' => "'", '%28' => '(', '%29' => ')' ) )
	);
}

/*	All Cookie Handling occurs here.
	$action - 'get', 'put', 'del'
*/
function jr_mt_cookie( $lang, $action, $cookie_value = '' ) {
	switch ( $lang ) {
		case 'js':
			$cookie_name = 'jr-mt-remember-query';
			$raw = TRUE;
			$expiry = '+36 hours';
			$function = 'setrawcookie';
			break;
		case 'php':
			$cookie_name = 'jr_mt_php_override_query';
			$raw = FALSE;
			$expiry = '+1 year';
			$function = 'setcookie';
			break;
	}
	if ( 'get' === $action ) {
		if ( isset( $_COOKIE[ $cookie_name ] ) ) {
			return $_COOKIE[ $cookie_name ];
		} else {
			return FALSE;
		}
	} else {
		global $jr_mt_cookie_track;
		if ( defined( 'JR_MT_TOO_LATE_FOR_COOKIES' ) ) {
			return FALSE;
		}
		/*	Determine Path off Domain to WordPress Address, not Site Address, for Cookie Path value.
			Using home_url().
		*/
		$cookie_path = parse_url( JR_MT_HOME_URL, PHP_URL_PATH ) . '/';
		switch ( $action ) {
			case 'put':
				if ( empty( $cookie_value ) ) {
					return FALSE;
				} else {
					return ( $jr_mt_cookie_track[ $lang ] = $function( $cookie_name, $cookie_value, strtotime( $expiry ), $cookie_path, $_SERVER['SERVER_NAME'] ) );
				}
				break;
			case 'del':
				/*	Don't clutter up output to browser with a Cookie Delete request if a Cookie does not exist.
				*/
				if ( isset( $_COOKIE[ $cookie_name ] ) ) {
					return ( $jr_mt_cookie_track[ $lang ] = setrawcookie( $cookie_name, '', strtotime( '-2 days' ), $cookie_path, $_SERVER['SERVER_NAME'] ) );
				}
				break;
			case 'clean':
				if ( 'all' === $lang ) {
					$clean_langs = array( 'php', 'js' );
				} else {
					$clean_langs = array( $lang );
				}
				foreach ( $clean_langs as $clean_lang ) {
					if ( !isset( $jr_mt_cookie_track[ $clean_lang ] ) ) {
						jr_mt_cookie( $clean_lang, 'del' );
					}
				}
				break;
		}
	}
}

/**	Will the url_to_postid() function be required?
 *	
 *	Only if:
 *		- Pretty Permalinks are being used, AND
 *		- ( All Posts setting is set, OR
 *		- All Pages setting is set )
 * @return   bool	if add_action is required
 */
function jr_mt_all_posts_pages() {
	$settings = get_option( 'jr_mt_settings' );
	return ( $settings['all_posts'] || $settings['all_pages'] );	
}

?>