<?php

return array (
    'staging' => array(
        'title' => __( 'Staging Enviroment', 'sg_woocommerce' ),
        'type' => 'checkbox',
        'label' => __( 'Use the Afirme ShieldGate Gateway staging environment.', 'sg_woocommerce' ),
        'default' => 'yes'
    ),
    'title' => array(
        'title' => __( 'Title', 'sg_woocommerce' ),
        'type' => 'text',
        'description' => __( 'This controls the title which the user sees during checkout.', 'sg_woocommerce' ),
        'default' => __( 'Afirme ShieldGate Gateway', 'sg_woocommerce' ),
        'desc_tip' => true,
    ),
    'description' => array(
        'title' => __( 'Customer Message', 'sg_woocommerce' ),
        'type' => 'textarea',
        'default' => __('Afirme ShieldGate is a complete solution for online payments. Safe, easy and fast.', 'sg_woocommerce
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
    'app_code_client' => array(
    'title' => __('App Code Client', 'sg_woocommerce'),
    'type' => 'text',
    'description' => __('Unique identifier in Afirme ShieldGate.', 'sg_woocommerce')
    ),
    'app_key_client' => array(
        'title' => __('App Key Client', 'sg_woocommerce'),
        'type' => 'text',
        'description' => __('Key used to encrypt communication with Afirme ShieldGate.', 'sg_woocommerce')
    ),
    'app_code_server' => array(
        'title' => __('App Code Server', 'sg_woocommerce'),
        'type' => 'text',
        'description' => __('Unique identifier in Afirme ShieldGate Server.', 'sg_woocommerce')
    ),
    'app_key_server' => array(
        'title' => __('App Key Server', 'sg_woocommerce'),
        'type' => 'text',
        'description' => __('Key used for reverse communication with Afirme ShieldGate Server.', 'sg_woocommerce')
    )
  );
