<?php
/**
* Plugin Name: Woocommerce Events Manager
* Plugin URI: http://mage-people.com
* Description: A Complete Event Solution for WordPress by MagePeople..
* Version: 2.6.5
* Author: MagePeople Team
* Author URI: http://www.mage-people.com/
* Text Domain: mage-eventpress
* Domain Path: /languages/
*/

include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
if ( is_plugin_active( 'woocommerce/woocommerce.php' ) ) {

if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
require_once(dirname(__FILE__) . "/inc/class/mep_settings_api.php");
require_once(dirname(__FILE__) . "/inc/mep_cpt.php");
require_once(dirname(__FILE__) . "/inc/mep_tax.php");
require_once(dirname(__FILE__) . "/inc/mep_event_meta.php");
require_once(dirname(__FILE__) . "/inc/mep_extra_price.php");
require_once(dirname(__FILE__) . "/inc/mep_shortcode.php");
require_once(dirname(__FILE__) . "/inc/admin_setting_panel.php");
require_once(dirname(__FILE__) . "/inc/mep_enque.php");
require_once(dirname(__FILE__) . "/inc/mep_csv_export.php");
require_once(dirname(__FILE__) . "/inc/mep_user_custom_style.php");
require_once(dirname(__FILE__) . "/inc/mep_tax_meta.php");
require_once(dirname(__FILE__) . "/inc/mep_addon_list.php");

// Language Load
add_action( 'init', 'mep_language_load');
function mep_language_load(){
    $plugin_dir = basename(dirname(__FILE__))."/languages/";
    load_plugin_textdomain( 'mage-eventpress', false, $plugin_dir );
}

// Flash Permalink only Once 

function mep_flash_permalink_once() {
    if ( get_option( 'mep_flash_event_permalink' ) != 'completed' ) {
         global $wp_rewrite;
         $wp_rewrite->flush_rules();
         update_option( 'mep_flash_event_permalink', 'completed' );
    }
}
add_action( 'admin_init', 'mep_flash_permalink_once' );

// Class for Linking with Woocommerce with Event Pricing 
add_action('plugins_loaded', 'mep_load_wc_class');
function mep_load_wc_class() {

  if ( class_exists('WC_Product_Data_Store_CPT') ) {

   class MEP_Product_Data_Store_CPT extends WC_Product_Data_Store_CPT {

    public function read( &$product ) {

        $product->set_defaults();

        if ( ! $product->get_id() || ! ( $post_object = get_post( $product->get_id() ) ) || ! in_array( $post_object->post_type, array( 'mep_events', 'product' ) ) ) { // change birds with your post type
            throw new Exception( __( 'Invalid product.', 'woocommerce' ) );
        }

        $id = $product->get_id();

        $product->set_props( array(
            'name'              => $post_object->post_title,
            'slug'              => $post_object->post_name,
            'date_created'      => 0 < $post_object->post_date_gmt ? wc_string_to_timestamp( $post_object->post_date_gmt ) : null,
            'date_modified'     => 0 < $post_object->post_modified_gmt ? wc_string_to_timestamp( $post_object->post_modified_gmt ) : null,
            'status'            => $post_object->post_status,
            'description'       => $post_object->post_content,
            'short_description' => $post_object->post_excerpt,
            'parent_id'         => $post_object->post_parent,
            'menu_order'        => $post_object->menu_order,
            'reviews_allowed'   => 'open' === $post_object->comment_status,
        ) );

        $this->read_attributes( $product );
        $this->read_downloads( $product );
        $this->read_visibility( $product );
        $this->read_product_data( $product );
        $this->read_extra_data( $product );
        $product->set_object_read( true );
    }

    /**
     * Get the product type based on product ID.
     *
     * @since 3.0.0
     * @param int $product_id
     * @return bool|string
     */
    public function get_product_type( $product_id ) {
        $post_type = get_post_type( $product_id );
        if ( 'product_variation' === $post_type ) {
            return 'variation';
        } elseif ( in_array( $post_type, array( 'mep_events', 'product' ) ) ) { // change birds with your post type
            $terms = get_the_terms( $product_id, 'product_type' );
            return ! empty( $terms ) ? sanitize_title( current( $terms )->name ) : 'simple';
        } else {
            return false;
        }
    }
}



function mep_get_order_info($info,$id){
  if($info){
  $stock_msg  = $info;
  $koba = explode("_", $stock_msg);
  return $koba[$id];
}else{
  return null;
}
}



add_filter( 'woocommerce_data_stores', 'mep_woocommerce_data_stores' );
function mep_woocommerce_data_stores ( $stores ) {     
      $stores['product'] = 'MEP_Product_Data_Store_CPT';
      return $stores;
  }

  } else {

    add_action('admin_notices', 'wc_not_loaded');
  }

function wc_not_loaded() {
    printf(
      '<div class="error" style="background:red; color:#fff;"><p>%s</p></div>',
      __('You Must Install WooCommerce Plugin before activating WooCommerce Event Manager, Becuase It is dependent on Woocommerce Plugin')
    );
}

add_action('woocommerce_before_checkout_form', 'mep_displays_cart_products_feature_image');

function mep_displays_cart_products_feature_image() {
    foreach ( WC()->cart->get_cart() as $cart_item ) {
        $item = $cart_item['data'];
    }
}


// Send Confirmation email to customer
function mep_event_confirmation_email_sent($event_id,$sent_email){
$values = get_post_custom($event_id);

$global_email_text = mep_get_option( 'mep_confirmation_email_text', 'email_setting_sec', '');
$global_email_form_email = mep_get_option( 'mep_email_form_email', 'email_setting_sec', '');
$global_email_form = mep_get_option( 'mep_email_form_name', 'email_setting_sec', '');
$global_email_sub = mep_get_option( 'mep_email_subject', 'email_setting_sec', '');
$event_email_text = $values['mep_event_cc_email_text'][0];
$admin_email = get_option( 'admin_email' );
$site_name = get_option( 'blogname' );


  if($global_email_sub){
    $email_sub = $global_email_sub;
  }else{
    $email_sub = 'Confirmation Email';
  }

  if($global_email_form){
    $form_name = $global_email_form;
  }else{
    $form_name = $site_name;
  }

  if($global_email_form_email){
    $form_email = $global_email_form_email;
  }else{
    $form_email = $admin_email;
  }

  if($event_email_text){
    $email_body = $event_email_text;
  }else{
    $email_body = $global_email_text;
  }

  $headers[] = "From: $form_name <$form_email>";

  if($email_body){
  $sent = wp_mail( $sent_email, $email_sub, $email_body, $headers );
  }
}

}

function mep_event_get_order_meta($item_id,$key){
global $wpdb;
  $table_name = $wpdb->prefix."woocommerce_order_itemmeta";
  $sql = 'SELECT meta_value FROM '.$table_name.' WHERE order_item_id ='.$item_id.' AND meta_key="'.$key.'"';
  $results = $wpdb->get_results($sql); //or die(mysql_error());
  foreach( $results as $result ) {
     $value = $result->meta_value;
  }
  return $value;
}





add_action( 'woocommerce_thankyou','mep_set_first_order_sts');
function mep_set_first_order_sts($order_id ){

   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $item_id = $item_id;
    }
$event_id           = mep_event_get_order_meta($item_id,'event_id');
$mep_atnd           = "_mep_atnd_".$order_id;
update_post_meta( $event_id, $mep_atnd, "a1");
}




add_action('woocommerce_checkout_order_processed', 'mep_event_order_status_make_pending', 10, 1);

function mep_event_order_status_make_pending($order_id)
{
   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $item_quantity = $item_values->get_quantity();
        $item_id = $item_id;
    }
$ordr_total         = $order->get_total();
$event_id           = mep_event_get_order_meta($item_id,'event_id');
if (get_post_type($event_id) == 'mep_events') { 
    $order_meta_text  = "_stock_msg_".$order_id;
    $order_processing = "pending_".$order_id;

    update_post_meta( $event_id, $order_meta_text, $order_processing);
}
}



// add_action('woocommerce_order_status_changed', 'your_function', 10, 4);
add_action('woocommerce_order_status_changed', 'mep_event_seat_management', 10, 4);
function mep_event_seat_management( $order_id, $from_status, $to_status, $order ) {
global $wpdb;


   // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

    $c = 1;
   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $item_quantity = $item_values->get_quantity();
        $item_id = $item_id;
    }
$ordr_total         = $order->get_total();
$event_id           = mep_event_get_order_meta($item_id,'event_id');

if (get_post_type($event_id) == 'mep_events') { 


$table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_item_id=$item_id" );


    $mep_total    = get_post_meta($event_id,'total_booking', true);
    if($mep_total){
      $mep_total_booking = $mep_total;
    }else{
      $mep_total_booking =0;
    }
    

    $email            = $order_meta['_billing_email'][0];
    $order_meta_text  = "_stock_msg_".$order_id;
    $order_processing = "processing_".$order_id;
    $order_completed  = "completed_".$order_id;
    $order_cancelled  = "cancelled_".$order_id;
    $mep_atnd         = "_mep_atnd_".$order_id;


// if($order->has_status( 'processing' ) || $order->has_status( 'pending' )) {
if($order->has_status( 'processing' ) || $order->has_status( 'pending' )) {
// update_post_meta( $event_id, $mep_atnd, "a2");

$mep_stock_msgc = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
$mep_stock_orderc = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);

if($mep_stock_orderc==$order_id){
      if($mep_stock_msgc=='cancelled'){

        foreach ( $result as $page ){
          if (strpos($page->meta_key, '_') !== 0) {

             $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));

             $order_option_qty = mep_get_string_part($page->meta_key,1);
             $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
          $ntes = ($tes+$order_option_qty);
          update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
           }
        }
      }
    }

    update_post_meta( $event_id, $order_meta_text, $order_processing);
    
    $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
    $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);


if($mep_stock_order==$order_id){
      if($mep_stock_msg=='completed'){
          update_post_meta( $event_id, $order_meta_text, $order_processing);
      }
      else{
          update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          update_post_meta( $event_id, $order_meta_text, $order_processing);

      }
    } 
}


if($order->has_status( 'cancelled' )) {
  update_post_meta( $event_id,$mep_atnd, "a2");
  update_post_meta( $event_id, $order_meta_text, $order_cancelled);
  $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
  $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);


    if($mep_stock_order==$order_id){        
        $update_total_booking    = update_post_meta( $event_id, 'total_booking', ($mep_total_booking-$item_quantity));

    foreach ( $result as $page ){
      if (strpos($page->meta_key, '_') !== 0) {
       $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));
       $order_option_qty = mep_get_string_part($page->meta_key,1);
       $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
    $ntes = ($tes-$order_option_qty);
    if($tes>0){
    update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
  }
     }
    }
    }

}







if( $order->has_status( 'completed' )) {
update_post_meta( $event_id, $mep_atnd, "a2");
      // update_post_meta( $event_id, $order_meta_text, $order_completed);
      $mep_stock_msg = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),0);
      $mep_stock_order = mep_get_order_info(get_post_meta($event_id,$order_meta_text, true),1);
      mep_event_confirmation_email_sent($event_id,$email);
          if($ordr_total==0){
            update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          } 

    if($mep_stock_order==$order_id){

      if($mep_stock_msg=='processing'){
          update_post_meta( $event_id, $order_meta_text, $order_completed);
      }elseif($mep_stock_msg=='pending'){

      if($ordr_total>0){
          update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          update_post_meta( $event_id, $order_meta_text, $order_completed);
          
          foreach ( $result as $page ){
          if (strpos($page->meta_key, '_') !== 0) {
           $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));
           $order_option_qty = mep_get_string_part($page->meta_key,1);
           $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
        $ntes = ($tes+$order_option_qty);
        update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
         }
        }
      }
      }
      else{

          // update_post_meta( $event_id, 'total_booking', ($mep_total_booking+$item_quantity));
          update_post_meta( $event_id, $order_meta_text, $order_completed);
          
          foreach ( $result as $page ){
          if (strpos($page->meta_key, '_') !== 0) {
           $order_option_name = $event_id.str_replace(' ', '', mep_get_string_part($page->meta_key,0));
           $order_option_qty = mep_get_string_part($page->meta_key,1);
           $tes = get_post_meta($event_id,"mep_xtra_$order_option_name",true);
        $ntes = ($tes+$order_option_qty);
        update_post_meta( $event_id, "mep_xtra_$order_option_name",$ntes);
         }
        }
      }

    }
  }
}
}



add_action('restrict_manage_posts', 'mep_filter_post_type_by_taxonomy');
function mep_filter_post_type_by_taxonomy() {
  global $typenow;
  $post_type = 'mep_events'; // change to your post type
  $taxonomy  = 'mep_cat'; // change to your taxonomy
  if ($typenow == $post_type) {
    $selected      = isset($_GET[$taxonomy]) ? $_GET[$taxonomy] : '';
    $info_taxonomy = get_taxonomy($taxonomy);
    wp_dropdown_categories(array(
      'show_option_all' => __("Show All {$info_taxonomy->label}"),
      'taxonomy'        => $taxonomy,
      'name'            => $taxonomy,
      'orderby'         => 'name',
      'selected'        => $selected,
      'show_count'      => true,
      'hide_empty'      => true,
    ));
  };
}




add_filter('parse_query', 'mep_convert_id_to_term_in_query');
function mep_convert_id_to_term_in_query($query) {
  global $pagenow;
  $post_type = 'mep_events'; // change to your post type
  $taxonomy  = 'mep_cat'; // change to your taxonomy
  $q_vars    = &$query->query_vars;

  if ( $pagenow == 'edit.php' && isset($q_vars['post_type']) && $q_vars['post_type'] == $post_type && isset($q_vars[$taxonomy]) && is_numeric($q_vars[$taxonomy]) && $q_vars[$taxonomy] != 0 ) {
    $term = get_term_by('id', $q_vars[$taxonomy], $taxonomy);
    $q_vars[$taxonomy] = $term->slug;
  }

}



add_filter('parse_query', 'mep_attendee_filter_query');
function mep_attendee_filter_query($query) {
  global $pagenow;
  $post_type = 'mep_events_attendees'; 
  $q_vars    = &$query->query_vars;

  if ( $pagenow == 'edit.php' && isset($_GET['post_type']) && $_GET['post_type'] == $post_type && isset($_GET['meta_value']) && $_GET['meta_value'] != 0) {

    $q_vars['meta_key'] = 'ea_event_id';
    $q_vars['meta_value'] = $_GET['meta_value'];

  }
}










// Add the data to the custom columns for the book post type:
add_action( 'manage_mep_events_posts_custom_column' , 'mep_custom_event_column', 10, 2 );
function mep_custom_event_column( $column, $post_id ) {
switch ( $column ) {
case 'mep_status' :          
$values = get_post_custom( $post_id );        
echo mep_get_event_status($values['mep_event_start_date'][0]);
 break;

        case 'mep_atten' :
            echo '<a class="button button-primary button-large" href="'.get_site_url().'/wp-admin/edit.php?post_type=mep_events_attendees&meta_value='.$post_id.'">Attendees List</a>'; 
            break;
    }
}


// Getting event exprie date & time
function mep_get_event_status($startdatetime){

  $time = strtotime($startdatetime);
  $newformat = date('Y-m-d H:i:s',$time);
  $datetime1 = new DateTime();
  $datetime2 = new DateTime($newformat);
  $interval = $datetime1->diff($datetime2);
// print_r($newformat);
  if(time() > strtotime($newformat)){
    return "<span class=err>Expired</span>";
  }
  else{
  $days = $interval->days;
  $hours = $interval->h;
  $minutes = $interval->i;
  if($days>0){ $dd = $days." days "; }else{ $dd=""; }
  if($hours>0){ $hh = $hours." hours "; }else{ $hh=""; }
  if($minutes>0){ $mm = $minutes." minutes "; }else{ $mm=""; }
   return "<span class='active'>$dd $hh $mm</span>";
  }
}





// Redirect to Checkout after successfuly event registration
add_filter ('woocommerce_add_to_cart_redirect', 'mep_event_redirect_to_checkout');
function mep_event_redirect_to_checkout() {
    global $woocommerce;
    $redirect_status = mep_get_option( 'mep_event_direct_checkout', 'general_setting_sec', 'yes' );
    if($redirect_status=='yes'){
    $checkout_url = wc_get_checkout_url();
    return $checkout_url;
  }
}

add_action('init','mep_include_template_parts');
function mep_include_template_parts(){
          $template_name = 'templating.php';
          $template_path = get_stylesheet_directory().'/mage-events/template-prts/';
        if(file_exists($template_path . $template_name)) {
          require_once(get_stylesheet_directory() . "/mage-events/template-prts/templating.php");
            }else{
          require_once(dirname(__FILE__) . "/templates/template-prts/templating.php");
            }
}


function mep_load_events_templates($template) {
    global $post;
  if ($post->post_type == "mep_events"){
          $template_name = 'single-events.php';
          $template_path = 'mage-events/';
          $default_path = plugin_dir_path( __FILE__ ) . 'templates/'; 
          $template = locate_template( array($template_path . $template_name) );

          
        if ( ! $template ) :
          $template = $default_path . $template_name;
        endif;
    return $template;
  }

    if ($post->post_type == "mep_events_attendees"){
        $plugin_path = plugin_dir_path( __FILE__ );
        $template_name = 'templates/single-mep_events_attendees.php';
        if($template === get_stylesheet_directory() . '/' . $template_name
            || !file_exists($plugin_path . $template_name)) {
            return $template;
        }
        return $plugin_path . $template_name;
    }

    return $template;
}
add_filter('single_template', 'mep_load_events_templates');





add_filter('template_include', 'mep_organizer_set_template');
function mep_organizer_set_template( $template ){

      $cat_template_name = 'taxonomy-category.php';
      $org_template_name = 'taxonomy-organozer.php';
      $template_path = get_stylesheet_directory().'/mage-events/';
      
    if( is_tax('mep_org')){
        
      if(file_exists($template_path . $org_template_name)) {
        $template = get_stylesheet_directory().'/mage-events/taxonomy-organozer.php';
        }else{
        $template = plugin_dir_path( __FILE__ ).'templates/taxonomy-organozer.php';;
        }
    }

    if( is_tax('mep_cat')){
        
        if(file_exists($template_path . $cat_template_name)) {
        $template = get_stylesheet_directory().'/mage-events/taxonomy-category.php';
        }else{
        $template = plugin_dir_path( __FILE__ ).'templates/taxonomy-category.php';
        }
    }    

    return $template;
}



function mep_social_share(){
?>
<ul class='mep-social-share'>
       <li> <a data-toggle="tooltip" title="" class="facebook" onclick="window.open('https://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>','Facebook','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://www.facebook.com/sharer.php?u=<?php the_permalink(); ?>" data-original-title="Share on Facebook"><i class="fa fa-facebook"></i></a></li>

        <!-- <li><a data-toggle="tooltip" title="" class="gpuls" onclick="window.open('https://plus.google.com/share?url=<?php the_permalink(); ?>','Google plus','width=585,height=666,left='+(screen.availWidth/2-292)+',top='+(screen.availHeight/2-333)+''); return false;" href="https://plus.google.com/share?url=<?php the_permalink(); ?>" data-original-title="Share on Google Plus"><i class="fa fa-google-plus"></i></a> </li>  -->                 

        <li><a data-toggle="tooltip" title="" class="twitter" onclick="window.open('https://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?>','Twitter share','width=600,height=300,left='+(screen.availWidth/2-300)+',top='+(screen.availHeight/2-150)+''); return false;" href="http://twitter.com/share?url=<?php the_permalink(); ?>&amp;text=<?php the_title(); ?>" data-original-title="Twittet it"><i class="fa fa-twitter"></i></a></li>
        </ul>
<?php
}

function mep_calender_date($datetime){
  $time       = strtotime($datetime);
  $newdate    = date('Ymd',$time);
  $newtime    = date('Hi',$time);
  $newformat  = $newdate."T".$newtime."00";
return $newformat;
}



function mep_add_to_google_calender_link($pid){
  $event        = get_post($pid);
  $event_meta   = get_post_custom($pid);
  $event_start  = $event_meta['mep_event_start_date'][0];
  $event_end    = $event_meta['mep_event_end_date'][0];

$location = $event_meta['mep_location_venue'][0]." ".$event_meta['mep_street'][0]." ".$event_meta['mep_city'][0]." ".$event_meta['mep_state'][0]." ".$event_meta['mep_postcode'][0]." ".$event_meta['mep_country'][0];


  ob_start();



?>

<div id="mep_add_calender_button" class='mep-add-calender'><i class="fa fa-calendar"></i><?php _e(mep_get_label($pid,'mep_calender_btn_text','Add Calendar'),'mage-eventpress'); ?></div>

<ul id="mep_add_calender_links">

<li><a href="http://www.google.com/calendar/event?
action=TEMPLATE&text=<?php echo $event->post_title; ?>&dates=<?php echo mep_calender_date($event_start); ?>/<?php echo mep_calender_date($event_end); ?>&details=<?php echo substr(strip_tags($event->post_content),0,1000); ?>&location=<?php echo $location; ?>&trp=false&sprop=&sprop=name:'" target="_blank" class='mep-add-calender' rel="nofollow">Google</a></li>

<li><a href="https://calendar.yahoo.com/?v=60&view=d&type=20&title=<?php echo $event->post_title; ?>&st=<?php echo mep_calender_date($event_start); ?>&et=<?php echo mep_calender_date($event_end); ?>&desc=<?php echo substr(strip_tags($event->post_content),0,1000); ?>&in_loc=<?php echo $location; ?>&uid=" target="_blank" class='mep-add-calender' rel="nofollow">Yahoo</a></li>


<li><a href ="https://outlook.live.com/owa/?path=/calendar/view/Month&rru=addevent&dtstart=<?php echo mep_calender_date($event_start); ?>&dtend=<?php echo mep_calender_date($event_end); ?>&subject=<?php echo $event->post_title; ?>" target="_blank" class='mep-add-calender' rel="nofollow">Outlook</a></li>

<li><a href="https://webapps.genprod.com/wa/cal/download-ics.php?date_end=<?php echo mep_calender_date($event_end); ?>&date_start=<?php echo mep_calender_date($event_start); ?>&summary=<?php echo $event->post_title; ?>&location=<?php echo $location; ?>&description=<?php echo substr(strip_tags($event->post_content),0,1000); ?>">APPlE</a></li>
</ul>


<script type="text/javascript">
  
jQuery(document).ready(function() {
jQuery("#mep_add_calender_button").click(function () {
jQuery("#mep_add_calender_links").toggle()
});
});

</script>
<style type="text/css">
  #mep_add_calender_links{    display: none;
    background: transparent;
    margin-top: -7px;
    list-style: navajowhite;
    margin: 0;
    padding: 0;}
/*  #mep_add_calender_links li{list-style: none !important; line-height: 0.2px; border:1px solid #d5d5d5; border-radius: 10px; margin-bottom: 5px;}
  #mep_add_calender_links a{background: none !important; color: #333 !important; line-height: 0.5px !important; padding:10px; margin-bottom: 3px;}
  #mep_add_calender_links a:hover{color:#ffbe30;}*/
  #mep_add_calender_button{
   /*background: #ffbe30 none repeat scroll 0 0;*/
    border: 0 none;
    border-radius: 50px;
    /*color: #ffffff !important;*/
    display: inline-flex;
    font-size: 14px;
    font-weight: 600;
    overflow: hidden;
    padding: 15px 35px;
    position: relative;
    text-align: center;
    text-transform: uppercase;
    z-index: 1;
    cursor: pointer;
  }
.mep-default-sidrbar-social .mep-event-meta{text-align: center;}
</style>
<?php
  $content = ob_get_clean();
  echo $content;
}




function mep_get_item_name($name){
  $explode_name = explode('_', $name, 2);
  $the_item_name = str_replace('-', ' ', $explode_name[0]);
  return $the_item_name;
}



function mep_get_item_price($name){
  $explode_name = explode('_', $name, 2);
  $the_item_name = str_replace('-', ' ', $explode_name[1]);
  return $the_item_name;
}



function mep_get_string_part($data,$string){  
  $pieces = explode(" x ", $data);
return $pieces[$string]; // piece1
}


function mep_get_event_order_metadata($id,$part){
global $wpdb;
$table_name = $wpdb->prefix . 'woocommerce_order_itemmeta';
$result = $wpdb->get_results( "SELECT * FROM $table_name WHERE order_item_id=$id" );

foreach ( $result as $page )
{
  if (strpos($page->meta_key, '_') !== 0) {
   echo mep_get_string_part($page->meta_key,$part).'<br/>';
 }
}

}

add_action('woocommerce_account_dashboard','mep_ticket_lits_users');
function mep_ticket_lits_users(){
ob_start();
?>
<div class="mep-user-ticket-list">
  <table>
    <tr>
      <th><?php _e('Name','mage-eventpress'); ?></th>
      <th><?php _e('Ticket','mage-eventpress'); ?></th>
      <th><?php _e('Event','mage-eventpress'); ?></th>
      <th><?php _e('Download','mage-eventpress'); ?></th>
    </tr>
    <?php 
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events_attendees' ),
                     'posts_per_page'   => -1,
    'meta_query' => array(
        array(
            'key' => 'ea_user_id',
            'value' => get_current_user_id()
        )
    )
  );
  $loop = new WP_Query( $args_search_qqq );
  while ($loop->have_posts()) {
  $loop->the_post(); 
$event_id = get_post_meta( get_the_id(), 'ea_event_id', true );
  $event_meta = get_post_custom($event_id);

  $time = strtotime($event_meta['mep_event_start_date'][0]);
    $newformat = date('Y-m-d H:i:s',$time);


 if(time() < strtotime($newformat)){
?>
    <tr>
      <td><?php echo get_post_meta( get_the_id(), 'ea_name', true ); ?></td>
      <td><?php echo get_post_meta( get_the_id(), 'ea_ticket_type', true ); ?></td>
      <td><?php echo get_post_meta( get_the_id(), 'ea_event_name', true ); ?></td>
      <td><a href="<?php the_permalink(); ?>"><?php _e('Download','mage-eventpress'); ?></a></td>
    </tr>
<?php
  } 
  }    
    ?>
  </table>
</div>
<?php
$content = ob_get_clean();
echo $content;
}

// event_template_name();
function event_template_name(){

          $template_name = 'index.php';
          $template_path = get_stylesheet_directory().'/mage-events/themes/';
          $default_path = plugin_dir_path( __FILE__ ) . 'templates/themes/'; 

        $template = locate_template( array($template_path . $template_name) );

       if ( ! $template ) :
         $template = $default_path . $template_name;
       endif;

// echo $template_path;
if (is_dir($template_path)) {
  $thedir = glob($template_path."*");
}else{
$thedir = glob($default_path."*");
// file_get_contents('./people.txt', FALSE, NULL, 20, 14);
}

$theme = array();
foreach($thedir as $filename){
    //Use the is_file function to make sure that it is not a directory.
    if(is_file($filename)){
      $file = basename($filename);
     $naame = str_replace("?>","",strip_tags(file_get_contents($filename, FALSE, NULL, 24, 14))); 
    }   
     $theme[$file] = $naame;
}
return $theme;
}



function event_single_template_list($current_theme){
$themes = event_template_name();
        $buffer = '<select name="mep_event_template">';
        foreach ($themes as $num=>$desc){
          if($current_theme==$num){ $cc = 'selected'; }else{ $cc = ''; }
            $buffer .= "<option value=$num $cc>$desc</option>";
        }//end foreach
        $buffer .= '</select>';
        echo $buffer;
}

function mep_title_cutoff_words($text, $length){
    if(strlen($text) > $length) {
        $text = substr($text, 0, strpos($text, ' ', $length));
    }

    return $text;
}

function mep_get_tshirts_sizes($event_id){
  $event_meta   = get_post_custom($event_id);
  $tee_sizes  = $event_meta['mep_reg_tshirtsize_list'][0];
  $tszrray = explode(',', $tee_sizes);
$ts = "";
  foreach ($tszrray as $value) {
    $ts .= "<option value='$value'>$value</option>";
  }
return $ts;
}


function my_function_meta_deta() {
  global $order;

$order_id = $_GET['post'];
    // Getting an instance of the order object
    $order      = wc_get_order( $order_id );
    $order_meta = get_post_meta($order_id); 

   # Iterating through each order items (WC_Order_Item_Product objects in WC 3+)
    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id     = $item_values->get_product_id(); 
        $item_data      = $item_values->get_data();
        $product_id     = $item_data['product_id'];
        $item_quantity  = $item_values->get_quantity();
        $product        = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name     = $item_data['name'];
        $event_id       = $product->ID;
        $item_id        = $item_id;
    }

$user_info_arr = wc_get_order_item_meta($item_id,'_event_user_info',true);

// print_r($user_info_arr);

 ob_start();
?>
<div class='event-atendee-infos'>
<table class="atendee-info">
  <tr>
    <th>Name</th>
    <th>City</th>
  </tr>
  <?php 
  foreach ($user_info_arr as $_user_info) {
    $uname          = $_user_info['user_name'];
    $email          = $_user_info['user_email'];
    $phone          = $_user_info['user_phone'];
    $address        = $_user_info['user_address'];
    $gender         = $_user_info['user_gender'];
    $company        = $_user_info['user_company'];
    $designation    = $_user_info['user_designation'];
    $website        = $_user_info['user_website'];
    $vegetarian     = $_user_info['user_vegetarian'];
    $tshirtsize     = $_user_info['user_tshirtsize'];
    $ticket_type    = $_user_info['user_ticket_type'];
?>
<tr><td><?php echo $uname; ?></td><td><?php echo $address; ?></td></tr>
<?php
  }
  ?>
</table>
</div>
<?php
 $content = ob_get_clean();
 echo $content;
}
 // add_action( 'woocommerce_admin_order_totals_after_refunded','my_function_meta_deta', $order->id );



// add_action( 'woocommerce_thankyou', 'woocommerce_thankyou_change_order_status', 10, 1 );
function woocommerce_thankyou_change_order_status( $order_id ){
    if( ! $order_id ) return;

    $order = wc_get_order( $order_id );

    if( $order->get_status() == 'processing' )
        $order->update_status( 'completed' );
}




function mep_event_list_price($pid){
global $post;
  $cur = get_woocommerce_currency_symbol();
  $mep_event_ticket_type = get_post_meta($pid, 'mep_event_ticket_type', true);
  $mep_events_extra_prices = get_post_meta($pid, 'mep_events_extra_prices', true);
  $n_price = get_post_meta($pid, '_price', true);

  if($n_price==0){
    $gn_price = "Free";
  }else{
    $gn_price = wc_price($n_price);
  }

  // if($mep_events_extra_prices){
  //   $gn_price = $cur.$mep_events_extra_prices[0]['option_price'];
  // }

  if($mep_event_ticket_type){
    $gn_price = wc_price($mep_event_ticket_type[0]['option_price_t']);
  }
  
return $gn_price;
}

function mep_get_label($pid,$label_id,$default_text){
 return  mep_get_option( $label_id, 'label_setting_sec', $default_text);
}

// Add the custom columns to the book post type:
add_filter( 'manage_mep_events_posts_columns', 'mep_set_custom_edit_event_columns' );
function mep_set_custom_edit_event_columns($columns) {

    unset( $columns['date'] );

    $columns['mep_status'] = __( 'Status', 'mage-eventpress' );

    return $columns;
}


function mep_get_full_time_and_date($datetime){
  $user_set_format = mep_get_option( 'mep_event_time_format','general_setting_sec',12);

    if($user_set_format==12){
      echo date_i18n('D, d M Y h:i A', strtotime($datetime));
    }
    if($user_set_format==24){
      echo date_i18n('D, d M Y H:i', strtotime($datetime));
    }
}


function mep_get_only_time($datetime){
  $user_set_format = mep_get_option( 'mep_event_time_format','general_setting_sec',12);

    if($user_set_format==12){
      echo date_i18n('h:i A', strtotime($datetime));
    }
    if($user_set_format==24){
      echo date_i18n('H:i', strtotime($datetime));
    }
}

 

function mep_get_event_city($id){
$location_sts = get_post_meta($id,'mep_org_address',true);
$event_meta = get_post_custom($id);
if($location_sts){
$org_arr = get_the_terms( $id, 'mep_org' );
$org_id = $org_arr[0]->term_id;
  echo "<span>".get_term_meta( $org_id, 'org_city', true )."</span>";
}else{

  echo "<span>".$event_meta['mep_city'][0]."</span>";

}
}

 

function mep_get_total_available_seat($event_id, $event_meta){
$book_count = get_post_meta(get_the_id(),'total_booking', true);
if($book_count){ $total_book = $book_count; }else{ $total_book = 0; } 
$simple_rsv = $event_meta['mep_rsv_seat'][0];
if($simple_rsv){
  $simple_rsv = $simple_rsv;
}else{
  $simple_rsv = 0;
}
$total_book = ($total_book + $simple_rsv);
$mep_event_ticket_type = get_post_meta(get_the_id(), 'mep_event_ticket_type', true);
if($mep_event_ticket_type){
$stc  = 0;
$leftt  = 0;
$res  = 0;
foreach ( $mep_event_ticket_type as $field ) {
$qm = $field['option_name_t'];
$tesqn = get_the_id().str_replace(' ', '', $qm);
$tesq = get_post_meta(get_the_id(),"mep_xtra_$tesqn",true);
$stc = $stc+$field['option_qty_t'];
$res = $res + (int)$field['option_rsv_t'];
$res = (int)$res;
$llft = ($field['option_qty_t'] - (int)$tesq);
$leftt = ($leftt+$llft);
}
  $leftt = $leftt-$res;
}else{
 $leftt = (int) $event_meta['mep_total_seat'][0]- (int) $total_book;
}
return $leftt;
}




function mep_event_location_item($event_id,$item_name){
  return get_post_meta($event_id,$item_name,true);
}

function mep_event_org_location_item($event_id,$item_name){
  $location_sts = get_post_meta($event_id,'mep_org_address',true);

    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    if($org_arr){
    $org_id       = $org_arr[0]->term_id;
    return get_term_meta( $org_id, $item_name, true );
}
}





function mep_get_event_locaion_item($event_id,$item_name){
  if($event_id){
$location_sts = get_post_meta($event_id,'mep_org_address',true);


if($item_name=='mep_location_venue'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_location', true );
  }else{
    return get_post_meta($event_id,'mep_location_venue',true);
  }
}


if($item_name=='mep_location_venue'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_location', true );
  }else{
    return get_post_meta($event_id,'mep_location_venue',true);
  }
}


if($item_name=='mep_street'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_street', true );
  }else{
    return get_post_meta($event_id,'mep_street',true);
  }
}


if($item_name=='mep_city'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_city', true );
  }else{
    return get_post_meta($event_id,'mep_city',true);
  }
}


if($item_name=='mep_state'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_state', true );
  }else{
    return get_post_meta($event_id,'mep_state',true);
  }
}



if($item_name=='mep_postcode'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_postcode', true );
  }else{
    return get_post_meta($event_id,'mep_postcode',true);
  }
}


if($item_name=='mep_country'){
  if($location_sts){
    $org_arr      = get_the_terms( $event_id, 'mep_org' );
    $org_id       = $org_arr[0]->term_id;
      return get_term_meta( $org_id, 'org_country', true );
  }else{
    return get_post_meta($event_id,'mep_country',true);
  }
}


}

}

function mep_save_attendee_info_into_cart($product_id){

  $user = array();

  if(isset($_POST['user_name'])){
    $mep_user_name          = $_POST['user_name'];
  }else{ $mep_user_name=""; } 

  if(isset($_POST['user_email'])){  
    $mep_user_email         = $_POST['user_email'];
  }else{ $mep_user_email=""; } 

  if(isset($_POST['user_phone'])){  
    $mep_user_phone         = $_POST['user_phone'];
  }else{ $mep_user_phone=""; } 

  if(isset($_POST['user_address'])){  
    $mep_user_address       = $_POST['user_address'];
  }else{ $mep_user_address=""; } 

  if(isset($_POST['gender'])){  
    $mep_user_gender        = $_POST['gender'];
  }else{ $mep_user_gender=""; } 

  if(isset($_POST['tshirtsize'])){  
    $mep_user_tshirtsize    = $_POST['tshirtsize'];
  }else{ $mep_user_tshirtsize=""; } 

  if(isset($_POST['user_company'])){  
    $mep_user_company       = $_POST['user_company'];
  }else{ $mep_user_company=""; } 

  if(isset($_POST['user_designation'])){  
    $mep_user_desg          = $_POST['user_designation'];
  }else{ $mep_user_desg=""; } 

  if(isset($_POST['user_website'])){  
    $mep_user_website       = $_POST['user_website'];
  }else{ $mep_user_website=""; } 

  if(isset($_POST['vegetarian'])){  
    $mep_user_vegetarian    = $_POST['vegetarian'];
  }else{ $mep_user_vegetarian=""; } 

  if(isset($_POST['ticket_type'])){  
    $mep_user_ticket_type   = $_POST['ticket_type'];
  }else{ $mep_user_ticket_type=""; } 

  if(isset($_POST['mep_ucf'])){
    $mep_user_cfd           = $_POST['mep_ucf'];
  }else{
    $mep_user_cfd           = "";
  }

  if($mep_user_name){ $count_user = count($mep_user_name); } else{ $count_user = 0; }

  for ( $iu = 0; $iu < $count_user; $iu++ ) {
    
    if (isset($mep_user_name[$iu])):
      $user[$iu]['user_name'] = stripslashes( strip_tags( $mep_user_name[$iu] ) );
      endif;

    if (isset($mep_user_email[$iu])) :
      $user[$iu]['user_email'] = stripslashes( strip_tags( $mep_user_email[$iu] ) );
      endif;

    if (isset($mep_user_phone[$iu])) :
      $user[$iu]['user_phone'] = stripslashes( strip_tags( $mep_user_phone[$iu] ) );
      endif;

    if (isset($mep_user_address[$iu])) :
      $user[$iu]['user_address'] = stripslashes( strip_tags( $mep_user_address[$iu] ) );
      endif;

    if (isset($mep_user_gender[$iu])) :
      $user[$iu]['user_gender'] = stripslashes( strip_tags( $mep_user_gender[$iu] ) );
      endif;

    if (isset($mep_user_tshirtsize[$iu])) :
      $user[$iu]['user_tshirtsize'] = stripslashes( strip_tags( $mep_user_tshirtsize[$iu] ) );
      endif;

    if (isset($mep_user_company[$iu])) :
      $user[$iu]['user_company'] = stripslashes( strip_tags( $mep_user_company[$iu] ) );
      endif;

    if (isset($mep_user_desg[$iu])) :
      $user[$iu]['user_designation'] = stripslashes( strip_tags( $mep_user_desg[$iu] ) );
      endif;

    if (isset($mep_user_website[$iu])) :
      $user[$iu]['user_website'] = stripslashes( strip_tags( $mep_user_website[$iu] ) );
      endif;

    if (isset($mep_user_vegetarian[$iu])) :
      $user[$iu]['user_vegetarian'] = stripslashes( strip_tags( $mep_user_vegetarian[$iu] ) );
      endif;

    if (isset($mep_user_ticket_type[$iu])) :
      $user[$iu]['user_ticket_type'] = stripslashes( strip_tags( $mep_user_ticket_type[$iu] ) );
      endif;    

  $mep_form_builder_data = get_post_meta($product_id, 'mep_form_builder_data', true);
  if ( $mep_form_builder_data ) {
    foreach ( $mep_form_builder_data as $_field ) {
          $user[$iu][$_field['mep_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['mep_fbc_id']][$iu] ) );
      }
    }
  }
  return $user;
}



function mep_wc_price( $price, $args = array() ) {
  $args = apply_filters(
    'wc_price_args', wp_parse_args(
      $args, array(
        'ex_tax_label'       => false,
        'currency'           => '',
        'decimal_separator'  => wc_get_price_decimal_separator(),
        'thousand_separator' => wc_get_price_thousand_separator(),
        'decimals'           => wc_get_price_decimals(),
        'price_format'       => get_woocommerce_price_format(),
      )
    )
  );

  $unformatted_price = $price;
  $negative          = $price < 0;
  $price             = apply_filters( 'raw_woocommerce_price', floatval( $negative ? $price * -1 : $price ) );
  $price             = apply_filters( 'formatted_woocommerce_price', number_format( $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] ), $price, $args['decimals'], $args['decimal_separator'], $args['thousand_separator'] );

  if ( apply_filters( 'woocommerce_price_trim_zeros', false ) && $args['decimals'] > 0 ) {
    $price = wc_trim_zeros( $price );
  }

  $formatted_price = ( $negative ? '-' : '' ) . sprintf( $args['price_format'], '' . '' . '', $price );
  $return          = '' . $formatted_price . '';

  if ( $args['ex_tax_label'] && wc_tax_enabled() ) {
    $return .= '' . WC()->countries->ex_tax_or_vat() . '';
  }

  /**
   * Filters the string of price markup.
   *
   * @param string $return            Price HTML markup.
   * @param string $price             Formatted price.
   * @param array  $args              Pass on the args.
   * @param float  $unformatted_price Price as float to allow plugins custom formatting. Since 3.2.0.
   */
  return apply_filters( 'mep_wc_price', $return, $price, $args, $unformatted_price );
}



function mep_get_event_total_seat($event_id){
  $event_meta = get_post_custom($event_id);
  $book_count = get_post_meta($event_id,'total_booking', true);
  if($book_count){ $total_book = $book_count; }else{ $total_book = 0; } 
  if(array_key_exists('mep_rsv_seat', $event_meta)){
  $simple_rsv = $event_meta['mep_rsv_seat'][0];
  }else{
  $simple_rsv = '';
  }
  if($simple_rsv){
    $simple_rsv = $simple_rsv;
  }else{
    $simple_rsv = 0;
  }
  $total_book = ($total_book + $simple_rsv);
  $mep_event_ticket_type = get_post_meta($event_id, 'mep_event_ticket_type', true);

  if($mep_event_ticket_type){
  $stc  = 0;
  $leftt  = 0;
  $res    = 0;
  foreach ( $mep_event_ticket_type as $field ) {
  $qm = $field['option_name_t'];
  $tesqn = $event_id.str_replace(' ', '', $qm);
  $tesq = get_post_meta($event_id,"mep_xtra_$tesqn",true);
  $stc = $stc+$field['option_qty_t'];
  $res = $res + (int)$field['option_rsv_t'];
  $res = (int)$res;
  $llft = ($field['option_qty_t'] - (int)$tesq);
  $leftt = ($leftt+$llft);
  }
  $leftt = $leftt-$res;
  ?>
    <span style="background: #dc3232;color: #fff;padding: 5px 10px;"> <?php echo $leftt; ?>/<?php echo $stc; ?> </span>
  <?php

  }else{
    if($event_meta['mep_total_seat'][0]){ ?>
    <span style="background: #dc3232;color: #fff;padding: 5px 10px;"> <?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>/<?php echo $event_meta['mep_total_seat'][0];  ?></span>
    <?php } 
  }
}




function mep_reset_event_booking($event_id){
  $mep_event_ticket_type = get_post_meta($event_id, 'mep_event_ticket_type', true);
  if($mep_event_ticket_type){
      foreach ( $mep_event_ticket_type as $field ) {
        $qm = $field['option_name_t'];
        $tesqn = $event_id.str_replace(' ', '', $qm);
        $reset =  update_post_meta($event_id,"mep_xtra_$tesqn",0);
      }
    // if($reset){ return 'Reset Done!'; }
  }else{
    $reset =  update_post_meta($event_id,"total_booking",0);
    // if($reset){ return 'Reset Done!'; }
  }
  $args_search_qqq = array (
                     'post_type'        => array( 'mep_events_attendees' ),
                     'posts_per_page'   => -1,
                     'post_status'      => 'publish',
                     'meta_query'       => array(
                        array(
                            'key'       => 'ea_event_id',
                            'value'     => $event_id,
                            'compare'   => '='
                        )
                    )                      
                );  
  $loop = new WP_Query($args_search_qqq);
  while ($loop->have_posts()) {
  $loop->the_post(); 
    $post_id = get_the_id(); // change this to your post ID
    $status = 'trash';
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    wp_update_post($current_post);
  }
}



// Add the custom columns to the book post type:
add_filter( 'manage_mep_events_posts_columns', 'mep_set_custom_mep_events_columns' );
function mep_set_custom_mep_events_columns($columns) {

    $columns['mep_event_seat'] = __( 'Seats', 'mage-eventpress' );

    return $columns;
}


// Add the data to the custom columns for the book post type:
add_action( 'manage_mep_events_posts_custom_column' , 'mep_mep_events_column', 10, 2 );
function mep_mep_events_column( $column, $post_id ) {
    switch ( $column ) {

        case 'mep_event_seat' :          
          echo mep_get_event_total_seat($post_id); 
        break; 
    }
}











}else{
function mep_admin_notice_wc_not_active() {
  $class = 'notice notice-error';
    printf(
      '<div class="error" style="background:red; color:#fff;"><p>%s</p></div>',
      __('You Must Install WooCommerce Plugin before activating WooCommerce Event Manager, Becuase It is dependent on Woocommerce Plugin')
    );
}
add_action( 'admin_notices', 'mep_admin_notice_wc_not_active' );
}