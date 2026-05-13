/*================================================
[  Table of contents  ]
================================================
:: Document ready functions
	:: Compare
	:: Code To Add Car in Compare
======================================
[ End table content ]
======================================*/
( function( $ ) {
	"use strict";

	// Check element exists.
	$.fn.exists = function () {
		return this.length > 0;
	};

	// Parent jQuery.
	var parentjQuery = window.parent.jQuery.noConflict();

	function is_iframe() {
		return window.self !== window.top;
	}

	function cardealer_trigger( event, data ) {
		if ( data === undefined || data === null ) {
			$( document ).trigger( event );
		} else {
			$( document ).trigger( event, data );
		}
		if ( is_iframe() ) {
			if ( data === undefined || data === null ) {
				parentjQuery( 'body' ).trigger( event );
				$( document ).trigger( event );
			} else {
				parentjQuery( 'body' ).trigger( event, data );
			}
		}
	}

	$(document).ready(function($) {

		// Trigger add to cart on Buy Online btn.
		$( document ).on( 'click', '.vehicle-button-link.car-buy-online-btn.car-buy-online-btn-iframe', function (e) {
			e.preventDefault(e);

			var el = $(this);

			if ( is_iframe() ) {
				parentjQuery( 'body' ).trigger( 'cardealer-vehicle-button-buy-online', el );
			} else {
				$( document ).trigger( 'cardealer-vehicle-button-buy-online', el );
			}
		});

		// Open Request price link from popup.
		$( document ).on( 'click', '.cardealer-lead-form-req-price-btn.cardealer-lead-form-req-price-btn-iframe', function (e) {
			e.preventDefault(e);
			top.location.href = $(this).attr('href');
		});
		$( document ).on( 'click', '.cd-vehicle-compare-wrapper .cd-vehicle-compare table td .compare-image-link, .cd-vehicle-compare-wrapper .cd-vehicle-compare table th .compare-title-link', function (e) {
			e.preventDefault(e);
			top.location.href = $(this).attr('href');
		});

		// Compare variables.
		var compare_menu_item         = $('.menu-item.menu-item-compare'),
			compare_wrapper           = $('.cd-vehicle-compare-wrapper'),
			compare_div               = compare_wrapper.find('.cd-vehicle-compare'),
			compare_table             = compare_wrapper.find('.cd-vehicle-compare-table'),
			compare_vehicle_ids       = new Array(),
			compare_vehicle_ids_count = 0,
			compare_modal_el          = $( '#cd-vehicle-compare-modal' ),
			compare_modal_body        = compare_modal_el.find('.modal-body'),
			compare_content           = compare_modal_el.find('.cd-vehicle-compare-content'),
			compare_loader            = compare_modal_el.find('.cd-vehicle-compare-loader'),
			compare_modal_iframe;

		// Initially actions.
		compare_menu_item.hide();      // Hide compare menu item if visble.
		update_vehicle_compare_data(); // Update vehicle compare data.
		update_compare_menu();

		// Update compare menu when update_compare_menu event is triggerred.
		$( document ).on( 'update_compare_menu', function() {
			update_compare_menu();
		});

		// Update vehicle data  update_vehicle_compare_data event is triggerred.
		$( document ).on( 'update_vehicle_compare_data', function() {
			update_vehicle_compare_data();
		});

		function update_vehicle_compare_data() {
			compare_vehicle_ids        = get_compare_vehicle_ids();
			compare_vehicle_ids_count  = get_compare_vehicle_ids_count;
			compare_table.attr( 'data-vehicle_count', compare_vehicle_ids_count );

			generate_compare_url( compare_vehicle_ids );
		}

		// Update compare based on number of vehicle IDs in the cookie.
		function update_compare_menu() {
			if ( null !== cardealer_compare_obj.compare_url ) {
				compare_vehicle_ids_count = get_compare_vehicle_ids_count();
				if ( compare_vehicle_ids_count > 0 ) {
					compare_menu_item.show();
					$( '.menu-item .compare-details.count' ).html( compare_vehicle_ids.length );
				} else {
					compare_menu_item.hide();
				}
			} else {
				compare_menu_item.hide();
			}
		}

		function get_compare_vehicle_ids() {
			var compare_vehicle_ids_cookie = cookies.get( 'compare_ids' );
			return ( compare_vehicle_ids_cookie === null || compare_vehicle_ids_cookie === '' ) ? new Array() : compare_vehicle_ids_cookie;
		}
		function get_compare_vehicle_ids_count() {
			return ( compare_vehicle_ids === null || compare_vehicle_ids === '' ) ? 0 : compare_vehicle_ids.length;
		}

		// Action when compare menu or compare icon on vehicles is clicked.
		$( document ).on( 'click', '.compare_pgs, .menu-item-compare', function() {
			// $( document ).trigger( 'update_vehicle_compare_data' ); // Update Car IDs.
			cardealer_trigger( 'update_vehicle_compare_data' ); // Update Car IDs.

			var car_list_click = 0;

			if ( $( this ).hasClass( 'compare_pgs' ) )  {
				var car_id  = parseInt( $( this ).data('id') );

				car_list_click = 1;

				$( this ).find('i').removeClass('fa-exchange-alt');
				$( this ).find('i').addClass('fa-check');

				compare_vehicle_ids_count = get_compare_vehicle_ids_count();
				if (
					compare_vehicle_ids_count === 0 // No IDs found.
					|| ( compare_vehicle_ids_count > 0 && $.inArray( car_id, compare_vehicle_ids ) === -1 ) // IDs found, ut selected ID not found.
				) {
					compare_vehicle_ids.push( car_id );
				}

				cookies.del( 'compare_ids' );
				cookies.set( 'compare_ids', JSON.stringify( compare_vehicle_ids ) );
				// $( document ).trigger( 'update_vehicle_compare_data' );
				// $( document ).trigger( 'update_compare_menu' );
				cardealer_trigger( 'update_vehicle_compare_data' )
				cardealer_trigger( 'update_compare_menu' )
			}
			generate_compare_url( compare_vehicle_ids ); // Generate URL.

			// Set iframe.
			compare_modal_iframe = $( '<iframe>' );

			compare_modal_iframe.css( 'opacity', '0' );
			compare_modal_iframe.css( 'width', '100%' );
			compare_modal_iframe.css( 'height', '100%' );
			compare_modal_iframe.attr( 'class', 'cd-vehicle-compare-iframe' );
			compare_modal_iframe.attr( 'frameborder', '0' );
			compare_modal_iframe.attr( 'src', cardealer_compare_obj.compare_url );

			compare_modal_iframe.on('load', function () {
				compare_loader.css( 'display', 'none' ); // Hide the loader.
				compare_modal_iframe.css( 'opacity', '1' ); // Show the iframe.
			});

			compare_content.html( compare_modal_iframe );

			compare_modal_iframe.on( 'reload', function(){
				alert('loading iframe');
				this.contentWindow.location.reload();
			} );

			if ( cardealer_compare_obj.compare_type == 'template' ) {
				window.location.href = cardealer_compare_obj.compare_url;
			} else {
				compare_modal_el.modal('show');
			}
		});

		compare_modal_el.on( 'shown.bs.modal', function(e){
			compare_loader.css( 'display', 'block' );
		});
		compare_modal_el.on( 'hide.bs.modal', function(e){
		});
		compare_modal_el.on( 'hidden.bs.modal', function(e){
			compare_modal_el.find('.cd-vehicle-compare-iframe').remove();
			compare_loader.css( 'display', 'block' );
		});

		jQuery(document).on('hidden.bs.modal', '#cd-vehicle-compare-modal',function(e){
			$('.compare_pgs i.fa-spinner').parent().addClass('compared_pgs');
		});

		/*********************************
		:: Compare Vehicle - Legacy
		**********************************/
		// Remove item from Compare
		$( document ).on('click', '.vehicle-column .compare-remove-column', function() {
			var car_id       = $(this).data('car_id'),
				column_class = $(this).data('column_class'),
				column       = $(column_class);

			// $( document ).trigger( 'update_vehicle_compare_data' );
			cardealer_trigger( 'update_vehicle_compare_data' );

			// Remove item from cookie.
			column.remove();

			// remove_from_array( compare_vehicle_ids, car_id );
			cardealer_trigger( 'remove_compare_vehicle_ids', car_id );

			cookies.del( 'compare_ids' );
			cookies.set( 'compare_ids', JSON.stringify( compare_vehicle_ids ) );
			// $( document ).trigger( 'update_vehicle_compare_data' );
			cardealer_trigger( 'update_vehicle_compare_data' );
			// $( document ).trigger( 'update_compare_menu' );
			cardealer_trigger( 'update_compare_menu' );
			generate_compare_url( compare_vehicle_ids );

			window.history.replaceState( {}, "Page", cardealer_compare_obj.compare_url );
		});

		if ( $( '.cd-compare-select-wrapper' ).exists() ) {
			$('.cd-compare-select-wrapper').each(function() {
				var compare_select_wrapper      = $(this ),
					compare_select_attr         = compare_select_wrapper.find('.cd-compare-select.cd-compare-select-attr'),
					compare_select_cntr_attr    = compare_select_wrapper.find('.cd-compare-select-field-container-attr'),
					compare_select_vehicle      = compare_select_wrapper.find('.cd-compare-select.cd-compare-select-vehicle'),
					compare_select_cntr_vehicle = compare_select_wrapper.find('.cd-compare-select-field-container-vehicle');

				var compare_select_attr_select2 = compare_select_attr.select2({
					id: '-1', // the value of the option
					allowClear: true,
					dropdownCssClass: 'cd-compare-select-attr-dropdown',
					dropdownParent: compare_select_cntr_attr,
				});

				var compare_select_vehicle_select2 = compare_select_vehicle.select2({
					id: '-1', // the value of the option
					allowClear: true,
					dropdownCssClass: 'cd-compare-select-vehicle-dropdown',
					dropdownParent: compare_select_cntr_vehicle,
				});

				$( compare_select_attr ).on('change', function() {
					var el             = this,
						$el            = $( this ),
						field_attr     = $( this ).data('field_attr'),
						field_attr_val = $el.val();

					$.ajax({
						url: cardealer_compare_obj.ajaxurl,
						type: 'post',
						data:{
							action              : 'cardealer_get_compare_cars',
							compare_nonce       : cardealer_compare_obj.compare_nonce,
							field_attr          : field_attr,
							field_attr_val      : $(this).val(),
							ppp                 : cardealer_compare_obj.select_vehicles_ppp,
						},
						beforeSend: function(){
							compare_select_vehicle_select2.data('select2').$container.addClass('cd-loading');
						},
						complete(xhr,status) {
							compare_select_vehicle_select2.data('select2').$container.removeClass('cd-loading');
						},
						success: function(response){
							// Clear existing options.
							compare_select_vehicle.empty();

							if ( response.hasOwnProperty('items') && response.items.length > 0 ) {
								var option = new Option( '', '' );
								compare_select_vehicle.append(option);
								// Using forEach to iterate over the array of objects
								response.items.forEach(function(obj) {
									var option = new Option( obj.title, obj.id );
									compare_select_vehicle.append(option);
								});
							}

							// Trigger change event to update Select2.
							// compare_select_vehicle.trigger('change');
						},
						error: function(error){
							alert( cardealer_compare_obj.compare_load_error_msg );
						},
					});
				});

				$( compare_select_vehicle ).on('change', function() {
					var car_id = parseInt( $(this).val() );

					// $( document ).trigger( 'update_vehicle_compare_data' );
					cardealer_trigger( 'update_vehicle_compare_data' );

					// add_compare_vehicle_ids( compare_vehicle_ids, car_id );
					// $(document).trigger( 'add_compare_vehicle_ids', car_id );
					// if ( is_iframe() ) {
						// parentjQuery('body').trigger( 'add_compare_vehicle_ids', car_id );
					// }
					cardealer_trigger( 'add_compare_vehicle_ids', car_id );

					cookies.del( 'compare_ids' );
					cookies.set( 'compare_ids', JSON.stringify( compare_vehicle_ids ) );

					// $( document ).trigger( 'update_vehicle_compare_data' );
					cardealer_trigger( 'update_vehicle_compare_data' );
					// $( document ).trigger( 'update_compare_menu' );
					// if ( is_iframe() ) {
						// parentjQuery('body').trigger('update_compare_menu');
					// }
					cardealer_trigger( 'update_compare_menu' );

					generate_compare_url( compare_vehicle_ids );

					// window.history.replaceState( {}, "Page", cardealer_compare_obj.compare_url );
					$('body').addClass('cd-vehicle-compare-loading');
					window.location.href = cardealer_compare_obj.compare_url;
				});
			});
		}

		function searchNumberInArray( value ) {
			for ( var i = 0; i < compare_vehicle_ids.length; i++ ) {
				// Check if the element is a number.

				if ( typeof compare_vehicle_ids[i] === 'number' && compare_vehicle_ids[i] === value ) {
					return i;
				}

				// If the element is a string, try parsing it to a number
				if (typeof compare_vehicle_ids[i] === 'string') {
					var parsedNumber = parseInt(compare_vehicle_ids[i]);
					if (!isNaN(parsedNumber) && parsedNumber === value) {
						return i;
					}
				}
			}

			return false;
		}

		$( document ).on( 'remove_compare_vehicle_ids', function(event, data) {
			const index = searchNumberInArray( data );
			if ( false !== index ) {
				compare_vehicle_ids.splice( index, 1 );
			}
		} );

		$( document ).on( 'add_compare_vehicle_ids', function(event, data) {
			var index = searchNumberInArray( data );
			if ( ! index ) {
				compare_vehicle_ids.push( data );
				$( document ).trigger( 'update_compare_menu' );
			}
		} );

		function generate_compare_url( car_ids ) {
			var compare_url = '';

			if ( null !== cardealer_compare_obj.compare_url ) {
				var current_url     = new URL( cardealer_compare_obj.compare_url ),
					new_current_url = cardealer_compare_obj.compare_url;

				// If your expected result is "http://example.com/?x=42&y=2"
				current_url.searchParams.set( 'car_ids', 'car_ids_placeholder' );

				new_current_url                   = current_url.toString().replace( 'car_ids_placeholder', car_ids.join(',') );
				cardealer_compare_obj.compare_url = new_current_url;
				compare_url                       = cardealer_compare_obj.compare_url;
			}

			return compare_url;
		}
	});
}( jQuery ) );
