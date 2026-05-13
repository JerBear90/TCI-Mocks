const initDuplicator = () => {
    	// Duplicator
	$(document).delegate( 'a.duplicate', 'click', function() {
		d('duplicate');
		var $c = $(this).parent();
		var $el = $c.find('.item').last();
		var $new = $el.clone();
		$new.find(':input').each( function() {
			$(this).val('');
		});

		var basename = $c.data('name');
		var i = $new.data('index');
		var c = i+1;
		d(i);
		d(c);
		$new.data('index',c);
		$new.attr('data-index',c);

		if( $c.hasClass('key-value') || $c.hasClass('key-arrays') ) {
			(function(find,replace,basename) {
				$new.find(':input').each( function() {
					var $field = $(this).closest('.field');
					var key = $field.data('key');
					var find = basename+'['+key+']['+i+']';
					var replace = basename+'['+key+']['+c+']';
					d(find);
					d(replace);
					var name = $(this).attr('name');
					var new_name = name.replace(find,replace);

					d(name);
					d(new_name);
					$(this).attr('name',new_name);
					d('-----');
				});
			})(find,replace,basename);
		} else {
			var find = basename+'['+i+']';
			var replace = basename+'['+c+']';

			d(find);
			d(replace);
			(function(find,replace) {
				$new.find(':input').each( function() {
					var name = $(this).attr('name');
					var new_name = name.replace(find,replace);
					$(this).attr('name',new_name);
					$(this).trigger('change');
					d(name);
					d(new_name);
				});
			})(find,replace);
		}
		$el.after( $new );
		$el.find(':input').first().trigger('change');
		return false;
	});

	$(document).delegate( 'a.remove-duplicate', 'click', function() {
		var $c = $(this).closest('.item');
		if( $c.siblings('.item').length == 0 ) {
			$c.find(':input').each( function() {
				$(this).val('');
			});
			return false;
		}
		$c.remove();
		return false;
	});
}
module.exports = initDuplicator;