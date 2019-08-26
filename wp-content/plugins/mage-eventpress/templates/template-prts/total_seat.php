<?php 
add_action('mep_event_seat','mep_ev_seat');


function mep_ev_seat(){
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
$stc 	= 0;
$leftt  = 0;
$res    = 0;
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
?>
	<h5><strong><?php echo mep_get_option('mep_total_seat_text', 'label_setting_sec') ? mep_get_option('mep_total_seat_text', 'label_setting_sec') : _e('Total Seat:','mage-eventpress');  ?></strong> <?php echo $stc; if($mep_available_seat=='on'){ ?> (<strong><?php echo $leftt; ?></strong> <?php _e('Left','mage-eventpress'); ?>)<?php } ?></h5>
<?php

}else{
	if($event_meta['mep_total_seat'][0]){ ?>
	<h5><strong><?php echo mep_get_option('mep_total_seat_text', 'label_setting_sec') ? mep_get_option('mep_total_seat_text', 'label_setting_sec') : _e('Total Seat:','mage-eventpress');  ?></strong> <?php echo $event_meta['mep_total_seat'][0]; if($mep_available_seat=='on'){ ?> (<strong><?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?></strong> <?php _e('Left','mage-eventpress'); ?>) <?php } ?></h5>
	<?php } 
}

}