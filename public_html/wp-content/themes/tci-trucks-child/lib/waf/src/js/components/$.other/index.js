const {renderOther} = require('./ui');
jQuery.fn.initOther = () => {
    const $ = jQuery.noConflict();
    $(document).on( 'change', 'form.waf select', renderOther );
    $(document).on( 'blur', 'form.waf input.other', renderOther );
    $(document).on( 'change', 'form.waf input[type=radio],input[type=checkbox]', renderOther );
    $(document).on( 'click', 'form.waf .clear-other', removeOther );
}
jQuery(document).ready(function ($) {
    $(document).initOther();
});