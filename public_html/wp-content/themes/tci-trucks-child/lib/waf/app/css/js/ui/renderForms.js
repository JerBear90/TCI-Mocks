const $ = require('./jquery');
const renderForms = (force = false) => {
    const $forms = $('form.waf');
    // d('rendering forms', $forms.length, $forms);
    $forms.each(function () {
        // d($(this).data());
        // d('rendering form');
        if( $(this).data('ajax') === false ) return;
        const { id, form } = $(this).data();
        if (id && !form) {
            // d('render form!', id);
            const form = $(this).render();
            $(this).data('uuid', form.uuid);
            $(this).attr('data-uuid', form.uuid);
            $(this).data('form', form);

        } else {
            // d('X form rendered previously', id, form);
        }
    });
}


module.exports = renderForms