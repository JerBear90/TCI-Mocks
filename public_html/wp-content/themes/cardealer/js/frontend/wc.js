/*================================================
[  Table of contents  ]
================================================
:: window load functions
:: Document ready functions
	:: Code For Cart Toggle in menu for mobile device starts
	:: Vehicle buy online
======================================
[ End table content ]
======================================*/
( function( $ ) {
	"use strict";

	jQuery(document).ready(function($) {
		/********************************************************
		:: Code For Cart Toggle in menu for mobile device starts
		*********************************************************/

		jQuery(document).on('click', '.cart-mobile-content', function( event ) {
			event.preventDefault();
			jQuery('.widget_shopping_cart_content').toggle();
		});

		/************************
		:: Vehicle buy online
		*************************/
		$( document ).on( 'cardealer-vehicle-button-buy-online', function( event, el ) {
			event.preventDefault();
			var vehicle_id = $(el).data('vehicle_id');
			var $this      = jQuery(el);

			jQuery.ajax({
				url: vehicle_wc_js_object.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {
					vehicle_id:vehicle_id,
					'ajax_nonce': vehicle_wc_js_object.cd_sell_car_online_ajax,
					'action':'cd_sell_car_online'
				},
				beforeSend: function(){
					$this.append('<span class="buy-online-spinner"><i class="fa fa-refresh fa-spin"></i></span>');
					$this.prop('disabled', true);
				},
				success: function(res){
					$this.find( '.buy-online-spinner' ).remove();
					$this.prop('disabled', false);
					if ( res.status == 'success' ) {
						window.location = res.redirect_url;
					} else {
						alert( res.msg );						
					}
				},
				error: function(res){
					console.log( vehicle_wc_js_object.error_msg );
				}
			});
		});
	});

}( jQuery ) );
