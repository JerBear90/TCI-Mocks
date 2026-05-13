const $ = jQuery.noConflict();
const Form = require('../../inc/Form');
$.fn.render = function (form) {
    if( $(this).data('ajax') == false ) return;
    if (!form) {
        const form_id = $(this).data('id');
        form = new Form(form_id);
    }
    const rendered = $(this).data('rendered');
    rendered ? $(this).data('form', form) : form.render();
    // d('remove disabled');
    $(this).data('form', form);
    form.$form = this;
    form.allFields().forEach(field => {
        
        // d('field', field.$field[0], 'render');
        field.$field.trigger('render');

    })
    $(this).find('*[type=submit][disabled]').removeAttr('disabled');

    return form;
}