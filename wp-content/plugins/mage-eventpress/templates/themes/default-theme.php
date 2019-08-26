<?php
// Template Name: Default Theme
?>

<div class="mep-default-theme">
	<div class="mep-default-content">
		<div class="mep-default-title">
			<?php do_action('mep_event_title'); ?>
		</div>
		<div class="mep-default-feature-image">
			<?php do_action('mep_event_thumbnail'); ?>
		</div>
		<div class="mep-default-feature-date-location">
			<div class="mep-default-feature-date">
				<div class="df-ico"><i class="fa fa-calendar"></i></div>
				<div class='df-dtl'>
				<h3>
              <?php echo mep_get_option('mep_event_date_text', 'label_setting_sec') ? mep_get_option('mep_event_date_text', 'label_setting_sec') : _e('Event Date:','mage-eventpress');  ?>	
				</h3>
				<?php do_action('mep_event_date_only'); ?>
			</div>
			</div>
			<div class="mep-default-feature-time">
				<div class="df-ico"><i class="fa fa-clock-o"></i></div>
				<div class='df-dtl'>
				<h3>
				<?php echo mep_get_option('mep_event_time_text', 'label_setting_sec') ? mep_get_option('mep_event_time_text', 'label_setting_sec') : _e('Event Time:','mage-eventpress');  ?>	
				</h3>
				<?php do_action('mep_event_time_only'); ?>	
				</div>				
			</div>
			<div class="mep-default-feature-location">
				<div class="df-ico"><i class="fa fa-map-marker"></i></div>
				<div class='df-dtl'>
				<h3>
                <?php echo mep_get_option('mep_event_location_text', 'label_setting_sec') ? mep_get_option('mep_event_location_text', 'label_setting_sec') : _e('Event Location:','mage-eventpress');  ?>
				</h3>
				<p><?php do_action('mep_event_location_venue'); ?>, <?php do_action('mep_event_location_city'); ?>	</p>
			</div>
			</div>
		</div>
		<div class="mep-default-feature-content">
			<?php do_action('mep_event_details'); ?>
		</div>
		<div class="mep-default-feature-cart-sec">
			<?php do_action('mep_add_to_cart') ?>
		</div>		

		<div class="mep-default-feature-faq-sec">
			<?php do_action('mep_event_faq'); ?>
		</div>




	</div>
	<div class="mep-default-sidebar">
		<div class="mep-default-sidrbar-map">
			<h3><?php echo mep_get_option('mep_event_location_text', 'label_setting_sec') ? mep_get_option('mep_event_location_text', 'label_setting_sec') : _e('Event Location:','mage-eventpress');  ?>
				</h3>
			</h3>
			<?php do_action('mep_event_map'); ?>
		</div>
		<div class="df-sidebar-part">
		<div class="mep-default-sidrbar-price-seat">
			<div class="df-price"><?php do_action('mep_event_price'); ?></div>
			<div class="df-seat"><?php do_action('mep_event_seat'); ?></div>
		</div>
		<div class="mep-default-sidrbar-meta">
			<i class="fa fa-link"></i> <?php do_action('mep_event_organizer'); ?>
		</div>
		<div class="mep-default-sidrbar-address">
			<ul>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_location_venue'); ?></li>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_location_street'); ?></li>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_location_city'); ?></li>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_location_state'); ?></li>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_location_country'); ?></li>
				<li><i class="fa fa-arrow-circle-right"></i> <?php do_action('mep_event_date'); ?></li>
			</ul>	
		</div>
		<div class="mep-default-sidrbar-social">
			<?php do_action('mep_event_social_share'); ?>
		</div>
		<div class="mep-default-sidrbar-calender-btn">
			<?php do_action('mep_event_add_calender'); ?>
		</div>
	</div>
	</div>
</div>