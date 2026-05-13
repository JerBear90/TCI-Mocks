/*================================================
[  Table of contents  ]
================================================
:: Menu Height
:: Document ready functions
	:: Search
	:: Header menu
======================================
[ End table content ]
======================================*/

( function( $ ) {
	"use strict";

	/*******************
	:: Menu Height
	*******************/

	jQuery(document).ready(function($) {

		/**************************************
		:: Search cars with autocomplte
		***************************************/

		// search-3
		if ( $( '.cd-search-wrap' ).length > 0 ) {
			$('.cd-search-wrap').each(function(i,el) {
				var search_wrap       = $(this),
					search_input      = search_wrap.find('.cd-search-autocomplete-input'),
					seach_type        = search_input.data('seach_type'),
					search_open_btn   = search_wrap.find('.search-open-btn'),
					search_submit_btn = search_wrap.find('.cd-search-submit'),
					autocomplete_wrap = search_wrap.find('.cd-search-autocomplete'),
					autocomplete_ul   = autocomplete_wrap.find('.cd-search-autocomplete-list'),
					search_min_length = 2;

				// Header search open.
				if ( search_open_btn.length > 0 ); {
					search_open_btn.on('click', function () {
						if ( ! search_wrap.hasClass('search-open') ) {
							search_wrap.addClass('search-open');
						} else {
							search_wrap.removeClass('search-open');
						}
						autocomplete_ul.empty();
						autocomplete_ul.removeClass('has-autocomplete-data');
						search_input.val('');
						return false;
					});
				}

				search_input.on('keypress', function (e) {
					if ( e.which == 13 ){ //Enter key pressed.
						search_submit_btn.click();
					}
				});

				// On submit button click.
				if ( search_submit_btn.length > 0 ); {
					search_submit_btn.on('click', function () {
						autocomplete_ul.empty();
						autocomplete_ul.removeClass('has-autocomplete-data');
					});
				}

				$(document).click(function (e){
					if ( ! search_wrap.is( e.target ) && search_wrap.has( e.target ).length === 0 ) {
						if ( search_wrap.hasClass('menu-search-wrap') && search_wrap.hasClass('search-open') ) {
							search_wrap.removeClass('search-open');
						}

						if ( autocomplete_ul.hasClass('has-autocomplete-data') ) {
							autocomplete_ul.empty();
							autocomplete_ul.removeClass('has-autocomplete-data');
						}
					}
				});

				search_input.on('input', function() {
					var search_input_value = this.value;
					if ( search_input_value.length < search_min_length ) {
						autocomplete_ul.empty();
						autocomplete_ul.removeClass('has-autocomplete-data');
					}
				});

				var search_input_autocomplete = search_input.autocomplete({
					minLength: search_min_length,
					search: function(event, ui) {
						autocomplete_ul.empty();
						autocomplete_ul.removeClass('has-autocomplete-data');
					},
					source: function( request, response ) {
						jQuery.ajax({
							url: cardealer_header_js.ajaxurl,
							type: 'POST',
							dataType: "json",
							data: {
								'action': 'pgs_auto_complate_search',
								'ajax_nonce': cardealer_header_js.pgs_auto_complate_search_nonce,
								'search': request.term,
								'seach_type': seach_type,
							},
							beforeSend: function(){
							},
							success: function( resp ) {
								response( jQuery.map( resp, function( result ) {
									var return_data = {
										status: result.status,
										image: result.image,
										title: result.title,
										link_url: result.link_url,
										msg: result.msg
									};
									return return_data;
								}));
							}
						}).done( function(){
						});
					},
					minLength: 2,
				}).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
					var html = '';
					if(item.status){
						html += '<a href="'+item.link_url+'">';
						html += '<div class="search-item-container">';
						if(item.image){
							html += item.image;
						}
						html += item.title;
						html += '</div>';
						html += '</a>';
					} else {
						html += item.msg;
					}
					autocomplete_ul.addClass('has-autocomplete-data');
					return jQuery( "<li></li>" )
						.data( "ui-autocomplete-item", item )
						.append(html)
						.appendTo( autocomplete_ul );
				};
			});
		}

		/*************************
		:: Header menu
		*************************/

		var $mobile_sticky_status  = ( cardealer_header_js.sticky_header_mobile == true ) ? true : false;
		var $desktop_sticky_status = ( cardealer_header_js.sticky_header_desktop == true ) ? true : false;
		var screen_width           = screen.width;

		jQuery( document ).scroll( function() {
			var c_scroll    = jQuery( window ).scrollTop();
			var menu_sticky = jQuery( '#menu-1' );

			// Sticky topbar
			if ( cardealer_header_js.sticky_topbar == true ) {
				var topbar_sticky = jQuery( '.topbar' );
				if ( c_scroll >= 250 && screen_width > 992 ) {
					topbar_sticky.addClass( 'topbar_fixed' );
				} else {
					topbar_sticky.removeClass( 'topbar_fixed' );
				}
			}

			// Sticky header
			if ( $desktop_sticky_status ) {
				if ( screen_width >= 992 ) {
					if ( c_scroll >= 250 ) {
						menu_sticky.addClass( 'desktopTopFixed' );
					} else {
						menu_sticky.removeClass( 'desktopTopFixed' );
					}
				}
			}

			// Sticky mobile header
			if ( $mobile_sticky_status ) {
				if ( screen_width < 992 ) {
					if ( c_scroll >= 200 ) {
						menu_sticky.addClass( 'mobileTopFixed' );
					} else {
						menu_sticky.removeClass( 'mobileTopFixed' );
					}
				}
			}
		});

		// Mobile menu trigger
		$( document ).on( 'click', '.menu-mobile-collapse-trigger', function (e) {
			$( '.menu-mobile-collapse-trigger' ).toggleClass( 'active' );
			if ( $( '.menu-mobile-collapse-trigger' ).hasClass( 'active' ) ) {
				if( $( '#primary-menu, #mega-menu-wrap-primary-menu' ).parent( '.header-mobile-navigation' ).length > 0 ) {
					$( '#primary-menu, #mega-menu-wrap-primary-menu' ).parents( '.header-mobile-navigation' ).parent('.header-mobile-overlay-menu').addClass( 'mobile-menu-active' );
				}
				$( '#primary-menu, #mega-menu-wrap-primary-menu' ).addClass( 'mobile-menu-active' );
				$( '#primary-menu, #mega-menu-wrap-primary-menu' ).show();
			} else {
				if( $( '#primary-menu, #mega-menu-wrap-primary-menu' ).parent( '.header-mobile-navigation' ).length > 0 ) {
					$( '#primary-menu, #mega-menu-wrap-primary-menu' ).parents( '.header-mobile-navigation' ).parent('.header-mobile-overlay-menu').removeClass( 'mobile-menu-active' );
				}
				$( '#primary-menu, #mega-menu-wrap-primary-menu' ).removeClass( 'mobile-menu-active' );
				$( '#primary-menu, #mega-menu-wrap-primary-menu' ).hide();
			}
		});

		// Mobile menu show/hide child elements
		$( document ).on( 'click', '.menu-item-has-children a i', function ( e ) {
			e.preventDefault();

			if ( ! $( this ).closest( '.menu-item-has-children' ).hasClass( 'activeTriggerMobile' ) ) {
				$( this ).closest( '.menu-item-has-children' ).addClass( 'activeTriggerMobile' );
			} else {
				$( this ).closest( '.menu-item-has-children' ).removeClass( 'activeTriggerMobile' );
			}

			if ( $( this ).closest( '.menu-item-has-children' ).hasClass( 'activeTriggerMobile' ) ) {
				$( this ).closest( '.menu-item-has-children' ).children( '.sub-menu' ).show();
			} else {
				$( this ).closest( '.menu-item-has-children' ).children( '.sub-menu' ).hide();
			}
		});
	});

}( jQuery ) );
