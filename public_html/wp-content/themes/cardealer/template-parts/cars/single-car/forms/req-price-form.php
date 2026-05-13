<?php
global $car_dealer_options, $post;

$lead_form     = cardealer_get_lead_form( 'req_price_form' );
$car_id        = $args['car_id'];
$is_page_price = $args['is_page_price'];
$btn_label     = $args['btn_label'];

if ( ( is_singular( 'cars' ) || is_singular( 'cardealer_template' ) ) && $is_page_price ) {
	?>
	<a class="cardealer-lead-form-req-price-btn" data-toggle="modal" data-target="<?php echo esc_attr( '#req-price-modal-' . $car_id ); ?>" href="#"><?php echo esc_html( $btn_label ); ?></a>
	<div class="modal fade cardealer-lead-form cardealer-lead-form-req-price" id="<?php echo esc_attr( 'req-price-modal-' . $car_id ); ?>" tabindex="-1" role="dialog" aria-labelledby="req-price-lbl" aria-hidden="true">
		<div class="modal-dialog modal-lg" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h6 class="modal-title" id="req-price-lbl"><?php echo esc_html( $lead_form['modal_title'] ); ?></h6>
				</div>
				<div class="modal-body">
					<?php
					global $cdhl_req_price_form, $cdhl_req_price_form_fields;

					$cdhl_req_price_form        = true;
					$cdhl_req_price_form_fields = array(
						'cdhl_req_price_form'            => 'yes',
						'cdhl_req_price_form_user_email' => get_the_author_meta( 'email' ),
					);

					echo do_shortcode( $car_dealer_options['req_price_form_shortcode'] );

					$cdhl_req_price_form        = false;
					$cdhl_req_price_form_fields = array();
					?>
				</div>
			</div>
		</div>
	</div>
	<?php
} else {
	$is_iframe = cardealer_is_iframe();
	$terget    = $is_iframe ? '_parent' : '_self';
	$class     = 'cardealer-lead-form-req-price-btn';

	if ( $is_iframe ) {
		$class .= ' cardealer-lead-form-req-price-btn-iframe';
	}
	?>
	<a class="<?php echo esc_attr( $class ); ?>" href="<?php echo esc_attr( get_permalink( $car_id ) ); ?>" terget="<?php echo esc_attr( $terget ); ?>"><?php echo esc_html( $btn_label ); ?></a>
	<?php
}
