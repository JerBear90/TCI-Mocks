const $ = jQuery.noConflict();
const ui = require('./ui');
$.fn.mediaInput = () => {
    $(document).on('click', '.media-button', ui.open );
        
    // Restore the main ID when the add media button is pressed
    jQuery('a.add_media').on('click', function () {
        wp.media.model.settings.post.id = wp_media_post_id;
    });
}
jQuery(document).ready(function ($) {
    d('-- load media input');
    $('.form-group.media').mediaInput(); 
});