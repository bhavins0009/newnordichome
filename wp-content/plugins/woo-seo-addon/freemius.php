<?php

if ( ! function_exists( 'premmerce_wsa_fs' ) ) {
    // Create a helper function for easy SDK access.
    function premmerce_wsa_fs() {
        global $premmerce_wsa_fs;

        if ( ! isset( $premmerce_wsa_fs ) ) {
            // Include Freemius SDK.
            require_once dirname(__FILE__) . '/freemius/start.php';

            $premmerce_wsa_fs = fs_dynamic_init( array(
                'id'                  => '1501',
                'slug'                => 'woo-seo-addon',
                'type'                => 'plugin',
                'public_key'          => 'pk_7e12c4755ce62e9391646e887499f',
                'is_premium'          => false,
                'has_addons'          => false,
                'has_paid_plans'      => false,
                'menu'                => array(
                    'slug'           => 'premmerce_seo',
                    'override_exact' => true,
                    'support'        => false,
                ),
            ) );
        }

        return $premmerce_wsa_fs;
    }

    // Init Freemius.
    premmerce_wsa_fs();
    // Signal that SDK was initiated.
    do_action( 'premmerce_wsa_fs_loaded' );

    function premmerce_wsa_fs_settings_url() {
        return admin_url( 'admin.php?page=premmerce_seo' );
    }

    premmerce_wsa_fs()->add_filter('connect_url', 'premmerce_wsa_fs_settings_url');
    premmerce_wsa_fs()->add_filter('after_skip_url', 'premmerce_wsa_fs_settings_url');
    premmerce_wsa_fs()->add_filter('after_connect_url', 'premmerce_wsa_fs_settings_url');
    premmerce_wsa_fs()->add_filter('after_pending_connect_url', 'premmerce_wsa_fs_settings_url');}
