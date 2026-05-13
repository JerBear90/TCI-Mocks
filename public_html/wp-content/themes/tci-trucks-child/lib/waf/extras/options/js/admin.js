var bheight = 0;
var field_id;
jQuery(document).ready(function ($) {

	var changeTab = function (e) {
		
		// get the id from the link
		var id = window.location.hash;
		if( !id ) id = $('#options .tabs a').first().attr('href');
		// d('id:',id);
		// d('changing tab', id)
		// find the closest form
		$form = $(id).closest('form')

		// Hide fieldsets
		$form.find('> .bd > fieldset').css('display', 'none');

		// Set form height
		$form.find('fieldset.buttons').each(function () {
			val = $(this).outerHeight();
			consolelog(val);
			bheight += parseInt(val);
		});

		// Show this one
		$(id).css('display', 'block');

		// Reset tab classes
		$('.tabs a').removeClass('current');
		$(this).addClass('current');
		

		// Set active options tab
		$('#options .tabs .nav-tab-active').removeClass('nav-tab-active');
		$('a[href="' + id + '"]').addClass('nav-tab-active');

		// Set "current" class for sidebar menu
		$('#toplevel_page_options li.current').removeClass('current');
		$('a[href$="'+id+'"]').parent().addClass('current');
		$(window).scrollTop(0);
		// Prevent link
		return false;
	}

	$("#options fieldset:not(.save-reset) >p:odd").addClass('odd');
	$("#options .item:odd").addClass('odd');
		
	// $(document).on('click', '.tabs > li > a', );
	changeTab();
	$(window).on( 'hashchange', changeTab );
});
