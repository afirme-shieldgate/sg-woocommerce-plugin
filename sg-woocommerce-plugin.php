<?php

/*
Plugin Name: ShieldGate WooCommerce Plugin
Plugin URI: http://www.shieldgate.mx
Description: This module is a solution that allows WooCommerce users to easily process credit card payments.
Version: 2.0.1
Author: ShieldGate
Author URI: https://developers.shieldgate.mx/docs/payments/
Text Domain: sg_woocommerce
Domain Path: /languages
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html
*/

require( dirname( __FILE__ ) . '/includes/sg-woocommerce-refund.php' );
require(dirname(__FILE__) . '/includes/sg-woocommerce-webhook-api.php');

if ( ! in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
    return;
}

const SG_FLAVOR = "Shieldgate";
const SG_DOMAIN = "shieldgate.mx";
const SG_REFUND = "/v2/transaction/refund/";
const SG_LTP    = "/linktopay/init_order/";

add_action( 'plugins_loaded', 'sg_woocommerce_plugin' );

function shieldgate_payment_webhook( WP_REST_Request $request ) {
    $parameters = $request->get_params();
    try {
        $order = new WC_Order($parameters['transaction']['dev_reference']);
        $response_params = WC_Payment_Webhook_SG::update_order($order, $parameters);
        return new WP_REST_Response($response_params['message'], $response_params['code']);
    } catch (Exception $e){
        return new WP_REST_Response("update order fails, details: {$e}", 400);
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'shieldgate/webhook/v1', 'params', array(
        'methods' => WP_REST_SERVER::CREATABLE,
        'callback' => 'shieldgate_payment_webhook',
        'args' => array(),
        'permission_callback' => function () {
            return true;
        }
    ) );
});

register_activation_hook( __FILE__, array( 'SG_WC_Helper', 'create_table' ) );

load_plugin_textdomain( 'sg_woocommerce', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

if (!function_exists('sg_woocommerce_plugin')) {
    function sg_woocommerce_plugin() {
        class SG_WC_Plugin extends WC_Payment_Gateway {

            public function __construct() {
                $this->id                 = 'sg_woocommerce';
                $this->icon               = apply_filters('woocomerce_pg_icon', plugins_url('/assets/imgs/payment_checkout.png', __FILE__));
                $this->method_title       = SG_FLAVOR;
                $this->method_description = __('This module is a solution that allows WooCommerce users to easily process credit card payments. Developed by: ', 'sg_woocommerce').SG_FLAVOR;
                $this->supports           = array( 'products', 'refunds' );

                $this->init_settings();
                $this->init_form_fields();

                $this->title                = $this->get_option('title');
                $this->description          = $this->get_option('description');
                $this->card_button_text     = $this->get_option('card_button_text');
                $this->ltp_button_text      = $this->get_option('ltp_button_text');

                $this->checkout_language    = $this->get_option('checkout_language');
                $this->environment          = $this->get_option('staging');
                $this->enable_ltp           = $this->get_option('enable_ltp');
                $this->enable_card          = $this->get_option('enable_card');
                $this->enable_installments  = $this->get_option('enable_installments');

                $this->app_code_client      = $this->get_option('app_code_client');
                $this->app_key_client       = $this->get_option('app_key_client');
                $this->app_code_server      = $this->get_option('app_code_server');
                $this->app_key_server       = $this->get_option('app_key_server');

                $this->css                  = plugins_url('/assets/css/styles.css', __FILE__);

                add_action('woocommerce_update_options_payment_gateways_' . $this->id, array(&$this, 'process_admin_options'));

                add_action('woocommerce_receipt_sg_woocommerce', array(&$this, 'receipt_page'));
            }

            public function init_form_fields() {
                $this->form_fields = require( dirname( __FILE__ ) . '/includes/admin/sg-woocommerce-settings.php' );
            }

            function admin_options() {
                $logo = plugins_url('/assets/imgs/payment.png', __FILE__);
                ?>
                <p>
                    <img style='width: 30%;position: relative;display: inherit;'src='<?php echo $logo;?>'>
                </p>
                <h2><?php echo SG_FLAVOR.' Gateway'; ?></h2>
                <table class="form-table">
                    <?php $this->generate_settings_html(); ?>
                </table>
                <?php
            }

            function receipt_page($orderId) {
                $order = new WC_Order($orderId);
                if ($this->enable_card == 'no' and $this->enable_ltp == 'no') {
                    ?>
                    <link rel="stylesheet" type="text/css" href="<?php echo $this->css; ?>">
                    <div>
                        <p class="alert alert-warning">
                            <?php _e( 'There are no payment methods selected by the merchant', 'sg_woocommerce' ) ?>
                        </p>
                    </div>
                    <div id="button-return">
                        <p>
                            <a class="return-button" href="<?php echo get_permalink( wc_get_page_id( 'checkout' ) ); ?>">
                                <?php _e( 'Return to checkout', 'sg_woocommerce' ) ?>
                            </a>
                        </p>
                    </div>
                    <?php
                }
                if ($this->enable_card == 'yes') {
                    WC()->cart->empty_cart();
                    $this->generate_cc_form($order);
                }
                if ($this->enable_ltp == 'yes') {
                    WC()->cart->empty_cart();
                    $this->generate_ltp_form($order);
                }
            }

            public function process_refund( $order_id, $amount = null,  $reason = '' ) {
                $refund = new WC_Payment_Refund_SG();
                $refund_data = $refund->refund($order_id, $amount);
                if ($refund_data['success']) {
                    $order = new WC_Order($order_id);
                    $order->add_order_note( __('Transaction: ', 'sg_woocommerce') . $refund_data['transaction_id'] . __(' refund status: ', 'sg_woocommerce') . $refund_data['status'] . __(' reason: ', 'sg_woocommerce') . $reason);
                    return $refund_data['success'];
                } else {
                    return $refund_data['success'];
                }
            }

            public function generate_ltp_form($order) {
                $url = SG_WC_Helper::generate_ltp($order, $this->environment);
                $order->update_status( 'on-hold', __( 'Payment status will be updated via webhook.', 'sg_woocommerce' ) );
                ?>
                <link rel="stylesheet" type="text/css" href="<?php echo $this->css; ?>">
                <div id="payment-buttons">
                    <button id="ltp-button" class="<?php if($url == NULL){echo "hide";} else {echo "ltp-button";} ?>" onclick="ltpRedirect()">
                        <?php echo $this->ltp_button_text; ?>
                    </button>
                </div>
                <script>
                    function ltpRedirect() {
                        location.replace("<?php echo $url; ?>")
                    }
                </script>
                <?php
            }

            public function generate_cc_form($order) {
                $webhook_p = plugins_url('/includes/sg-woocommerce-webhook-checkout.php', __FILE__);
                $checkout = plugins_url('/assets/js/payment_checkout.js', __FILE__);
                $order_data = SG_WC_Helper::get_checkout_params($order);
                SG_WC_Helper::get_installments_type($this->enable_installments);
                ?>
                <link rel="stylesheet" type="text/css" href="<?php echo $this->css; ?>">

                <div id="msj-succcess" class="hide"> <p class="alert alert-success" ><?php _e('Your payment has been made successfully. Thank you for your purchase.', 'sg_woocommerce'); ?></p> </div>
                <div id="msj-failed" class="hide"> <p class="alert alert-warning"><?php _e('An error occurred while processing your payment and could not be made. Try another Credit Card.', 'sg_woocommerce'); ?></p> </div>

                <div id="button-return" class="hide">
                    <p>
                        <a class="return-button" href="<?php echo get_permalink( wc_get_page_id( 'shop' ) ); ?>"><?php _e( 'Return to Store', 'sg_woocommerce' ) ?></a>
                    </p>
                </div>

                <div id="payment-buttons">
                    <script src="https://cdn.shieldgate.mx/ccapi/sdk/payment_checkout_stable.min.js"></script>
                </div>

                <button id="checkout-button" class="js-payment-checkout"><?php echo $this->card_button_text; ?></button>

                <div id="order_data" class="hide">
                    <?php echo json_encode($order_data); ?>
                </div>

                <script id="woocommerce_checkout_pg"
                        webhook_p="<?php echo $webhook_p; ?>"
                        app_key="<?php echo $this->app_key_client; ?>"
                        app_code="<?php echo $this->app_code_client; ?>"
                        checkout_language="<?php echo $this->checkout_language; ?>"
                        environment="<?php echo $this->environment; ?>"
                        enable_installments="<?php echo $this->enable_installments; ?>"
                        src="<?php echo $checkout; ?>">
                </script>
                <?php
            }

            /**
             * Process the payment and return the result
             *
             * @param int $orderId
             * @return array
             */
            public function process_payment($orderId) {
                $order = new WC_Order($orderId);
                return array(
                    'result' => 'success',
                    'redirect' => $order->get_checkout_payment_url(true)
                );
            }
        }
    }
}

function add_sg_woocommerce_plugin( $methods ) {
    $methods[] = 'SG_WC_Plugin';
    return $methods;
}

add_filter( 'woocommerce_payment_gateways', 'add_sg_woocommerce_plugin' );
