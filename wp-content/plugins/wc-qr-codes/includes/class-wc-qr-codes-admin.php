<?php

/**
 * Admin class
 */
class WCQRCodesAdmin {

    function __construct() {
        add_action('add_meta_boxes', array($this, 'add_qr_metabox'), 30);
        add_action('edit_post', array($this, 'save_product_qr_code'), 10, 2);
        add_action('before_delete_post', array($this, 'delete_associated_qr_code'), 10, 1);
        add_action('admin_enqueue_scripts', array(&$this, 'enqueue_admin_script'), 30);
        if (!get_option('is_dismiss_wallet_pomotion_notice', false) && !WooCommerceQrCodesDependencies::is_woo_wallet_active()) {
            add_action('admin_notices', array($this, 'promote_woo_wallet'));
        }
    }

    /**
     * Add QR Code Metabox in product page
     * @global type $WooCommerceQrCodes
     */
    public function add_qr_metabox() {
        add_meta_box('qr-product-metabox', __('QR Code', 'wc-qr-codes'), array($this, 'qr_product_metabox_callback'), 'product', 'side', 'high');
    }

    /**
     * QR Code metabox callback function
     * @param object $product
     */
    public function qr_product_metabox_callback($product) {
        $is_qr_code_exist = get_post_meta($product->ID, '_is_qr_code_exist', true);
        if (!empty($is_qr_code_exist)) {
            $product_qr_code = get_post_meta($product->ID, '_product_qr_code', true);
            if (!empty($product_qr_code) && file_exists(WCQRC_QR_IMAGE_DIR . $product_qr_code)) {
                echo '<button type="button" data-product_id="' . $product->ID . '" class="wcqrc-refresh button-primary dashicons-before dashicons-update"></button>';
                echo '<div class="product-qr-code-container">';
                echo '<img class="product-qr-code-img" src="' . WCQRC_QR_IMAGE_URL . $product_qr_code . '" alt="QR Code" />';
                echo '</div>';
            } else {
                delete_post_meta($product->ID, '_is_qr_code_exist');
                delete_post_meta($product->ID, '_product_qr_code');
            }
        }
    }

    /**
     * Save generated QR code 
     * @global type $WooCommerceQrCodes
     * @param type $post_id
     */
    public function save_product_qr_code($post_id, $post) {
        global $WooCommerceQrCodes;
        if ($post->post_type == 'product') {
            $is_qr_code_exist = get_post_meta($post_id, '_is_qr_code_exist', true);
            if (empty($is_qr_code_exist)) {
                $permalink = apply_filters('wcqrc_product_permalink', get_permalink($post_id), $post_id);
                $image_name = time() . '_' . $post_id . '.png';
                $qr_size = intval($WooCommerceQrCodes->settings_api->get_option('qr_size', 'wcqrc_basics', 4));
                $qr_frame_size = intval($WooCommerceQrCodes->settings_api->get_option('qr_frame_size', 'wcqrc_basics', 2));
                $WooCommerceQrCodes->QRcode->png(esc_url($permalink), WCQRC_QR_IMAGE_DIR . $image_name, QR_ECLEVEL_M, $qr_size, $qr_frame_size);
                update_post_meta($post_id, '_is_qr_code_exist', 1);
                update_post_meta($post_id, '_product_qr_code', $image_name);
            }
        }
    }

    /**
     * Delete associated QR image
     * @param type $post_id
     */
    public function delete_associated_qr_code($post_id) {
        if (get_post_type($post_id) == 'product') {
            $is_qr_code_exist = get_post_meta($post_id, '_is_qr_code_exist', true);
            if (!empty($is_qr_code_exist)) {
                $product_qr_code = get_post_meta($post_id, '_product_qr_code', true);
                if (!empty($product_qr_code) && file_exists(WCQRC_QR_IMAGE_DIR . $product_qr_code)) {
                    unlink(WCQRC_QR_IMAGE_DIR . $product_qr_code);
                }
            }
        }
    }

    /**
     * enqueue admin sctipt
     * @global type $WooCommerceQrCodes
     */
    public function enqueue_admin_script() {
        global $WooCommerceQrCodes;
        $screen = get_current_screen();
        if ($screen->id == 'product') {
            wp_enqueue_style('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/admin/css/wcqrc-product.css', array(), $WooCommerceQrCodes->version);
            wp_enqueue_script('wcqrc-product', $WooCommerceQrCodes->plugin_url . 'assets/admin/js/wcqrc-product.js', array('jquery'), $WooCommerceQrCodes->version, true);
            wp_localize_script('wcqrc-product', 'wcqrc_product', array('ajax_url' => admin_url('admin-ajax.php')));
        }
    }

    public function promote_woo_wallet() {
        global $WooCommerceQrCodes;
        ?>
        <div class="updated" id="promote-woo-wallet">
            <div class="woo-wallet-upsell-logo">
                <img src="<?php echo $WooCommerceQrCodes->plugin_url . 'assets/admin/images/wallet-upsell-banner.png'; ?>" width="272" height="71" alt="WooWallet logo" />
            </div>
            <div class="woo-wallet-upsell-text">
                <p>Join the cashless Revolution with WooCommerce Wallet plugin, the leading wallet plugin with partial payment, refunds, cashbacks and what not!</p>
            </div>
            <div class="woo-wallet-upsell-cta">
                <a href="<?php echo admin_url('plugin-install.php?s=WooCommerce+Wallet+–+credit%2C+cashback%2C+refund+system&tab=search&type=term'); ?>" id="weforms-upsell-prompt-btn" class="button">Install Now</a>
                <a href="https://wordpress.org/plugins/woo-wallet/">Learn More</a>
            </div>
            <button type="button" class="notice-dismiss promote-woo-wallet-dismiss" style="padding: 3px;" title="Dismiss this notice.">
                <span class="screen-reader-text">Dismiss this notice.</span>
            </button>
        </div>
        <style type="text/css">
            div#promote-woo-wallet{display:flex;flex-direction:row;flex-wrap:nowrap;justify-content:flex-start;align-content:flex-start;align-items:flex-start;position:relative;border:none;margin:5px 0 15px;padding:0 0 0 10px}div#promote-woo-wallet *{box-sizing:border-box}.woo-wallet-upsell-logo{margin:0;height:71px;order:0;flex:0 1 272px;align-self:auto;padding-left:15px}.woo-wallet-upsell-text{background:#96588A;color:#fff;padding:0;height:71px;margin-left:-35px;order:0;flex:1 1 auto;align-self:auto}.woo-wallet-upsell-cta{text-align:center;order:0;flex:0 1 220px;align-self:auto;padding-top:20px;vertical-align:middle;height:71px;line-height:28px}
        </style>
        <script type="text/javascript">
            jQuery('.promote-woo-wallet-dismiss').on('click', function () {
                jQuery.post(
                        '<?php echo admin_url('admin-ajax.php'); ?>',
                        {
                            action: 'woo_wallet_dismiss_pomotion_notice'
                        },
                        function (response) {
                            jQuery('#promote-woo-wallet').hide();
                        }
                );

            });
        </script>
        <?php
    }

}
