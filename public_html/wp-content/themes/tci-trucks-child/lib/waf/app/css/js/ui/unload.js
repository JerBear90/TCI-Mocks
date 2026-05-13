const $ = require('./jquery');
export const unloadWarning = e => {
    // d('unload!');
    const $forms = $('form.waf');
    let block = false;
    $forms.each(function () {
        if( $(this).data('ajax') === false) return true;
        const form = $(this).getForm();
        if (form.args.exitWarning && form.changed) block = true;
    })
    if (block) return false;
}