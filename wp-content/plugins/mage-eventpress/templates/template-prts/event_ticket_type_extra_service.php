<?php 
function mep_output_add_to_cart_custom_fields() {


global $post,$event_meta,$total_book;


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

$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);


if(array_key_exists('mep_available_seat', $event_meta)){ 
  $mep_available_seat = $event_meta['mep_available_seat'][0];
}else{
  $mep_available_seat = 'on';
}

if($mep_event_ticket_type){

$stc = 0;
$leftt = 0;
$res = 0;


foreach ( $mep_event_ticket_type as $field ) {
$qm = $field['option_name_t'];
$tesqn = $post->ID.str_replace(' ', '', $qm);

$tesq = get_post_meta($post->ID,"mep_xtra_$tesqn",true);

$stc = $stc+$field['option_qty_t'];

$res = $res + (int)$field['option_rsv_t'];

$res = (int)$res;


$llft = ($field['option_qty_t'] - (int)$tesq);
$leftt = ($leftt+$llft);
}
$leftt = $leftt-$res;

}else{
$leftt = $event_meta['mep_total_seat'][0]- $total_book;
}


if($leftt>0){

do_action('mep_event_ticket_types');
do_action('mep_event_extra_service');
}

else{
    ?>
  <span class=event-expire-btn>
   <?php  _e('No Seat Available','mage-eventpress');  ?>
  </span>
    <?php
    do_action('mep_after_no_seat_notice');
}


}
add_action( 'mep_event_ticket_type_extra_service', 'mep_output_add_to_cart_custom_fields', 10 );
