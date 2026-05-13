( function( $ ) {
	"use strict";

	// Check element exists.
	$.fn.exists = function () {
		return this.length > 0;
	};

	jQuery(window).load(function() {
	});

	jQuery(document).ready(function($) {

		/***************************************
		:: 360 Popup
		***************************************/

		if ( $( '.btn-open-vehicle-view360' ).exists() ) {
			jQuery( '.btn-open-vehicle-view360' ).magnificPopup({
				callbacks: {
					beforeOpen: function() {
						cardealer_destroy_cloudimage_360_view();
					},
					open: function() {
						cardealer_init_cloudimage_360_view();
					},
				},
				type:'inline',
				midClick: true,
				mainClass: 'magnific-popup-vehicle-view360 mfp-fade',
			});
		}

		cardealer_init_cloudimage_360_view();
		$( document.body ).on( 'cardealer_initiate_cloudimage_360_view', function() {
			// console.log( 'cardealer_initiate_cloudimage_360_view 2' );
			cardealer_init_cloudimage_360_view();
		});

		$( document.body ).on( 'cardealer_destroy_cloudimage_360_view', function() {
			cardealer_destroy_cloudimage_360_view();
		});

		function cardealer_init_cloudimage_360_view(){
			window.CI360.init();
		}
		function cardealer_destroy_cloudimage_360_view(){
			window.CI360.destroy();
		}
	});
}( jQuery ) );
