<?php 

add_action('mep_event_extra_service','mep_ev_extra_serv');

function mep_ev_extra_serv(){
global $post, $product;
$pid = $post->ID;
$count=1;

$mep_events_extra_prices = get_post_meta($post->ID, 'mep_events_extra_prices', true);
  if ( $mep_events_extra_prices ){
echo "<h3 class='ex-sec-title'>".mep_get_label($pid,'mep_event_extra_service_text','Extra Service:')."</h3>";   
    ?>
<table>    
<tr>
<td align="left"><?php _e('Name','mage-eventpress'); ?></td>
<td><?php _e('Quantity','mage-eventpress'); ?></td>
<td><?php _e('Price','mage-eventpress'); ?></td>
</tr>
<?php
foreach ($mep_events_extra_prices as $field) {
  $total_ext = (int) $field['option_qty'];
  $opt_name =  $pid.str_replace(' ', '', $field['option_name']);
  $tes = (int) get_post_meta($pid,"mep_xtra_$opt_name",true);
  $ext_left = ($total_ext-$tes);
  $qty_type = $field['option_qty_type'];
?>
<tr>
<td align="Left"><?php echo $field['option_name']; ?>
  <div class="xtra-item-left"><?php echo $ext_left; ?> <?php _e('Left','mage-eventpress'); ?></div>
</td>
<td>


<?php 
if($ext_left>0){
if($qty_type=='dropdown'){ ?>
<select name="option_qty[]" id="eventpxtp_<?php //echo $count; ?>" class='extra-qty-box'>
  <?php for ($i = 0; $i <= $ext_left; $i++) { ?>
    <option value="<?php echo $i; ?>"><?php echo $i; ?> <?php echo $field['option_name']; ?></option>
  <?php } ?>  
</select>
<?php }else{ ?> 
<input id="eventpx" <?php //if($ext_left<=0){ echo "disabled"; } ?> size="4" pattern="[0-9]*" inputmode="numeric" type="number" class='extra-qty-box' name='option_qty[]' data-price='<?php echo $field['option_price']; ?>' value='0' min="0" max="<?php echo $ext_left; ?>">
<?php } }else{ _e('Not Available','mage-eventpress'); }?>
</td>
<td><?php echo wc_price($field['option_price']); if($ext_left>0){ ?>
  <p style="display: none;" class="price_jq"><?php echo $field['option_price']; ?></p>
  <input type="hidden" name='option_name[]' value='<?php echo $field['option_name']; ?>'>
<input type="hidden" name='option_price[]' value='<?php echo $field['option_price']; ?>'>
<?php } ?>
</td>
</tr>
<?php
$count++;
}

?>
</table>
<?php
}

}