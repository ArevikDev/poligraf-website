<?php 
// Template Name: Bristol
?>
<div class="mep-template-3-sahan">
	<div class="mep-tem3-thumbnail">
		<?php do_action('mep_event_thumbnail'); ?>
	</div>
	<div class="mep-tem3-title-sec">
		<div class="mep-tem3-title"><?php do_action('mep_event_title'); ?></div>
		<div class="mep-tem3-datetime"><i class="fa fa-calendar"></i><?php do_action('mep_event_date'); ?></div>
		<div class="mep-tem3-location"><i class="fa fa-location-arrow"></i><?php do_action('mep_event_location') ?></div>
	</div>

<!-- Mid Sec Start -->
	<div class="mep-tem3-mid-sec">

<!-- mid left start -->
		<div class="mid-sec-left">
			<div class="mep-tem3-cart-sec">
				<?php do_action('mep_add_to_cart') ?>
			</div>
		</div>
<!-- mid right end -->

		<div class="mid-sec-right">
			<div class="mep-tem3-share-btn">
				<?php do_action('mep_event_price'); ?>
				<?php do_action('mep_event_seat'); ?>				
				<?php do_action('mep_event_add_calender'); ?>
				<?php do_action('mep_event_social_share'); ?>
			</div>
			<div class="tmep-emplate-3-faq-sec">
				<?php do_action('mep_event_faq'); ?>
			</div>			
		</div>

	</div>
<!-- Mid sec end -->

	<div class="mep-tem3-event-details">
		<h2><?php _e('About The Event','mage-eventpress'); ?>:</h2>
		<?php do_action('mep_event_details'); ?>
	</div>

	<div class="mep-tm3-map">
		<?php do_action('mep_event_map'); ?>
	</div>

</div>	