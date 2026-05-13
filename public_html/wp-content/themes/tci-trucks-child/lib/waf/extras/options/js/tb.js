jQuery(document).ready(function($) {
	$(document).delegate( '.upload-button', 'click', function() {
		field_id = $(this).attr('id').replace('_button','');
		if( $(field_id).attr('type') == 'hidden' ) image_id = 1;
		tb_show('', 'media-upload.php?type=image&_button_label=Use%20this%20One&amp;TB_iframe=true');
		return false;
	});
	
	window.send_to_editor = function(html) {
		imgurl = $('img',html).attr('src');
		if (imgurl == undefined) {
			var url = $(html).attr('href').replace(blogurl,'');
			$('#'+field_id).val(url);
			var title = $(html).attr('title');
		} else {
			console.log( imgurl+', '+field_id );
			var url = imgurl.replace( blogurl, '');
			var title = $('img',html).attr('title');
			$('#'+field_id).val(url);
			$('#'+field_id+'_x').val( $('img',html).attr('width') );
			$('#'+field_id+'_y').val( $('img',html).attr('height') );
			$('#'+field_id+'_title').val( title );
			$('#'+field_id).parent().find('.preview:first img:first').attr( 'src',imgurl );
		}
		tb_remove();		
	}
});
