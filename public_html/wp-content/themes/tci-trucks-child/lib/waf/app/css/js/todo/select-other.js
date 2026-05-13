const initSelectOther = () => {
    // Select field "other" value
	$(document).delegate( 'select', 'change', function(e) {
		var $s = $(this);
		var val = $s.val()
		if( val ) val = $.trim(val);
		var required;
		if( val == 'other' || val == 'Other' ) {
			var $p = $s.closest('.form-group');
			var name = $s.attr('name');
			if( $(this).attr('aria-required') ) var required = ' aria-required="true"';
			if( $(this).attr('required') ) var required = ' required="required"';
			var html = '<div class="other-input"><input class="other" type="text" name="'+name+'" value=""'+required+'><a class="cancel" href="#">X</a></div>';
			$s[0].outerHTML = html;
			$p.data('html',$s[0].outerHTML);
			var $input = $p.find('input');
			var dname = $s.attr('data-name');
			if( dname ) $input.attr('data-name',dname);
			$input.focus().select();
		}
	});

	// Other input cancel button
	$(document).delegate('.other-input a.cancel', 'click', function(e) {
		var html = $(this).closest('.form-group').data( 'html' );
		$(this).closest('.other-input')[0].outerHTML  = html;
		e.preventDefault();
	});

	$(document).delegate( 'input.other', 'blur', function() {
		var html = $($(this).closest('.form-group').data( 'html' ));
		var val = $(this).val();

		if( val ) {
			var new_option = '<option class="other" value="'+val+'" selected="selected">'+val+'</option>';
			$(html).find('option[value=other]').before( new_option );
		}
		$(this).closest('.other-input')[0].outerHTML  = $(html)[0].outerHTML;
	});

}
module.exports = initSelectOther;