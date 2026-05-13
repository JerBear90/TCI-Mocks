const $ = jQuery;
export const submit = e => {
    e.preventDefault();
    if( $(e.target).data('ajax') === false ) return true;
    const form = $(e.target).getForm();
    d('form:',form,$(e.target)[0]);
    d('data:',$(e.target).data());
    if( !form ) return true;
    // d('submitting form?',form);
    if (form) {
        if (!form.args.ajax) return true;
        e.preventDefault();
        form.submit();

    } else {
        d('no form object found');
        return;
    }
}
export const complete = (e, data) => {
    if( e.isDefaultPrevented() ) {
        d('--default prevented');
        return;
    }
    // d('complete',data);
    if ($(e.target).data('ajax') === false) return true;
    const form = $(e.target).getForm();
    if( !form ) return;
    if( form.defaultPrevented ) return;
    form.addLoading('danger', true);
    form.changed = false;
    // d('data:',data);
    if( data ) {
        d('complete',data);
        form.removeLoading();
        // console.log('[formHandler] form response', data);z
        if (typeof (data) == 'string' ) return form.message({ status: 'info', message: data })
        if (data.status && data.message) form.message(data);
        
        if (data.messages) data.messages.forEach(item => form.message(item));
        data.messages = null;
        // d('form',form.$form[0])
        $(form.$form).processResponse(data);
    }
}
export const saveToLocalStorage = e => {
    const field = $(e.target).getField();
    if( !field ) return;
    if (field.disabled) return;
    const formObj = $(e.target).getForm();
    
    const {id,method,form} = formObj.args;
    const form_id = id ? form+'_'+id : form;
    const key = 'waf_' + form_id;

    const values = [];
    formObj.allFields().forEach( field => {
        const data = {
            path: field.path,
            value: field.value
        }
        values.push(data);
    });
    localStorage.setItem( key, JSON.stringify(values) );
    // d(' set form',JSON.parse(localStorage.getItem(key)));
}