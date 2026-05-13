const $ = jQuery.noConflict();
const ui = require('./ui');

$.fn.stripeInput = () => {
    $(document).on( 'render', '.form-group.stripe', e => {
        // d('--rendering stripe');
        $(this).closest('form').addClass('stripe');
        const existing = $('input[name=stripe_card]:checked').val();
        if( !existing || $('input[name=stripe_card]').length == 0 ) ui.renderStripe(e);
    });
    $(document).on( 'submit', 'form.stripe', ui.submitStripe );
    $(document).on('change', '[name=stripe_card]', ui.toggleCardInput);
}
jQuery(document).ready(function ($) {
    $(document).stripeInput();
});