<?php
function mep_add_custom_fields_text_to_cart_item( $cart_item_data, $product_id, $variation_id ){

  $tp = get_post_meta($product_id,'_price',true);

  $new = array();

  if(isset($_POST['mep_event_location_cart'])){
    $event_cart_location   =  $_POST['mep_event_location_cart'];
  }else{ $event_cart_location   = ""; } 

  if(isset($_POST['mep_event_date_cart'])){
    $event_cart_date   =  $_POST['mep_event_date_cart'];
  }else{ $event_cart_date   = ""; } 

  if(isset($_POST['event_addt_price'])){
    $checked                = $_POST['event_addt_price'];
  }else{ $checked=""; } 

   if(isset($_POST['option_name'])){
    $names                  = $_POST['option_name'];
  }else{ $names=array(); } 

  if(isset($_POST['option_qty'])){  
    $qty                    = $_POST['option_qty'];
  }else{ $qty=""; } 

  if(isset($_POST['option_price'])){  
    $price                  = $_POST['option_price'];
  }else{ $price=""; } 

  $count = count( $names );

 if(isset($_POST['option_name'])){
  for ( $i = 0; $i < $count; $i++ ) {
    if ( $names[$i] != '' ) :
      $new[$i]['option_name'] = stripslashes( strip_tags( $names[$i] ) );
      endif;
    if ( $price[$i] != '' ) :
      $new[$i]['option_price'] = stripslashes( strip_tags( $price[$i] ) );
      endif;
    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;
    $opttprice =   ($price[$i]*$qty[$i]);
    $tp = ($tp+$opttprice);
  }
}




if(isset($_POST['mep_event_ticket_type'])){
  $ttp                                  = $_POST['mep_event_ticket_type'];
  $ttpqt                                = $_POST['tcp_qty'];
  $ticket_type                          = mep_get_order_info($ttp,1);
  $ticket_type_price                    = (mep_get_order_info($ttp,0)*$ttpqt);
  $cart_item_data['event_ticket_type']  = $ticket_type;
  $cart_item_data['event_ticket_price'] = $ticket_type_price;
  $cart_item_data['event_ticket_qty']   = $ttpqt;
  $tp                                   = $tp+$ticket_type_price;
}

    $form_position = mep_get_option( 'mep_user_form_position', 'general_attendee_sec', 'details_page' );
    if($form_position=='details_page'){
      $user = mep_save_attendee_info_into_cart($product_id);
    }else{
      $user = '';
    }

  $cart_item_data['event_extra_option']   = $new;
  $cart_item_data['event_user_info']      = $user;
  $cart_item_data['event_tp']             = $tp;
  $cart_item_data['line_total']           = $tp;
  $cart_item_data['line_subtotal']        = $tp;
  $cart_item_data['event_id']             = $product_id;
  $cart_item_data['event_cart_location']  = $event_cart_location;
  $cart_item_data['event_cart_date']      = $event_cart_date;


  return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'mep_add_custom_fields_text_to_cart_item', 10, 3);



add_action( 'woocommerce_before_calculate_totals', 'add_custom_price' );
function add_custom_price( $cart_object ) {

    foreach ( $cart_object->cart_contents as $key => $value ) {
$eid = $value['event_id'];
if (get_post_type($eid) == 'mep_events') {      
            $cp = $value['event_tp'];
            $value['data']->set_price($cp);
            $new_price = $value['data']->get_price();
    }
  }
}





function mep_display_custom_fields_text_cart( $item_data, $cart_item ) {
$mep_events_extra_prices = $cart_item['event_extra_option'];
// print_r($cart_item);
$eid                    = $cart_item['event_id'];
if (get_post_type($eid) == 'mep_events') { 
echo "<ul class='event-custom-price'>";
?>
<li><?php _e('Event Date','mage-eventpress'); ?>: <?php echo $cart_item['event_cart_date']; ?></li>
<li><?php _e('Event Location','mage-eventpress'); ?>: <?php echo $cart_item['event_cart_location']; //echo $cart_item['event_ticket_type']; ?></li>
<?php
if($mep_events_extra_prices){
  foreach ( $mep_events_extra_prices as $field ) {
    if($field['option_qty']>0){
  ?>
  <li><?php echo esc_attr( $field['option_name'] ); ?> x <?php echo esc_attr( $field['option_qty'] ); ?>: <?php echo wc_price($field['option_qty'] *$field['option_price'] ); ?>  </li>
  <?php
  }
}
}
if(array_key_exists('event_ticket_type', $cart_item)){
// if($cart_item['event_ticket_type']){
echo "<li> Ticket: ".$cart_item['event_ticket_type']." x ".$cart_item['event_ticket_qty'].": ".wc_price($cart_item['event_ticket_price'])."</li>";
}
    echo "</ul>";
  }
  return $item_data;

}
add_filter( 'woocommerce_get_item_data', 'mep_display_custom_fields_text_cart', 10, 2 );




function mep_add_custom_fields_text_to_order_items( $item, $cart_item_key, $values, $order ) {
$eid                    = $values['event_id'];
if (get_post_type($eid) == 'mep_events') { 
$mep_events_extra_prices = $values['event_extra_option'];
if(isset($values['event_ticket_type'])){
$event_ticket_type       = $values['event_ticket_type'];
}else{
$event_ticket_type = " "; 
}
if(isset($values['event_ticket_price'])){
$event_ticket_price      = $values['event_ticket_price'];
}else{
$event_ticket_price      = " ";
}
if(isset($values['event_ticket_qty'])){
$event_ticket_qty        = $values['event_ticket_qty'];
}else{
$event_ticket_qty        = " ";  
}
$product_id              = $values['product_id'];
$cart_location           = $values['event_cart_location'];
$cart_date               = $values['event_cart_date'];
$form_position = mep_get_option( 'mep_user_form_position', 'general_attendee_sec', 'details_page' );

    if($form_position=='details_page'){
      $event_user_info         = $values['event_user_info'];
    }else{
      $event_user_info = mep_save_attendee_info_into_cart($eid);
    }




$item->add_meta_data('Date',$cart_date);
$item->add_meta_data('Location',$cart_location);




if (is_array($mep_events_extra_prices) || is_object($mep_events_extra_prices)){
foreach ( $mep_events_extra_prices as $field ) {
    if($field['option_qty']>0){

      $item->add_meta_data(esc_attr( $field['option_name'] )." x ".$field['option_qty'], wc_price($field['option_qty'] *$field['option_price'] ) );


      $opt_name =  $product_id.str_replace(' ', '', $field['option_name']);
      $opt_qty = $field['option_qty'];

// $tes = 0;
$tes = get_post_meta($product_id,"mep_xtra_$opt_name",true);
$ntes = ($tes+$opt_qty);
update_post_meta( $product_id, "mep_xtra_$opt_name",$ntes);

  }

} 
}

if($event_ticket_type){

// $event_ticket_type = "Ticket:".$event_ticket_type;

// $item->add_meta_data( $event_ticket_type." x ".$event_ticket_qty,get_woocommerce_currency_symbol().$event_ticket_price);
$tck_name = $product_id.str_replace(' ', '', $event_ticket_type);
$tesqt = get_post_meta($product_id,"mep_xtra_$tck_name",true);
$ntesqt = ($tesqt+$event_ticket_qty);
update_post_meta( $product_id, "mep_xtra_$tck_name",$ntesqt);
$item->add_meta_data('_event_ticket_type','ticket_typs');
}else{
  $item->add_meta_data('_event_ticket_type','normal');
}

$item->add_meta_data('_event_user_info',$event_user_info);
$item->add_meta_data('_no_of_ticket',count($event_user_info));
$item->add_meta_data('_event_service_info',$mep_events_extra_prices);
$item->add_meta_data('event_id',$eid);

}
}
add_action( 'woocommerce_checkout_create_order_line_item', 'mep_add_custom_fields_text_to_order_items', 10, 4 );