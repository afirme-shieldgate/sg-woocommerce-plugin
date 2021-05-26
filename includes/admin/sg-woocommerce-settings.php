<?php

return array (
    'staging' => array(
        'title' => __( 'Staging Environment', 'sg_woocommerce' ),
        'type' => 'checkbox',
        'label' => __( 'Use staging environment in ', 'sg_woocommerce' ).SG_FLAVOR.'.',
        'default' => 'yes'
    ),
    'enable_card' => array(
        'title' => __( 'Enable Card Payment', 'sg_woocommerce' ),
        'type' => 'checkbox',
        'label' => __( 'If selected, card payment can be used to pay.', 'sg_woocommerce' ),
        'default' => 'no'
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
    'card_button_text' => array(
        'title' => __( 'Card Button Text', 'sg_woocommerce' ),
        'type' => 'text',
        'description' => __( 'This controls the text that the user sees in the card payment button.', 'sg_woocommerce' ),
        'default' => __('Pay With Card', 'sg_woocommerce'),
        'desc_tip' => true,
    ),
    'ltp_button_text' => array(
        'title' => __( 'LinkToPay Button Text', 'sg_woocommerce' ),
        'type' => 'text',
        'description' => __( 'This controls the text that the user sees in the LinkToPay button.', 'sg_woocommerce' ),
        'default' =>  __( 'Pay with Cash/Bank Transfer', 'sg_woocommerce' ),
        'desc_tip' => true,
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
    'enable_installments' => array(
        'title' => __('Enable Installments', 'sg_woocommerce'),
        'type' => 'checkbox',
        'default' => 'no',
        'label' => __('If selected, the installments options will be showed on the payment screen (Only on card payment).', 'sg_woocommerce')
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
