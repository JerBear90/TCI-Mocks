const $ = jQuery;
export const submit = e => {
    d('submit?');
    if( $(e.target).data('ajax') === false ) return true;
    const form = $(e.target).getForm();
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
export const complete = (e, res) => {
    if( e.isDefaultPrevented() ) return;
    if ($(e.target).data('ajax') === false) return true;
    const form = $(e.target).getForm();
    form.addLoading('danger', true);
    form.changed = false;
    const { data } = res; 
    // d('res:',res);
    if (data) {
        // d('complete',data);
        form.removeLoading();
        if (typeof (data) == 'string') return form.message({ status: 'success', message: data })
        if (data.status && data.message) form.message(data);
        if (data.messages) data.messages.forEach(item => form.message(item));
        // d('form',form.$form[0])
        $(form.$form).processResponse(data);
    }
}