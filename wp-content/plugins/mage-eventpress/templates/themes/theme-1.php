<?php 
// Template Name: Springfield
?>
<div class="mep-event-detailsss mep-event-theme-1">
		<div class="mep-top-part">
			<div class="mep-left-col">
				<?php do_action('mep_event_thumbnail'); ?>			
			</div>
			<div class="mep-right-col">
				<div class="mep-event-title-header">
					<?php do_action('mep_event_title'); ?>
					<?php do_action('mep_event_organizer'); ?>
					<?php do_action('mep_event_price'); ?>
					<?php do_action('mep_event_seat'); ?>
					<?php do_action('mep_event_add_calender'); ?>
				</div>	
			</div>
		</div>
<?php do_action('mep_event_map'); ?>
		<div class="mep-btn-part">
			<div class="mep-left-col">
				<?php do_action('mep_event_social_share'); ?>				
			</div>
			<div class="mep-right-col">
				<div class="mep-event-datetime">
					<h3><?php _e('Date and Time:','mage-eventpress'); ?></h3>
					<?php do_action('mep_event_date'); ?>
				</div>
			</div>			
		</div>
		<div class="mep-content-part">
			<div class="mep-left-col">
				<div class="mep-event-details">
					<h3 class="mep-desc-title"><?php _e('Description','mage-eventpress'); ?></h3>
					<?php do_action('mep_event_details'); ?>
					<div class="mep-theme1-faq-sec">
						<?php do_action('mep_event_faq'); ?>
					</div>
				</div>				
			</div>
			<div class="mep-right-col">
				<div class="mep-reg-btn-sec">
					<div class="cart-btn-sec">
						<?php do_action('mep_add_to_cart') ?>
					</div>
				</div>	
				<div class="mep-event-location">
					<h3><?php _e('Event Location:','mage-eventpress'); ?></h3>
					<?php do_action('mep_event_location') ?>					
				</div>
			</div>			
		</div>
	</div>