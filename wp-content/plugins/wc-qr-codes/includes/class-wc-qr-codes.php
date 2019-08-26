<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

/**
 * 
 */
class WCQRCodes {

    public $admin;
    public $frontend;
    public $ajax;
    public $settings_api;
    public $settings;

    function __construct() {
        /*
         * Recheck folder exist
         * Issue resolved for mutisite 
         */
        if(!file_exists(WCQRC_QR_IMAGE_DIR) || !file_exists(WCQRC_QR_IMAGE_DIR . 'index.html' )){
            WCQRCodesInstall::create_files();
        }
        add_action('init', array($this, 'bootstrap_woocommerce_qr_codes'));
    }

    public function bootstrap_woocommerce_qr_codes() {
        global $WooCommerceQrCodes;
        if (is_admin()) {
            require_once('class-wc-qr-codes-admin.php');
            $this->admin = new WCQRCodesAdmin();
            require_once('class-wc-qr-codes-settings.php');
            $this->settings = new WCQRC_Settings($WooCommerceQrCodes->settings_api);
        }
        if (!is_admin() || defined('DOING_AJAX')) {
            require_once('class-wc-qr-codes-frontend.php');
            $this->frontend = new WCQRCodesFrentend();
            require_once('class-wc-qr-codes-shortcodes.php');
            WCQRCodesShortcodes::init();
        }

        if (defined('DOING_AJAX')) {
            require_once('class-wc-qr-codes-ajax.php');
            $this->ajax = new WCQRCodesAjax();
        }
    }

}
