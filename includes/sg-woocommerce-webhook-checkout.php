<?php
header("HTTP/1.0 204 transaction_id already received");
date_default_timezone_set("UTC");
require_once('../../../../wp-load.php');
require_once( dirname( __FILE__ ) . '/sg-woocommerce-helper.php' );
require_once( dirname( __DIR__ ) . '/sg-woocommerce-plugin.php' );

$requestBodyJs = json_decode(file_get_contents('php://input'), true);

$status = $requestBodyJs["transaction"]['status'];
$status_detail = (int)$requestBodyJs["transaction"]['status_detail'];
$transaction_id = $requestBodyJs["transaction"]['id'];
$authorization_code = $requestBodyJs["transaction"]['authorization_code'];
$dev_reference = $requestBodyJs["transaction"]['dev_reference'];
$payment_message = $requestBodyJs["transaction"]['message'];
$payment_stoken = $requestBodyJs["transaction"]['stoken'];
$payment_date = strtotime($requestBodyJs["transaction"]['date']);
$actual_date = strtotime(date("Y-m-d H:i:s",time()));
$time_difference = ceil(($actual_date - $payment_date)/60);
$user_id = $requestBodyJs["user"]["id"];

if ($time_difference > 3 || !$payment_stoken) {
  header("HTTP/1.0 400 time error");
}

global $woocommerce;
$order = new WC_Order($dev_reference);
$statusOrder = $order->get_status();

update_post_meta($order->id, '_transaction_id', $transaction_id);

if ($payment_stoken) {
  if (!in_array($payment_stoken, get_stokens($user_id, $transaction_id))) {
    header("HTTP/1.0 203 token error");
  }
}

function get_stokens($user_id, $transaction_id) {
  $webhookObj = new SG_WC_Plugin();
  $stoken_client = md5($transaction_id ."_". $webhookObj->app_code_client ."_". $user_id ."_". $webhookObj->app_key_client);
  $stoken_server = md5($transaction_id ."_". $webhookObj->app_code_server ."_". $user_id ."_". $webhookObj->app_key_server);
  return array($stoken_server, $stoken_client);
}

if (!in_array($statusOrder, ['completed', 'cancelled', 'refunded', 'processing'])) {
    $description = __("Payment Response: ", "sg_woocommerce") .
                   __(" | Status: ", "sg_woocommerce") . $status .
                   __(" | Status_detail: ", "sg_woocommerce") . $status_detail .
                   __(" | Dev_Reference: ", "sg_woocommerce") . $dev_reference .
                   __(" | Authorization_Code: ", "sg_woocommerce") . $authorization_code .
                   __(" | Transaction_Code: ", "sg_woocommerce") . $transaction_id;

    if ($status_detail == 3) {
      $comments = __("Successful Payment", "sg_woocommerce");
      $order->update_status('processing');
      $order->reduce_order_stock();
      $order->add_order_note( __('The payment has been made successfully. Transaction Code: ', 'sg_woocommerce') . $transaction_id . __(' and its Authorization Code is: ', 'sg_woocommerce') . $authorization_code);
    } elseif (in_array($status_detail, [0, 1, 31, 35, 36])) {
      $comments = __("Pending Payment", "sg_woocommerce");
      $order->update_status('on-hold');
      $order->add_order_note( __('The payment is pending.', 'sg_woocommerce') . $transaction_id . __(' the reason is: ', 'sg_woocommerce') . $payment_message);
    } elseif (in_array($status_detail, [7, 34, 21, 22, 23, 24, 25, 26, 27, 28, 29])) {
      $order->update_status('refunded');
      $order->add_order_note( __('Transaction refunded: ', 'sg_woocommerce') . $transaction_id . __(' status: ', 'sg_woocommerce') . $payment_message);
    } elseif ($status_detail == 8) {
      $description = "Chargeback";
      $comments = __("Payment Cancelled", "sg_woocommerce");
      $order->update_status('cancelled');
      $order->add_order_note( __('The payment was cancelled. Transaction Code: ', 'gp_woocommerce') . $transaction_id . __(' the reason is chargeback. ', 'sg_woocommerce'));
    } else {
      $comments = __("Failed Payment", "sg_woocommerce");
      $order->update_status('failed');
      $order->add_order_note( __('The payment has failed. Transaction Code: ', 'sg_woocommerce') . $transaction_id . __(' the reason is: ', 'sg_woocommerce') . $payment_message);
    }
    header("HTTP/1.0 200 updated");
}

SG_WC_Helper::insert_data($status, $comments, $description, $dev_reference, $transaction_id);