<?php 
add_action('mep_add_to_cart','mep_get_event_reg_btn');

// Get Event Registration Button
function mep_get_event_reg_btn(){
    global $post,$event_meta;
    $post_id = $post->ID;
    $event_meta           = get_post_custom($post_id);
    $event_expire_date    = $event_meta['mep_event_start_date'][0];
    $event_sqi            = $event_meta['mep_sqi'][0];
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
    $event_ecternal_link   = '';
    $book_count            = get_post_meta($post_id,'total_booking', true);

    if($book_count){ $total_book = $book_count; }else{ $total_book = 0; } 

if(array_key_exists('mep_rsv_seat', $event_meta)){
$simple_rsv = $event_meta['mep_rsv_seat'][0];
}else{
$simple_rsv = '';
}
    if($simple_rsv){
      $simple_rsv = $simple_rsv;
    }else{
      $simple_rsv = 0;
    }
    $total_book = ($total_book + $simple_rsv);
    $seat_left  = ((int)$event_meta['mep_total_seat'][0]- (int)$total_book);
    $time       = strtotime($event_expire_date);
    $newformat  = date('Y-m-d H:i:s',$time);
    $datetime1  = new DateTime();
    $datetime2  = new DateTime($newformat);
    $interval   = $datetime1->diff($datetime2);
    $mep_event_ticket_type = get_post_meta($post_id, 'mep_event_ticket_type', true);

    $stc        = 0;
    $leftt      = 0;
if (is_array($mep_event_ticket_type) || is_object($mep_event_ticket_type)){
foreach ($mep_event_ticket_type as $field ) {
  $qm = $field['option_name_t'];
  $tesqn = $post_id.str_replace(' ', '', $qm);
  $tesq = get_post_meta($post_id,"mep_xtra_$tesqn",true);
  $stc = $stc+$field['option_qty_t'];
  $llft = ($field['option_qty_t'] - (int)$tesq);
  $leftt = $leftt+$llft;
}
}else{$qm='';}
if($mep_event_ticket_type){
  $seat_left = $leftt;
}else{
  $seat_left = $seat_left;
}
if(time() > strtotime($newformat)){
  ?>
    <span class=event-expire-btn>
    <?php _e('Event Expired','mage-eventpress'); ?>
    </span>
    <?php
  }
elseif($seat_left<=0){
  ?>
  <span class=event-expire-btn>
   <?php  _e('No Seat Available','mage-eventpress');  ?>
  </span>
    <?php
    do_action('mep_after_no_seat_notice');
  }
else{

if(array_key_exists('mep_rsv_seat', $event_meta)){
$simple_rsv = $event_meta['mep_rsv_seat'][0];
}else{
$simple_rsv = '';
}
  if($simple_rsv){
    $simple_rsv = $simple_rsv;
  }else{
    $simple_rsv = 0;
  }
  $total_book = ($total_book + $simple_rsv);

  $mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);


  if(array_key_exists('mep_available_seat', $event_meta)){ 
    $mep_available_seat = $event_meta['mep_available_seat'][0];
  }else{
    $mep_available_seat = 'on';
  }

if($mep_event_ticket_type){

  $stc    = 0;
  $leftt  = 0;
  $res    = 0;

foreach ( $mep_event_ticket_type as $field ) {
    $qm = $field['option_name_t'];
    $tesqn = $post->ID.str_replace(' ', '', $qm);
    $tesq = get_post_meta($post->ID,"mep_xtra_$tesqn",true);
    $stc = $stc+$field['option_qty_t'];
    $res = $res + (int)$field['option_rsv_t'];
    $res = (int)$res;
    $llft = ($field['option_qty_t'] - (int)$tesq);
    $leftt = ($leftt+$llft);
  }
  $leftt = $leftt-$res;
}else{
  $leftt = $event_meta['mep_total_seat'][0]- $total_book;
}

  $days = $interval->d;
  $hours = $interval->h;
  $minutes = $interval->i;
  if($days>0){ $dd = $days." days "; }else{ $dd=""; }
  if($hours>0){ $hh = $hours." hours "; }else{ $hh=""; }
  if($minutes>0){ $mm = $minutes." minutes "; }else{ $mm=""; }


  $qty_typec = $event_meta['qty_box_type'][0];
if(array_key_exists('mep_reg_status', $event_meta)){ 
    $reg_status = $event_meta['mep_reg_status'][0];
  }else{
    $reg_status = '';
}
if($reg_status!='off'){
  ?>
  <h4 class="mep-cart-table-title">
    <?php echo mep_get_option('mep_register_now_text', 'label_setting_sec') ? mep_get_option('mep_register_now_text', 'label_setting_sec') : _e('Register Now:','mage-eventpress');  ?>

  </h4>
<form action="" method='post'>
<?php 
  do_action('mep_event_ticket_type_extra_service');  
  if($leftt>0){
?>
<input type='hidden' id='rowtotal' value="<?php echo get_post_meta($post_id,"_price",true); ?>"/>
<table>
<tr>
<td align="left" class='total-col'><?php echo mep_get_option('mep_quantity_text', 'label_setting_sec') ? mep_get_option('mep_quantity_text', 'label_setting_sec') : _e('Quantity:','mage-eventpress');
$mep_event_ticket_type = get_post_meta($post_id, 'mep_event_ticket_type', true);
if($mep_event_ticket_type){ ?>
 <input id="quantity_5a7abbd1bff73" class="input-text qty text extra-qty-box" step="1" min="1" max="<?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="hidden">
 <span id="ttyttl"></span>
<?php
}else{
if($event_sqi==1){ 
  $qmx = ($event_meta['mep_total_seat'][0]- $total_book);
  if($qty_typec=='dropdown'){ ?>
  <select name="quantity" id="quantity_5a7abbd1bff73" class='input-text qty text extra-qty-box'>
    <?php for ($i = 1; $i <= $qmx; $i++) { ?>
      <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
    <?php } ?>  
  </select>
<?php }else{ ?> 
  <input id="quantity_5a7abbd1bff73" class="input-text qty text extra-qty-box" step="1" min="1" max="<?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="number">
<?php 
} 
}else{ 
  echo 1; 
?>
  <input id="quantity_5a7abbd1bff73" class="input-text qty text extra-qty-box" step="1" min="1" max="<?php echo ($event_meta['mep_total_seat'][0]- $total_book); ?>" name="quantity" value="1" title="Qty" size="4" pattern="[0-9]*" inputmode="numeric" type="hidden">
<?php
}
}
?> 
  <span class='the-total'>
<?php echo mep_get_option('mep_total_text', 'label_setting_sec') ? mep_get_option('mep_total_text', 'label_setting_sec') : _e('Total','mage-eventpress');  ?>
  <span id="usertotal"></span></span>
</td>
<td align="right">
  <input type="hidden" name="mep_event_location_cart" value="<?php trim(mep_ev_location_ticket($post_id,$event_meta)); ?>"> 
  <input type="hidden" name="mep_event_date_cart" value="<?php do_action('mep_event_date'); ?>"> 
  <p class="mep_no_tickt_notice" id="mep_btn_notice">
  <?php echo mep_get_option('mep_no_ticket_selected_text', 'label_setting_sec') ? mep_get_option('mep_no_ticket_selected_text', 'label_setting_sec') : _e('No Ticket Selected!','mage-eventpress');  ?>
  </p>
  <button type="submit" name="add-to-cart" value="<?php echo esc_attr($post_id); ?>" class="single_add_to_cart_button button alt btn-mep-event-cart"><?php _e(mep_get_label($post_id,'mep_cart_btn_text','Register This Event'),'mage-eventpress'); ?> </button>
</td>
</tr>
</table>
<?php 
$mep_event_ticket_type = get_post_meta($post->ID, 'mep_event_ticket_type', true);
if(!$mep_event_ticket_type){
    if($qm){$qm=$qm;}else{$qm='';}
?><?php do_action('mep_single_page_reg'); ?><?php } } ?>
</form><?php
}
}
}