<?php

/*	Exit if .php file accessed directly
*/
if ( !defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Define the settings
 * 
 * Everything to be stored and/or can be set by the user
 *
 */
function jr_mt_admin_init() {
	$settings = get_option( 'jr_mt_settings' );
	foreach ( array( 'query', 'url', 'url_prefix', 'url_asterisk' ) as $key ) {
		if ( !empty( $settings[ $key ] ) ) {
			DEFINE( 'JR_MT_LIST_SETTINGS', TRUE );
			break;
		}
	}
	if ( defined( 'JR_MT_LIST_SETTINGS' ) ) {
		add_settings_section(
				'jr_mt_delete_settings_section', 
				'Current Theme Selection Entries', 
				'jr_mt_delete_settings_expl', 
				'jr_mt_settings_page' 
			);
		add_settings_field(
			'del_entry', 
			'Theme Selection Entries:', 
			'jr_mt_echo_delete_entry', 
			'jr_mt_settings_page', 
			'jr_mt_delete_settings_section'
		);
	}
	add_settings_section( 
		'jr_mt_site_home_section',
		'<input name="jr_mt_settings[tab1]" type="submit" value="Save All Changes" class="button-primary" /></h3><h3>Site Home',
		'jr_mt_site_home_expl',
		'jr_mt_settings_page' 
	);
	add_settings_field( 
		'site_home', 
		'Select Theme for Site Home<br /><code>' . JR_MT_HOME_URL . '</code>', 
		'jr_mt_echo_site_home', 
		'jr_mt_settings_page', 
		'jr_mt_site_home_section' 
	);
	add_settings_section(
		'jr_mt_single_settings_section', 
		'For An Individual Page, Post or other non-Admin page;<br />or a group of pages, specified by URL Prefix, optionally with Asterisk(s)', 
		'jr_mt_single_settings_expl', 
		'jr_mt_settings_page'
	);
	add_settings_field( 'add_is_prefix', 'Select here if URL is a Prefix', 'jr_mt_echo_add_is_prefix', 'jr_mt_settings_page', 'jr_mt_single_settings_section' );
	add_settings_field( 'add_theme', 'Theme', 'jr_mt_echo_add_theme', 'jr_mt_settings_page', 'jr_mt_single_settings_section' );
	add_settings_field( 'add_path_id', 'URL of Page, Post, Prefix or other', 'jr_mt_echo_add_path_id', 'jr_mt_settings_page', 'jr_mt_single_settings_section' );
	add_settings_section( 'jr_mt_querykw_section', 
		'For A Query Keyword on any Page, Post or other non-Admin page', 
		'jr_mt_querykw_expl', 
		'jr_mt_settings_page' 
	);
	add_settings_field( 'add_querykw_theme', 'Theme', 'jr_mt_echo_add_querykw_theme', 'jr_mt_settings_page', 'jr_mt_querykw_section' );
	add_settings_field( 'add_querykw_keyword', 'Query Keyword', 'jr_mt_echo_add_querykw_keyword', 'jr_mt_settings_page', 'jr_mt_querykw_section' );
	add_settings_section( 'jr_mt_query_section', 
		'For A Query Keyword=Value on any Page, Post or other non-Admin page', 
		'jr_mt_query_expl', 
		'jr_mt_settings_page'
	);
	add_settings_field( 'add_query_theme', 'Theme', 'jr_mt_echo_add_query_theme', 'jr_mt_settings_page', 'jr_mt_query_section' );
	add_settings_field( 'add_query_keyword', 'Query Keyword', 'jr_mt_echo_add_query_keyword', 'jr_mt_settings_page', 'jr_mt_query_section' );
	add_settings_field( 'add_query_value', 'Query Value', 'jr_mt_echo_add_query_value', 'jr_mt_settings_page', 'jr_mt_query_section' );
	add_settings_section( 'jr_mt_aliases_section', 
		'<input name="jr_mt_settings[tab1]" type="submit" value="Save All Changes" class="button-primary" /></h3></div><div id="jr-mt-settings2" style="display: none;"><h3>Site Aliases used in URLs to Access This WordPress Site', 
		'jr_mt_aliases_expl', 
		'jr_mt_settings_page' 
	);
	/*	There is always an entry for the Site URL ("Home").
	*/
	if ( count( $settings['aliases'] ) > 1 ) {
		add_settings_section(
			'jr_mt_delete_aliases_section', 
			'Current Site Alias Entries', 
			'jr_mt_delete_aliases_expl', 
			'jr_mt_settings_page' 
		);
		add_settings_field(
			'del_alias_entry', 
			'Site Alias Entries:', 
			'jr_mt_echo_delete_alias_entry', 
			'jr_mt_settings_page', 
			'jr_mt_delete_aliases_section'
		);
	}
	add_settings_section(
		'jr_mt_create_alias_section', 
		'Create New Site Alias Entry', 
		'jr_mt_create_alias_expl', 
		'jr_mt_settings_page' 
	);
	add_settings_field( 
		'add_alias', 
		'Site Alias', 
		'jr_mt_echo_add_alias', 
		'jr_mt_settings_page', 
		'jr_mt_create_alias_section' 
	);
	add_settings_section( 'jr_mt_sticky_section', 
		'<input name="jr_mt_settings[tab2]" type="submit" value="Save All Changes" class="button-primary" /></h3></div><div id="jr-mt-settings3" style="display: none;"><h3>Advanced Settings</h3><p><b>Warning:</b> As the name of this section implies, Advanced Settings should be fully understood or they may surprise you with unintended consequences, so please be careful.</p><h3>Sticky and Override', 
		'jr_mt_sticky_expl', 
		'jr_mt_settings_page' 
	);
	add_settings_field( 'query_present', 'When to add Sticky Query to a URL', 'jr_mt_echo_query_present', 'jr_mt_settings_page', 'jr_mt_sticky_section' );
	add_settings_field( 'sticky_query', 'Keyword=Value Entries:', 'jr_mt_echo_sticky_query_entry', 'jr_mt_settings_page', 'jr_mt_sticky_section' );
	add_settings_section( 'jr_mt_everything_section',
		'Theme for Everything',
		'jr_mt_everything_expl', 
		'jr_mt_settings_page'
	);
	add_settings_field( 'current', 
		'Select Theme for Everything, to Override WordPress Current Theme (<b>' . wp_get_theme()->Name . '</b>)', 
		'jr_mt_echo_current', 
		'jr_mt_settings_page', 
		'jr_mt_everything_section' 
	);
	add_settings_section( 'jr_mt_all_settings_section', 
		'For All Pages and/or All Posts', 
		'jr_mt_all_settings_expl', 
		'jr_mt_settings_page' 
	);
	$suffix = array(
		'Pages' => '<br />(Pages created with Add Page)',
		'Posts' => ''
	);
	foreach ( array( 'Pages', 'Posts' ) as $thing ) {
		add_settings_field( 'all_' . jr_mt_strtolower( $thing ), "Select Theme for All $thing" . $suffix[$thing], 'jr_mt_echo_all_things', 'jr_mt_settings_page', 'jr_mt_all_settings_section', 
			array( 'thing' => $thing ) );
	}
	add_settings_section( 'jr_mt_ajax_section', 
		'AJAX', 
		'jr_mt_ajax_expl', 
		'jr_mt_settings_page' 
	);
	add_settings_field( 'ajax_all', 
		'Theme for <code>admin-ajax.php</code>', 
		'jr_mt_echo_ajax_all', 
		'jr_mt_settings_page', 
		'jr_mt_ajax_section' 
	);
}

/**
 * Section text for Section1
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_delete_settings_expl() {
	if ( defined( 'JR_MT_LIST_SETTINGS' ) ) {
		?>
		<p>
		All Theme Selection entries are displayed below,
		in the exact order in which they will be processed.
		For example,
		if a match is made with the first Entry,
		the first Entry's Theme will be used,
		no matter what Theme the Second and subsequent Entries specify.
		</p>
		<p>
		You can delete any of these entries by filling in the check box beside the entry
		and clicking any of the <b>Save All Changes</b> buttons.
		To change the Theme for an entry,
		you will need to delete the entry
		and add the same entry with a different Theme in the relevant section
		on this or the Advanced Settings tab.
		</p>
		<p>
		To add or remove (or to learn about) the Sticky or Override setting for a Query,
		see the Advanced Settings tab.
		</p>
		<?php
	}
}

function jr_mt_echo_delete_entry() {
	echo 'In order of Selection:<ol>';
	$settings = get_option( 'jr_mt_settings' );
	/*	Display any Override entries first,
		because they have the highest priority.
	*/
	foreach ( $settings['override']['query'] as $override_keyword => $override_value_array ) {
		foreach ( $override_value_array as $override_value => $bool ) {
			jr_mt_theme_entry( 
				'Query',
				wp_get_theme( $settings['query'][ $override_keyword ][ $override_value ] )->Name,
				$override_keyword,
				$override_value
			);
		}
	}
	/*	Display Non-Overrides:
		first, keyword=value query in URL with matching setting entry.
	*/
	foreach ( $settings['query'] as $keyword => $value_array ) {
		foreach ( $value_array as $value => $theme ) {
			/*	Wildcard Keyword=* entries come later
			*/
			if ( '*' !== $value ) {
				if ( !isset( $settings['override']['query'][ $keyword ][ $value ] ) ) {
					jr_mt_theme_entry(
						'Query',
						wp_get_theme( $theme )->Name,
						$keyword,
						$value
					);
				}
			}
		}
	}
	/*	Display Non-Overrides:
		second, wildcard keyword=* query in URL with matching setting entry.
	*/
	foreach ( $settings['query'] as $keyword => $value_array ) {
		foreach ( $value_array as $value => $theme ) {
			/*	Wildcard Keyword=* entries
				Overrides are not allowed, so no need to check.
			*/
			if ( '*' === $value ) {
				jr_mt_theme_entry(
					'Query',
					wp_get_theme( $theme )->Name,
					$keyword,
					'*'
				);
			}
		}
	}
	/*	Display URL entries:
		first, exact match URL entries;
		second, prefix URL entries;
		then, prefix URL entries with asterisk wildcards.
	*/
	foreach ( array(
		'url' => 'URL',
		'url_prefix' => 'URL Prefix',
		'url_asterisk' => 'URL Prefix*'
		) as $key => $description ) {
		foreach ( $settings[ $key ] as $settings_array ) {
			jr_mt_theme_entry(
				$key,
				wp_get_theme( $settings_array['theme'] )->Name,
				$settings_array['url'],
				$description
			);
		}
	}
	/*	Home Entry, then All Posts and Pages, and Everything Else
	*/
	foreach ( array(
		'ajax_all'  => 'AJAX',
		'site_home' => 'Home',
		'all_posts' => 'All Posts',
		'all_pages' => 'All Pages',
		'current'   => 'Everything Else'
		) as $key => $description ) {
		if ( '' !== $settings[ $key ] ) {
			jr_mt_theme_entry(
				$key,
				wp_get_theme( $settings[ $key ] )->Name,
				$description
			);
		}
	}
	if ( '' === $settings['current'] ) {
		jr_mt_theme_entry(
			'wordpress'
		);
	}
	echo '</ol>';
}

/**
 * Section text for Section2
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_site_home_expl() {
	?>
	<p>
	In this section, you can select a different Theme for Site Home.
	To remove a previously selected Theme, select the blank entry from the drop-down list.
	</p>
	<p>
	In the <i>next</i> section, you will be able to select a Theme, including the Current Theme, for individual Pages, Posts or
	any other non-Admin pages that have their own Permalink; for example, specific Archive or Category pages.
	Or groups of Pages, Posts or any other non-Admin pages that share the same URL Prefix.
	</p>
	<p>	
	There is also a Query Keyword section 
	farther down this Settings page
	that allows
	you to select a Theme to use whenever a specified 
	Query Keyword (<code>?keyword=value</code> or <code>&keyword=value</code>)
	appears in the URL of any Page, Post or other non-Admin page.
	Query entries will even override the Site Home entry,
	if the Query Keyword follows the Site Home URL.
	</p>
	<?php	
}

function jr_mt_echo_site_home() {
	$settings = get_option( 'jr_mt_settings' );
	jr_mt_themes_field( 'site_home', $settings['site_home'], 'jr_mt_settings', FALSE );
}

/**
 * Section text for Section3
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_single_settings_expl() {
	?>
	<p>
	Select a Theme for an individual Page, Post	or
	any other non-Admin page that has its own Permalink; for example, a specific Archive or Category page.
	Or for a group of pages which have URLs that all begin with the same characters ("Prefix"),
	optionally specifying an Asterisk ("*") to match all subdirectories at specific levels.
	</p>
	<p>
	Then cut and paste the URL of the desired Page, Post, Prefix or other non-Admin page.
	And click any of the <b>Save All Changes</b> buttons to add the entry.
	</p>
	There are three types of Entries that you can specify here:
	<ol>
	<li>
	<b>URL</b> - if Visitor URL matches this URL, use this Theme
	</li>
	<li>
	<b>URL Prefix</b> - any Visitor URL that begins with this URL Prefix will use this Theme
	</li>
	<li>
	<b>URL Prefix with Asterisk(s)</b> - URL Prefix that matches any subdirectory where Asterisk ("*") is specified
	</li>
	</ol>
	For the third type, an Asterisk can only be specified to match the entire subdirectory name, not parts of the name:
	<blockquote>
	For example, using a Permalink structure that uses dates,
	where a typical Post might be at URL
	<code>http://example.com/wp/2014/04/13/daily-thoughts/</code>,
	a URL Prefix with Asterisk entry of
	<code>http://example.com/wp/*/04/*/d</code>
	would match all April Posts with Titles that begin with the letter "d", no matter what year they were posted.
	</blockquote>
	</p>
	</p>
	Beginning with Version 5.0, <code>keyword=value</code> Queries are now supported in all URLs
	(on this Settings tab;
	Site Aliases may not include Queries).
	</p>
	<?php	
}

function jr_mt_echo_add_is_prefix() {
	echo '<input type="radio" id="add_is_prefix" name="jr_mt_settings[add_is_prefix]" value="false" checked="checked" /> URL';
	?>
	<br/>
	<input type="radio" id="add_is_prefix" name="jr_mt_settings[add_is_prefix]" value="prefix" /> URL Prefix<br/>
	<input type="radio" id="add_is_prefix" name="jr_mt_settings[add_is_prefix]" value="*" /> URL Prefix with Asterisk ("*")
	<?php
}

function jr_mt_echo_add_theme() {
	jr_mt_themes_field( 'add_theme', '', 'jr_mt_settings', FALSE );
}

function jr_mt_echo_add_path_id() {
	?>
	<input id="add_path_id" name="jr_mt_settings[add_path_id]" type="text" size="75" maxlength="256" value="" />
	<br />
	&nbsp;
	(cut and paste URL here of Page, Post, Prefix or other)
	<br />
	&nbsp;
	URL must begin with
	the current
	<a href="options-general.php">Site Address (URL)</a>:
	<?php
	echo '<code>' . JR_MT_HOME_URL . '/</code>.';
}

/**
 * Section text for Section4
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_querykw_expl() {
	?>
	<p>
	Select a Theme to use 
	whenever the specified Query Keyword (<code>?keyword=</code> or <code>&keyword=</code>)
	is found in the URL of
	any Page, Post or
	any other non-Admin page.
	And click any of the <b>Save All Changes</b> buttons to add the entry.
	</p>
	<p>
	<b>
	Note
	</b>
	that Query Keyword takes precedence over all other types of Theme selection entries.
	For example, 
	<?php
	echo '<code>' . JR_MT_HOME_URL . '?firstname=dorothy</code>'
		. ' would use the Theme specified for the <code>firstname</code> keyword, not the Theme specified for Site Home.'
		. ' Query matching is case-insensitive, so all Keywords entered are stored in lower-case.</p>';
}
function jr_mt_echo_add_querykw_theme() {
	jr_mt_themes_field( 'add_querykw_theme', '', 'jr_mt_settings', FALSE );
}
function jr_mt_echo_add_querykw_keyword() {
	$three_dots = '&#133;';
	echo '<code>'
		. JR_MT_HOME_URL 
		. "/</code>$three_dots<code>/?"
		. '<input id="add_querykw_keyword" name="jr_mt_settings[add_querykw_keyword]" type="text" size="20" maxlength="64" value="" />=</code>'
		. $three_dots;
}

/**
 * Section text for Section5
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_query_expl() {
	?>
	<p>
	Select a Theme to use 
	whenever the specified Query Keyword <b>and</b> Value (<code>?keyword=value</code> or <code>&keyword=value</code>)
	are found in the URL of
	any Page, Post or
	any other non-Admin page.
	And click any of the <b>Save All Changes</b> buttons to add the entry.
	</p>
	<p>
	<b>
	Note
	</b>
	that Query Keyword=Value takes precedence over all other Theme selection entries,
	including a Query Keyword entry for the same Keyword.
	For example, 
	<?php
	echo '<code>' . JR_MT_HOME_URL . '?firstname=dorothy</code>'
		. ' would use the Theme specified for the <code>firstname=dorothy</code> keyword=value pair,'
		. ' not the Theme specified for Site Home nor even the Theme specified for the Keyword <code>firstname</code>.'
		. ' Query matching is case-insensitive, so all Keywords and Values entered are stored in lower-case.</p>';
}
function jr_mt_echo_add_query_theme() {
	jr_mt_themes_field( 'add_query_theme', '', 'jr_mt_settings', FALSE );
}
function jr_mt_echo_add_query_keyword() {
	$three_dots = '&#133;';
	echo '<code>'
		. JR_MT_HOME_URL 
		. "/</code>$three_dots<code>/?"
		. '<input id="add_query_keyword" name="jr_mt_settings[add_query_keyword]" type="text" size="20" maxlength="64" value="" /></code>';
}
function jr_mt_echo_add_query_value() {
	echo '<code>'
		. '='
		. '<input id="add_query_value" name="jr_mt_settings[add_query_value]" type="text" size="20" maxlength="64" value="" /></code>';
}

function jr_mt_aliases_expl() {
	?>
	<p>
	Define any
	<b>
	Site Aliases
	</b>
	that may be used to access your WordPress website.
	</p>
	<p>
	This plugin uses the value of
	<b>
	Site Address (URL)
	</b>
	defined on the
	<a href="options-general.php">
	General Settings</a>
	Admin panel
	to match URLs against the Theme Selection settings.
	By default, when the plugin is first installed,
	or the value of Site Address changed,
	a 
	<i>
	www Alias Entry
	</i>
	is automatically defined
	to handle the most common Alias used on WordPress sites:
	by adding or removing the "www." prefix of the Domain Name.
	</p>
	<p>
	If your WordPress website is accessed by
	anything other than the Site Address or Site Aliases defined below,
	this Plugin will always use the WordPress Active Theme defined on the
	<a href="themes.php">
	Appearance-Themes</a>
	Admin panel.
	</p>
	<p>
	Although by no means exhaustive,
	this list can help you remember where you might have defined Aliases that need to be defined below.
	</p>
	<ul class="jrmtpoints">
	<?php
	if ( is_multisite() ) {
		echo '<li><b>Mapped Domain</b>. Plugins such as <a href="https://wordpress.org/plugins/wordpress-mu-domain-mapping/">WordPress MU Domain Mapping</a> allow each Site in a WordPress Network to have its own Domain Name.</li>';
	}
	?>
	<li>
	<b>IP Address</b>.
	Most sites can be accessed by an IP address,
	either the IPv4 format of four numbers separated by dots (168.1.0.1)
	or the newer IPv6 format of several hexadecimal numbers separated by colons (2001:0DB8:AC10:FE01::).
	</li>
	<li>
	<b>Parked Domain</b>.
	example.com might have example.club as a Alias defined as a Parked Domain to your web host.
	</li>
	<li>
	<b>ServerAlias</b> or equivalent.
	Apache allows one or more Domain or Subdomain aliases to be defined with the SeverAlias directive;
	non-Apache equivalents offer similar capabilities.
	</li>
	<li>
	<b>Redirection</b>.
	Most domain name registration and web hosting providers also offer a Redirection service.
	Optionally, Redirection can be Masked (or not) to keep the redirected URL in the browser's address bar.
	</li>
	<li>
	<b>.htaccess RewriteRule</b> or equivalent.
	Each directory can contain a hidden file named
	<code>.htaccess</code>.
	These files may include RewriteRule statements that modify the URL
	to change the URL of a site
	as it appears in the Site Visitor's web browser address bar
	from,
	for example, 
	<code>http://example.com/wordpress</code>
	to
	<code>http://example.com</code>.
	</li>
	</ul>
	<?php
}

function jr_mt_delete_aliases_expl() {
	?>
	<p>
	Here you can see, 
	and are able to delete, 
	any Site Aliases that have been created in the 
	Create New Sites Alias Entry section below, 
	or were created by default by this Plugin
	for the current Site Address (URL) defined on the 
	<a href="options-general.php">General Settings</a>
	Admin panel:
	<?php
	echo '<code>' . JR_MT_HOME_URL. '</code></p>';
}

function jr_mt_echo_delete_alias_entry() {
	$settings = get_option( 'jr_mt_settings' );
	echo '<p>In addition to the <a href="options-general.php">Site Address (URL)</a> <code>'
		. JR_MT_HOME_URL
		. '</code>, this Plugin will also control Themes for the following Site Aliases:</p><ol>';
	foreach ( $settings['aliases'] as $array_index => $alias ) {
		/*	Do not allow the Site URL ("Home") alias to be deleted.
			In fact, do not even display it.
		*/
		if ( !$alias['home'] ) {
			echo '<li>Delete <input type="checkbox" id="del_alias_entry" name="jr_mt_settings[del_alias_entry][]" value="'
				. $array_index
				. '"> <code>'
				. $alias['url']
				. '</code></li>';
		}
	}
	echo '</ol>';
}

function jr_mt_create_alias_expl() {
	echo '<p>To add another Site Alias, cut and paste its URL below.</p>';
}

function jr_mt_echo_add_alias() {
	?>
	<input id="add_alias" name="jr_mt_settings[add_alias]" type="text" size="75" maxlength="256" value="" />
	<br />
	&nbsp;
	(cut and paste URL of a new Site Alias here)
	<br />
	&nbsp;
	URL must begin with
	<code>http://</code>
	or
	<code>https://</code>
	<?php
}

/**
 * Section text for Section6
 * 
 * Display an explanation of this Section
 *
 */
function jr_mt_sticky_expl() {
	/* "Membership System V2" is a paid plugin that blocks (our sticky) Cookies
	*/
	global $jr_mt_plugins_cache;
	foreach ( $jr_mt_plugins_cache as $rel_path => $plugin_data ) {
		if ( 0 === strncasecmp( 'memberium', $rel_path, 9 ) ) {
			echo '<b><u>IMPORTANT</u></b>: The Sticky feature of this plugin does not work with the <b>Membership System V2</b> plugin, which blocks the required Cookies.  At least one plugin from memberium.com appears to have been installed: '
				. $plugin_data['Name'];
			break;
		}
	}
	?>
	<p>
	If one of the
	<b>
	Keyword=Value Entries
	</b>
	shown below
	(if any)
	is present in the URL of a WordPress non-Admin webpage on the current WordPress Site
	and that Entry is:
	<ol>
	<li>
	<b>Sticky</b>,
	then the specified Theme will continue to be displayed for subsequent
	WordPress non-Admin webpages
	viewed by the same Visitor
	until an Override entry is encountered by the same Visitor.
	</li>
	<li>
	<b>Override</b>,
	then the specified Theme will be displayed,
	effectively ending any previous Sticky Theme that was being displayed
	for the same Visitor.
	</li>
	</ol>
	<b>
	Note
	</b>
	that,
	as explained in the
	Query Keyword=Value
	section on the Settings tab,
	Query Keyword=Value already takes precedence over all other Theme selection entries,
	even without the Override checkbox selected.
	Override is only intended to cancel a Sticky entry
	and display the specified Theme on the current WordPress non-Admin webpage.
	</p>
	<p>
	Implementation Notes:
	<ol>
	<li>
	The term "Same Visitor",
	used above,
	refers to a single combination of
	computer, browser and possibly computer user name,
	if the visitor's computer has multiple accounts or user names.
	A computer could be a smartphone, tablet, laptop, desktop or other Internet access device used by the Visitor.
	</li>
	<li>
	When Sticky is active for a given Visitor,
	the associated Query Keyword=Value is added to the
	URL of links displayed on the current WordPress non-Admin webpage.
	With the following exceptions:
	<ul>
	<li>
	a)
	Only links pointing to non-Admin webpages of the current WordPress Site are altered.
	</li>
	<li>
	b)
	The 
	"When to add Sticky Query to a URL"
	setting below also controls when a Sticky Keyword=Value is added to a URL.
	</li>
	</ul>
	<li>
	Cookies are used for Sticky entries. If the visitor's browser refuses Cookies,
	or another Plugin blocks cookies,
	this setting will not work and no error messages will be displayed.
	</li>
	</ol>
	</p>
	<p>
	<b>
	Important:
	</b>
	the Sticky feature cannot be made to work in all WordPress environments.
	Timing, Cookie and other issues may be caused by other plugins, themes and visitor browser settings,
	so please test carefully and realize that the solution to some problems will involve a choice between not using the Sticky feature and not using a particular plugin or theme.
	</p>
	<?php
}

function jr_mt_echo_query_present() {
	$settings = get_option( 'jr_mt_settings' );
	/*
		FALSE if Setting "Append if no question mark ("?") found in URL", or
		TRUE if Setting "Append if no Override keyword=value found in URL"
	*/
	echo '<input type="radio" id="query_present" name="jr_mt_settings[query_present]" value="false" ';
	checked( $settings['query_present'], FALSE );
	echo ' /> Append if no question mark ("?") found in URL<br/><input type="radio" id="query_present" name="jr_mt_settings[query_present]" value="true" ';
	checked( $settings['query_present'] );
	echo ' /> Append if no Override <code>keyword=value</code> found in URL';
}

function jr_mt_echo_sticky_query_entry() {
	global $jr_mt_kwvalsep;
	$settings = get_option( 'jr_mt_settings' );
	$three_dots = '&#133;';
	$first = TRUE;
	if ( !empty( $settings['query'] ) ) {
		foreach ( $settings['query'] as $keyword => $value_array ) {
			foreach ( $value_array as $value => $theme ) {
				if ( '*' !== $value ) {
					if ( $first ) {
						$first = FALSE;
					} else {
						echo '<br />';
					}
					echo 'Sticky <input type="checkbox" id="sticky_query_entry" name="jr_mt_settings[sticky_query_entry][]" value="'
						. "$keyword$jr_mt_kwvalsep$value"
						. '" ';
					checked( isset( $settings['remember']['query'][$keyword][$value] ) );
					echo ' /> &nbsp; Override <input type="checkbox" id="override_query_entry" name="jr_mt_settings[override_query_entry][]" value="'
						. "$keyword$jr_mt_kwvalsep$value"
						. '" ';
					checked( isset( $settings['override']['query'][$keyword][$value] ) );
					echo ' /> &nbsp; Theme='
						. wp_get_theme( $theme )->Name . '; '
						. 'Query='
						. '<code>'
						. JR_MT_HOME_URL 
						. "/</code>$three_dots<code>/?"
						. "<b><input type='text' readonly='readonly' disable='disabled' name='jr_mt_stkw' value='$keyword' size='"
						. jr_mt_strlen( $keyword )
						. "' /></b>"
						. '='
						. "<b><input type='text' readonly='readonly' disable='disabled' name='jr_mt_stkwval' value='$value' size='"
						. jr_mt_strlen( $value )
						. "' /></b></code>";
				}
			}
		}
	}
	if ( $first ) {
		echo 'None';
	}
}

function jr_mt_everything_expl() {
	?>
	<p>
	<b>Theme for Everything</b>
	simplifies the use of a Theme with Theme Settings that you need to change frequently,
	when the Theme is only going to be used on one or more Pages or Posts.
	The Theme can be set as the WordPress Active Theme through the Appearance-Themes admin panel,
	and set for specific Pages or Posts using this plugin's settings (on Settings tab),
	with another Theme specified below as the plugin's default theme ("Theme for Everything").
	</p>
	<?php
}

function jr_mt_echo_current() {
	$settings = get_option( 'jr_mt_settings' );
	jr_mt_themes_field( 'current', $settings['current'], 'jr_mt_settings', TRUE );
	echo '<br /> &nbsp; (select blank entry for default: WordPress Active Theme defined in Appearance-Themes, currently <b>' . wp_get_theme()->Name . '</b>)';
}

function jr_mt_all_settings_expl() {
	?>
	<p>
	These are
	<b>
	Advanced Setting
	</b>
	because they may not work with every other plugin, theme or permalinks setting.
	This plugin is only able to determine whether what is about to be displayed at the current URL
	is a Page or Post
	after all other Plugins have been loaded;
	the one exception to this is the Default setting for Permalinks,
	when <code>?p=</code> and <code>?page_id=</code> are used.
	</p>
	<p>
	Some other plugins and themes request the name of the current Theme
	<i>
	too early,
	</i>
	while they are being loaded,
	which is before this plugin is able to determine if it is on a Page or Post.
	For this reason,
	using either of these settings may not work properly for all other plugins and themes.
	As a result,
	if you choose to use either or both of these two settings,
	careful testing is advised immediately
	<u>and</u>
	whenever you change the Permalink setting, activate a plugin or start using a different theme.
	</p>
	<p>
	In this section, you can select a different Theme for All Pages and/or All Posts.
	To remove a previously selected Theme, select the blank entry from the drop-down list.
	</p>
	<p>
	On the Settings tab, you were able to select a Theme, including WordPress' Active Theme, to override any choice you make here, for individual Pages, Posts or
	any other non-Admin pages that have their own Permalink; for example, specific Archive or Category pages.
	Or groups of Pages, Posts or any other non-Admin pages that share the same URL Prefix.
	</p>
	<p>	
	The Settings tab also has a Query Keyword section 
	that allows
	you to select a Theme to use whenever a specified 
	Query Keyword (<code>?keyword=value</code> or <code>&keyword=value</code>)
	appears in the URL of any Page, Post or other non-Admin page.
	</p>
	<?php
}

function jr_mt_echo_all_things( $thing ) {
	$settings = get_option( 'jr_mt_settings' );
	$field = 'all_' . jr_mt_strtolower( $thing['thing'] );
	jr_mt_themes_field( $field, $settings[$field], 'jr_mt_settings', TRUE );
}

function jr_mt_ajax_expl() {
	?>
	<p>
	This setting selects a Theme for URLs 
	that include
	<code>admin-ajax.php</code>,
	a common WordPress technique for using AJAX.
	If more than one Theme or Plugin uses AJAX in this manner,
	then this Advanced Setting probably will not work correctly.
	</p>
	<p>
	If the URL also contains a Query
	(<code>?keyword=value</code>),
	a Query Setting is almost always a better choice than this Advanced Setting.
	</p>
	<?php
}

function jr_mt_echo_ajax_all() {
	$settings = get_option( 'jr_mt_settings' );
	jr_mt_themes_field( 'ajax_all', $settings['ajax_all'], 'jr_mt_settings', TRUE );
}

?>