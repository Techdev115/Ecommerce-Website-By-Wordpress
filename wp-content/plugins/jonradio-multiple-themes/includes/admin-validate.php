<?php

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

function jr_mt_validate_settings( $input ) {
	global $jr_mt_kwvalsep;
	$valid = array();
	
	$prefix_types = array(
		'false'  => 'url',
		'prefix' => 'url_prefix',
		'*'      => 'url_asterisk'
	);
	
	$settings = get_option( 'jr_mt_settings' );
	$query = $settings['query'];
	$aliases = $settings['aliases'];
	
	/*	Begin by deciding which Tab to display on the plugin's Settings page
	
		Default value should never be used if plugin is written correctly.
	*/
	$tab = 1;
	for ( $i = 1; $i <= 6; $i++ ) {
		if ( isset( $input[ "tab$i" ] ) ) {
			$tab = $i;
			break;
		}
	}
	set_transient( 'jr_mt_' . get_current_user_id() . '_tab', $tab, 5 );
	
	if ( isset( $input['permalink'] ) ) {
		$internal_settings = get_option( 'jr_mt_internal_settings' );
		$internal_settings['permalink'] = get_option( 'permalink_structure' );
		update_option( 'jr_mt_internal_settings', $internal_setting );
	}
	
	foreach ( array( 'all_pages', 'all_posts', 'site_home', 'current', 'ajax_all' ) as $thing ) {
		$valid[$thing] = $input[$thing];
	}
	
	foreach ( $prefix_types as $key => $thing ) {
		$valid[$thing] = $settings[$thing];
	}
	$remember = array( 'query' => array() );
	if ( isset( $input['sticky_query_entry'] ) ) {
		foreach	( $input['sticky_query_entry'] as $query_entry ) {
			list( $keyword, $value ) = explode( $jr_mt_kwvalsep, $query_entry );
			/*	Data Sanitization not required as
				Keyword and Value are not entered by a human,
				but extracted from previously-generated HTML.
			*/
			$remember['query'][$keyword][$value] = TRUE;
		}
	}

	$override = array( 'query' => array() );
	if ( isset( $input['override_query_entry'] ) ) {
		foreach	( $input['override_query_entry'] as $query_entry ) {
			list( $keyword, $value ) = explode( $jr_mt_kwvalsep, $query_entry );
			/*	Data Sanitization not required as
				Keyword and Value are not entered by a human,
				but extracted from previously-generated HTML.
			*/
			$override['query'][$keyword][$value] = TRUE;
		}
	}
	
	if ( isset ( $input['del_entry'] ) ) {
		foreach ( $input['del_entry'] as $del_entry ) {
			$del_array = explode( '=', $del_entry, 3 );
			if ( 'query' === $del_array[0] ) {
				unset( $query[ $del_array[1] ][ $del_array[2] ] );
				if ( empty( $query[ $del_array[1] ] ) ) {
					unset( $query[ $del_array[1] ] );
				}
				/*	unset() does nothing if a variable or array element does not exist.
				*/
				unset( $remember['query'][ $del_array[1] ][ $del_array[2] ] );
				if ( empty( $remember['query'][ $del_array[1] ] ) ) {
					unset( $remember['query'][ $del_array[1] ] );
				}
				unset( $override['query'][ $del_array[1] ][ $del_array[2] ] );
				if ( empty( $override['query'][ $del_array[1] ] ) ) {
					unset( $override['query'][ $del_array[1] ] );
				}
			} else {
				/*	Check for a URL entry
				*/
				if ( 'url' === jr_mt_substr( $del_array[0], 0, 3 ) ) {
					foreach ( $valid[ $del_array[0] ] as $i => $entry_array ) {
						if ( $entry_array['url'] === $del_array[2] ) {
							/*	Cannot unset $entry_array, even if prefixed by & in foreach
							*/
							unset( $valid[ $del_array[0] ][ $i ] );
							break;
						}
					}
				} else {
					/*	Must be Home, All Pages or Posts, or Everything
					*/
					$valid[ $del_array[0] ] = '';
				}
			}
		}
	}
	
	/*	Handle troublesome %E2%80%8E UTF Left-to-right Mark (LRM) suffix first.
	*/
	$url = jr_mt_sanitize_url( $input['add_path_id'] );
	
	if ( ( empty( $input['add_theme'] ) && !empty( $url ) ) || ( !empty( $input['add_theme'] ) && empty( $url ) ) ) {
		add_settings_error(
			'jr_mt_settings',
			'jr_mt_emptyerror',
			'Both URL and Theme must be specified to add an Individual entry',
			'error'
		);		
	} else {
		if ( !empty( $url ) ) {
			if ( jr_mt_same_prefix_url( JR_MT_HOME_URL, $url ) ) {
				if ( ( '*' !== $input['add_is_prefix'] ) && ( FALSE !== strpos( $url, '*' ) ) ) {
					add_settings_error(
						'jr_mt_settings',
						'jr_mt_queryerror',
						'Asterisk ("*") only allowed when "URL Prefix with Asterisk" selected: <code>' . $url . '</code>',
						'error'
					);
				} else {									
					$prep_url = jr_mt_prep_url( $url );
					if ( 'false' === $input['add_is_prefix'] ) {
						if ( jr_mt_same_url( $prep_url, JR_MT_HOME_URL ) ) {
							add_settings_error(
								'jr_mt_settings',
								'jr_mt_homeerror',
								'Please use "Select Theme for Site Home" field instead of specifying Site Home URL as an individual entry.',
								'error'
							);
						} else {
							if ( jr_mt_same_prefix_url( $prep_url, admin_url() ) ) {
								add_settings_error(
									'jr_mt_settings',
									'jr_mt_adminerror',
									'Admin Page URLs are not allowed because no known Themes alter the appearance of Admin pages: <code>' . $url . '</code>',
									'error'
								);
							}
						}
					} else {
						if ( '*' === $input['add_is_prefix'] ) {
							$url_dirs = explode( '/', str_replace( '\\', '/', $url ) );
							foreach ( $url_dirs as $dir ) {
								if ( FALSE !== strpos( $dir, '*' ) ) {
									$asterisk_found = TRUE;
									if ( '*' !== $dir ) {
										$asterisk_not_alone = TRUE;
									}
									break;
								}
							}
							if ( isset( $asterisk_found ) ) {
								if ( isset( $asterisk_not_alone ) ) {
									add_settings_error(
										'jr_mt_settings',
										'jr_mt_queryerror',
										'An Asterisk ("*") may only replace a full subdirectory name, not just a portion of it: <code>' . $url . '</code>',
										'error'
									);	
								}
							} else {
								add_settings_error(
									'jr_mt_settings',
									'jr_mt_queryerror',
									'No Asterisk ("*") specified but "URL Prefix with Asterisk" selected: <code>' . $url . '</code>',
									'error'
								);	
							}
						}
					}

					function jr_mt_settings_errors() {
						$errors = get_settings_errors();
						if ( !empty( $errors ) ) {
							foreach ( $errors as $error_array ) {
								if ( 'error' === $error_array['type'] ) {
									return TRUE;
								}
							}
						}
						return FALSE;
					}

					/*	If there have been no errors detected,
						create the new URL setting entry.
					*/
					if ( !jr_mt_settings_errors() ) {
						/*	['url'], ['url_prefix'] or ['url_asterisk']
						*/
						$key = $prefix_types[ $input['add_is_prefix'] ];
						$rel_url = jr_mt_relative_url( $url, JR_MT_HOME_URL );
						$valid[ $key ][] = array(
							'url'   => $url,
							'rel_url' => $rel_url,
							'theme' => $input['add_theme']
						);
						/*	Get index of element just added to array $valid[ $key ]
						*/
						end( $valid[ $key ] );
						$valid_key = key( $valid[ $key ] );
						/*	Create the URL Prep array for each of the current Site Aliases,
							including the Current Site URL
						*/
						foreach ( $aliases as $index => $alias ) {
							$valid[ $key ][ $valid_key ]['prep'][] = jr_mt_prep_url( $alias['url'] . '/' . $rel_url );
						}
						/*	Only for URL type Setting, not Prefix types.
						*/
						if ( 'url' === $key ) {
							/*	Try and figure out ID and WordPress Query Keyword for Type, if possible and relevant
							*/
							if ( ( 0 === ( $id = url_to_postid( $url ) ) ) &&
								( version_compare( get_bloginfo( 'version' ), '4', '>=' ) ) ) {
								$id = attachment_url_to_postid( $url );
							}
							if ( !empty( $id ) ) {
								/*	Type Cast ID as String, to match ?p=id Query in URL after Prep
								*/
								$valid[ $key ][ $valid_key ]['id'] = (string) $id;
								if ( NULL !== ( $post = get_post( $id ) ) ) {
									switch ( $post->post_type ) {
										case 'post':
											$valid[ $key ][ $valid_key ]['id_kw'] = 'p';
											break;
										case 'page':
											$valid[ $key ][ $valid_key ]['id_kw'] = 'page_id';
											break;
										case 'attachment':
											$valid[ $key ][ $valid_key ]['id_kw'] = 'attachment_id';
											break;
									}
								}
							}
						}
					}
				}
			} else {
				add_settings_error(
					'jr_mt_settings',
					'jr_mt_urlerror',
					' URL specified is not part of current WordPress web site: <code>'
						. $url
						. '</code>.  URL must begin with <code>'
						. JR_MT_HOME_URL
						. '</code>.',
					'updated'
				);			
			}
		}
	}
	
	/*	Make sure reserved characters are not used
		in URL Query keyword or value fields on Settings page.
	*/
	function jr_mt_query_chars( $element, $where ) {
		foreach (
			array(
				'='	 => 'Equals Sign'   ,
				'?'	 => 'Question Mark' ,
				'&'	 => 'Ampersand'     ,
				' '	 => 'Blank'         ,
				'#'	 => 'Number Sign'   ,
				'/'	 => 'Slash'         ,
				'\\' => 'Backslash'     ,
				'['	 => 'Square Bracket',
				']'	 => 'Square Bracket'
			) as $char => $name ) {
			if ( FALSE !== strpos( $element, $char ) ) {
				add_settings_error(
					'jr_mt_settings',
					'jr_mt_queryerror',
					'Illegal character used in '
					. $where
					. ': '
					. $name
					. ' ("' . $char . '") in "'
					. $element
					. '"',
					'error'
				);
				return FALSE;
			}
		}
		return TRUE;
	}
	/*	Data Sanitization needed here
	*/
	$keyword = jr_mt_prep_query_keyword( $input['add_querykw_keyword'] );
	if ( !empty( $input['add_querykw_theme'] ) && !empty( $keyword ) ) {
		if ( jr_mt_query_chars( $keyword, 'Query Keyword' ) ) {
			/*	If there is an existing entry for the Keyword,
				then replace it.
				Otherwise, create a new entry.
			*/
			$query[ $keyword ]['*'] = $input['add_querykw_theme'];
		}
	} else {
		if ( !( empty( $input['add_querykw_theme'] ) && empty( $keyword ) ) ) {
			add_settings_error(
				'jr_mt_settings',
				'jr_mt_emptyerror',
				'Both Query Keyword and Theme must be specified to add an Individual Query Keyword entry',
				'error'
			);
		}
	}
	
	/*	Data Sanitization needed here
	*/
	$keyword = jr_mt_prep_query_keyword( $input['add_query_keyword'] );
	$value = jr_mt_prep_query_value( $input['add_query_value'] );
	if ( !empty( $input['add_query_theme'] ) && !empty( $keyword ) && !empty( $value ) ) {
		if ( jr_mt_query_chars( $keyword, 'Query Keyword' ) && jr_mt_query_chars( $value, 'Query Value' ) ) {
			/*	If there is an existing entry for the Keyword and Value pair,
				then replace it.
				Otherwise, create a new entry.
				
				Be sure that a numeric Keyword or Value still is type String.
			*/
			$query[ $keyword ][ $value ] = $input['add_query_theme'];
		}
	} else {
		if ( !( empty( $input['add_query_theme'] ) && empty( $keyword ) && empty( $value ) ) ) {
			add_settings_error(
				'jr_mt_settings',
				'jr_mt_emptyerror',
				'Query Keyword, Value and Theme must all be specified to add an Individual Query entry',
				'error'
			);
		}
	}
	
	if ( 'true' === $input['query_present'] ) {
		$valid['query_present'] = TRUE;
	} else {
		if ( 'false' === $input['query_present'] ) {
			$valid['query_present'] = FALSE;
		}
	}
	
	/*	Handle Alias tab
	
		Always handle Delete first, to allow Replacement (Delete then Add)
	*/
	if ( isset ( $input['del_alias_entry'] ) ) {
		foreach ( $input['del_alias_entry'] as $del_alias_entry ) {
			$int_key = (int) $del_alias_entry;
			unset( $aliases[ $int_key ] );
			/*	Now go through all the URL-based Settings,
				and delete the Prep for the deleted Alias.
					
				$int_key is the array integer key of the just-deleted
				Alias, as that will also be the array key for each of the
				Settings ['prep'] arrays.
			*/
			foreach ( $prefix_types as $form_prefix => $settings_prefix ) {
				foreach ( $valid[ $settings_prefix ] as $key => $url_entry ) {
					unset( $valid[ $settings_prefix ][ $key ]['prep'][ $int_key ] );
				}
			}
		}
	}
	$alias_url = jr_mt_sanitize_url( $input['add_alias'] );
	if ( !empty( $alias_url ) ) {
		/*	URL has been trimmed but Case has not been altered
		*/
		if ( ( ( 0 !== substr_compare( $alias_url, 'http://', 0, 7, TRUE ) )
				&& ( 0 !== substr_compare( $alias_url, 'https://', 0, 8, TRUE ) )
				)
			|| ( FALSE === ( $parse_url = parse_url( $alias_url ) ) ) ) {
			add_settings_error(
				'jr_mt_settings',
				'jr_mt_badurlerror',
				"Alias URL specified is invalid: <code>$url</code>",
				'error'
			);			
		} else {
			$url_ok = TRUE;
			foreach ( array( 'user', 'pass', 'query', 'fragment' ) as $component ) {
				if ( isset( $parse_url[ $component ] ) ) {
					$url_ok = FALSE;
					break;
				}
			}
			if ( $url_ok ) {
				/*	Be sure there is NOT a trailing slash
				*/
				$alias_url = rtrim( $alias_url, '/\\' );
				$aliases[] = array(
					'url'  => $alias_url,
					'prep' => jr_mt_prep_url( $alias_url ),
					'home' => FALSE
					);
				/*	Now go through all the URL-based Settings,
					and add a Prep for the new Alias.
					
					First, determine the array integer key of the newly-added
					Alias, as that will also be the array key for each of the
					Settings ['prep'] arrays.
				*/
				end( $aliases );
				$prep_key = key( $aliases );
				
				foreach ( $prefix_types as $form_prefix => $settings_prefix ) {
					foreach ( $valid[ $settings_prefix ] as $key => $url_entry ) {
						$valid[ $settings_prefix ][ $key ]['prep'][ $prep_key ] = jr_mt_prep_url( 
							$alias_url . '/' . $valid[ $settings_prefix ][ $key ]['rel_url']
						);
					}
				}
			} else {
				add_settings_error(
					'jr_mt_settings',
					'jr_mt_badurlerror',
					'Alias URL cannot contain user, password, query ("?") or fragment ("#"): <code>'
						. "$url</code>",
					'error'
				);		
			}
		}
	}
	
	$errors = get_settings_errors();
	if ( empty( $errors ) ) {
		add_settings_error(
			'jr_mt_settings',
			'jr_mt_saved',
			'Settings Saved',
			'updated'
		);	
	}
	$valid['query'] = $query;
	$valid['remember'] = $remember;
	$valid['override'] = $override;
	$valid['aliases'] = $aliases;
	return $valid;
}

?>