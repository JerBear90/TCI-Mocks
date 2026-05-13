(function($){
	"use strict";

	jQuery( document ).ready( function($) {
		// Check element exists.
		$.fn.exists = function () {
			return this.length > 0;
		};

		$( document ).on( 'submit', 'form#create_cardealer_sidebar_form', function( e ) {
			e.preventDefault();

			jQuery.ajax({
				url        : cardealer_custom_sidebar_obj.ajaxurl,
				method     : 'POST',
				data       : {
					'nonce'                  : cardealer_custom_sidebar_obj.sidebar_nonce,
					'action'                 : 'create_cardealer_sidebar',
					'cardealer_sidebar_name' : $( '#cardealer_sidebar_name' ).val(),
				},
				dataType   : 'json',
				beforeSend : function() {
					jQuery( '#create_cardealer_sidebar' ).prop( 'disabled', true );
					jQuery( '.cardealer-sidebar-table-body' ).addClass( 'loading' );
				},
				success: function(responseObj){
					jQuery( '.cardealer-sidebar-table-body' ).removeClass( 'loading' );
					jQuery( '#create_cardealer_sidebar' ).prop( 'disabled', false );

					if ( responseObj.error ) {
						$.alert({
							title: cardealer_custom_sidebar_obj.alert,
							content: responseObj.msg,
							escapeKey: true,
						});
					} else {
						$.each( responseObj.sidebar, function( key, value ) {
							var tbodyRef = document.getElementById('cardealer-admin-sidebar-table').getElementsByTagName('tbody')[0];
							// Insert a row at the end of table
							var newRow    = tbodyRef.insertRow();
								newRow.id = value.id;

							// Insert a cell at the end of the row
							var newCell_name             = newRow.insertCell();
								newCell_name.className   = 'cardealer-sidebar-table-name';
							var newCell_id               = newRow.insertCell();
								newCell_id.className     = 'cardealer-sidebar-table-id';
							var newCell_action           = newRow.insertCell();
								newCell_action.className = 'cardealer-sidebar-table-action';
								newCell_action.innerHTML   = '<a data-id="' + value.id + '" class="delete-sidebar button button-danger" href="javascript:void(0);"><i class="fa fa-trash"></i></a>';

							newCell_name.appendChild( document.createTextNode( value.name ) );
							newCell_id.appendChild( document.createTextNode( value.id ) );
						});

						$( '#cardealer_sidebar_name' ).val('');

						if ( $( '#cardealer-admin-sidebar-table .cardealer-sidebar-table-body tr:not(.empty-sidebar)' ).length > 0 ) {
							$( '#cardealer-admin-sidebar-table .cardealer-sidebar-table-body tr.empty-sidebar' ).remove();
						}
					}
				}
			});
		});

		$( document ).on( 'click', '#cardealer-admin-sidebar-table .delete-sidebar', function( e ) {
			e.preventDefault();

			var current_button    = $( this ),
				current_button_id = current_button.attr( 'data-id' ),
				tbody             = current_button.closest('.cardealer-sidebar-table-body');

			$.confirm({
				title: false,
				content: cardealer_custom_sidebar_obj.delete_sidebar_alert,
				escapeKey: 'cancel',
				buttons: {
			        confirm: {
						text: cardealer_custom_sidebar_obj.btn_del, // text for button
						btnClass: 'btn-red', // class for the button
						action: function( button ){
							jQuery.ajax({
								url        : cardealer_custom_sidebar_obj.ajaxurl,
								method     : 'POST',
								data       : {
									'nonce'  : cardealer_custom_sidebar_obj.sidebar_nonce,
									'action' : 'delete_cardealer_sidebar',
									'id'     : current_button_id,
								},
								dataType   : 'json',
								beforeSend : function() {
									jQuery( this ).prop( 'disabled', true );
									jQuery( '.cardealer-sidebar-table-body' ).addClass( 'loading' );
								},
								success: function(responseObj){
									jQuery( '.cardealer-sidebar-table-body' ).removeClass( 'loading' );
									jQuery( this ).prop( 'disabled', false );
									if ( responseObj.error ) {
										$.alert({
											title: cardealer_custom_sidebar_obj.alert,
											content: responseObj.msg,
											escapeKey: true,
										});
									} else {
										var sidebar_id = responseObj.sidebar_id;
										$( '#cardealer-admin-sidebar-table tr#' + sidebar_id  ).remove();
										if ( $( '#cardealer-admin-sidebar-table .cardealer-sidebar-table-body tr:not(.empty-sidebar):not(#' + sidebar_id + ')' ).length === 0 ) {
											var new_row    = $('<tr>', { class: 'empty-sidebar' }),
												new_row_td = $('<td>', { colspan: 3 }).appendTo( new_row );

											new_row_td.text( cardealer_custom_sidebar_obj.no_sidebar );
											new_row.append( new_row_td );
											tbody.append( new_row );
										}
									}
								}
							});
						}
					},
			        cancel: {
						text: cardealer_custom_sidebar_obj.btn_cancel,
						action: function( button ){
						}
					}
				}
			});
		});
	});

})(jQuery);
