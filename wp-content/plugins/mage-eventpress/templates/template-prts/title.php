<?php 

add_action('mep_event_title','mep_ev_title');
function mep_ev_title(){
	?>
		<h2><?php the_title(); ?></h2>
	<?php
}
