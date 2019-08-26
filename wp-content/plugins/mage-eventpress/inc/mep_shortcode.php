<?php
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

add_shortcode( 'event-calendar', 'mep_event_calender' );
function mep_event_calender($atts, $content=null){
?>


<div class="event-calendar"></div>

<script>
jQuery(document).ready( function() {	
const myEvents = [
	<?php
	$now = date('Y-m-d H:i:s');
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events' ),
                     'posts_per_page'   => -1,
                     'meta_query' => array(
                        array(
                            'key'       => 'mep_event_start_date',
                            'value'     => $now,
                            'compare'   => '>'
                        )
                    )                      

                ); 	
 
	$loop = new WP_Query($args_search_qqq);
	$i = 1;
	$count = $loop->post_count-1;

	while ($loop->have_posts()) {
	$loop->the_post(); 
	$event_meta = get_post_custom(get_the_id());
	$author_terms = get_the_terms(get_the_id(), 'mep_org');
	$time = strtotime($event_meta['mep_event_start_date'][0]);
  	$newformat = date('Y-m-d H:i:s',$time);

// echo $newformat;
 // if(time() < strtotime($newformat)){
	?>
	{
		start: '<?php echo date('Y-m-d H:i',strtotime($event_meta['mep_event_start_date'][0])); ?>',
		end: '<?php echo date('Y-m-d H:i',strtotime($event_meta['mep_event_end_date'][0])); ?>',
		title: '<?php the_title(); ?>',
		url: '<?php the_permalink(); ?>',
		class: '',
		color: '#000',
		data: {}
	},<?php //if ($i == $count) { echo "";}else{ echo ","; } ?><?php $i++;  } ?>]

jQuery('.event-calendar').equinox({
  events: myEvents
});

});
</script>
<?php
}








add_shortcode( 'event-list', 'mep_event_list' );
function mep_event_list($atts, $content=null){
		$defaults = array(
			"cat"					=> "0",
			"org"					=> "0",
			"style"					=> "grid",
			"cat-filter"			=> "no",
			"org-filter"			=> "no",
			"show"					=> "-1",
			"pagination"			=> "no",
			'sort'                  => 'ASC'    
		);

		$params 					= shortcode_atts($defaults, $atts);
		$cat						= $params['cat'];
		$org						= $params['org'];
		$style						= $params['style'];
		$cat_f						= $params['cat-filter'];
		$org_f						= $params['org-filter'];
		$show						= $params['show'];
		$pagination					= $params['pagination'];
		$sort					    = $params['sort'];
ob_start();
?>
<div class='mep_event_list'>



<?php if($cat_f=='yes'){ ?>
<div class="mep-events-cats-list">
<?php 
$terms = get_terms( array(
    'taxonomy' => 'mep_cat'
) );
?>
<div class="mep-event-cat-controls">
<button type="button" class="mep-cat-control" data-filter="all"><?php _e('All','mage-eventpress'); ?></button><?php
foreach ($terms as $_terms) {
	?><button type="button" class="mep-cat-control" data-filter=".<?php echo $_terms->slug; ?>"><?php echo $_terms->name; ?></button><?php
}
?>
</div>
</div>

<?php } if($org_f=='yes'){ ?>
<div class="mep-events-cats-list">
<?php 
$terms = get_terms( array(
    'taxonomy' => 'mep_org'
) );
?>
<div class="mep-event-cat-controls">
<button type="button" class="mep-cat-control" data-filter="all"><?php _e('All','mage-eventpress'); ?></button><?php
foreach ($terms as $_terms) {
	?><button type="button" class="mep-cat-control" data-filter=".<?php echo $_terms->slug; ?>"><?php echo $_terms->name; ?></button><?php
}
?>
</div>
</div>
<?php } ?>

<div class="mep_event_list_sec">
<?php
$now = date('Y-m-d H:i:s');
$show_price = mep_get_option( 'mep_event_price_show', 'general_setting_sec', 'yes');
$show_price_label = mep_get_option( 'event-price-label', 'general_setting_sec', 'Price Starts from:');
$paged = get_query_var("paged")?get_query_var("paged"):1;
if($cat>0){
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events' ),
                     'paged'             => $paged,
                     'posts_per_page'   => $show,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date',
                     'meta_query' => array(
                        array(
                                'key'       => 'mep_event_start_date',
                                'value'     => $now,
                                'compare'   => '>'
                            )
                        ),                     
                      'tax_query'       => array(
								array(
							            'taxonomy'  => 'mep_cat',
							            'field'     => 'term_id',
							            'terms'     => $cat
							        )
                        )

                );
 }
elseif($org>0){
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events' ),
                     'posts_per_page'   => $show,
                     'paged'             => $paged,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date', 
                     'meta_query' => array(
                        array(
                                'key'       => 'mep_event_start_date',
                                'value'     => $now,
                                'compare'   => '>'
                            )
                        ),
                      'tax_query'       => array(
								array(
							            'taxonomy'  => 'mep_org',
							            'field'     => 'term_id',
							            'terms'     => $org
							        )
                        )

                );
}
 else{
     $args_search_qqq = array (
                     'post_type'         => array( 'mep_events' ),
                     'paged'             => $paged,
                     'posts_per_page'    => $show,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date',
                     'meta_query' => array(
                        array(
                            'key'       => 'mep_event_start_date',
                            'value'     => $now,
                            'compare'   => '>'
                        )
                    )                     

                ); 	
 }

	$loop = new WP_Query( $args_search_qqq );
	while ($loop->have_posts()) {
	$loop->the_post(); 
	$event_meta = get_post_custom(get_the_id());
$author_terms = get_the_terms(get_the_id(), 'mep_org');
	$time = strtotime($event_meta['mep_event_start_date'][0]);
  	$newformat = date('Y-m-d H:i:s',$time);

 	$tt = get_the_terms( get_the_id(), 'mep_cat');
 	$torg = get_the_terms( get_the_id(), 'mep_org');
 	$available_seat = mep_get_total_available_seat(get_the_id(), $event_meta);
?>
	<div class='mep_event_<?php echo $style; ?>_item mix <?php if($tt){ echo $tt[0]->slug; } ?> <?php if($torg){ echo $torg[0]->slug; } ?>'>
		<div class="mep_list_thumb">
			<a href="<?php the_permalink(); ?>"><?php the_post_thumbnail('full'); ?></a>
			<div class="mep-ev-start-date">
				<div class="mep-day"><?php echo date('d', strtotime($event_meta['mep_event_start_date'][0])); ?></div>
				<div class="mep-month"><?php echo date('M', strtotime($event_meta['mep_event_start_date'][0])); ?></div>
			</div>
		</div>
		<div class="mep_list_event_details"><a href="<?php the_permalink(); ?>">		
		<div class="mep-list-header">
					<h2 class='mep_list_title'><?php the_title(); ?></h2>
					<?php if($available_seat==0){ do_action('mep_show_waitlist_label'); } ?>
					<h3 class='mep_list_date'> <?php if($show_price=='yes'){  echo $show_price_label." ".mep_event_list_price(get_the_id()); }?><!-- <i class="fa fa-calendar"></i> <?php echo date('h:i A', strtotime($event_meta['mep_event_start_date'][0])); ?> - <?php echo $event_meta['mep_event_end_date'][0]; ?> --></h3>
		</div>

<?php 
if($style=='list'){
?>
<div class="mep-event-excerpt">
	<?php the_excerpt(); ?>
</div>
<?php } ?>

		<div class="mep-list-footer">
			<ul>
				<li>
					<div class="evl-ico"><i class="fa fa-university"></i> </div>
					<div class="evl-cc">
						<h5>
							<?php echo mep_get_option('mep_organized_by_text', 'label_setting_sec') ? mep_get_option('mep_organized_by_text', 'label_setting_sec') : _e('Organized By:','mage-eventpress');  ?>
						</h5>
						<h6><?php  if($author_terms){ echo $author_terms[0]->name; } ?></h6>
						</div>
				</li>
				<li>
					<div class="evl-ico"><i class="fa fa-location-arrow"></i> </div>
					<div class="evl-cc">
						<h5>
						<?php echo mep_get_option('mep_location_text', 'label_setting_sec') ? mep_get_option('mep_location_text', 'label_setting_sec') : _e('Location:','mage-eventpress');  ?>	

						</h5>
						<h6><?php mep_get_event_city(get_the_id()); ?></h6>
					</div>
				</li>	
				<li>
					<div class="evl-ico"><i class="fa fa-calendar"></i> </div>
					<div class="evl-cc">
						<h5>
						 <?php echo mep_get_option('mep_time_text', 'label_setting_sec') ? mep_get_option('mep_time_text', 'label_setting_sec') : _e('Time:','mage-eventpress');  ?>	
						</h5>
						<h6><?php mep_get_only_time($event_meta['mep_event_start_date'][0]); ?> - <?php mep_get_only_time($event_meta['mep_event_end_date'][0]); ?></h6>
					</div>
				</li>			
			</ul>		
</div></a>
	</div>
</div>
<?php
}
wp_reset_postdata();
//}
if($pagination=='yes'){
?>

<div class="row">
	<div class="col-md-12"><?php
	$pargs = array(
		"current"=>$paged,
		"total"=>$loop->max_num_pages
	);
	echo "<div class='pagination-sec'>".paginate_links($pargs)."</div>";
	?>	
	</div>
</div>
<?php } ?>




</div>
</div>
<script>
	jQuery(document).ready( function() {
            var containerEl = document.querySelector('.mep_event_list_sec');
            var mixer = mixitup(containerEl);
});
</script>
<?php
$content = ob_get_clean();
return $content;
}







add_shortcode( 'expire-event-list', 'mep_expire_event_list' );
function mep_expire_event_list($atts, $content=null){
		$defaults = array(
			"cat"					=> "0",
			"org"					=> "0",
			"style"					=> "grid",
			"cat-filter"			=> "no",
			"org-filter"			=> "no",
			"show"					=> "-1",
			"pagination"			=> "no",
			'sort'                  => 'ASC'    
		);

		$params 					= shortcode_atts($defaults, $atts);
		$cat						= $params['cat'];
		$org						= $params['org'];
		$style						= $params['style'];
		$cat_f						= $params['cat-filter'];
		$org_f						= $params['org-filter'];
		$show						= $params['show'];
		$pagination					= $params['pagination'];
		$sort					    = $params['sort'];
ob_start();
?>
<div class='mep_event_list'>



<?php if($cat_f=='yes'){ ?>
<div class="mep-events-cats-list">
<?php 
$terms = get_terms( array(
    'taxonomy' => 'mep_cat'
) );
?>
<div class="mep-event-cat-controls">
<button type="button" class="mep-cat-control" data-filter="all">All</button><?php
foreach ($terms as $_terms) {
	?><button type="button" class="mep-cat-control" data-filter=".<?php echo $_terms->slug; ?>"><?php echo $_terms->name; ?></button><?php
}
?>
</div>
</div>

<?php } if($org_f=='yes'){ ?>
<div class="mep-events-cats-list">
<?php 
$terms = get_terms( array(
    'taxonomy' => 'mep_org'
) );
?>
<div class="mep-event-cat-controls">
<button type="button" class="mep-cat-control" data-filter="all">All</button><?php
foreach ($terms as $_terms) {
	?><button type="button" class="mep-cat-control" data-filter=".<?php echo $_terms->slug; ?>"><?php echo $_terms->name; ?></button><?php
}
?>
</div>
</div>
<?php } ?>

<div class="mep_event_list_sec">



<?php
$now = date('Y-m-d H:i:s');
$paged = get_query_var("paged")?get_query_var("paged"):1;
if($cat>0){
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events' ),
                     'paged'             => $paged,
                     'posts_per_page'   => $show,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date',
                     'meta_query' => array(
                        array(
                                'key'       => 'mep_event_start_date',
                                'value'     => $now,
                                'compare'   => '<'
                            )
                        ),                     
                      'tax_query'       => array(
								array(
							            'taxonomy'  => 'mep_cat',
							            'field'     => 'term_id',
							            'terms'     => $cat
							        )
                        )

                );
 }
elseif($org>0){
     $args_search_qqq = array (
                     'post_type'        => array( 'mep_events' ),
                     'posts_per_page'   => $show,
                     'paged'             => $paged,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date', 
                     'meta_query' => array(
                        array(
                                'key'       => 'mep_event_start_date',
                                'value'     => $now,
                                'compare'   => '<'
                            )
                        ),                     
                      'tax_query'       => array(
								array(
							            'taxonomy'  => 'mep_org',
							            'field'     => 'term_id',
							            'terms'     => $org
							        )
                        )

                );
}
 else{
     $args_search_qqq = array (
                     'post_type'         => array( 'mep_events' ),
                     'paged'             => $paged,
                     'posts_per_page'    => $show,
                     'order'             => $sort,
                     'orderby'           => 'meta_value',
                     'meta_key'          => 'mep_event_start_date',
                     'meta_query' => array(
                        array(
                                'key'       => 'mep_event_start_date',
                                'value'     => $now,
                                'compare'   => '<'
                            )
                        )                      

                ); 	
 }

	$loop = new WP_Query( $args_search_qqq );
	while ($loop->have_posts()) {
	$loop->the_post(); 
	$event_meta = get_post_custom(get_the_id());
$author_terms = get_the_terms(get_the_id(), 'mep_org');
	$time = strtotime($event_meta['mep_event_start_date'][0]);
  	$newformat = date('Y-m-d H:i:s',$time);


 //if(time() > strtotime($newformat)){
 	$tt = get_the_terms( get_the_id(), 'mep_cat');
 	$torg = get_the_terms( get_the_id(), 'mep_org');
 	// print_r($tt);
 	

	?>
	<div class='mep_event_<?php echo $style; ?>_item mix <?php echo $tt[0]->slug; ?> <?php echo $torg[0]->slug; ?>'>
		<div class="mep_list_thumb">
			<?php the_post_thumbnail('full'); ?>
			<div class="mep-ev-start-date">
				<div class="mep-day"><?php echo date('d', strtotime($event_meta['mep_event_start_date'][0])); ?></div>
				<div class="mep-month"><?php echo date('M', strtotime($event_meta['mep_event_start_date'][0])); ?></div>
			</div>
		</div>
		<div class="mep_list_event_details"><a href="<?php the_permalink(); ?>">		
		<div class="mep-list-header">
					<h2 class='mep_list_title'><?php the_title(); ?></h2>
					<h3 class='mep_list_date'> Price Start from: <?php echo mep_event_list_price(get_the_id()); ?><!-- <i class="fa fa-calendar"></i> <?php echo date('h:i A', strtotime($event_meta['mep_event_start_date'][0])); ?> - <?php echo $event_meta['mep_event_end_date'][0]; ?> --></h3>
		</div>

<?php 
if($style=='list'){
?>
<div class="mep-event-excerpt">
	<?php the_excerpt(); ?>
</div>
<?php } ?>

		<div class="mep-list-footer">
			<ul>
				<li>
					<div class="evl-ico"><i class="fa fa-university"></i> </div>
					<div class="evl-cc">
						<h5>
						 <?php echo mep_get_option('mep_organized_by_text', 'label_setting_sec') ? mep_get_option('mep_organized_by_text', 'label_setting_sec') : _e('Organized By:','mage-eventpress');  ?>
						</h5>
						<h6><?php  echo $author_terms[0]->name; ?></h6>
						</div>
				</li>
				<li>
					<div class="evl-ico"><i class="fa fa-location-arrow"></i> </div>
					<div class="evl-cc">
						<h5>
						<?php echo mep_get_option('mep_location_text', 'label_setting_sec') ? mep_get_option('mep_location_text', 'label_setting_sec') : _e('Location:','mage-eventpress');  ?>	

						</h5>
						<h6><?php echo $event_meta['mep_city'][0]; ?></h6>
					</div>
				</li>	
				<li>
					<div class="evl-ico"><i class="fa fa-calendar"></i> </div>
					<div class="evl-cc">
						<h5>
						<?php echo mep_get_option('mep_time_text', 'label_setting_sec') ? mep_get_option('mep_time_text', 'label_setting_sec') : _e('Time:','mage-eventpress');  ?>	

						</h5>
						<h6><?php mep_get_only_time($event_meta['mep_event_start_date'][0]); ?> - <?php mep_get_only_time($event_meta['mep_event_end_date'][0]); ?></h6>
					</div>
				</li>			
			</ul>		
</div></a>
	</div>
</div>
<?php








//}
}
wp_reset_postdata();
if($pagination=='yes'){
?>

<div class="row">
	<div class="col-md-12"><?php
	$pargs = array(
		"current"=>$paged,
		"total"=>$loop->max_num_pages
	);
	echo "<div class='pagination-sec'>".paginate_links($pargs)."</div>";
	?>	
	</div>
</div>
<?php } ?>




</div>
</div>
<script>
	jQuery(document).ready( function() {
            var containerEl = document.querySelector('.mep_event_list_sec');
            var mixer = mixitup(containerEl);
});
</script>
<?php
$content = ob_get_clean();
return $content;
}