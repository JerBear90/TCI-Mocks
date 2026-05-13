const $ = jQuery.noConflict();
const Form = require('../../inc/Form');
$.fn.render = function (form) {
    
    // d('rendering:',$(this)[0]);
    // if( $(this).data('ajax') == false ) return;
    const handler = $(this).data('handler');
    // d('handler:',handler);
    // if( handler == 'none' || handler == 'simple' ) {
    //     // d('-- skipe form',$(this)[0])
    //     return;
    // }
    if (!form) {
        const form_id = $(this).data('name');
        // d('rendering',form_id);
        if (!window.formConfig.forms[form_id]) {
            // d('--skip for now',form_id);
            return
            
        }
        form = new Form(form_id);
    }
    form.$form = this;
    // localStorage
    // d('form:',form);
    const hasLocal = form.$form.hasClass('localStorage');
    if (hasLocal) {
        
        const form_id = form.args.id ? form.args.form + '_' + form.args.id : form.args.form;
        const key = 'waf_' + form_id;
        const localString = localStorage.getItem(key);
        if (localString) var localForm = JSON.parse(localString);
        if (localForm) form.value = localForm;
    }
    // d('form:',form);
    const rendered = $(this).data('rendered');
    // d('rendered:', rendered);
    // d('rendered:',rendered);
    rendered ? $(this).data('form', form) : form.render();
    // d('remove disabled');
    // d('form:',form.name);
    $(this).data('form', form);
    // form.$form = this;
    // d('render form!', form.name);
    const url = form.$form.attr('action');
    const method = form.$form.attr('method');

    
    
    // d(form.$form[0]);
    // if( url ) form.args.url = url;
    if( method ) form.args.method = method;

    // d("SETTING FORMDATA")
    // d('fields:',form.allFields());
    form.allFields().forEach(field => {
        
        const {type} = field.context;
        field.disabled = true;
        if( field.context.type == 'fieldset' || field.context.type == 'duplicator' ) return;
       
        field.$field.trigger('render');
        // d('field:', field.name, field.$field[0] );
        if( field.$input.length ) {
            // d('unset value');
            field._value = undefined;
        }
        // field.$input.trigger('change');
        
        field.disabled = false;
    })
    // d('form:',form.$form.data())
    
    form.$form.find('[type=submit]').removeAttr('disabled');
    // form.$form.data('uuid',form.uuid);
    // $(this).attr('data-uuid', form.uuid);
    form.$form.data('form',form);
    form.$form.trigger('render');
    
    return form;
}