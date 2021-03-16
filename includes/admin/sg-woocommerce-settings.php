<?php

return array (
    'staging' => array(
        'title' => __( 'Staging Environment', 'sg_woocommerce' ),
        'type' => 'checkbox',
        'label' => __( 'Use staging environment in ', 'sg_woocommerce' ).SG_FLAVOR.'.',
        'default' => 'yes'
    ),
    'enable_ltp' => array(
        'title' => __( 'Enable LinkToPay', 'sg_woocommerce' ),
        'type' => 'checkbox',
        'label' => __( 'If selected, LinkToPay(Bank transfer, cash) can be used to pay.', 'sg_woocommerce' ),
        'default' => 'no'
    ),
    'title' => array(
        'title' => __( 'Title', 'sg_woocommerce' ),
        'type' => 'text',
        'description' => __( 'This controls the title which the user sees during checkout page.', 'sg_woocommerce' ),
        'default' => SG_FLAVOR.' Gateway',
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __( 'Customer Message', 'sg_woocommerce' ),
        'type' => 'textarea',
        'description' => __( 'This controls the message which the user sees during checkout page.', 'sg_woocommerce' ),
        'default' => SG_FLAVOR.__(' is a complete solution for online payments. Safe, easy and fast.', 'sg_woocommerce
        ')
    ),
    'checkout_language' => array(
      'title' => __('Checkout Language', 'sg_woocommerce'),
      'type' => 'select',
      'default' => 'en',
      'options' => array(
        'en' => 'EN',
        'es' => 'ES',
        'pt' => 'PT',
      ),
      'description' => __('User\'s preferred language for checkout window. English will be used by default.', 'sg_woocommerce')
    ),
    'installments_type' => array(
      'title' => __('Installments Type', 'sg_woocommerce'),
      'type' => 'select',
      'default' => -1,
      'options' => array(
        -1 => __('Disabled', 'sg_woocommerce'),
        0  => __('Enabled', 'sg_woocommerce'),
      ),
      'description' => __('Select the installments type that will be enabled on the payment screen (Only on card payment).', 'sg_woocommerce')
    ),
    'app_code_client' => array(
      'title' => __('App Code Client', 'sg_woocommerce'),
      'type' => 'text',
      'description' => __('Unique commerce identifier in ', 'sg_woocommerce').SG_FLAVOR.'.'
    ),
    'app_key_client' => array(
      'title' => __('App Key Client', 'sg_woocommerce'),
      'type' => 'text',
      'description' => __('Key used to encrypt communication with ', 'sg_woocommerce').SG_FLAVOR.'.'
    ),
    'app_code_server' => array(
      'title' => __('App Code Server', 'sg_woocommerce'),
      'type' => 'text',
      'description' => __('Unique commerce identifier to perform admin actions on ', 'sg_woocommerce').SG_FLAVOR.'.'
    ),
    'app_key_server' => array(
      'title' => __('App Key Server', 'sg_woocommerce'),
      'type' => 'text',
      'description' => __('Key used to encrypt admin communication with ', 'sg_woocommerce').SG_FLAVOR.'.'
    )
  );
