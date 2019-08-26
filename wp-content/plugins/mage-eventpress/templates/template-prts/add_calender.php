<?php 
add_action('mep_event_add_calender','mep_ev_calender');


function mep_ev_calender(){
?>
<div class="calender-url">
	<?php mep_add_to_google_calender_link(get_the_id()); ?>
</div>
<?php
}

