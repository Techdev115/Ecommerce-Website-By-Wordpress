/*	When the page has fully loaded,
	check if the Plugin's cookie is present
	and decipher it into values that will
	control processing:
		an array consisting of:
		- keyword=value query to append to URL
		- FALSE if Setting "append if question mark ("?") not present", or
			TRUE if Setting "append if another Override query not present"
		- an array of all sticky or override queries (empty array if FALSE)
	If cookie is present, look at all <a> tag href= URLs.
	Restriction:  ignore all Relative URLs - those without ://
	For each one, check if it is in the current WordPress site,
	but not in Admin panels (/wp-admin/):
		- strip up to and including ://
		- convert to lower-case
		- if present, strip leading www.
		- convert \ to /
		- remove trailing / of Home URL and Site Admin URL
	If so, append the current Query (keyword=value) specified in 
	the plugin's Cookie, to the href= URL, except when:
		- a ? is present in the URL if the plugin Setting is set to that;
		- an override query is already present in the URL, if the plugin
			Setting allows ? in the URL
		- the same sticky query is already present in the URL
	Appending a Query to the URL is done by:
		- inserting it before any # (Fragment, i.e. - Bookmark) in the URL
		- appending to URL otherwise
		- prefixed by ? is ? is not already present
		- prefixed by & if ? is already present
	In addition, hidden text in the HTML provides:
		- WordPress Site Address (URL)
		- WordPress /wp-admin/ URL
	These values are not available for the Cookie, because WordPress must be
	loaded before these values are available.
	
	Whether pre-processed in PHP when passed, or within this JavaScript,
	all comparisons are effectively case-insensitive, by converting
	all values to lower-case.
	
	Version 6.0 - this code has not been upgraded to support Site Aliases!
*/
window.onload =
	function ( ) {
		var allCookies, keywordMatch, pos, valueStart, cookieArray, query, homeUrl, siteAdmin, appendSetting, overrideQueries, overrideQueriesEmpty, hrefs, i, max, href, colonSlashSlash, hrefCompare, addQuery, posQuestionMark, posNumberSign, hrefQueries,
			cookieEquals = 'jr-mt-remember-query=',
			cookieSplit = '; ';

		/*	Check if there are any Cookies at all.
		*/
		if ( '' !== allCookies ) {
			allCookies = cookieSplit + document.cookie + cookieSplit;
			keywordMatch = cookieSplit + cookieEquals;
			pos = allCookies.indexOf( keywordMatch );
			/*	Check if our cookie is present.
			*/
			if ( -1 !== pos ) {
				valueStart = pos + keywordMatch.length;
				/*	Extract Value of Cookie, which is found after the equals sign and before the semicolon
				*/
				cookieArray = JSON.parse( decodeURIComponent( allCookies.substring( pos + keywordMatch.length, allCookies.indexOf( cookieSplit, pos + cookieSplit.length ) ) ) );
				/*	Assign Cookie array elements to meaningfully-named variables.
				*/
				query = cookieArray[0];
				appendSetting = cookieArray[1];
				overrideQueries = cookieArray[2];
				overrideQueriesEmpty = ( 0 === overrideQueries.length );
				
				homeUrl = document.getElementById( 'jr-mt-home-url' )['title'];
				siteAdmin = document.getElementById( 'jr-mt-site-admin' )['title'];
 
				hrefs = document.getElementsByTagName ( 'a' );
				for ( i = 0, max = hrefs.length; i < max; i++ ) {
					href = hrefs [ i ] ['href'];
					colonSlashSlash = href.indexOf( '://' );
					/*	Ignore Relative URLs:  those without ://
					*/
					if ( -1 !== colonSlashSlash ) {
						hrefCompare = href.substring( colonSlashSlash + 3 ).toLowerCase();
						if ( 'www.' === hrefCompare.substring( 0, 4 ) ) {
							hrefCompare = hrefCompare.substring( 4 );
						}
						hrefCompare = hrefCompare.replace( /\\/g, '/' );
						addQuery = '';
						if ( hrefCompare.substring( 0, homeUrl.length ) === homeUrl ) {
							if ( hrefCompare.substring( 0, siteAdmin.length ) !== siteAdmin ) {
								posQuestionMark = href.indexOf( '?' );
								posNumberSign = href.indexOf( '#' );
								if ( -1 === posNumberSign ) {
									posNumberSign = href.length;
								}
								/*	Only add a Query if one does not already exist.
								*/
								if ( -1 === posQuestionMark ) {
									/*	Query must be before the Fragment (#anchor),
										when one exists.
									*/
									addQuery = '?';
								} else {
									/*	Query exists because "?" found in URL.
										Check if setting set to add query when one already exists.
										If not, don't touch this URL.
									*/
									if ( appendSetting ) {
										addQuery = '&';
										hrefQueries = href.substring( posQuestionMark + 1, posNumberSign ).split( '&' );
										/*	Don't append Sticky query if it is already present in the URL
										*/										
										if ( -1 !== hrefQueries.indexOf( query ) ) {
											addQuery = '';
										} else {
											if ( !overrideQueriesEmpty ) {
												for ( j = 0, hrefQueriesLength = hrefQueries.length; j < hrefQueriesLength; j++ ) {
													/*	Don't append Sticky query if an Override query is present in URL
													*/
													if ( -1 !== overrideQueries.indexOf( hrefQueries[ j ].toLowerCase() ) ) {
														/*	Override query already in URL.
														*/
														addQuery = '';
														break;
													}
												}
											}
										}
									}
								}
							}
						}
						if ( '' !== addQuery ) {
							/*	No override or sticky query already in URL, so safe to add current sticky query.
							*/
							hrefs [ i ] ['href'] = href.substring( 0, posNumberSign ) + addQuery + query + href.substring( posNumberSign );
						}
					}
				}					
			}				
		}
	};
