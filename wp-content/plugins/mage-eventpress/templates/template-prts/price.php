<?php 
add_action('mep_event_price','mep_ev_price');


function mep_ev_price(){
global $event_meta;
if($event_meta['_price'][0]>0){
						if($event_meta['mep_price_label'][0]){
					?>
					<h3><?php echo $event_meta['mep_price_label'][0]; ?>: </h3>
					<?php } 
					echo wc_price($event_meta['_price'][0]); 

					?>
					<?php } else{ echo ''; }
}