<?php 
add_action('mep_event_faq','mep_faq_part');
function mep_faq_part(){
global $post;
$mep_event_faq = get_post_meta($post->ID, 'mep_event_faq', true);
 if ( $mep_event_faq ) {
?>
<div class="mep-event-faq-part">
<h4><?php _e('Event F.A.Q','mage-eventpress'); ?></h4>
<div id='mep-event-accordion' class="">
<?php  
  foreach ( $mep_event_faq as $field ) {
  ?>
  <h3><?php if($field['mep_faq_title'] != '') echo esc_attr( $field['mep_faq_title'] ); ?></h3>
  <p><?php if($field['mep_faq_content'] != '') echo esc_attr( $field['mep_faq_content'] ); ?></p> 
  <?php
  }
?>
  </div>
</div>
<?php
}
}