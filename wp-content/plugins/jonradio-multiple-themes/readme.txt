=== Multiple Themes ===
Contributors: dgewirtz
Donate link: http://zatzlabs.com/lab-notes/
Tags: themes, theme, sections, style, template, stylesheet, accessibility
Requires at least: 3.4
Tested up to: 5.7
Stable tag: 7.1.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Select different Themes for one or more WordPress Pages, Posts or other non-Admin pages.  Or Site Home.

== Description ==

**IMPORTANT: Support has moved to the ZATZLabs site and is no longer provided on the WordPress.org forums. If you need a timely reply from the developer, please [open a ticket](http://zatzlabs.com/submit-ticket/).**

Settings provide many different ways to Select which Theme should appear where on your WordPress web site:

* Just for Site Home
* By URL (Version 5 adds support for Query keyword=value as part of a unique URL);
* By Prefix URL, matching all URLs that begin with the same characters ("Prefix URL");
* By Wildcard Prefix URL with one or more Asterisks ("*") representing arbritrary subdirectories in the URL;
* By Query Keyword found in any URL, not matter what the Value;
* By Query keyword=value found in any URL;
* By Sticky Query keyword=value that sets the Theme for a given Visitor until an Override Query keyword=value is found in a future URL (Advanced Setting requiring the Visitor's browser to accept Cookies; incompatible with certain plugins that block Cookies);
* Everywhere (Advanced Setting that overrides the WordPress Active Theme);
* All Pages (Advanced Setting that works with most, but not all, Themes and Plugins);
* All Posts (Advanced Setting that works with most, but not all, Themes and Plugins).

None of the Themes in the WordPress Theme Directory alter the appearance of the WordPress Admin panels.  As a result, this plugin does not allow Theme Selection entries to specify URLs for WordPress Admin panels.

**Use with Paid Themes**:  On-going full scale testing of Themes by this plugin's author is only possible if the Theme is found in the WordPress Theme Directory.  Although (Version 5 of) this plugin should now work with all Themes, any problems encountered while using Paid Themes will be difficult to diagnose.  As described in the FAQ tab, a WordPress Network (Multisite) is an alternative to this plugin, as a way to create a single web site with more than one Theme.

A similar situation exists with Paid Plugins.

**Use with Plugins that Cache**:  You may find that you have to flush the Cache whenever you change Settings in the *jonradio Multiple Themes* plugin. Some Caching plugins only cache for visitors who are not logged in as users, so be sure to log out before testing the results of your *jonradio Multiple Themes* settings.

**Changing Theme Options (Widgets, Sidebars, Menus, Templates, Background, Header, etc.)?**:  After installing and activating the plugin, see the plugin's Settings page and click on the **Theme Options** tab for important information on changing Options for Themes other than the Active Theme.

**How it Works**:  The plugin does not change the Active Theme defined to WordPress in the Appearances-Themes Admin panel.  Instead, it dynamically (and selectively) overrides that setting.  Which means that simply deactivating (or deleting) this plugin will restore the way that Themes were displayed prior to installing this plugin.  However, in some situations, it is possible to inadvertently alter Theme Options for the Active Theme when using either of the Methods described on the plugin's Setting page Theme Options tab.

> <strong>Adoption Notice</strong><br>
> This plugin was recently adopted by David Gewirtz and ongoing support and updates will continue. Feel free to visit [David's Lab Notes](http://zatzlabs.com/lab-notes/) for additional details and to sign up for emailed news updates.

Special thanks to Jon 'jonradio' Pearkins for creating the plugin and making adoption possible.

== Installation ==

**IMPORTANT: Support has moved to the ZATZLabs site and is no longer provided on the WordPress.org forums. Please visit the new [ZATZLabs Forums](http://zatzlabs.com/forums/). If you need a timely reply from the developer, please [open a ticket](http://zatzlabs.com/submit-ticket/).**

This section describes how to install the *jonradio Multiple Themes* plugin and get it working.

1. Use **Add Plugin** within the WordPress Admin panel to download and install this *jonradio Multiple Themes* plugin from the WordPress.org plugin repository (preferred method).  Or download and unzip this plugin, then upload the `/jonradio-multiple-themes/` directory to your WordPress web site's `/wp-content/plugins/` directory.
1. Activate the *jonradio Multiple Themes* plugin through the **Installed Plugins** Admin panel in WordPress.  If you have a WordPress Network ("Multisite"), you can either **Network Activate** this plugin through the **Installed Plugins** Network Admin panel, or Activate it individually on the sites where you wish to use it.  Activating on individual sites within a Network avoids some of the confusion created by WordPress' hiding of Network Activated plugins on the Plugin menu of individual sites.  Alternatively, to avoid this confusion, you can install the *jonradio Reveal Network Activated Plugins* plugin.
1. Be sure that all Themes you plan to use have been installed.
1. Select Themes to be used on the Settings or Advanced Settings tab of the plugin's **Multiple Themes plugin** Settings page in the WordPress Admin panels, which is found in both the **Appearance** and **Settings** sections.  You can also get to this Settings page by clicking on the **Settings** link for this plugin on the **Installed Plugins** page of the Admin panel.
1. If you need to change Theme Options (Widgets, Sidebars, Menus, Templates, Background, Header, etc.) for any Theme *other than* the Active Theme, see the plugin's Settings page and click on the **Theme Options** tab for important information.

== Frequently Asked Questions ==

**IMPORTANT: Support has moved to the ZATZLabs site and is no longer provided on the WordPress.org forums. If you need a timely reply from the developer, please [open a ticket](http://zatzlabs.com/submit-ticket/).**

= What if my Themes or other plugins don't seem to be working with the jonradio Multiple Themes plugin? =

Please visit the new [ZATZLabs Forums](http://zatzlabs.com/forums/). If you need a timely reply from the developer, please [open a ticket](http://zatzlabs.com/submit-ticket/).

If we cannot solve the problem, please consider using a WordPress Network.  One install of WordPress allows you to have multiple separate Sites ("MultiSite"), each with a different Theme, without using the jonradio Multiple Themes plugin.  The sites can look to the outside world as if they are just one web site by using the Sub-directories option.  For example, Site 1 would be at example.com, and Site 2 could be at example.com/forum.

= Will this plugin work with Paid Themes? =

With the massive rewrite of Theme Selection logic in Version 5 of this plugin, it should now work with all Paid Themes and Plugins.  But we have only tested the few Paid Themes whose authors have provided us with permission to use, without charge, their themes for test purposes.  Elegant, for example, allows us to accept copies of its Themes provided by its customers who require assistance with the jonradio Multiple Themes plugin.  On the other hand, some other Paid Theme authors have simply ignored our requests, despite our stated willingness to sign a non-disclosure agreement.

We do encourage you to contact us if you run into problems when using the jonradio Multiple Themes plugin with a Paid Theme, as the problem may not be unique to the Paid Theme.

To state the obvious, the cost of purchasing a license for all Paid Themes for testing purposes is prohibitive for an Open Source plugin such as this one.

= How do I change the Theme Options (Widgets, Sidebars, Menus, Background, Header, etc.) used for each Theme? =

For the Active Theme, nothing changes when using the jonradio Multiple Themes plugin.  For other Themes, selected using this plugin, changing Theme Options is explained, in detail, on the Theme Options tab of the plugin's Settings page.

= How do I change the Template for a specific Page or Post? =

For the Current Theme, nothing changes when using the jonradio Multiple Themes plugin.  For a Page or Post where another Theme is displayed, as selected by this plugin's Settings, specifying the Template to be used is explained, in detail, on the Theme Options tab of the plugin's Settings page.

= How do I select a Theme for a Category of Posts? =

That functionality, to directly specify a Theme for a Category on the Settings page, is being investigated for a future version of the jonradio Multiple Themes plugin.  But there is already a solution based on Permalinks:

1. In the WordPress Admin panels, go to Settings-Permalinks
1. Specify a Permalinks structure that begins with /%category%/
1. Push the Save Changes button
1. Go to Settings-Multiple Themes plugin
1. In the Section "For An Individual Page, Post or other non-Admin page", select the Theme for the Category of Posts
1. Enter the URL of the Categories page, e.g. - http://domain.com/news/
1. Click the checkbox "Select here if URL is a Prefix"
1. Push the Save Changes button

= How do I Edit a Theme? =

WordPress includes a built-in Theme Editor.  Select Editor in the Admin panel's Appearance menu items on the left sidebar.

By default, the style.css file of the Current Theme is displayed.  You can edit other Themes by selecting them in the "Select theme to edit" field and clicking the Select button.

Alternatively, you can edit any Theme on your own computer.  If your computer runs Windows, NotePad++ and FileZilla run very well together, using FileZilla's View/Edit feature to provide a Theme Editor with syntax highlighting and other advanced features.

If one or more of the Active Themes have their own Theme Editor or other type of Theme Options panels, such as Elegant's epanel, please read the next FAQ.

= How do I use Elegant's epanel? =

Nothing changes for the Current Theme.  epanel can be accessed just as it would be without the jonradio Multiple Themes plugin, simply by selecting the WordPress Admin panel's Appearance submenu item titled Theme Options preceded by the name of your Elegant Theme.

To make changes to other Active Themes that you will be specifying with the jonradio Multiple Themes plugin:

1. Deactivate jonradio Multiple Themes 
1. Install the Theme Test Drive plugin found at http://wordpress.org/extend/plugins/theme-test-drive/
1. Activate the Theme Test Drive plugin
1. Go to Appearance-Theme Test Drive 
1. In the Usage section, select an alternate Theme you will be using with jonradio Multiple Themes 
1. Push the Enable Theme Drive button at the bottom 
1. Click on the Appearance menu item on the left sidebar of the WordPress Admin panel to refresh the submenu
1. Click on the submenu item titled with your Elegant theme's name followed by "Theme Options"
1. Elegant's epanel will now appear
1. Make all the changes for this Theme, being sure to push the Save button
1. If you have more than one alternate Theme with Options you wish to change, repeat Steps 4-10 for each alternate Theme 
1. Deactivate the Theme Test Drive plugin 
1. Activate jonradio Multiple Themes
1. Changes to the Options for the Current Theme can now be made normally, just as you would without either plugin
1. Both the alternate and Current Themes should now display all Theme options properly when selected through the jonradio Multiple Themes plugin

Thanks to Elegant for allowing us to test copies of any of their Themes provided by their customers.

= What happens when I change Permalinks? =

Although it depends on what kind of change you make to your Permalink structure, you should expect to have to replace (delete and add) all of the plugin's Settings that specify a URL.

= I added a new entry but why doesn't it appear in the list of entries? =

You should have seen an explanatory error message after hitting the Save Changes button.

= How can I change the Theme for an entry? =

You will need to delete the entry and add it again, with the new Theme specified.

== Screenshots ==

1. Top of Settings tab on Plugin's Settings page
2. Bottom of Settings tab on Plugin's Settings page
3. Advanced Settings tab on Plugin's Settings page
4. Theme Options tab on Plugin's Settings page
5. System Information tab on Plugin's Settings page
6. New in V5 tab on Plugin's Settings page

== Changelog ==

= 7.1.1 =
* Minor support update

= 7.1 =
* Very Simple AJAX Support: select a theme whenever /wp-admin/admin-ajax.php is accessed by URL
* Remove P2 Theme automatic handling as it no longer works with current version of P2 Theme

= 7.0.3 =
* Fix All Posts and All Pages setting's url_to_postid() function issues introduced in 7.0.2

= 7.0.2 =
* Fix Port URL matching issue

= 7.0.1 =
* Fix Query Matching when Value is an Integer in Query Settings

= 7.0 =
* Check and cleanup Settings on every viewing of the Settings page, to maximize compatibility with previous versions
* Store PostID as String instead of Integer in URL Settings so it will match incoming URL with p=id, etc.
* Make sure that both Query Keyword and Value are never any Numeric type, but always String
* Correct numerous small bugs

= 6.0.2 =
* Fix bug that stripped off Queries in URL Settings

= 6.0.1 =
* Store $wp->public_query_vars in Internal Settings for use before 'init' action
* Tolerate missing $_SERVER['QUERY_STRING']

= 6.0 =
* Add URL Alias settings
* Move "Settings" link to beginning of links on Installed Plugins Admin page

= 5.0.3 =
* Remove Cleanup code for Settings for non-existent Themes, as it conflicts with delete/reinstall theme version upgrades, and certain caching plugins

= 5.0.2 =
* Remove /downgrade/ directory in case it caused the reported PCLZIP_ERR_BAD_FORMAT errors

= 5.0.1 =
* Fix All Pages and All Posts by complete rewrite of Theme Selection logic for those Advanced Settings

= 5.0 =
* Major rewrite of the Theme Selection logic
* Tabs added to the Settings page, for easier navigation
* Greatly expanded compatibility with other Plugins and Themes
* Allow ?keyword=value&keyword=value Queries in URL, URL Prefix and URL Prefix with Asterisk ("*") Theme Selection entries
* Accurate Theme Selection even for plugins and themes that request Stylesheet or Template information before WordPress is fully loaded
* Tabs for the Setting page written in JavaScript for instant switching between tabs and preservation of input data, e.g. - switch between Settings and Advanced Settings tabs without having to retype your changes when you switch back
* Theme Selection entries, if any, displayed in the order in which they are processed, to clarify which Theme will be displayed for any given URL
* Complete How-To details on Theme Option and Template selection right on the Settings page, replacing the FAQs	in the WordPress Plugin Directory
* Automatic deletion of Theme Selection entries for Themes that have been deleted
* Enhanced performance with tighter code, and less of it, on the public side of your WordPress site
* Changing Permalinks invalidates more Theme Selection settings than in previous versions
* Changing the Theme for an Entry requires Deletion of the old Entry before or after adding the same Entry with the new Theme specified
* The new Theme Selection logic, based on URL Matching rather than Page, Post, Attachment, Category and Archive IDs, requires a conversion of some Settings from prior versions of the plugin. This conversion to the new format occurs automatically the first time that Version 5 runs. Old format settings are retained, transparently without being displayed on the Settings page, to allow downgrading to Version 4 from Version 5.

= 4.11.3 =
* Fix Array to String Warning on some Search plugins by rewriting jt_mt_themes_defined() which creates list of Themes referenced in plugin Settings
* Add More Diagnostic Information to Settings page
* Suggest "URL Prefix" setting when "URL" setting does not work, especially for WooCommerce
* Detect Memberium plugins and explain that Sticky will not work with Membership System V2 because it blocks Cookies
* Add Warning and change one confusing Error Message when Query is incorrectly included in URL of Page/Post entry

= 4.11.2 =
* Made all Query comparisons (URL match Setting) case insensitive
* Eliminate Connection Info prompt from Settings page that occurred for certain User Permissions
* Add File Permissions table to Settings

= 4.11.1 =
* Correct foreach() error

= 4.11 =
* Complete Rewrite of Sticky logic, adding additional Settings, including Override

= 4.10.1 =
* Sticky:  add a unique Query to URL so that Caching plugins will cache separate copies for each Theme used on a particular page

= 4.10 =
* Add a Sticky option (Advanced Setting) for URL Queries (keyword=value) that will select the same Theme for all subsequent pages viewed by the same Visitor
* Enhance performance by eliminating processing related to each Type of Setting when no Setting entries of that Type exist

= 4.9 =
* Add an Asterisk ("*") to match any Subdirectory at a given level of the File Hierarchy, as another form of the Prefix URL option
* Reorganize Settings page

= 4.8 =
* Delay intercept of get_options 'stylesheet' and 'template' until 'plugins_loaded' (NextGen Gallery conflict)
* Check for illegal characters in Keyword and Value of Query portion of URL in Settings fields

= 4.7.3 =
* Add support for dot in URL Queries (keyword or value) by replacing parse_str()
* Removed subfolder /includes/debug/

= 4.7.2 =
* Do not execute select-theme.php on Admin panels, to eliminate error message whenever any plugin is uninstalled
* Handle URL Query Keyword[]=Value
* Add Polylang to list of incompatible plugins

= 4.7.1 =
* Handle PHP without mbstring extension

= 4.7 =
* Add option to select a Theme based on Query Keyword and Value pair in URL
* Redesign how Query entries are stored
* Full testing completed with WordPress Version 3.8

= 4.6 =
* Add option to select a Theme based on Query Keyword in URL
* Rearrange Settings page

= 4.5.2 =
* Eliminate Fatal Error if php zip_open() function is not available, when readme.txt is out of date

= 4.5.1 =
* Remove %E2%80%8E suffix from URLs being entered

= 4.5 =
* Check with get_page_by_path() and get_posts( array( 'name' => $page_url ) ) if url_to_postid() fails to find URL input

= 4.4 =
* Rewrite Plugin's handling of its own version number to fix issues when new sites are activated in a Network and plugin is Network-Activated
* Prevent Fatal Error for Versions of WordPress before 3.4, and Deactivate Plugin instead, because plugin requires at least 3.4 to function
* Security:  require "switch_themes" Capability rather than "manage_options" Capability to access plugin's Settings page

= 4.3 =
* Add SSL support so that visitors can view the WordPress site with https:// URLs and Site URL can be https://

= 4.2 =
* Add option to override WordPress Current Theme
* Security enhancements to eliminate direct execution of .php files

= 4.1.1 =
* Handle situations where readme.txt file in plugin's directory cannot be read or written

= 4.1 =
* Support for non-alphanumeric characters in URLs, e.g. - languages using characters not in the English alphabet
* Support for Live Search feature of KnowHow Theme
* Display errors, not settings, on plugin's Admin page for activated BuddyPress or Theme Test Drive plugins, or old versions of WordPress
* Add error checking/messages and diagnostic information to plugin's Admin page

= 4.0.2 =
* Prevent Warning and Notice by initializing global $wp

= 4.0.1 =
* Prevent Fatal Error by initializing global $wp_rewrite

= 4 =
* Discovered url_to_postid() function, to address situations where Slug differed from Permalink, such as Posts with Year/Month folders

= 3.3.1 =
* Fix White Screen of Death on a Page selected by plugin

= 3.3 =
* Support Child Themes and any other situation where stylesheet and template names are not the same

= 3.2 =
* Correct Problem with P2 Theme, and its logged on verification at wp-admin/admin-ajax.php?p2ajax=true&action=logged_in_out&_loggedin={nonce}
* Add "Settings Saved" message to Admin page
* Tested with WordPress Version 3.5 beta

= 3.1 =
* Add Support for Prefixes, where all URLs beginning with the specified characters ("Prefix") can be assigned to a specified Theme

= 3.0 =
* Add Support for Categories and Archives when no Permalinks exist (support already existed Categories and Archives with Permalinks)
* Resolve several minor bugs

= 2.9 =
* Rewrite much of the Settings page and Plugin Directory documentation
* Add Support for IIS which returns incorrect values in $_SERVER['REQUEST_URI']
* Make it easier to select the Theme for the Site Home by providing a new Settings field
* Remove ability to set Theme for Admin pages since no known Theme provides Admin templates, and because the previous implementation sometimes displayed the incorrect Current Theme in Admin;  this feature may be re-added in a future release, and could even be used to change Settings of Themes that are not currently the Current Theme
* Add version upgrade detection to add, remove and update Settings fields
* Move Settings link on Plugins page from beginning to end of links

= 2.0 =
* Address pecularities of wp_make_link_relative() related to root-based WordPress sites using Permalinks

= 1.1 =
* Fix foreach failing on some systems, based on PHP warning level

= 1.0 =
* Make plugin conform to WordPress plugin repository standards.
* Beta testing completed.

== Upgrade Notice ==

= 7.1 =
AJAX Support

= 7.0.3 =
Make All Posts and All Pages settings work on sites without a Port Number specified

= 7.0.2 =
Correct URL Matching for Sites with a Port Number in their URL

= 7.0.1 =
Correctly handle p= and page_id= in Query Settings

= 7.0 =
Checks all Settings whenever Settings page displayed

= 6.0.2 =
Correct bug that stripped Queries from URLs in Settings

= 6.0.1 =
Tolerate missing $_SERVER['QUERY_STRING'] and $wp->public_query_vars not set up yet

= 6.0 =
Support Domain Mapping, Parked Domains and other Site Alias usages

= 5.0.3 =
Remove cleanup of non-existent themes specified in Settings

= 5.0.2 =
Remove /downgrade/ directory that may have caused PCLZIP_ERR_BAD_FORMAT errors on some web hosts

= 5.0.1 =
Correct All Pages and All Posts (Advanced) Setting bug

= 5.0 =
Greatly expanded compatibility with other Plugins and Themes

= 4.11.2 =
Standardize to case insensitive Query comparisons and eliminate Connection Info prompt on Settings page

= 4.11.1 =
Fix foreach() error

= 4.11 =
Complete rewrite of Sticky logic

= 4.10.1 =
Make Sticky work with Caching plugins

= 4.10 =
Performance improvements and add Sticky Queries

= 4.9 =
Allow Prefix URLs to match all subdirectories with an Asterisk ("*")

= 4.8 =
Compatibility with NextGen Gallery plugin

= 4.7.3 =
Allow dots in URL Queries

= 4.7.2 =
Avoid Error Message during Uninstall of other Plugins

= 4.7.1 =
Avoid mb_ function errors for PHP without mbstring extension

= 4.7 =
Select Theme by Query Keyword/Value pair in URL

= 4.6 =
Select Theme by Query Keyword in URL

= 4.5.2 =
Fix zip_open Fatal Error

= 4.5.1 =
Fix %E2%80%8E suffix problem on input URLs

= 4.5 =
Handle URL input for non-standard Pages and Posts

= 4.4 =
Fix errors when new Network site created or old WordPress version used, and correct Setting page Permissions to "switch_themes"

= 4.3 =
Add SSL support for sites with https:// URLs

= 4.2 =
Add "Select Theme for Everything" feature and improve security

= 4.1.1 =
Resolve issues with readme.txt permissions introduced in Version 4.1's compatibility checking

= 4.1 =
Support non-English alphabet in URLs and Live Search feature in KnowHow Theme

= 4.0.2 =
Fix "Warning: in_array() expects parameter 2 to be array, null given in domain.com/wp-includes/rewrite.php on line 364"

= 4.0.1 =
Fix "Fatal error: Call to a member function wp_rewrite_rules() on a non-object in domain.com/wp-includes/rewrite.php on line 294"

= 4 =
Fix Posts not working in some Permalink setups, most notably Year/Month

= 3.3.1 =
Fix White Screen of Death on a Page, Post or other element selected by plugin

= 3.3 =
Remove Restriction that Stylesheet Name must match Template Name, which it does not with Child Themes 

= 3.2 =
Add Support for P2 Theme and provide "Settings Saved" message

= 3.1 =
Allow Prefix URLs to be used to specify where a Theme will be displayed

= 3.0 =
Improve support for Categories and Archives, and eliminate all known bugs.

= 2.9 =
Improve Settings fields, correct display of wrong Current Theme in Appearance-Themes Admin panel, and add IIS Support.

= 2.0 =
Selecting Individual Pages and Posts on a WordPress site installed in the root and using Permalinks now works correctly.

= 1.1 =
Eliminate possibility of foreach error message if PHP warning level is set at a high level

= 1.0 =
Beta version 0.9 had not been tested when installed from the WordPress Plugin Repository

