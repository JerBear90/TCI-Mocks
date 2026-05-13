const initValidity = () => {
    	// Remove invalidity on keyup
	$(document).on( 'keyup', '.invalid', function() {
		var $form_p = $(this).closest('.form-group');
		$(this).removeClass('invalid');
		$form_p.find('.invalid').removeClass('invalid');
		$form_p.find('.invalid-error').remove();

		// Clear form invalid message if this is the last invalid field
		if( $(this).closest('form').find('.form-group .invalid-error').length == 0 ) {
			$(this).closest('form').find('.messages .invalid-error').remove();
		}
	});

	// Remove invalidity on readio or checkbox click
	$(document).on( 'click', 'input[type=checkbox].invalid,input[type=radio].invalid', function() {
		var $form_p = $(this).closest('.form-group');
		$(this).removeClass('invalid');
		$form_p.find('.invalid').removeClass('invalid');
		$form_p.find('.invalid-error').remove();

		// Clear form invalid message if this is the last invalid field
		if( $(this).closest('form').find('.form-group .invalid-error').length == 0 ) {
			$(this).closest('form').find('.messages .invalid-error').remove();
		}
	});

	// Remove invalidity on select change
	$(document).on( 'change', 'select.invalid', function() {
		if( $(this).val() ) {
			var $form_p = $(this).closest('.form-group');
			$(this).removeClass('invalid');
			$form_p.find('.invalid').removeClass('invalid');
			$form_p.find('.invalid-error').remove();

			// Clear form invalid message if this is the last invalid field
			if( $(this).closest('form').find('.form-group .invalid-error').length == 0 ) {
				$(this).closest('form').find('.messages .invalid-error').remove();
			}
		}
	});
}
module.exports = initValidity;