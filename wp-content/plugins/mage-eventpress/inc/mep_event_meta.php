<?php 
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.
add_action( 'add_meta_boxes', 'mep_event_meta_box_add' );
function mep_event_meta_box_add(){
  
    add_meta_box( 'mep-event-meta', __('Event Venue','mage-eventpress'), 'mep_event_venue_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-price', __('Event Price (Event Base price, It will not work if you add Event Ticket type Price)','mage-eventpress'), 'mep_event_price_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-extra-price', __('Event Extra Service (Extra Service as Product that you can sell and it is not included on event package)','mage-eventpress'), 'mep_event_extra_price_option', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-ticket-type', __('Event Ticket Type','mage-eventpress'), 'mep_event_ticket_type', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-date', __('Event Date & Time','mage-eventpress'), 'mep_event_date_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-emails', __('Event Email text','mage-eventpress'), 'mep_event_email_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-template', __('Template','mage-eventpress'), 'mep_event_template_meta_box_cb', 'mep_events', 'side', 'low' );

    add_meta_box( 'mep-event-faq-box', __('Event F.A.Q','mage-eventpress'), 'mep_event_faq_meta_box_cb', 'mep_events', 'normal', 'high' );

    add_meta_box( 'mep-event-reg-on-off', __('Registration Status','mage-eventpress'), 'mep_event_change_reg_status_cb', 'mep_events', 'side', 'low' );

    add_meta_box( 'mep-event-available-set-on-off', __('Show Available Seat Count?','mage-eventpress'), 'mep_event_available_seat_cb', 'mep_events', 'side', 'low' );

    add_meta_box( 'mep-event-day-details', __('Event Daywise Details','mage-eventpress'), 'mep_event_day_details_cb', 'mep_events', 'normal', 'high' );
    add_meta_box( 'mep-event-day-details', __('Event Daywise Details','mage-eventpress'), 'mep_event_day_details_cb', 'mep_events', 'normal', 'high' );

    if(get_option( 'woocommerce_calc_taxes' )=='yes'){
        add_meta_box( 'mep-event-tax-sec', __('Event Tax','mage-eventpress'), 'mep_event_tax_cb', 'mep_events', 'side', 'low' );

    }
add_meta_box( 'mep-event-rest-count-sec', __('Event Reset Booking Count','mage-eventpress'), 'mep_event_reset_booking_count', 'mep_events', 'side', 'low' );
}




function mep_event_reset_booking_count($post){
  $values = get_post_custom( $post->ID );
      wp_nonce_field( 'mep_event_reset_btn_nonce', 'mep_event_reset_btn_nonce' );
?>
<div class='sec'>
  <h6><? _e('Current Status','mage-eventpress');  echo mep_get_event_total_seat($post->ID); ?></h6>
  <p style="color: red;"><?php _e('If you reset this count, All booking information will be removed including attendee list & its impossible to undo','mage-eventpress'); ?></p>
    <label for="mep_ev_20988999"> <?php _e('Reset Booking Count :','mage-eventpress'); ?> 
      <label class="switch">
        <input type="checkbox" id="mep_ev_20988999" name='mep_reset_status'/>
        <span class="slider round"></span>
      </label> 
    </label>
</div>
  <?php
}




function mep_event_change_reg_status_cb($post){
  $values = get_post_custom( $post->ID );
      wp_nonce_field( 'mep_event_reg_btn_nonce', 'mep_event_reg_btn_nonce' );
?>
<div class='sec'>
    <label for="mep_ev_20988"> <?php _e('Registration On/Off:','mage-eventpress'); ?> 
<label class="switch">
  <input type="checkbox" id="mep_ev_20988" name='mep_reg_status' <?php if(array_key_exists('mep_reg_status', $values)){ if($values['mep_reg_status'][0]=='on'){ echo 'checked'; } }else{ echo 'Checked'; } ?>/>
  <span class="slider round"></span>
</label> 
    </label>
</div>
  <?php
}







function mep_event_tax_cb($post){
    $values = get_post_custom( $post->ID );
    wp_nonce_field( 'mep_event_reg_btn_nonce', 'mep_event_reg_btn_nonce' );
    echo $values['_tax_status'][0];

if(array_key_exists('_tax_status', $values)){ $tx_status = $values['_tax_status'][0]; }else{ $tx_status = ''; }

if(array_key_exists('_tax_class', $values)){ $tx_class = $values['_tax_class'][0]; }else{ $tx_class = ''; }

    ?>
      <div class='sec'>
          <label for="_tax_status"> <?php _e('Tax status','mage-eventpress'); ?> 
              <select style="" id="_tax_status" name="_tax_status" class="select short">
                    <option value="taxable" <?php if($tx_status=='taxable'){ echo 'Selected'; } ?>>Taxable</option>
                    <option value="shipping" <?php if($tx_status=='shipping'){ echo 'Selected'; } ?>>Shipping only</option>
                    <option value="none" <?php if($tx_status=='none'){ echo 'Selected'; } ?>>None</option>   
              </select>
          </label>
      </div>      
      <div class='sec'>
          <label for="_tax_class"> <?php _e('Tax class','mage-eventpress'); ?> 
              <select style="" id="_tax_class" name="_tax_class" class="select short">
                    <option value="standard" <?php if($tx_class=='standard'){ echo 'Selected'; } ?>>Standard</option>
                    <?php 
                      $mep_get_tax_class = get_option( 'woocommerce_tax_classes' );
                      $mep_get_tax_arr = explode( "\n", get_option( 'woocommerce_tax_classes' ));
                      foreach ($mep_get_tax_arr as $mep_tax_class ) {
                        $tax_slug = sanitize_title($mep_tax_class);
                        ?>
                      <option value="<?php echo $tax_slug; ?>" <?php if($tx_class==$tax_slug){ echo 'Selected'; } ?>><?php echo $mep_tax_class; ?></option>
                      <?php
                      }
                    ?>  
              </select>
          </label>
      </div>
    <?php
}






function mep_event_available_seat_cb($post){
  $values = get_post_custom( $post->ID );
      wp_nonce_field( 'mep_event_reg_btn_nonce', 'mep_event_reg_btn_nonce' );
?>
<div class='sec'>
    <label for="mep_ev_209882"> <?php _e('Show Available Seat?','mage-eventpress'); ?> 
      <label class="switch">
        <input type="checkbox" id="mep_ev_209882" name='mep_available_seat' <?php if(array_key_exists('mep_available_seat', $values)){ if($values['mep_available_seat'][0]=='on'){ echo 'checked'; } }else{ echo 'Checked'; } ?>/>
        <span class="slider round"></span>
      </label> 
    </label>
</div>
  <?php
}

add_action('save_post','mep_reg_status_meta_save');
function mep_reg_status_meta_save($post_id){
  
  global $wpdb;
  
  if ( ! isset( $_POST['mep_event_reg_btn_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_reg_btn_nonce'], 'mep_event_reg_btn_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;

   
if (get_post_type($post_id) == 'mep_events') { 

if(isset($_POST['mep_reg_status'])){
    $mep_reg_status     = strip_tags($_POST['mep_reg_status']);
}else{
  $mep_reg_status     = 'off';
}  


if(isset($_POST['mep_reset_status'])){
  $mep_reset_status     = strip_tags($_POST['mep_reset_status']);
}else{
  $mep_reset_status     = 'off';
}


if(isset($_POST['mep_available_seat'])){
  $mep_available_seat     = strip_tags($_POST['mep_available_seat']);
}else{
  $mep_available_seat     = 'off';
}


if($mep_reset_status=='on'){
  mep_reset_event_booking($post_id);
}





if(isset($_POST['_tax_status'])){
    $_tax_status     = strip_tags($_POST['_tax_status']);
}else{
  $_tax_status     = 'none';
}

if(isset($_POST['_tax_class'])){
    $_tax_class     = strip_tags($_POST['_tax_class']);
}else{
  $_tax_class     = '';
}

$update_ava_seat    = update_post_meta( $post_id, 'mep_available_seat', $mep_available_seat);
$update_seat        = update_post_meta( $post_id, 'mep_reg_status', $mep_reg_status);
$update__tax_status        = update_post_meta( $post_id, '_tax_status', $_tax_status);
$update__tax_class        = update_post_meta( $post_id, '_tax_class', $_tax_class);
}

}





function mep_event_venue_meta_box_cb($post){
$values   = get_post_custom( $post->ID );
$user_api = mep_get_option( 'google-map-api', 'general_setting_sec', '');
$map_type = mep_get_option( 'mep_google_map_type', 'general_setting_sec', 'iframe');
?>


<div class='sec'>
    <label for="mep_ev_9890"> <?php _e('Show Address from Organizer?:','mage-eventpress'); ?> </label>
    <span><input style='text-align: left;width: auto;' id='mep_ev_9890' type="checkbox" name='mep_org_address' value='1' <?php if(array_key_exists('mep_org_address', $values)){ $mep_org_address = $values['mep_org_address'][0]; if($mep_org_address==1){ echo 'checked'; } } ?> > Yes (If Yes, Organizer Address will show from organizer area.)</span>
</div>






<div class='sec'>
    <label for="mep_ev_2"> <?php _e('Location/Venue:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_2' type="text" name='mep_location_venue' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_location_venue'); //if(array_key_exists('mep_location_venue', $values)){ echo $values['mep_location_venue'][0]; } ?>'> </span>
</div>




<div class='sec'>
    <label for="mep_ev_3"> <?php _e('Street:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_3' type="text" name='mep_street' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_street'); //if(array_key_exists('mep_street', $values)){ echo $values['mep_street'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_4"> <?php _e('City:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_4' type="text" name='mep_city' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_city'); //if(array_key_exists('mep_city', $values)){ echo $values['mep_city'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_5"> <?php _e('State:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_5' type="text" name='mep_state' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_state'); //if(array_key_exists('mep_state', $values)){ echo $values['mep_state'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_6"> <?php _e('Postcode:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_6' type="text" name='mep_postcode' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_postcode'); //if(array_key_exists('mep_postcode', $values)){ echo $values['mep_postcode'][0]; } ?>'> </span>
</div>



<div class='sec'>
    <label for="mep_ev_7"> <?php _e('Country:','mage-eventpress'); ?> </label>
    <span><input id='mep_ev_7' type="text" name='mep_country' value='<?php echo mep_get_event_locaion_item($post->ID,'mep_country'); //if(array_key_exists('mep_country', $values)){ echo $values['mep_country'][0]; } ?>'> </span>
</div>
<div class='sec'>
    <label for="mep_ev_989"> <?php _e('Show Google Map:','mage-eventpress'); ?> </label>
    <span><input style='text-align: left;width: auto;' id='mep_ev_989' type="checkbox" name='mep_sgm' value='1' <?php if(array_key_exists('mep_sgm', $values)){ $mep_sgm = $values['mep_sgm'][0]; if($mep_sgm==1){ echo 'checked'; } } ?> > Yes</span>
</div>



<!-- <a id="check_gmap"  type="submit" style="display: block;background: #3496f3;text-align: center;color: #fff;font-size: 19px;padding: 15px 20px;margin: 20px auto;width: 200px;cursor: pointer;">Show Google Map</a> -->
<script type="text/javascript">

jQuery("#mep_ev_9890").click(function(){

  if(jQuery(this).is(':checked')){
  
    jQuery('#mep_ev_2').val('<?php echo mep_event_org_location_item($post->ID,'org_location'); ?>');
    jQuery('#mep_ev_3').val('<?php echo mep_event_org_location_item($post->ID,'org_street'); ?>');
    jQuery('#mep_ev_4').val('<?php echo mep_event_org_location_item($post->ID,'org_city'); ?>');
    jQuery('#mep_ev_5').val('<?php echo mep_event_org_location_item($post->ID,'org_state'); ?>');
    jQuery('#mep_ev_6').val('<?php echo mep_event_org_location_item($post->ID,'org_postcode'); ?>');
    jQuery('#mep_ev_7').val('<?php echo mep_event_org_location_item($post->ID,'org_country'); ?>');
      var location = jQuery('#mep_ev_2').val();
        jQuery('#show_gmap').html('<iframe id="gmap_canvas" src="https://maps.google.com/maps?q='+location+'&t=&z=19&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>')
}else{

    jQuery('#mep_ev_2').val('<?php echo mep_event_location_item($post->ID,'mep_location_venue'); ?>');
    jQuery('#mep_ev_3').val('<?php echo mep_event_location_item($post->ID,'mep_street'); ?>');
    jQuery('#mep_ev_4').val('<?php echo mep_event_location_item($post->ID,'mep_city'); ?>');
    jQuery('#mep_ev_5').val('<?php echo mep_event_location_item($post->ID,'mep_state'); ?>');
    jQuery('#mep_ev_6').val('<?php echo mep_event_location_item($post->ID,'mep_postcode'); ?>');
    jQuery('#mep_ev_7').val('<?php echo mep_event_location_item($post->ID,'mep_country'); ?>');
      var location = jQuery('#mep_ev_2').val();
    jQuery('#show_gmap').html('<iframe id="gmap_canvas" src="https://maps.google.com/maps?q='+location+'&t=&z=19&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>')
  }
})




jQuery('#mep_ev_2').keypress(function(){
// alert('Yes Dudue');
var location = jQuery(this).val();
// var location = jQuery('#mep_ev_2').val();
if(location==''){
  // alert('Please Enter Location First');
}else{
jQuery('#show_gmap').html('<iframe id="gmap_canvas" src="https://maps.google.com/maps?q='+location+'&t=&z=19&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>')
}
  })
</script>
<?php 
if($map_type=='iframe'){
?>
<style type="text/css">
  iframe#gmap_canvas {
    width: 100%;
    height: 300px;
}
</style>
<div id="show_gmap">
  <iframe id="gmap_canvas" src="https://maps.google.com/maps?q=<?php echo mep_get_event_locaion_item($post->ID,'mep_location_venue'); ?>&t=&z=19&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe>
</div>

<?php } if($map_type=='api'){
if($user_api){
?>

<div class='sec'>
<input id="pac-input" name='location_name' value='<?php //echo $values['location_name'][0]; ?>'/>
</div>


<input type="hidden" class="form-control" required name="latitude" value="<?php if(array_key_exists('latitude', $values)){ echo $values['latitude'][0]; } ?>">
<input type="hidden" class="form-control" required name="longitude" value="<?php if(array_key_exists('longitude', $values)){ echo $values['longitude'][0]; } ?>">


<div id="map"></div>

<?php 
}else{
    echo "<span class=mep_status><span class=err>No Google MAP API Key Found. Please enter API KEY <a href=".get_site_url()."/wp-admin/options-general.php?page=mep_event_settings_page>Here</a></span></span>";
}



if(array_key_exists('latitude', $values) && !empty($values['latitude'][0])){
    $lat = $values['latitude'][0];
}else{ $lat = '37.0902'; }


if(array_key_exists('longitude', $values) && !empty($values['longitude'][0])){
    $lon = $values['longitude'][0];
}else{ $lon = '95.7129'; }

?>
<script>


function initMap() {
  var map = new google.maps.Map(document.getElementById('map'), {
    center: {
      lat: <?php echo $lat; ?>,
      lng: <?php echo $lon; ?>
    },
    zoom: 17
  });



  var input = /** @type {!HTMLInputElement} */ (
    document.getElementById('pac-input'));

  var types = document.getElementById('type-selector');
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);
  map.controls[google.maps.ControlPosition.TOP_LEFT].push(types);

  var autocomplete = new google.maps.places.Autocomplete(input);
  autocomplete.bindTo('bounds', map);

  var infowindow = new google.maps.InfoWindow();
  var marker = new google.maps.Marker({
    map: map,
    anchorPoint: new google.maps.Point(0, -29),
    draggable: true,
    position: {lat: <?php echo $lat; ?>, lng: <?php echo $lon; ?>}
  });

  google.maps.event.addListener(marker, 'dragend', function() {
     document.getElementsByName('latitude')[0].value = marker.getPosition().lat();
     document.getElementsByName('longitude')[0].value = marker.getPosition().lng();
  })



  autocomplete.addListener('place_changed', function() {
    infowindow.close();
    marker.setVisible(false);
    var place = autocomplete.getPlace();
    if (!place.geometry) {
      window.alert("Autocomplete's returned place contains no geometry");
      return;
    }

    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
      map.fitBounds(place.geometry.viewport);
    } else {
      map.setCenter(place.geometry.location);
      map.setZoom(17); // Why 17? Because it looks good.
    }
    marker.setIcon( /** @type {google.maps.Icon} */ ({
      url: 'http://maps.google.com/mapfiles/ms/icons/red.png',
      size: new google.maps.Size(71, 71),
      origin: new google.maps.Point(0, 0),
      anchor: new google.maps.Point(17, 34),
      scaledSize: new google.maps.Size(35, 35)
    }));
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);

    var address = '';
    if (place.address_components) {
      address = [
        (place.address_components[0] && place.address_components[0].short_name || ''),
        (place.address_components[1] && place.address_components[1].short_name || ''),
        (place.address_components[2] && place.address_components[2].short_name || '')
      ].join(' ');
    }

    var latitude = place.geometry.location.lat();
    var longitude = place.geometry.location.lng();

    $("input[name=coordinate]").val(address);
    $("input[name=latitude]").val(latitude);
    $("input[name=longitude]").val(longitude);

    //infowindow.setContent('<div><strong>' + place.name + '</strong><br>' + address);
    //infowindow.open(map, marker);
  });
}
google.maps.event.addDomListener(window, "load", initMap);
</script>
<?php
}
}


function mep_event_price_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>


  <table id="" width="100%">
  <thead>
    <tr>
      <th width="20%"><?php _e('Price Label','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Price','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Quantity','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Reserve Qty','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Input Type','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Show Quantity Box','mage-eventpress'); ?></th>
    </tr>
  </thead>
  <tbody>
    <tr>
      <td align="center"><input id='mep_ev_8' type="text" name='mep_price_label' value='<?php if(array_key_exists('mep_price_label', $values)){ echo $values['mep_price_label'][0];} ?>'></td>
      <td align="center"><input style="width: 100px;" id='mep_ev_9' type="number" name='_price' step="0.01" required min='0' value='<?php if(array_key_exists('_price', $values)){ echo $values['_price'][0]; } else{ echo 0; } ?>'></td>
      <td align="center"><input style="width: 100px;" id='mep_ev_1' type="number" name='mep_total_seat' value='<?php if(array_key_exists('mep_total_seat', $values)){ echo $values['mep_total_seat'][0]; } ?>'> </td>      
      <td align="center"><input id='mep_ev_1' style="width: 100px;" type="number" name='mep_rsv_seat' value='<?php if(array_key_exists('mep_rsv_seat', $values)){ echo $values['mep_rsv_seat'][0]; } ?>'> </td>
      <td align="center">  <?php if(array_key_exists('qty_box_type', $values)){ $qty_typec = $values['qty_box_type'][0]; }else{ $qty_typec=""; } ?>
      <select name="qty_box_type" id="mep_ev_9800" class=''>
    <option value="inputbox" <?php if($qty_typec=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_typec=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
      </select></td>
      <td align="center">    <span><input style='text-align: left;width: auto;' id='mep_ev_98' type="checkbox" name='mep_sqi' value='1' <?php if(array_key_exists('mep_sqi', $values)){ $sqi = $values['mep_sqi'][0]; }else{ $sqi =0; } if($sqi==1){ echo 'checked'; } ?> > <?php _e('Yes','mage-eventpress'); ?></span></td>
    </tr>
  </tbody>
</table>







<?php
}


















function mep_event_faq_meta_box_cb() {
  global $post;
  $mep_event_faq = get_post_meta($post->ID, 'mep_event_faq', true);
  wp_nonce_field( 'mep_event_faq_nonce', 'mep_event_faq_nonce' );
  ?>
<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-faq-row' ).on('click', function() {
      var row = $( '.empty-row-faq.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-faq screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-faq-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-faq-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-faq-one" width="100%">

  <tbody>
  <?php
  if ( $mep_event_faq ) :
  foreach ( $mep_event_faq as $field ) {
  ?>
  <tr>
    <td>
    <div id='mep_event_faq_r' class="">
      <input placeholder="FAQ Title" type="text" class="mep-faq-input" value="<?php if($field['mep_faq_title'] != '') echo esc_attr( $field['mep_faq_title'] ); ?>" name="mep_faq_title[]">
      <textarea placeholder="FAQ Contents" name="mep_faq_content[]" id="" cols="50" rows="4" class="mep-faq-input"><?php if($field['mep_faq_content'] != '') echo esc_attr( $field['mep_faq_content'] ); ?></textarea>
      <a class="button remove-faq-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
    </div>
    </td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-faq screen-reader-text">
    <td>
    <div id='mep_event_faq_r' class="">
      <input placeholder="FAQ Title" type="text" class="mep-faq-input" name="mep_faq_title[]">
      <textarea placeholder="<?php _e('FAQ Contents','mage-eventpress'); ?>" name="mep_faq_content[]" id="" cols="50" rows="4" class="mep-faq-input"></textarea>
      <a class="button remove-faq-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
    </div>
      
    </td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-faq-row" class="button" href="#"><?php _e('Add New F.A.Q','mage-eventpress'); ?></a></p>
  
  <?php
}



add_action('save_post', 'mep_event_faq_save');
function mep_event_faq_save($post_id) {
  global $wpdb;
  
  
  if ( ! isset( $_POST['mep_event_faq_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_faq_nonce'], 'mep_event_faq_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  

if (get_post_type($post_id) == 'mep_events') { 


  $old = get_post_meta($post_id, 'mep_event_faq', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['mep_faq_title'];
  $cntent = $_POST['mep_faq_content'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['mep_faq_title'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $cntent[$i] != '' ) :
      $new[$i]['mep_faq_content'] = stripslashes( strip_tags( $cntent[$i] ) );
      endif;

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_event_faq', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_event_faq', $old );
}

}



















function mep_event_day_details_cb() {
  global $post;
  $mep_event_day = get_post_meta($post->ID, 'mep_event_day', true);
  wp_nonce_field( 'mep_event_day_nonce', 'mep_event_day_nonce' );
  ?>
<script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-day-row' ).on('click', function() {
      var row = $( '.empty-row-day.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-day screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-day-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-day-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-day-one" width="100%">
  <tbody>
  <?php
  if ( $mep_event_day ) :
  foreach ( $mep_event_day as $field ) {
  ?>
  <tr>
    <td>
    <div id='mep_event_day_r' class="">
      <input placeholder="Day Title" type="text" class="mep-faq-input" value="<?php if($field['mep_day_title'] != '') echo esc_attr( $field['mep_day_title'] ); ?>" name="mep_day_title[]">
      <textarea placeholder="Day Details" name="mep_day_content[]" id="" cols="50" rows="4" class="mep-faq-input"><?php if($field['mep_day_content'] != '') echo esc_attr( $field['mep_day_content'] ); ?></textarea>
      <a class="button remove-day-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
    </div>
    </td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-day screen-reader-text">
    <td>
    <div id='mep_event_day_r' class="">
      <input placeholder="Day Title" type="text" class="mep-faq-input" name="mep_day_title[]">
      <textarea placeholder="<?php _e('Day Details','mage-eventpress'); ?>" name="mep_day_content[]" id="" cols="50" rows="4" class="mep-faq-input"></textarea>
      <a class="button remove-day-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a>
    </div>
      
    </td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-day-row" class="button" href="#"><?php _e('Add New Day','mage-eventpress'); ?></a></p>
  
  <?php
}



add_action('save_post', 'mep_event_day_data_save');
function mep_event_day_data_save($post_id) {
  global $wpdb;
  
  if ( ! isset( $_POST['mep_event_day_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_day_nonce'], 'mep_event_day_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  

if (get_post_type($post_id) == 'mep_events') { 


  $old = get_post_meta($post_id, 'mep_event_day', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['mep_day_title'];
  $cntent = $_POST['mep_day_content'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['mep_day_title'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $cntent[$i] != '' ) :
      $new[$i]['mep_day_content'] = stripslashes( strip_tags( $cntent[$i] ) );
      endif;

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_event_day', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_event_day', $old );
}

}










function mep_event_extra_price_option() {
  global $post;
  $mep_events_extra_prices = get_post_meta($post->ID, 'mep_events_extra_prices', true);
  wp_nonce_field( 'mep_events_extra_price_nonce', 'mep_events_extra_price_nonce' );
  ?>
  <script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-row' ).on('click', function() {
      var row = $( '.empty-row.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-one tbody>tr:last' );
      return false;
    });
    
    $( '.remove-row' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-one" width="100%">
  <thead>
    <tr>
      <th width="30%"><?php _e('Extra Service Name','mage-eventpress'); ?></th>
      <th width="30%"><?php _e('Service Price','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Available Qty','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Qty Box Type','mage-eventpress'); ?></th>
      <th width="10%"></th>
    </tr>
  </thead>
  <tbody>
  <?php
  
  if ( $mep_events_extra_prices ) :
  
  foreach ( $mep_events_extra_prices as $field ) {
    $qty_type = esc_attr( $field['option_qty_type'] );
  ?>
  <tr>
    <td><input type="text" class="widefat" name="option_name[]" value="<?php if($field['option_name'] != '') echo esc_attr( $field['option_name'] ); ?>" /></td>

    <td><input type="number" step="0.01" class="widefat" name="option_price[]" value="<?php if ($field['option_price'] != '') echo esc_attr( $field['option_price'] ); else echo ''; ?>" /></td>

    <td><input type="number" class="widefat" name="option_qty[]" value="<?php if ($field['option_qty'] != '') echo esc_attr( $field['option_qty'] ); else echo ''; ?>" /></td>

 <td align="center">
<select name="option_qty_type[]" id="mep_ev_9800kj8" class=''>
    <option value="inputbox" <?php if($qty_type=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_type=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select>
    </td> 
    <td><a class="button remove-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row screen-reader-text">
    <td><input type="text" class="widefat" name="option_name[]" /></td>
    <td><input type="number" class="widefat" step="0.01" name="option_price[]" value="" /></td>
    <td><input type="number" class="widefat" name="option_qty[]" value="" /></td>
    
<td><select name="option_qty_type[]" id="mep_ev_9800kj8" class=''>
  <option value=""><?php _e('Please Select Type','mage-eventpress'); ?></option>
    <option value="inputbox"><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown"><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select></td>
    <td><a class="button remove-row" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
    
  </tr>
  </tbody>
  </table>
  <p><a id="add-row" class="button" href="#"><?php _e('Add Extra Price','mage-eventpress'); ?></a></p>
  <?php
}














function mep_event_ticket_type() {
  global $post;
  $mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
  wp_nonce_field( 'mep_event_ticket_type_nonce', 'mep_event_ticket_type_nonce' );
  ?>
  <script type="text/javascript">
  jQuery(document).ready(function( $ ){
    $( '#add-row-t' ).on('click', function() {
      var row = $( '.empty-row-t.screen-reader-text' ).clone(true);
      row.removeClass( 'empty-row-t screen-reader-text' );
      row.insertBefore( '#repeatable-fieldset-one-t tbody>tr:last' );
      return false;
    });
    
    $( '.remove-row-t' ).on('click', function() {
      $(this).parents('tr').remove();
      return false;
    });
  });
  </script>
  
  <table id="repeatable-fieldset-one-t" width="100%">
  <thead>
    <tr>
      <th width="30%"><?php _e('Ticket Type Name','mage-eventpress'); ?></th>
      <th width="30%"><?php _e('Ticket Price','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Available Qty','mage-eventpress'); ?></th>
      <th width="20%"><?php _e('Reserve Qty','mage-eventpress'); ?></th>
      <th width="10%"><?php _e('Qty Box Type','mage-eventpress'); ?></th>
      <th width="10%"></th>
    </tr>
  </thead>
  <tbody>
  <?php
  
  if ( $mep_event_ticket_type ) :
  
  foreach ( $mep_event_ticket_type as $field ) {
    $qty_t_type = esc_attr( $field['option_qty_t_type'] );
  ?>
  <tr>
    <td><input type="text" class="widefat" name="option_name_t[]" value="<?php if($field['option_name_t'] != '') echo esc_attr( $field['option_name_t'] ); ?>" /></td>

    <td><input type="number" step="0.01" class="widefat" name="option_price_t[]" value="<?php if ($field['option_price_t'] != '') echo esc_attr( $field['option_price_t'] ); else echo ''; ?>" /></td>

    <td><input type="number" class="widefat" name="option_qty_t[]" value="<?php if ($field['option_qty_t'] != '') echo esc_attr( $field['option_qty_t'] ); else echo ''; ?>" /></td>

    <td><input type="number" class="widefat" name="option_rsv_t[]" value="<?php if ($field['option_rsv_t'] != '') echo esc_attr( $field['option_rsv_t'] ); else echo ''; ?>" /></td>

<td><select name="option_qty_t_type[]" id="mep_ev_9800kj8" class=''>
    <option value="inputbox" <?php if($qty_t_type=='inputbox'){ echo "Selected"; } ?>><?php _e('Input Box','mage-eventpress'); ?></option>
    <option value="dropdown" <?php if($qty_t_type=='dropdown'){ echo "Selected"; } ?>><?php _e('Dropdown List','mage-eventpress'); ?></option>
</select></td>

    <td><a class="button remove-row-t" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  <?php
  }
  else :
  // show a blank one
 endif; 
 ?>
  
  <!-- empty hidden one for jQuery -->
  <tr class="empty-row-t screen-reader-text">
    <td><input type="text" class="widefat" name="option_name_t[]" /></td>
    <td><input type="number" class="widefat" step="0.01" name="option_price_t[]" value="" /></td>
    <td><input type="number" class="widefat" name="option_qty_t[]" value="" /></td>
    <td><input type="number" class="widefat" name="option_rsv_t[]" value="" /></td>
    <td><select name="option_qty_t_type[]" id="mep_ev_9800kj8" class=''><option value=""><?php _e('Please Select Type','mage-eventpress'); ?></option><option value="inputbox"><?php _e('Input Box','mage-eventpress'); ?></option><option value="dropdown"><?php _e('Dropdown List','mage-eventpress'); ?></option></select></td>    
    <td><a class="button remove-row-t" href="#"><?php _e('Remove','mage-eventpress'); ?></a></td>
  </tr>
  </tbody>
  </table>
  <p><a id="add-row-t" class="button" href="#"><?php _e('Add New Ticket Type','mage-eventpress'); ?></a></p>
  <?php
}













function mep_event_date_meta_box_cb($post){
$values = get_post_custom( $post->ID );
$more_date   = get_post_meta($post->ID, 'mep_event_more_date', true);
// print_r($values);
?>

<div class='sec'>
    <label for="event_start_date"> <?php _e('Start Date & Time:','mage-eventpress'); ?> </label>
    <span><input class='event_start' id='event_start_date' type="text" name='mep_event_start_date' value='<?php if(array_key_exists('mep_event_start_date', $values)){ echo $values['mep_event_start_date'][0]; } ?>'> </span>
</div>


<div class="sec">
<table width="100%" class="mama">
<tr >
     <td><input placeholder="Day 2 (Use this only if you have same event multiple day)" type="text" class='event_more_date' name="event_more_date[]" value="<?php if(array_key_exists('mep_event_more_date', $values)){ echo $more_date[0]['event_more_date']; } ?>" /></td>
</tr>
<tr >
     <td><input placeholder="Day 3(Use this only if you have same event multiple day)" type="text" class='event_more_date' name="event_more_date[]" value="<?php if(array_key_exists('mep_event_more_date', $values)){ echo $more_date[1]['event_more_date']; } ?>" /></td>
</tr>
<tr >
     <td><input placeholder="Day 4(Use this only if you have same event multiple day)" type="text" class='event_more_date' name="event_more_date[]" value="<?php if(array_key_exists('mep_event_more_date', $values)){ echo $more_date[2]['event_more_date']; } ?>" /></td>
</tr>
</table>
</div>  



<div class='sec'>
    <label for="event_end_date"> <?php _e('End Date & Time:','mage-eventpress'); ?> </label>
    <span><input class='event_end' id='event_end_date' type="text" name='mep_event_end_date' value='<?php if(array_key_exists('mep_event_end_date', $values)){ echo $values['mep_event_end_date'][0]; } ?>'> </span>
</div>
<?php
}



function mep_event_email_meta_box_cb($post){
$values = get_post_custom( $post->ID );
?>
<div class='sec'>
    <label for="event_start_date"> <?php _e('Confirmation Email Text:','mage-eventpress'); ?> </label>
    <span><textarea style='border: 1px solid #ddd;width: 100%;min-height: 200px;margin: 10px 0;padding: 5px;' class='' id='' type="text" name='mep_event_cc_email_text'><?php if(array_key_exists('mep_event_cc_email_text', $values)){ echo $values['mep_event_cc_email_text'][0]; } ?></textarea> </span>
</div>
<?php
}








function mep_event_template_meta_box_cb($post){
$values = get_post_custom( $post->ID );
$global_template = mep_get_option( 'mep_global_single_template', 'general_setting_sec', 'theme-2');
if(array_key_exists('mep_event_template', $values)){
$current_template = $values['mep_event_template'][0];
}else{
    $current_template='';
}
if($current_template){
  $_current_template = $current_template;
}else{
  $_current_template = $global_template;
}
?>
<div class='sec'>
    <span><?php event_single_template_list($_current_template); ?></span>
</div>
<?php
}

















add_action('save_post', 'mep_events_ticket_type_save');
function mep_events_ticket_type_save($post_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'mep_event_ticket_type';
  
  if ( ! isset( $_POST['mep_event_ticket_type_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_event_ticket_type_nonce'], 'mep_event_ticket_type_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  

if (get_post_type($post_id) == 'mep_events') { 

  $old = get_post_meta($post_id, 'mep_event_ticket_type', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['option_name_t'];
  // $selects = $_POST['select'];
  $urls = $_POST['option_price_t'];
  $qty = $_POST['option_qty_t'];
  $rsv = $_POST['option_rsv_t'];
  $qty_type = $_POST['option_qty_t_type'];
  // $required = $_POST['option_required_t'];
  // $total_sold = $_POST['option_sold'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['option_name_t'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $urls[$i] != '' ) :
      $new[$i]['option_price_t'] = stripslashes( strip_tags( $urls[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty_t'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_rsv_t'] = stripslashes( strip_tags( $rsv[$i] ) );
      endif;

    if ( $qty_type[$i] != '' ) :
      $new[$i]['option_qty_t_type'] = stripslashes( strip_tags( $qty_type[$i] ) );
      endif;

    // if ( $required[$i] != '' ) :
    //   $new[$i]['option_required_t'] = stripslashes( strip_tags( $required[$i] ) );
    //   endif;

 

    $opt_name =  $post_id.str_replace(' ', '', $names[$i]);

    // update_post_meta( $post_id, "mep_xtra_$opt_name",0 );

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_event_ticket_type', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_event_ticket_type', $old );
}
}











add_action('save_post', 'mep_events_repeatable_meta_box_save');
function mep_events_repeatable_meta_box_save($post_id) {
  global $wpdb;
  $table_name = $wpdb->prefix . 'event_extra_options';
  if ( ! isset( $_POST['mep_events_extra_price_nonce'] ) ||
  ! wp_verify_nonce( $_POST['mep_events_extra_price_nonce'], 'mep_events_extra_price_nonce' ) )
    return;
  
  if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
    return;
  
  if (!current_user_can('edit_post', $post_id))
    return;
  
if (get_post_type($post_id) == 'mep_events') { 


  $old = get_post_meta($post_id, 'mep_events_extra_prices', true);
  $new = array();
  // $options = hhs_get_sample_options();
  
  $names = $_POST['option_name'];
  // $selects = $_POST['select'];
  $urls = $_POST['option_price'];
  $qty = $_POST['option_qty'];
  $qty_type = $_POST['option_qty_type'];
  // $required = $_POST['option_required'];
  // $total_sold = $_POST['option_sold'];

  $order_id = 0;
  $count = count( $names );
  
  for ( $i = 0; $i < $count; $i++ ) {
    
    if ( $names[$i] != '' ) :
      $new[$i]['option_name'] = stripslashes( strip_tags( $names[$i] ) );
      endif;

    if ( $urls[$i] != '' ) :
      $new[$i]['option_price'] = stripslashes( strip_tags( $urls[$i] ) );
      endif;

    if ( $qty[$i] != '' ) :
      $new[$i]['option_qty'] = stripslashes( strip_tags( $qty[$i] ) );
      endif;

    if ( $qty_type[$i] != '' ) :
      $new[$i]['option_qty_type'] = stripslashes( strip_tags( $qty_type[$i] ) );
      endif;

    // if ( $required[$i] != '' ) :
    //   $new[$i]['option_required'] = stripslashes( strip_tags( $required[$i] ) );
    //   endif;

 

    $opt_name =  $post_id.str_replace(' ', '', $names[$i]);

    // update_post_meta( $post_id, "mep_xtra_$opt_name",0 );

  }

  if ( !empty( $new ) && $new != $old )
    update_post_meta( $post_id, 'mep_events_extra_prices', $new );
  elseif ( empty($new) && $old )
    delete_post_meta( $post_id, 'mep_events_extra_prices', $old );
}

}








add_action('save_post','mep_events_meta_save');
function mep_events_meta_save($post_id){
    global $post; 
if($post){
    $pid = $post->ID;
    if ($post->post_type != 'mep_events'){
        return;
    }
}else{
    $pid='';
}

if (get_post_type($post_id) == 'mep_events') { 

  $oldm = get_post_meta($post_id, 'mep_event_more_date', true);
  $mdate = array();
  // $options = hhs_get_sample_options();
  
if(isset($_POST['event_more_date'])){$more_date = $_POST['event_more_date'];}else{$more_date=array();}

$mcount = count( $more_date );
  for ( $m = 0; $m < $mcount; $m++ ) {
    if ( $more_date[$m] != '' ) :
      $mdate[$m]['event_more_date'] = stripslashes( strip_tags( $more_date[$m] ) );
      endif;
}
if ( !empty( $mdate ) && $mdate != $oldm )
    update_post_meta( $post_id, 'mep_event_more_date', $mdate );
elseif ( empty($mdate) && $oldm )
    delete_post_meta( $post_id, 'mep_event_more_date', $oldm );













    //if you get here then it's your post type so do your thing....
    if(isset($_POST['mep_total_seat'])){
    $seat                           = strip_tags($_POST['mep_total_seat']);
    $rsvs                           = strip_tags($_POST['mep_rsv_seat']);
    $mep_location_venue             = strip_tags($_POST['mep_location_venue']);
    $mep_street                     = strip_tags($_POST['mep_street']);
    $mep_city                       = strip_tags($_POST['mep_city']);
    $mep_state                      = strip_tags($_POST['mep_state']);
    $mep_postcode                   = strip_tags($_POST['mep_postcode']);
    $mep_country                    = strip_tags($_POST['mep_country']);
    $mep_price_label                = strip_tags($_POST['mep_price_label']);
    $mep_sqi                        = strip_tags($_POST['mep_sqi']);
    $qty_box_type                   = strip_tags($_POST['qty_box_type']);
    $mep_sgm                        = strip_tags($_POST['mep_sgm']);
    $mep_org_address                = strip_tags($_POST['mep_org_address']);
    $_price                         = strip_tags($_POST['_price']);
    $mep_event_start_date           = strip_tags($_POST['mep_event_start_date']);
    $mep_event_end_date             = strip_tags($_POST['mep_event_end_date']);
    $mep_event_cc_email_text             = strip_tags($_POST['mep_event_cc_email_text']);

    $latitude                       = strip_tags($_POST['latitude']);
    $longitude                      = strip_tags($_POST['longitude']);
    $location_name                  = strip_tags($_POST['location_name']);

    $mep_full_name                  = strip_tags($_POST['mep_full_name']);
    $mep_reg_email                  = strip_tags($_POST['mep_reg_email']);
    $mep_reg_phone                  = strip_tags($_POST['mep_reg_phone']);
    $mep_reg_address                = strip_tags($_POST['mep_reg_address']);
    $mep_reg_designation            = strip_tags($_POST['mep_reg_designation']);
    $mep_reg_website                = strip_tags($_POST['mep_reg_website']);
    $mep_reg_veg                    = strip_tags($_POST['mep_reg_veg']);
    $mep_reg_company                = strip_tags($_POST['mep_reg_company']);
    $mep_reg_gender                 = strip_tags($_POST['mep_reg_gender']);
    $mep_reg_tshirtsize             = strip_tags($_POST['mep_reg_tshirtsize']);
    $mep_reg_tshirtsize_list        = strip_tags($_POST['mep_reg_tshirtsize_list']);
    $mep_event_template             = strip_tags($_POST['mep_event_template']);



$update_reg_name        = update_post_meta( $pid, 'mep_full_name', $mep_full_name);
$update_reg_email       = update_post_meta( $pid, 'mep_reg_email', $mep_reg_email);
$update_reg_phone       = update_post_meta( $pid, 'mep_reg_phone', $mep_reg_phone);
$update_reg_address     = update_post_meta( $pid, 'mep_reg_address', $mep_reg_address);
$update_reg_desg        = update_post_meta( $pid, 'mep_reg_designation', $mep_reg_designation);
$update_reg_web         = update_post_meta( $pid, 'mep_reg_website', $mep_reg_website);
$update_reg_veg         = update_post_meta( $pid, 'mep_reg_veg', $mep_reg_veg);
$update_reg_comapny     = update_post_meta( $pid, 'mep_reg_company', $mep_reg_company);
$update_reg_gender      = update_post_meta( $pid, 'mep_reg_gender', $mep_reg_gender);
$update_tshirtsize      = update_post_meta( $pid, 'mep_reg_tshirtsize', $mep_reg_tshirtsize);
$mep_reg_tshirtsize_list      = update_post_meta( $pid, 'mep_reg_tshirtsize_list', $mep_reg_tshirtsize_list);
$update_template        = update_post_meta( $pid, 'mep_event_template', $mep_event_template);
$update_mep_org_address        = update_post_meta( $pid, 'mep_org_address', $mep_org_address);





$mep_event_ticket_type = get_post_meta($pid, 'mep_event_ticket_type', true);


if($mep_event_ticket_type){
  $st_msg = 'no';
  $seat = "";
  $_price =0;
  $rsvs =0;
}else{
  $st_msg = 'yes';
  $_price = $_price;
    $seat = $seat;
    $rsvs = $rsvs;

}




    $update_seat                    = update_post_meta( $pid, 'mep_total_seat', $seat);
    $update_seat                    = update_post_meta( $pid, 'mep_rsv_seat', $rsvs);

    $update_seat_stock_status       = update_post_meta( $pid, '_manage_stock', $st_msg);

    $update_seat_stock          = update_post_meta( $pid, '_stock', $seat);
$sts_msg = update_post_meta( $pid, '_stock_msg', 'new');
// $ttl_booking = update_post_meta( $pid, 'total_booking', '0');

    $longitude              = update_post_meta( $pid, 'longitude', $longitude);
    $latitude               = update_post_meta( $pid, 'latitude', $latitude);
    $location_name          = update_post_meta( $pid, 'location_name', $location_name);



    $update_location        = update_post_meta( $pid, 'mep_location_venue', $mep_location_venue);
    $update_mep_street      = update_post_meta( $pid, 'mep_street', $mep_street);
    $update_seat_stock_status         = update_post_meta( $pid, '_sold_individually', 'no');
    $update_city          = update_post_meta( $pid, 'mep_city', $mep_city);
    $update_mep_state       = update_post_meta( $pid, 'mep_state', $mep_state);
    $update_postcode        = update_post_meta( $pid, 'mep_postcode', $mep_postcode);
    $update_conuntry        = update_post_meta( $pid, 'mep_country', $mep_country);
    $update_sqi     = update_post_meta( $pid, 'mep_sqi', $mep_sqi);
    $qty_box_type     = update_post_meta( $pid, 'qty_box_type', $qty_box_type);
    $update_mep_sgm     = update_post_meta( $pid, 'mep_sgm', $mep_sgm);
    $update_price_label     = update_post_meta( $pid, 'mep_price_label', $mep_price_label);
    $update_price         = update_post_meta( $pid, '_price', $_price);
    $update_start         = update_post_meta( $pid, 'mep_event_start_date', $mep_event_start_date);
    $update_virtual       = update_post_meta( $pid, '_virtual', 'yes');
    $update_end           = update_post_meta( $pid, 'mep_event_end_date', $mep_event_end_date);
    $mep_event_cc_email_text           = update_post_meta( $pid, 'mep_event_cc_email_text', $mep_event_cc_email_text);
    $mep_event_sku          = update_post_meta( $pid, '_sku', $pid);
    }
}



}

add_action( 'add_meta_boxes', 'mep_meta_box_add' );
function mep_meta_box_add(){
    add_meta_box( 'my-meta-box-id', 'Information', 'mep_meta_box_cb', 'mep_events_attendees', 'normal', 'high' );
}




function mep_meta_box_cb($post){
$values = get_post_custom( $post->ID );

$event_meta           = get_post_custom($values['ea_event_id'][0]);
$ticket_type           = get_post_meta( $post->ID, 'ea_ticket_type', true );

    $mep_full_name         = strip_tags($event_meta['mep_full_name'][0]);
    $mep_reg_email         = strip_tags($event_meta['mep_reg_email'][0]);
    $mep_reg_phone         = strip_tags($event_meta['mep_reg_phone'][0]);
    $mep_reg_address       = strip_tags($event_meta['mep_reg_address'][0]);
    $mep_reg_designation   = strip_tags($event_meta['mep_reg_designation'][0]);
    $mep_reg_website       = strip_tags($event_meta['mep_reg_website'][0]);
    $mep_reg_veg           = strip_tags($event_meta['mep_reg_veg'][0]);
    $mep_reg_company       = strip_tags($event_meta['mep_reg_company'][0]);
    $mep_reg_gender        = strip_tags($event_meta['mep_reg_gender'][0]);
    $mep_reg_tshirtsize    = strip_tags($event_meta['mep_reg_tshirtsize'][0]);




    $order      = wc_get_order( $values['ea_order_id'][0] );

    foreach ( $order->get_items() as $item_id => $item_values ) {
        $product_id     = $item_values->get_product_id(); 
        $item_data      = $item_values->get_data();
        $product_id     = $item_data['product_id'];
        $item_quantity  = $item_values->get_quantity();
        $product        = get_page_by_title( $item_data['name'], OBJECT, 'mep_events' );
        $event_name     = $item_data['name'];
        $event_id       = $product->ID;
        $item_id        = $item_id;
    }

$extra_info_arr = wc_get_order_item_meta($item_id,'_event_service_info',true);






?>

<div class="mep-attendee-sec-details">
<div class='sec'>
    <span class="ea-label"><?php _e('Event:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_event_name'][0]; ?> </span>
</div>

<div class='sec'>
    <span class="ea-label"><?php _e('UserID:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_user_id'][0]; ?></span>
</div>


<?php if($mep_full_name){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Full Name:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_name'][0]; ?></span>
</div>
<?php } if($mep_reg_email){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Email:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_email'][0]; ?></span>  
</div>
<?php } if($mep_reg_phone){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Phone:','mage-eventpress'); ?> </span>
    <span class="ea-value"><?php echo $values['ea_phone'][0]; ?></span>  
</div>
<?php } if($mep_reg_address){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Addres:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_address_1'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_gender){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Gender:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_gender'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_company){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Company:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_company'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_designation){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Designation:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_desg'][0]; ?>  
    </span>  
</div>
<?php } if($mep_reg_website){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Website:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_website'][0]; ?>  
    </span>  
</div>

<?php } if($mep_reg_veg){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('Vegetarian?:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_vegetarian'][0]; ?>  
    </span>  
</div>

<?php } if($mep_reg_tshirtsize){ ?>
<div class='sec'>
    <span class="ea-label"><?php _e('T-Shirt Size?:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_tshirtsize'][0]; ?>  
    </span>  
</div>
<?php } ?>

<?php 
$mep_form_builder_data = get_post_meta($values['ea_event_id'][0], 'mep_form_builder_data', true);
  if ( $mep_form_builder_data ) {
    foreach ( $mep_form_builder_data as $_field ) {
        // if ( $mep_user_ticket_type[$iu] != '' ) :
        //   $user[$iu][$_field['mep_fbc_id']] = stripslashes( strip_tags( $_POST[$_field['mep_fbc_id']][$iu] ) );
        //   endif; 

?>
<div class='sec'>
    <span class="ea-label"><?php echo $_field['mep_fbc_label']; ?>:</span>
    <span class="ea-value">
    <?php $vname = "ea_".$_field['mep_fbc_id']; echo $values[$vname][0]; ?>  
    </span>  
</div>
<?php

    }
  }


foreach ($extra_info_arr as $_exs) {
if($_exs['option_qty']>0){
    $rs[] = $_exs['option_name']." (".$_exs['option_qty'].")";
    if($ticket_type != $_exs['option_name']){
    ?>
<div class='sec'>
    <span class="ea-label"><?php $_exs['option_name']; ?> </span>
    <span class="ea-value">
    <?php echo $_exs['option_qty']; ?>  
    </span>  
</div>
    <?php
}
}
}
?>




<div class='sec'>
    <span class="ea-label"><?php _e('Order ID:','mage-eventpress'); ?> </span>
    <span class="ea-value">
    <?php echo $values['ea_order_id'][0]; ?>  
    </span>  
</div>
</div>
<div class='sec'>
    <span>
    <a href="<?php echo get_site_url(); ?>/wp-admin/post.php?post=<?php echo $values['ea_order_id'][0]; ?>&action=edit" class='button button-primary button-large'><?php _e('View Order','mage-eventpress'); ?></a>
    </span>  
</div>
<?php
}