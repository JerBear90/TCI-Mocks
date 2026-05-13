global.d = console.log;
const $ = jQuery.noConflict();
const inputHandler = require('../ui/inputHandler');
const fieldHandler = require('../ui/fieldHandler')
const formHandler = require('../ui/formHandler')
const { unloadWarning } = require('../ui/unload');
const initEventHandlers = async () => {
    if (!window.formConfig) return;
    // Inputs
    $(document).on('keyup change', '.form-group[data-path] :input', inputHandler.change);
    $(document).on('click', '.form-group[data-uuid] .clear', inputHandler.clear);
    $(document).on('focus', '.form-group[data-uuid] :input', inputHandler.focus);
    // $(document).on('render', '.form-group[data-uuid]', renderInput);

    // Fields
    $(document).on('change', 'form.waf :input', fieldHandler.submit);
    $(document).on('click', '.form-group[data-route] .btn', fieldHandler.submit);
    $(document).on('complete', '.form-group', fieldHandler.complete)

    // Mark which submit button
    $(document).on('click','.form-group.submit', fieldHandler.submitValue );
    // Forms
    $(document).on('submit', 'form.waf', e => {
        d('--submit')
        formHandler.submit(e);
    });
    $(document).on('complete', 'form.waf', formHandler.complete);
    
    // Unload
    $(window).on('beforeunload', unloadWarning);

    // Invalid inputs
    $(document).on( 'change', '.invalid *,.invalid', e => {
        $(e.target).removeClass('invalid');
        $(e.target).closest('.invalid').removeClass('invalid');
    });
}

module.exports = { initEventHandlers } 