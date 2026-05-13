const $ = jQuery.noConflict();

export const open = function (e) {
    var file_frame;
    var wp_media_post_id = wp.media.model.settings.post.id;

    var post_id = $("#post_ID").val();

    // Uploading files
    var $input = $(this).parent().find('input');
    var $preview = $(this).parent().find('.preview');
    var $img = $(this).parent().find('img');
    var $button = $(this);

    var type = $button.data('type') ? $button.data('type') : ['video', 'image']
    d('type:', type);
    e.preventDefault();
    if (file_frame) {
        // if( post_id ) file_frame.uploader.uploader.param('post_id', post_id);
        // Open frame
        file_frame.open();
        return;
    } else {
        // Set the wp.media post id so the uploader grabs the ID we want when initialised
        if (post_id) wp.media.model.settings.post.id = post_id;
    }
    // Create the media frame.
    file_frame = wp.media.frames.file_frame = wp.media({
        title: 'Select a image to upload',
        button: {
            text: 'Finished',
        },
        library: {
            type: type
        },
        multiple: false	// Set to true to allow multiple files to be selected
    });

    // When an image is selected, run a callback.
    file_frame.on('select', function () {
        // We set multiple to false so only get one image from the uploader
        var attachment = file_frame.state().get('selection').first().toJSON();
        // Do something with attachment.id and/or attachment.url here
        d(attachment);
        $preview.show();
        $img.attr('src', attachment.url).css('height', 'auto');
        $input.val(attachment.url);
        // Restore the main post ID
        wp.media.model.settings.post.id = wp_media_post_id;
    });
    // Finally, open the modal
    file_frame.open();
}