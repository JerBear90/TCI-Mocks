(function($){
	"use strict";

	// Check element exists.
	$.fn.exists = function () {
		return this.length > 0;
	};

	jQuery( document ).ready(function($) {

		if ( $( '.featured-vehicle.featured-vehicle-tooltip' ).exists() ) {
			$( '.featured-vehicle.featured-vehicle-tooltip.featured-vehicle-tooltip-by-admin' ).tooltip({
				classes: {
					"ui-tooltip": "ui-tooltip-featured-vehicle ui-tooltip-featured-vehicle-by-admin",
				},
				position: {
					my: "center bottom",
					at: "right top"
				}
			});
			$( '.featured-vehicle.featured-vehicle-tooltip.featured-vehicle-tooltip-by-seller' ).tooltip({
				classes: {
					"ui-tooltip": "ui-tooltip-featured-vehicle ui-tooltip-featured-vehicle-by-seller",
				},
				position: {
					my: "center bottom",
					at: "right top"
				}
			});


			$('.featured-vehicle.featured-vehicle-tooltip.featured-vehicle-tooltip-by-seller').on('click', function () {
				return confirm( 'This vehicle is set featured by the seller. Are you sure you want to remove it from Featured?' );
			})
		}

		/* Mega Menu */
		jQuery('#cd_megamenu_wrapper').appendTo( jQuery('#post-body #post-body-content') ).show();

		// Third Party Testing - Tabs
		jQuery( document ).on( 'click', '.cardealer-debug-tab a', function( e ) {

			e.preventDefault();

			if ( ! jQuery( '.cardealer-debug-content-response' ).hasClass( 'hide' ) ) {
				jQuery( '.cardealer-debug-content-response' ).addClass( 'hide' );
			}

			if ( ! jQuery( '.cardealer-mailchimp-debug-content-response' ).hasClass( 'hide' ) ) {
				jQuery( '.cardealer-mailchimp-debug-content-response' ).addClass( 'hide' );
			}

			if ( ! jQuery( '.cardealer-vinquery-debug-content-response' ).hasClass( 'hide' ) ) {
				jQuery( '.cardealer-vinquery-debug-content-response' ).addClass( 'hide' );
			}

			jQuery( '.cardealer-debug-tab a' ).removeClass( 'activelink' );
			jQuery( this ).addClass( 'activelink' );
			var tag_id = jQuery( this ).attr( 'data-tag' );
			cookies.set( 'cardealer_debug_current_tab', tag_id );
			jQuery( '.cardealer-debug-content' ).removeClass( 'active' ).addClass( 'hide' );
			jQuery( '#' + tag_id ).addClass( 'active' ).removeClass( 'hide' );
		});

		// Third Party Testing - Debug Mail
		jQuery( "#cardealer-debug-send-mail" ).submit(function( event ) {
			event.preventDefault();

			var from_email = jQuery( '#debug-from-user-email' ).val();
			var to_email   = jQuery( '#debug-to-user-email' ).val();
			var data       = {'action': 'cardealer_debug_send_mail', 'ajax_nonce': cardealer_admin_js.pgs_mail_debug_nonce };

			if ( from_email ) {
				data.from_email = from_email;
			}

			if ( to_email ) {
				data.to_email = to_email;
			}

			jQuery.ajax({
				url: cardealer_admin_js.ajaxurl,
				type: 'POST',
				dataType: "json",
				data : data,
				beforeSend: function(){
					jQuery( '.cardealer-debug-content' ).addClass( 'loading' );
				},
				success: function( resp ) {
					jQuery( '.cardealer-debug-content-response.success' ).removeClass( 'success' );
					jQuery( '.cardealer-debug-content-response.failed' ).removeClass( 'failed' );
					if ( resp.status ) {
						jQuery( '.cardealer-debug-content-response' ).addClass( 'success' );
						jQuery( '.cardealer-debug-content-response' ).html( resp.msg );
					} else {
						jQuery( '.cardealer-debug-content-response' ).addClass( 'failed' );
						jQuery( '.cardealer-debug-content-response' ).html( resp.msg );
					}
				}
			}).done( function(){
				jQuery( '.cardealer-debug-content-response' ).removeClass( 'hide' );
				jQuery( '.cardealer-debug-content' ).removeClass( 'loading' );
			});
		});

		// Third Party Testing - Debug VINquery
		jQuery( "#cardealer-debug-vinquery" ).submit(function( event ) {
			event.preventDefault();

			var vinnumber = jQuery( '#debug-vinnumber' ).val();
			var data      = {'action': 'cardealer_debug_vinquery', 'ajax_nonce': cardealer_admin_js.pgs_vinquery_debug_nonce };

			if ( vinnumber ) {
				data.vinnumber = vinnumber;
			}

			jQuery.ajax({
				url: cardealer_admin_js.ajaxurl,
				type: 'POST',
				dataType: "json",
				data : data,
				beforeSend: function(){
					jQuery( '.cardealer-debug-content' ).addClass( 'loading' );
				},
				success: function( resp ) {
					jQuery( '.cardealer-vinquery-debug-content-response.success' ).removeClass( 'success' );
					jQuery( '.cardealer-vinquery-debug-content-response.failed' ).removeClass( 'failed' );
					if ( resp.status ) {
						jQuery( '.cardealer-vinquery-debug-content-response' ).addClass( 'success' );
						jQuery( '.cardealer-vinquery-debug-content-response' ).html( resp.msg );
					} else {
						jQuery( '.cardealer-vinquery-debug-content-response' ).addClass( 'failed' );
						jQuery( '.cardealer-vinquery-debug-content-response' ).html( resp.msg );
					}
				}
			}).done( function(){
				jQuery( '.cardealer-vinquery-debug-content-response' ).removeClass( 'hide' );
				jQuery( '.cardealer-debug-content' ).removeClass( 'loading' );
			});
		});

		// Third Party Testing - Debug Mailchimp
		jQuery( document ).on( 'click', '#debug-user-mailchimp', function( event ) {
			event.preventDefault();

			var data      = {'action': 'cardealer_debug_mailchimp', 'ajax_nonce': cardealer_admin_js.pgs_mailchimp_debug_nonce };
			jQuery.ajax({
				url: cardealer_admin_js.ajaxurl,
				type: 'POST',
				dataType: "json",
				data : data,
				beforeSend: function(){
					jQuery( '.cardealer-debug-content' ).addClass( 'loading' );
				},
				success: function( resp ) {
					jQuery( '.cardealer-mailchimp-debug-content-response.success' ).removeClass( 'success' );
					jQuery( '.cardealer-mailchimp-debug-content-response.failed' ).removeClass( 'failed' );
					if ( resp.status ) {
						jQuery( '.cardealer-mailchimp-debug-content-response' ).addClass( 'success' );
						jQuery( '.cardealer-mailchimp-debug-content-response' ).html( resp.msg );
					} else {
						jQuery( '.cardealer-mailchimp-debug-content-response' ).addClass( 'failed' );
						jQuery( '.cardealer-mailchimp-debug-content-response' ).html( resp.msg );
					}
				}
			}).done( function(){
				jQuery( '.cardealer-mailchimp-debug-content-response' ).removeClass( 'hide' );
				jQuery( '.cardealer-debug-content' ).removeClass( 'loading' );
			});
		});

		// Third Party Testing - Debug PDF Generator
		if ( $( '.cardealer-debug-pdf-generator-vehicle' ).exists() && $( '.cardealer-debug-pdf-generator-html-template' ).exists() && $( '#cardealer-debug-pdf-generator-check-pdf' ).exists() ) {
			jQuery( document ).on( 'click', '#cardealer-debug-pdf-generator-check-pdf', function( event ) {
				event.preventDefault();

				var vehicle_id          = $('.cardealer-debug-pdf-generator-vehicle > select').val(),
					template            = $('.cardealer-debug-pdf-generator-html-template > select').val(),
					current_tab_content = $(this).closest('.cardealer-debug-content'),
					response_el         = current_tab_content.find( '.cardealer-debug-response' );

				var data = {
					action: 'cardealer_debug_generate_pdf',
					ajax_nonce: cardealer_admin_js.cardealer_debug_nonce,
					id: vehicle_id,
					pdf_template_title: template,
				};
				jQuery.ajax({
					url: cardealer_admin_js.ajaxurl,
					type: 'POST',
					dataType: "json",
					data : data,
					beforeSend: function(){
						response_el.addClass( 'hide' );
						jQuery( '.cardealer-debug-content' ).addClass( 'loading' );
					},
					success: function( resp ) {
						response_el.removeClass( 'success' );
						response_el.removeClass( 'failed' );

						if ( resp.status ) {
							response_el.addClass( 'success' );
							response_el.html( resp.msg );
						} else {
							response_el.addClass( 'failed' );
							response_el.html( resp.msg );
						}
					}
				}).done( function(){
					response_el.removeClass( 'hide' );
					jQuery( '.cardealer-debug-content' ).removeClass( 'loading' );
				});

			});
		}

		jQuery(document).on('click', '.set-position-button', function(e) {
			e.preventDefault();

			if( jQuery(this).hasClass('already-set') ) {
				return;
			}
			var $image_source=$('.image_source').find(":selected").text();

			var $image_src = ($image_source=="Image") ? $('.cdhl-hotspot-img .gallery_widget_attached_images img').attr('src') : $('.hotspot_box_img_link').val(),
				$position = $(this).parents('.cdhl-position').find('.list_items_position').val();	
				
			$image_src = $image_src.replace('-150x150', '', $image_src);
			$(this).parents('.cdhl-position').append('<div class="hotspot-image-wrapper"><div class="cdhl-hotspot-cover"><img src="'+ $image_src +'" /><div class="cdhl-hotspot-overlay"></div><div class="cdhl-hotspot-pointer"></div></div></div>');

			$position = $position.split('||');
			$(this).parents('.cdhl-position').find('.cdhl-hotspot-pointer').css({"top":$position[1]+"%", "left":$position[0]+"%" });
			$(this).addClass('already-set');

			var $containment = $(this).parents('.cdhl-position');

			jQuery('.cdhl-hotspot-pointer').draggable({
				containment: $containment.find('.cdhl-hotspot-cover'),
				scroll: false,
				drag: function( event, ui ) {
					var relativeX = (ui.position.left + 3) / $containment.find('.cdhl-hotspot-cover').width() * 100,
						relativeY = (ui.position.top + 3) / $containment.find('.cdhl-hotspot-cover').height() * 100;
					relativeX = relativeX.toFixed(2);
					relativeY = relativeY.toFixed(2);

					$containment.find('.list_items_position').val(relativeX +'||'+ relativeY);
					$containment.find('.cdhl-hotspot-pointer').css({"top":relativeY+"%", "left":relativeX+"%"});
				}
			});
		});
		
		jQuery(document).on('click', '.cdhl-position .cdhl-hotspot-cover .cdhl-hotspot-overlay', function(e) {
			var offset = jQuery(this).offset(),
				relativeX = (e.pageX - offset.left) / jQuery(this).width() * 100,
				relativeY = (e.pageY - offset.top) / jQuery(this).height() * 100;

			relativeX = relativeX.toFixed(2);
			relativeY = relativeY.toFixed(2);

			jQuery(this).parents('.cdhl-position').find('.list_items_position').val(relativeX +'||'+ relativeY);
			jQuery(this).parents('.cdhl-position').find('.cdhl-hotspot-pointer').css({"top":relativeY+"%", "left":relativeX+"%" });
		});

		jQuery(document).on('click', '.vc_param_group-add_content, .column_toggle', function(){
			jQuery('.cdhl-position').each(function() {
				if( ! jQuery(this).find( '.set-position-button' ).length ) {
					jQuery(this).find('.edit_form_line').append('<button class="vc_ui-button vc_ui-button-action button set-position-button">'+ cardealer_admin_js.set_possition +'</button>');
				}
			});
		});
	});

	// Function to show/hide fields based on the selected option in the mailchimp_api_source dropdown
	function toggle_mailchimp_fields(wrapper) {
		var api_source = wrapper.find('.mailchimp-api-source').val();

		if ( 'custom' === api_source ) {
			wrapper.find('.mailchimp-api-source-default-fields').hide();
			wrapper.find('.mailchimp-api-source-custom-fields').show();
		} else {
			wrapper.find('.mailchimp-api-source-default-fields').show();
			wrapper.find('.mailchimp-api-source-custom-fields').hide();
		}
	}

	// Event handler for the widget-added event
	$(document).on('widget-added', function (event, widget) {
		var wrapper = $(widget).find('.mailchimp-api-fields');
		toggle_mailchimp_fields( wrapper );
	});
	$(document).on('widget-updated', function (event, widget) {
		var wrapper = $(widget).find('.mailchimp-api-fields');
		toggle_mailchimp_fields( wrapper );
	});

	// Attach change event handler to the mailchimp_api_source dropdown
	$(document).on('change', '.mailchimp-api-source', function () {
		var wrapper = $( this ).parents( '.mailchimp-api-fields' );
		toggle_mailchimp_fields( wrapper );
	});

	// Initial execution of the function for the existing widgets on the page
	$( document ).ready( function($) {
		$( '.mailchimp-api-fields' ).each(function () {
			var wrapper = $(this);
			toggle_mailchimp_fields( wrapper );
		});
	});

})(jQuery);

function show_cd_map_canvas() {
	map = new google.maps.Map(document.getElementById( 'cd-map-canvas' ), {
		center: { lat: -34.397, lng: 150.644 },
		zoom: 8,
	});
}

// Color variant shortcode field dependacy callback
var vcCdhlColorVariantCallback;
vcCdhlColorVariantCallback = function() {
	(function ( $, that ) {

		$( '[name="variant_list_variant_type"]' ).each(
			function() {
				var fields_value = $(this).val();
				if ( 'color' === fields_value ) {
					$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_text"]' ).hide();
				} else if( 'text' === fields_value ) {
					$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_color"]' ).hide();
				}
			}
		);

		$( document ).on( 'change', '[name="variant_list_variant_type"]', function () {
			var fields_value = $(this).val();
			if ( 'color' === fields_value ) {
				$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_text"]' ).hide();
				$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_color"]' ).show();
			} else if( 'text' === fields_value ) {
				$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_color"]' ).hide();
				$(this).parents( '.wpb_vc_row' ).find( '[data-vc-shortcode-param-name="variant_list_variant_text"]' ).show();
			}
		});

	}( window.jQuery, this ));
};