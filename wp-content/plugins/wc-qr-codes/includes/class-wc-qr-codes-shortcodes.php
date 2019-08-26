<?php

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WCQRCodesShortcodes {

    public static function init() {
        $shortcodes = array(
            'wc_qr_code' => __CLASS__ . '::wc_qr_code'
        );
        foreach ($shortcodes as $shortcode => $function) {
            add_shortcode(apply_filters("{$shortcode}_shortcode_tag", $shortcode), $function);
        }
    }

    public static function shortcode_wrapper($function, $atts = array()) {
        ob_start();
        call_user_func($function, $atts);
        return ob_get_clean();
    }

    public static function wc_qr_code($atts) {
        global $product;
        $default = array(
            'id' => '',
            'title' => ''
        );
        $atts = shortcode_atts($default, $atts, 'wc_qr_code');
        $product_obj = wc_get_product($atts['id']) ? wc_get_product($atts['id']) : $product;
        if($atts['title']){
            echo '<h2 class="wc-qr-codes-h2">'.$atts['title'].'</h2>';
        }
        if ($product_obj && get_wc_product_qr_code_src($product_obj->get_id())) {
            echo '<div class="wc-qr-codes-container">';
            echo '<img class="wcqrc-qr-code-img" src="' . get_wc_product_qr_code_src($product_obj->get_id()) . '" alt="QR Code" />';
            echo '</div>';
        }
    }

}
