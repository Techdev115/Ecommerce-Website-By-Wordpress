<?php

/**
 * Plugin core start
 * Checked Woocommerce activation
 */
if( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ){

    /**
     * bKash gateway register
     */
    add_filter('woocommerce_payment_gateways', 'softtech_bkash_payment_gateways');
    function softtech_bkash_payment_gateways( $gateways ){
        $gateways[] = 'SoftTechIT_Bkash';
        return $gateways;
    }

    /**
     * bKash gateway init
     */
    add_action('plugins_loaded', 'softtech_bkash_plugin_activation');
    function softtech_bkash_plugin_activation(){

        class SoftTechIT_Bkash extends WC_Payment_Gateway {

            public $bkash_number;
            public $number_type;
            public $order_status;
            public $instructions;
            public $bkash_charge;

            public function __construct(){
                $this->id                     = 'softtech_bkash';
                $this->title                 = $this->get_option('title', 'bKash');
                $this->description             = $this->get_option('description', 'bKash payment Gateway');
                $this->method_title         = esc_html__("bKash", "stb");
                $this->method_description     = esc_html__("bKash Payment Gateway Options", "stb" );
                $this->icon                 = plugins_url('images/bkash.png', __FILE__);
                $this->has_fields             = true;

                $this->softtech_bkash_options_fields();
                $this->init_settings();

                $this->bkash_number = $this->get_option('bkash_number');
                $this->number_type     = $this->get_option('number_type');
                $this->order_status = $this->get_option('order_status');
                $this->instructions = $this->get_option('instructions');
                $this->bkash_charge = $this->get_option('bkash_charge');

                add_action( 'woocommerce_update_options_payment_gateways_'.$this->id, array( $this, 'process_admin_options' ) );
                add_filter( 'woocommerce_thankyou_order_received_text', array( $this, 'softtech_bkash_thankyou_page' ) );
                add_action( 'woocommerce_email_before_order_table', array( $this, 'softtech_bkash_email_instructions' ), 10, 3 );
            }


            public function softtech_bkash_options_fields(){
                $this->form_fields = array(
                    'enabled'     =>    array(
                        'title'        => esc_html__( 'Enable/Disable', "stb" ),
                        'type'         => 'checkbox',
                        'label'        => esc_html__( 'bKash Payment', "stb" ),
                        'default'    => 'yes'
                    ),
                    'title'     => array(
                        'title'     => esc_html__( 'Title', "stb" ),
                        'type'         => 'text',
                        'default'    => esc_html__( 'bKash', "stb" )
                    ),
                    'description' => array(
                        'title'        => esc_html__( 'Description', "stb" ),
                        'type'         => 'textarea',
                        'default'    => esc_html__( 'Please complete your bKash payment at first, then fill up the form below.', "stb" ),
                        'desc_tip'    => true
                    ),
                    'order_status' => array(
                        'title'       => esc_html__( 'Order Status', "stb" ),
                        'type'        => 'select',
                        'class'       => 'wc-enhanced-select',
                        'description' => esc_html__( 'Choose whether status you wish after checkout.', "stb" ),
                        'default'     => 'wc-on-hold',
                        'desc_tip'    => true,
                        'options'     => wc_get_order_statuses()
                    ),
                    'bkash_number'    => array(
                        'title'            => esc_html__( 'bKash Number', "stb" ),
                        'description'     => esc_html__( 'Add a bKash mobile no which will be shown in checkout page', "stb" ),
                        'type'            => 'text',
                        'desc_tip'      => true
                    ),
                    'number_type'    => array(
                        'title'            => esc_html__( 'Agent/Personal', "stb" ),
                        'type'            => 'select',
                        'class'           => 'wc-enhanced-select',
                        'description'     => esc_html__( 'Select bKash account type', "stb" ),
                        'options'    => array(
                            'Agent'        => esc_html__( 'Agent', "stb" ),
                            'Personal'    => esc_html__( 'Personal', "stb" )
                        ),
                        'desc_tip'      => true
                    ),
                    'bkash_charge'     =>    array(
                        'title'            => esc_html__( 'Enable bKash Charge', "stb" ),
                        'type'             => 'checkbox',
                        'label'            => esc_html__( 'Add 1.85% bKash "Send Money" charge to net price', "stb" ),
                        'description'     => esc_html__( 'If a product price is 1000 then customer have to pay ( 1000 + 18.5 ) = 1018.5 Here 18.5 is bKash send money charge', "stb" ),
                        'default'        => 'no',
                        'desc_tip'        => true
                    ),
                    'instructions' => array(
                        'title'           => esc_html__( 'Instructions', "stb" ),
                        'type'            => 'textarea',
                        'description'     => esc_html__( 'Instructions that will be added to the thank you page and emails.', "stb" ),
                        'default'         => esc_html__( 'Thanks for purchasing through bKash. We will check and give you update as soon as possible.', "stb" ),
                        'desc_tip'        => true
                    ),
                );
            }


            public function payment_fields(){

                global $woocommerce;
                $bkash_charge = ($this->bkash_charge == 'yes') ? esc_html__(' Also note that 1.85% bKash "SEND MONEY" cost will be added with net price. Total amount you need to send us at', "stb" ). ' ' . get_woocommerce_currency_symbol() . $woocommerce->cart->total : '';
                echo wpautop( wptexturize( esc_html__( $this->description, "stb" ) ) . $bkash_charge  );
                echo wpautop( wptexturize( "bKash ".$this->number_type." Number : ".$this->bkash_number ) );

                ?>
                    <table border="0">
                      <tr>
                        <td><label for="bkash_number"><?php esc_html_e( 'bKash Number', "stb" );?></label></td>
                        <td><input class="widefat" type="text" name="bkash_number" id="bkash_number" placeholder="017XXXXXXXX"></td>
                      </tr>
                      <tr>
                        <td><label for="bkash_transaction_id"><?php esc_html_e( 'bKash Transaction ID', "stb" );?></label></td>
                        <td><input class="widefat" type="text" name="bkash_transaction_id" id="bkash_transaction_id" placeholder="8N7A6D5EE7M"></td>
                      </tr>
                    </table>


                <?php
            }


            public function process_payment( $order_id ) {
                global $woocommerce;
                $order = new WC_Order( $order_id );

                $status = 'wc-' === substr( $this->order_status, 0, 3 ) ? substr( $this->order_status, 3 ) : $this->order_status;
                // Mark as on-hold (we're awaiting the bKash)
                $order->update_status( $status, esc_html__( 'Checkout with bKash payment. ', "stb" ) );

                // Reduce stock levels
                $order->reduce_order_stock();

                // Remove cart
                $woocommerce->cart->empty_cart();

                // Return thankyou redirect
                return array(
                    'result' => 'success',
                    'redirect' => $this->get_return_url( $order )
                );
            }


            public function softtech_bkash_thankyou_page() {
                $order_id = get_query_var('order-received');
                $order = new WC_Order( $order_id );
                if( $order->get_payment_method() == $this->id ){

                    $thankyou = $this->instructions;
                    return $thankyou;
                } else {

                    return esc_html__( 'Thank you. Your order has been received.', "stb" );
                }

            }


            public function softtech_bkash_email_instructions( $order, $sent_to_admin, $plain_text = false ) {
                if( $order->get_payment_method() != $this->id )
                    return;
                if ( $this->instructions && ! $sent_to_admin && $this->id === $order->get_payment_method() ) {
                    echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
                }
            }

        }

    }

    /**
     * Add settings page link in plugins
     */
    add_filter( "plugin_action_links_". plugin_basename(__FILE__), 'softtech_bkash_settings_link' );
    function softtech_bkash_settings_link( $links ) {

        $settings_links = array();
        $settings_links[] ='<a href="https://www.facebook.com/softtechitinstitute/" target="_blank">' . esc_html__( 'Follow US', 'stb' ) . '</a>';
        $settings_links[] ='<a href="' . admin_url( 'admin.php?page=wc-settings&tab=checkout&section=softtech_bkash' ) . '">' . esc_html__( 'Settings', 'stb' ) . '</a>';

        // add the links to the list of links already there
        foreach($settings_links as $link) {
            array_unshift($links, $link);
        }

        return $links;
    }

    /**
     * If bKash charge is activated
     */
    $bkash_charge = get_option( 'woocommerce_softtech_bkash_settings' );
    if( isset($bkash_charge['bkash_charge']) ){
        if( $bkash_charge['bkash_charge'] == 'yes' ){


            add_action( 'woocommerce_cart_calculate_fees', 'softtech_bkash_charge' );
            function softtech_bkash_charge(){

                global $woocommerce;
                $available_gateways = $woocommerce->payment_gateways->get_available_payment_gateways();
                $current_gateway = '';

                if ( !empty( $available_gateways ) ) {
                    if ( isset( $woocommerce->session->chosen_payment_method ) && isset( $available_gateways[ $woocommerce->session->chosen_payment_method ] ) ) {
                        $current_gateway = $available_gateways[ $woocommerce->session->chosen_payment_method ];
                    }
                }

                if( $current_gateway!='' ){

                    $current_gateway_id = $current_gateway->id;

                    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
                        return;

                    if ( $current_gateway_id =='softtech_bkash' ) {
                        $percentage = 0.0185;
                        $surcharge = round( $woocommerce->cart->cart_contents_total * $percentage );
                        $woocommerce->cart->add_fee( esc_html__('bKash Charge', 'stb'), $surcharge, true, '' );
                    }

                }

            }

        }
    }

    /**
     * Empty field validation
     */
    add_action( 'woocommerce_checkout_process', 'softtech_bkash_payment_process' );
    function softtech_bkash_payment_process(){

        if($_POST['payment_method'] != 'softtech_bkash')
            return;

        $bkash_number = sanitize_text_field( $_POST['bkash_number'] );
        $bkash_transaction_id = sanitize_text_field( $_POST['bkash_transaction_id'] );

        $match_number = isset($bkash_number) ? $bkash_number : '';
        $match_id = isset($bkash_transaction_id) ? $bkash_transaction_id : '';

        $validate_number = preg_match( '/^01[1-9]\d{8}$/', $match_number );
        $validate_id = preg_match( '/[a-zA-Z0-9]+/',  $match_id );

        if( !isset($bkash_number) || empty($bkash_number) )
            wc_add_notice( esc_html__( 'Please add your mobile number', 'stb'), 'error' );

        if( !empty($bkash_number) && $validate_number == false )
            wc_add_notice( esc_html__( 'Incorrect mobile number. It must be 11 digit, starts with 013 / 015 / 016 / 017 / 018 / 019', 'stb'), 'error' );

        if( !isset($bkash_transaction_id) || empty($bkash_transaction_id) )
            wc_add_notice( esc_html__( 'Please add your bKash transaction ID', 'stb' ), 'error' );

        if( !empty($bkash_transaction_id) && $validate_id == false )
            wc_add_notice( esc_html__( 'Only number or letter is acceptable', 'stb'), 'error' );

    }

    /**
     * Update bKash field to database
     */
    add_action( 'woocommerce_checkout_update_order_meta', 'softtech_bkash_additional_fields_update' );
    function softtech_bkash_additional_fields_update( $order_id ){

        if($_POST['payment_method'] != 'softtech_bkash' )
            return;

        $bkash_number = sanitize_text_field( $_POST['bkash_number'] );
        $bkash_transaction_id = sanitize_text_field( $_POST['bkash_transaction_id'] );

        $number = isset($bkash_number) ? $bkash_number : '';
        $transaction = isset($bkash_transaction_id) ? $bkash_transaction_id : '';

        update_post_meta($order_id, '_bkash_number', $number);
        update_post_meta($order_id, '_bkash_transaction', $transaction);

    }

    /**
     * Admin order page bKash data output
     */
    add_action('woocommerce_admin_order_data_after_billing_address', 'softtech_bkash_admin_order_data' );
    function softtech_bkash_admin_order_data( $order ){

        if( $order->get_payment_method() != 'softtech_bkash' )
            return;


        $number = (get_post_meta($_GET['post'], '_bkash_number', true)) ? get_post_meta($_GET['post'], '_bkash_number', true) : '';
        $transaction = (get_post_meta($_GET['post'], '_bkash_transaction', true)) ? get_post_meta($_GET['post'], '_bkash_transaction', true) : '';

        ?>
        <div class="form-field form-field-wide">
            <img src='<?php echo plugins_url("images/bkash.png", __FILE__); ?>' alt="bKash">
            <table class="wp-list-table widefat fixed striped posts">
                <tbody>
                    <tr>
                        <th><strong><?php esc_html_e('bKash No.', 'stb') ;?></strong></th>
                        <td>: <?php echo esc_attr( $number );?></td>
                    </tr>
                    <tr>
                        <th><strong><?php esc_html_e('Transaction ID', 'stb') ;?></strong></th>
                        <td>: <?php echo esc_attr( $transaction );?></td>

                    </tr>
                </tbody>
            </table>
        </div>
        <?php

    }

    /**
     * Order review page bKash data output
     */
    add_action('woocommerce_order_details_after_customer_details', 'softtech_bkash_additional_info_order_review_fields' );
    function softtech_bkash_additional_info_order_review_fields( $order ){

        if( $order->get_payment_method() != 'softtech_bkash' )
            return;

        global $wp;

        // Get the order ID
        $order_id  = absint( $wp->query_vars['order-received'] );

        $number = (get_post_meta($order_id, '_bkash_number', true)) ? get_post_meta($order_id, '_bkash_number', true) : '';
        $transaction = (get_post_meta($order_id, '_bkash_transaction', true)) ? get_post_meta($order_id, '_bkash_transaction', true) : '';

        ?>
        <table>
            <tr>
                <th><?php esc_html_e('bKash No:', 'stb');?></th>
                <td><?php echo esc_attr( $number );?></td>
            </tr>
            <tr>
                <th><?php esc_html_e('Transaction ID:', 'stb');?></th>
                <td><?php echo esc_attr( $transaction );?></td>
            </tr>
        </table>
        <?php

    }

    /**
     * Register new admin column
     */
    add_filter( 'manage_edit-shop_order_columns', 'softtech_bkash_admin_new_column' );
    function softtech_bkash_admin_new_column($columns){

        $new_columns = (is_array($columns)) ? $columns : array();
        unset( $new_columns['order_actions'] );
        $new_columns['mobile_no']     = esc_html__('bKash No.', 'stb');
        $new_columns['tran_id']     = esc_html__('Tran. ID', 'stb');

        $new_columns['order_actions'] = $columns['order_actions'];
        return $new_columns;

    }

    /**
     * Load data in new column
     */
    add_action( 'manage_shop_order_posts_custom_column', 'softtech_bkash_admin_column_value', 2 );
    function softtech_bkash_admin_column_value($column){

        global $post;

        $mobile_no = (get_post_meta($post->ID, '_bkash_number', true)) ? get_post_meta($post->ID, '_bkash_number', true) : '';
        $tran_id = (get_post_meta($post->ID, '_bkash_transaction', true)) ? get_post_meta($post->ID, '_bkash_transaction', true) : '';

        if ( $column == 'mobile_no' ) {
            echo esc_attr( $mobile_no );
        }
        if ( $column == 'tran_id' ) {
            echo esc_attr( $tran_id );
        }
    }

} else {
    /**
     * Admin Notice
     */
    add_action( 'admin_notices', 'softtech_bkash_admin_notice__error' );
    function softtech_bkash_admin_notice__error() {
        ?>
        <div class="notice notice-error">
            <p><a href="http://wordpress.org/extend/plugins/woocommerce/"><?php esc_html_e( 'Woocommerce', 'stb' ); ?></a> <?php esc_html_e( 'plugin needs to actived if you want to install this plugin.', 'stb' ); ?></p>
        </div>
        <?php
    }

    /**
     * Deactivate Plugin
     */
    add_action( 'admin_init', 'softtech_bkash_deactivate' );
    function softtech_bkash_deactivate() {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        unset( $_GET['activate'] );
    }
}
