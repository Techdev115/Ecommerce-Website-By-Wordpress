<?php

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Convert pre-Version 5 ['ids'] Settings to new Version 5 format.
 * 
 * Mainly, it involves converting Post ID to URL.
 * 'setup_theme' is the earliest Action where
 * all functions in jr_mt_convert_ids() work properly.
 *
 */
function jr_mt_convert_ids( $settings ) {
	if ( is_array( $settings['ids'] ) ) {
		foreach ( $settings['ids'] as $key => $ids_array ) {
			/*	Be sure that Theme has not been deleted.
			*/
			$jr_mt_all_themes = jr_mt_all_themes();
			if ( isset( $jr_mt_all_themes[ $ids_array['theme'] ] ) ) {
				/*	$key:
						'' - Home entry
				*/
				if ( '' === $key ) {
					if ( '' === $settings['site_home'] ) {
						$settings['site_home'] = $ids_array['theme'];
					}
				} else {
					if ( isset( $ids_array['type'] ) ) {
						switch ( $ids_array['type'] ) {
							case 'admin':
								/*	Ignore as Admin pages are ignored
								*/
								break;
							case 'prefix':
								/*	URL Prefix
								*/
								$url = JR_MT_HOME_URL . "/$key";
								$settings['url_prefix'][] = array(
									'url'   => $url,
									'prep'  => jr_mt_prep_url( $url ),
									'theme' => $ids_array['theme']
								);
								break;
							case '*':
								/*	URL Prefix with Asterisk
								*/
								$url = JR_MT_HOME_URL . "/$key";
								$settings['url_asterisk'][] = array(
									'url'   => $url,
									'prep'  => jr_mt_prep_url( $url ),
									'theme' => $ids_array['theme']
								);
								break;
							case 'cat':
								if ( is_wp_error( get_the_category_by_ID( $key ) ) ) {
									/*	Ignore non-existent Categories.
										They were likely deleted.
									*/
									jr_mt_messages( 'Setting deleted for non-existent Category with ID=' . $key );
								} else {
									$url = get_category_link( $key );
									$settings['url'][] = array(
										'url'   => $url,
										'prep'  => jr_mt_prep_url( $url ),
										'theme' => $ids_array['theme']
									);
								}
								break;
							case 'archive':
								/*	From ?m=yyyymm query originally
								*/
								$yyyymm = $ids_array['id'];
								$year = intval( $yyyymm / 100 );
								$month = $yyyymm % 100;
								$url = get_month_link( $year, $month );
								$settings['url'][] = array(
									'url'   => $url,
									'prep'  => jr_mt_prep_url( $url ),
									'theme' => $ids_array['theme']
								);
								break;
							default:
								if ( FALSE === $ids_array['id'] ) {
									/*	Exact URL
									*/
									$url = JR_MT_HOME_URL . "/$key";
									$settings['url'][] = array(
										'url'   => $url,
										'prep'  => jr_mt_prep_url( $url ),
										'theme' => $ids_array['theme']
									);
								} else {
									/*	Some Post type
									
										get_permalink() can be used as early as Action Hook 'setup_theme',
										but not in 'plugins_loaded' (Fatal Error).
									*/
									if ( FALSE === ( $url = get_permalink( $key ) ) ) {
										/*	Ignore any non-existent IDs, typically deleted.
										*/
										jr_mt_messages( 'Setting deleted for non-existent Post/Page/Attachment with ID=' . $key );
									} else {
										$settings['url'][] = array(
											'url'   => $url,
											'prep'  => jr_mt_prep_url( $url ),
											'theme' => $ids_array['theme']
										);
									}
								}
						}
					}
				}
			}
		}
	}
	/*	Maybe later:
		unset( $settings['ids'] );
	*/
	return $settings;
}

?>