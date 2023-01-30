=== Plugin Name ===
Contributors: sujon3g, developerzinan
Tags: woocommerce, bkash, rocket, nagad, Bangladesh, BDT, gateway, woocommerce bkash, sujan, softtech, softtech-it, mtm sujan, toriqul, toriqul mowla, toriqul mowla sujan
Requires at least: 4.5.0
Tested up to: 5.4
Stable tag: 2.2
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Easy to use bKash , Rocket and Nagad Payment Gateway for Woocommerce

== Description ==

This is a cool plugin to integrate bKash , Rocket and Nagad payment gateway and also SMS api in any woocommerce based website. It is lightweight and very easy to use.

Please note:
- This is a woocommerce based plugin so woocommerce plugin must be activated.
- You must have a bKash or Rocket or Nagad account to get payments.
- you must have bulksmsbd.com account to integrate sms api

= Using the Plugin =

* Download the plugin, install and active.

* Now go to <strong>WooCommerce</strong> > <strong>Settings</strong> > <strong>bKash/Rocket/Nagad</strong>, location URL would be like below -

`
http://yourwebsite.domain/wp-admin/admin.php?page=wc-settings&tab=checkout&section=softtech_bkash
`

* Now you will see few default setup but you need to fill up bKash/Rocket/Nagad account number under <strong>bKash Number/Rocket Number/Nagad Number</strong> field, also you might adjust other fileds too.

* If you want to send sms to users automatically when they order any product, 
then at first create an account to bulksmsbd.com and recharge some amount to buy sms credit. you will get an option named API in the bulksmsbd dashboard, go to the API page and you will get the API url in that page. after that please login to your wordpress dashboard and then go to
WooCommerce > SMS API Integration, then insert the api url, your bulksmsbd username and password and save

* That's it. You are ready to go!

== Installation ==

You may install the plugin using one of the three following methods:

1. Upload the plugin files to the `/wp-content/plugins/` directory, or install the plugin through the WordPress plugins screen directly.
2. Then activate the plugin.
3. Go to woocommerce > settings > checkout > bKash / Rocket / Nagad and setup your necessary settings.


== Frequently Asked Questions ==

= What is bKash/Rocket/Nagad? =
bKash/Rocket/Nagad all of them are mobile financial services in Bangladesh operating under the authority of Bangladesh Bank as a subsidiary of BRAC Bank Limited (bKash), Dutch Bangla Bank (Rocket), Bangladesh Post and Telecommunication Division (Nagad).

= Is it Woocommerce dependent plugin? =
Yes, You must install and active woocommerce plugin to make this plugin work.

= Is bKash / Rocket / Nagad merchant account necessary? =
No, it could be personal or agent account.

= Is is secured? =
Yes definitely, Maintained high level validation in both frontend and backend.


== Screenshots ==

1. Overview Screenshot

== Changelog ==

= 2.2 =
* Added - sms api integrated

= 2.1 =
* Fix - made the bkash , rocket and nagad charges to round number

= 2.0 =
* Added - Rocket Payment Gateway has been added
* Added - Nagad Payment Gateway has been added
* Fix - bKash, Rocket and Nagad charge has been fixed

= 1.3 =
* Fix - issues has been fixed while activating the plugin
* Fix - Errors has been fixed in order page of dashboard
* Change - Changed the name and logo of the plugin because of some issues with bkash trademark

= 1.2 =
* Added - Woocommerce activation checked
* Localization - Bangla translation file added(100% Bangla).

= 1.1 =
* Fix - Transaction id issue
* Localization - .pot file (Translation facility added).

= 1.0 =
* Initial release.
