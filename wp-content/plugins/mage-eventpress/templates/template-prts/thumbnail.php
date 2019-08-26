<?php 
add_action('mep_event_thumbnail','mep_thumbnail');

function mep_thumbnail(){
 ?>
	<div class="mep-event-thumbnail">
		<?php the_post_thumbnail('full'); ?>
	</div>	
 <?php
}

