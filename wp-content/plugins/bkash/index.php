<?php
/*
Plugin Name: SoftTech-IT bKash, Rocket, Nagad
Plugin URI:  http://softtech-it.com
Description: This plugin helps users to integrate bkash , rocket and nagad payment gateway along with SMS API with woocommerce. bKash, rocket and nagad all of them are money transfer systems of Bangladesh by facilitating money transfer through mobile phones. This plugin is an addon for woocommerce, so woocommerce is mandatory
Version:     2.2
Author:      Md Toriqul Mowla Sujan
Author URI:  http://facebook.com/sujan4g
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Domain Path: /languages
Text Domain: stb
*/
defined('ABSPATH') or die('Only a foolish person try to access directly to see this white page. :-) ');

/**
 * Plugin language
 */
load_plugin_textdomain( 'stb', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );

add_action("admin_menu", "softtechit_add_sms_submenu_page");

function softtechit_add_sms_submenu_page(){
  add_submenu_page( 'woocommerce', 'SMS API Integration Page', 'SMS API Integration',
    'manage_options', 'stit-sms-integration', 'stit_sms_integration_callback');
}

function stit_sms_integration_callback(){

  if (!current_user_can('manage_options')) {
      wp_die('Unauthorized user');
  }

  if (isset($_POST['save'])) {

    if (isset($_POST['sms-api-url'])) {
      $url = $_POST["sms-api-url"];
      update_option('sms-api-url', $url);
    }

    if (isset($_POST['sms-api-username'])) {
      $username = $_POST["sms-api-username"];
      update_option('sms-api-username', $username);
    }

    if (isset($_POST['sms-api-password'])) {
      $password = $_POST["sms-api-password"];
      update_option('sms-api-password', $password);
    }

    echo '<div id="setting-error-settings_updated" class="notice notice-success settings-error is-dismissible">
<p><strong>Settings saved.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';

  }

  $sms_url = get_option('sms-api-url', 'http://66.45.237.70/api.php');
  $sms_username = get_option('sms-api-username', 'user');
  $sms_password = get_option('sms-api-password', 'pass');
  ?>
  <div class="wrap woocommerce">
    <form method="post" id="mainform" action="" enctype="multipart/form-data">
    <?php settings_errors(); ?>
    <h1>SMS API Integration</h1>
    <table class="form-table">
      <tbody>
        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-url">API URL </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>API URL</span></legend>
              <input class="input-text regular-input " type="url" name="sms-api-url" id="sms-api-url" style="" value="<?php echo $sms_url; ?>" placeholder="">
              <p class="description">The API link / url you have got from your sms gateway provider. It can be like this ( http://66.45.237.70/api.php )</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-username">Username </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Username</span></legend>
              <input class="input-text regular-input " type="text" name="sms-api-username" id="sms-api-username" style="" value="<?php echo $sms_username; ?>" placeholder="">
              <p class="description">The Username of your API</p>
            </fieldset>
          </td>
        </tr>

        <tr valign="top">
          <th scope="row" class="titledesc">
            <label for="sms-api-password">Password </label>
          </th>
          <td class="forminp">
            <fieldset>
              <legend class="screen-reader-text"><span>Password</span></legend>
              <input class="input-text regular-input " type="password" name="sms-api-password" id="sms-api-password" style="" value="<?php echo $sms_password; ?>" placeholder="">
              <p class="description">The Password of your API</p>
            </fieldset>
          </td>
        </tr>


      </tbody>
    </table>

    <p class="submit">
      <?php wp_nonce_field( 'sms_nonce_field' ); ?>
      <button name="save" class="button-primary woocommerce-save-button" type="submit" value="Save changes">Save changes</button>

    </p>
  </form>
  </div>
  <?php
}

add_filter("woocommerce_thankyou_order_received_text", "softtechit_send_sms_when_order_received");

function softtechit_send_sms_when_order_received(){

  $order_id = get_query_var('order-received');
  $order = new WC_Order( $order_id );

  $sms_url = get_option('sms-api-url', 'http://66.45.237.70/api.php');
  $sms_username = get_option('sms-api-username', 'user');
  $sms_password = get_option('sms-api-password', 'pass');

  $url = $sms_url;
  $username = $sms_username;
  $password = $sms_password;
  $phone = "88" . $order->get_billing_phone();
  $message = "Hello " . $order->get_billing_first_name() . " " . $order->get_billing_last_name() . "," ."\n\nthank you for your order \n#" . $order_id . ". View the latest status of your order here\n" . site_url() ."/my-account/orders/";
  $data = array(
    "username" => $username,
    "password" => $password,
    "number" => $phone,
    "message" => $message,
  );

  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url );
  curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data) );
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true );
  $smsresult = curl_exec($ch);
}








add_action( 'wp_enqueue_scripts', 'softtech_payment_method_script' );
function softtech_payment_method_script(){
    wp_enqueue_script( 'stb-script', plugins_url( 'js/scripts.js', __FILE__ ), array('jquery'), '1.0', true );
    wp_enqueue_style( 'stb-style', plugins_url( 'css/style.css', __FILE__ ));
}

require_once('bkash.php');
require_once('rocket.php');
require_once('nagad.php');
