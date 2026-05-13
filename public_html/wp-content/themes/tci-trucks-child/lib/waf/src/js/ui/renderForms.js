const $ = require('./jquery');
const renderForms = (force = false) => {
    const $forms = $('form.waf');
    // d('rendering forms', $forms.length, $forms);
    $forms.each(function () {
        // d($(this).data());
        // d('rendering form',$(this)[0]);
        // if( $(this).data('ajax') === false ) return;
        const { name, form } = $(this).data();
        // d('load form',$(this)[0],name,form)
        if (name && !form) {
            // d('render form!', name);
            const form = $(this).render();
            // d('form:',form)
            if( !$(this).data('rendered') ) {
                // d('-- render to dom');
                form.render( $(this) );
            }

            if (form) {
                // $(this).data('uuid', form.uuid);
                // $(this).attr('data-uuid', form.uuid);
                // d('add form to data', $(this)[0], form);
                form.$form.data('form', form);
            }

        } else {
            // d('X form rendered previously', id, form);
        }
    });
}


module.exports = renderForms