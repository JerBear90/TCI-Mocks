/*================================================
[  Table of contents  ]
================================================
:: Document ready functions
	:: Newsletter shortcode
	:: Newsletter mailchimp
======================================
[ End table content ]
======================================*/
( function( $ ) {
	"use strict";

	// Check element exists.
	$.fn.exists = function () {
		return this.length > 0;
	};

	jQuery(document).ready(function($) {

		/************************
		:: Newsletter mailchimp
		************************/
		if ( $( '.mailchimp-newsletter-form' ).exists() ) {
			$( '.mailchimp-newsletter-form' ).each( function( index, element ) {
				var newsletter_form      = $( this ),
					newsletter_msg       = $( this ).find( '.newsletter-msg' ),
					newsletter_btn       = $( this ).find( '.newsletter-mailchimp' ),
					newsletter_spinner   = $( this ).find( '.newsletter-spinner' ),
					newsletter_loader    = $( newsletter_spinner ).find( '.cd-loader' ),
					newsletter_email     = $( this ).find( '.newsletter-email' ),
					newsletter_email_val = '',
					api_source           = newsletter_form.data('api_source'),
					api_key              = newsletter_form.data('api_key'),
					list_id              = newsletter_form.data('list_id'),
					mailchimp_nonce      = $( this ).find( '.mailchimp-nonce').val();

				newsletter_form.on( 'submit', function( event ){
					event.preventDefault();
					newsletter_btn.trigger('click');
				});

				newsletter_email.on( 'focus', function() {
					$( newsletter_msg ).hide().removeClass( 'error_msg' ).html( '' );
				});

				newsletter_btn.on( 'click', function() {
					newsletter_email_val = $( newsletter_email ).val();
					// var form_id           = $( this ).attr( 'data-form-id' );
					$.ajax({
						url: mailchimp_js_obj.ajaxurl,
						type:'post',
						data: {
							'action': 'mailchimp_singup' ,
							'newsletter_email' : newsletter_email_val,
							'mailchimp_nonce' : mailchimp_nonce,
							'api_source' : api_source,
							'api_key' : api_key,
							'list_id' : list_id,
						},
						beforeSend: function() {
							newsletter_loader.removeClass('hidden');
							$( newsletter_msg ).hide().removeClass( 'error_msg' ).html( '' );
						},
						success: function(msg){
							newsletter_msg.show();
							newsletter_msg.removeClass('error_msg');
							newsletter_msg.html(msg);
							// $('#process').css('display','none');
							// $('form#'+form_id+' .news_letter_name').val('');
							newsletter_email.val('');
							newsletter_loader.addClass('hidden');
						},
						error: function(msg){
							newsletter_msg.addClass('error_msg');
							newsletter_msg.html(msg);
							newsletter_msg.show();
							// $('#process').css('display','none');
						}
					});
					return false;
				});


			});
		}

	});
}( jQuery ) );
