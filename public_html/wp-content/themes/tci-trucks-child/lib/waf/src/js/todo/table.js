const initTable = () => {
    	// Table row add
	$(document).delegate( 'th a.add-row', 'click', function() {
		var $table = $(this).closest('table');
		$addrow = $table.find('tbody tr:last').clone();
		$addrow.clearForm().removeClass('addrow');

		var basename = $table.data('name');
		var i = $addrow.data('index');
		var c = i+1;
		$addrow.data('index',c);
		$addrow.attr('data-index',c);
		var find = basename+'['+i+']';
		var replace = basename+'['+c+']';

		(function(find,replace) {
			$addrow.find(':input').each( function() {
				var name = $(this).attr('name');

				var new_name = name.replace(find,replace);
				$(this).attr('name',new_name);
			});
		})(find,replace);
		// Alter any textfield ids for tinymce compatibility
		/*
		$addrow.find( '.tinymce-bd' ).each( function() {
			var id = $(this).attr('id');
			var rand = Math.floor( Math.random()*1000 );
			id = id + '_' + rand;
			d(id);
			$(this).attr(id);
		});
		*/


		$table.find('tbody').append($addrow);
		return false;
	});

	// Table row remove
	$(document).delegate( 'td a.remove-row', 'click', function() {
		$tbody = $(this).closest('tbody');
		if( $tbody.find('tr').length > 1 ) $(this).closest('tr').remove();
		return false;
    });
}
module.exports = initTable;