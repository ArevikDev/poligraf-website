<?php

/**
 * ajax class
 *
 * @author bappa
 */
class WCQRCodesAjax {

    /**
     * construct
     */
    function __construct() {
        add_action('wp_ajax_regenerate_qr_code', array($this, 'ajax_regenerate_qr_code'));
        add_action('wp_ajax_regenerate_all_qr_code', array($this, 'ajax_regenerate_all_qr_code'));
        add_action('wp_ajax_woo_wallet_dismiss_pomotion_notice', array($this, 'woo_wallet_dismiss_pomotion_notice'));
        add_action('wp_ajax_wp_count_products', array($this, 'wp_count_products'));
    }

    /**
     * regenerate product qr code
     */
    public function ajax_regenerate_qr_code() {
        $post_id = isset($_POST['product_id']) ? $_POST['product_id'] : 0;
        echo regenerate_qr_code($post_id);
        wp_die();
    }

    public function ajax_regenerate_all_qr_code() {
        $offset = isset($_POST['offset']) ? $_POST['offset'] : 0;
        $args = array(
            'posts_per_page'   => 5,
            'offset'           => $offset,
            'post_type'        => 'product',
            'post_status'      => 'publish',
            'suppress_filters' => true
        );
        $products_array = get_posts($args);
        $response = array();
        foreach ($products_array as $product){
            $response[] = regenerate_qr_code($product->ID);
        }
        wp_send_json($response);
    }
    
    public function woo_wallet_dismiss_pomotion_notice(){
        update_option('is_dismiss_wallet_pomotion_notice', true);
        wp_die();
    }
    
    public function wp_count_products(){
        $counts = wp_count_posts('product');
        wp_send_json($counts);
    }

}
