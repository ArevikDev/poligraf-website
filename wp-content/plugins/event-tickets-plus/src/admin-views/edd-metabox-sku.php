<?php
/**
 * Filter the display of SKU for the ticket
 *
 * @since  4.6
 *
 * @param boolean true/false - show/hide
 */
?>
<div
	class="<?php $this->tr_class(); ?> input_block tribe-dependent"
	data-depends="#Tribe__Tickets_Plus__Commerce__EDD__Main_radio"
	data-condition-is-checked
>
	<label for="ticket_edd_sku" class="ticket_form_label ticket_form_left"><?php esc_html_e( 'SKU:', 'event-tickets-plus' ); ?></label>
	<input
		type="text"
		id="ticket_edd_sku"
		name="ticket_sku"
		class="ticket_field sku_input ticket_form_right"
		size="14"
		value="<?php echo esc_attr( $sku ); ?>"
	/>
	<p class="description ticket_form_right">
		<?php esc_html_e( 'A unique identifying code for each ticket type you\'re selling', 'event-tickets-plus' ); ?>
	</p>
</div>
<?php
