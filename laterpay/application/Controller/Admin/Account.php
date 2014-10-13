<?php

class LaterPay_Controller_Admin_Account extends LaterPay_Controller_Abstract
{

    /**
     * @see LaterPay_Controller_Abstract::load_assets
     */
    public function load_assets() {
        parent::load_assets();

        // load page-specific JS
        wp_register_script(
            'laterpay-backend-account',
            $this->config->js_url . 'laterpay-backend-account.js',
            array( 'jquery' ),
            $this->config->version,
            true
        );
        wp_enqueue_script( 'laterpay-backend-account' );

        // pass localized strings and variables to script
        wp_localize_script(
            'laterpay-backend-account',
            'lpVars',
            array(
                'i18nApiKeyInvalid'         => __( 'The API key you entered is not a valid LaterPay API key!', 'laterpay' ),
                'i18nMerchantIdInvalid'     => __( 'The Merchant ID you entered is not a valid LaterPay Merchant ID!', 'laterpay' ),
                'i18nPreventUnload'         => __( 'LaterPay does not work properly with invalid API credentials.', 'laterpay' ),
            )
        );
    }

    /**
     * @see LaterPay_Controller_Abstract::render_page
     */
    public function render_page() {
        $this->load_assets();

        $this->assign( 'sandbox_merchant_id',    get_option( 'laterpay_sandbox_merchant_id' ) );
        $this->assign( 'sandbox_api_key',        get_option( 'laterpay_sandbox_api_key' ) );
        $this->assign( 'live_merchant_id',       get_option( 'laterpay_live_merchant_id' ) );
        $this->assign( 'live_api_key',           get_option( 'laterpay_live_api_key' ) );
        $this->assign( 'plugin_is_in_live_mode', $this->config->get( 'is_in_live_mode' ) );
        $this->assign( 'top_nav',                $this->get_menu() );
        $this->assign( 'admin_menu',             LaterPay_Helper_View::get_admin_menu() );

        $this->render( 'backend/account' );
    }

    /**
     * Process Ajax requests from account tab.
     *
     * @return void
     */
    public static function process_ajax_requests() {
        if ( isset( $_POST['form'] ) ) {
            // check for required capabilities to perform action
            if ( ! current_user_can( 'activate_plugins' ) ) {
                wp_send_json(
                    array(
                        'success' => false,
                        'message' => __( "You don't have sufficient user capabilities to do this.", 'laterpay' )
                    )
                );
            }
            if ( function_exists( 'check_admin_referer' ) ) {
                check_admin_referer( 'laterpay_form' );
            }

            switch ( $_POST['form'] ) {
                case 'laterpay_sandbox_merchant_id':
                    self::_update_merchant_id();
                    break;

                case 'laterpay_sandbox_api_key':
                    self::_update_api_key();
                    break;

                case 'laterpay_live_merchant_id':
                    self::_update_merchant_id( true );
                    break;

                case 'laterpay_live_api_key':
                    self::_update_api_key( true );
                    break;

                case 'laterpay_plugin_mode':
                    self::_update_plugin_mode();
                    break;

                default:
                    wp_send_json(
                        array(
                            'success' => false,
                            'message' => __( 'An error occurred when trying to save your settings. Please try again.', 'laterpay' )
                        )
                    );

                    die;
            }
        }
    }

    /**
     * Update LaterPay Merchant ID, required for making test transactions against Sandbox or Live environments.
     *
     * @param null $is_live
     *
     * @return void
     */
    protected static function _update_merchant_id( $is_live = null ) {

        $merchant_id_form = new LaterPay_Form_MerchantId( $_POST );

        $merchant_id        = $merchant_id_form->get_field_value( 'merchant_id' );
        $merchant_id_type   = $is_live ? 'live' : 'sandbox';

        if ( $merchant_id_form->is_valid() ) {
            update_option( sprintf( 'laterpay_%s_merchant_id', $merchant_id_type ), $merchant_id );
            wp_send_json(
                array(
                    'success' => true,
                    'message' => __( ucfirst( $merchant_id_type ) . ' Merchant ID verified and saved.', 'laterpay' ),
                )
            );
        } elseif ( strlen( $merchant_id ) == 0 ) {
            update_option( sprintf( 'laterpay_%s_merchant_id', $merchant_id_type ), '' );
            wp_send_json(
                array(
                    'success' => true,
                    'message' => __( sprintf( 'The %s Merchant ID has been removed.',ucfirst( $merchant_id_type ) ), 'laterpay' ),
                )
            );
        } else {
            wp_send_json(
                array(
                    'success' => false,
                    'message' => __( sprintf( 'The Merchant ID you entered is not a valid LaterPay %s Merchant ID!',ucfirst( $merchant_id_type ) ), 'laterpay' ),
                )
            );
        }

        die;
    }

    /**
     * Update LaterPay API Key, required for making test transactions against Sandbox or Live environments.
     *
     * @param null $is_live
     *
     * @return void
     */
    protected static function _update_api_key( $is_live = null ) {
        $api_key_form = new LaterPay_Form_ApiKey( $_POST );

        $api_key            = $api_key_form->get_field_value( 'api_key' );
        $api_key_type       = $is_live ? 'live' : 'sandbox';
        $transaction_type   = $is_live ? 'REAL' : 'TEST';

        if ( $api_key_form->is_valid() ) {
            update_option( sprintf( 'laterpay_%s_api_key', $api_key_type ), $api_key );
            wp_send_json(
                array(
                    'success' => true,
                    'message' => __( sprintf( 'Your %s API key is valid. You can now make %s transactions.' , ucfirst( $api_key_type ), $transaction_type ), 'laterpay' )
                )
            );
        } elseif ( strlen( $api_key ) == 0 ) {
            update_option( sprintf( 'laterpay_%s_api_key', $api_key_type ), '' );
            wp_send_json(
                array(
                    'success' => true,
                    'message' => __( sprintf( 'The %s API key has been removed.', ucfirst( $api_key_type ) ), 'laterpay' )
                )
            );
        } else {
            wp_send_json(
                array(
                    'success' => false,
                    'message' => __( sprintf( 'The API key you entered is not a valid LaterPay %s API key!', ucfirst( $api_key_type ) ), 'laterpay' )
                )
            );
        }

        die;
    }

    /**
     * Update LaterPay plugin mode (test or live).
     *
     * @return void
     */
    protected static function _update_plugin_mode() {

        $plugin_mode_form = new LaterPay_Form_PluginMode();

        if ( ! $plugin_mode_form->is_valid( $_POST ) ) {
            wp_send_json(
                array(
                    'success' => false,
                    'message' => __( 'Error occurred. Incorrect data provided.', 'laterpay' )
                )
            );
        }

        $plugin_mode    = $plugin_mode_form->get_field_value( 'plugin_is_in_live_mode' );
        $result         = update_option( 'laterpay_plugin_is_in_live_mode', $plugin_mode );

        if ( $result ) {
            if ( get_option( 'laterpay_plugin_is_in_live_mode' ) ) {
                wp_send_json(
                    array(
                        'success'   => true,
                        'mode'      => 'live',
                        'message'   => __( 'The LaterPay plugin is in LIVE mode now. All payments are actually booked and credited to your account.', 'laterpay' ),
                    )
                );
            } else {
                wp_send_json(
                    array(
                        'success'   => true,
                        'mode'      => 'test',
                        'message'   => __( 'The LaterPay plugin is in TEST mode now. Payments are only simulated and not actually booked.', 'laterpay' ),
                    )
                );
            }
        } else {
            wp_send_json(
                array(
                    'success'   => false,
                    'mode'      => 'test',
                    'message'   => __( 'The LaterPay plugin needs valid API credentials to work.', 'laterpay' ),
                )
            );
        }

        die;
    }

}
