<?php 
add_action('mep_event_date','mep_ev_datetime');


function mep_ev_datetime(){
global $event_meta;	

if(array_key_exists('mep_event_more_date', $event_meta)){ 
$more_date = unserialize($event_meta['mep_event_more_date'][0]);
}else{
	$more_date = '';
}


?>
<p><?php mep_get_full_time_and_date($event_meta['mep_event_start_date'][0]); if($more_date){ foreach ($more_date as $md) {
	echo " - "; mep_get_full_time_and_date($md['event_more_date']);
} } else{ echo " - "; } ?> <?php if($more_date){ echo " - "; }  mep_get_full_time_and_date($event_meta['mep_event_end_date'][0]); ?></p>
<?php
}



add_action('mep_event_date_only','mep_ev_date');
function mep_ev_date(){
global $event_meta;	
?>
<p><?php echo date_i18n('d M Y', strtotime($event_meta['mep_event_start_date'][0])); ?> </p>
<?php
}

add_action('mep_event_time_only','mep_ev_time');
function mep_ev_time(){
global $event_meta;	
?>
<p><?php mep_get_only_time($event_meta['mep_event_start_date'][0]); ?> </p>
<?php
}




function mep_ev_time_ticket($event_meta){
// global $event_meta;	
?>
<?php mep_get_only_time($event_meta['mep_event_start_date'][0]); ?>
<?php
}

function mep_ev_date_ticket($event_meta){
// global $event_meta;	
?>
<?php echo date_i18n('d M Y', strtotime($event_meta['mep_event_start_date'][0])); ?> 
<?php
}