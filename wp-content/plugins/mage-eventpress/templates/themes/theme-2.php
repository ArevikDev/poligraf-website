<?php 
// Template Name: Franklin
?>
<div class="mep-template-2-hamza">
	<div class="mep-tem2-title">
		<?php do_action('mep_event_title'); ?>
	</div>
	
	<div class="mep-tem2-date">
		<?php do_action('mep_event_date'); ?>
	</div>	
	
	<div class="mep-tem2-thumbnail">
		<?php do_action('mep_event_thumbnail'); ?>	
	</div>	

	<div class="mep-tem2-details">
		<?php do_action('mep_event_details'); ?>
		<?php do_action('mep_event_add_calender'); ?>
	</div>	
<div class="tem2-carts">
	<div class="tem2-cart-sec">
	<?php do_action('mep_event_price'); ?>
	<?php do_action('mep_event_seat'); ?>
	<?php do_action('mep_add_to_cart') ?>		
	</div>
	<div class="tem2-faq-sec">
		<?php do_action('mep_event_faq'); ?>
	</div>
</div>
	<div class="mep-tem2-venue">
			<div class="tm2-location">
				<h3><?php _e('Organizer:','mage-eventpress'); ?></h3>
				<?php do_action('mep_event_organizer'); ?>
				<h3><?php _e('Venue:','mage-eventpress'); ?></h3>
				<?php do_action('mep_event_location') ?>				
			</div>
			<div class="tm2-map">
				<?php do_action('mep_event_map'); ?>
			</div>
	</div>
	<div class="tm2-share-button">
		<?php do_action('mep_event_social_share'); ?>
	</div>

</div>