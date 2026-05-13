global.d = console.log;
const $ = jQuery.noConflict();
const inputHandler = require('../ui/inputHandler');
const fieldHandler = require('../ui/fieldHandler')
const fieldsetHandler = require('../ui/fieldsetHandler')
const formHandler = require('../ui/formHandler')
const { unloadWarning } = require('../ui/unload');
const initEventHandlers = async () => {
    if (!window.formConfig) return;
    // Inputs
    // $(document).on('keyup change', '.form-group[data-path] :input', inputHandler.change);
    $(document).on('change', 'form.autosubmit :input', e => {
        if( !e.isDefaultPrevented() ) formHandler.submit(e);
    });
    $(document).on('input change', 'form.waf[data-handler=advanced] .form-group[data-path] :input[type=range]', inputHandler.rangeChange);
    $(document).on('click', 'form.waf[data-handler=advanced] .form-group[data-path] .clear', inputHandler.clear);
    $(document).on('focus', 'form.waf[data-handler=advanced] .form-group[data-path] :input', inputHandler.focus);
    $(document).on('render', 'form.waf[data-handler=advanced] .form-group[data-path]', inputHandler.render );
    // $(document).on('change', '.form-group :input', inputHandler.change );
    $(document).on('keydown', 'form.waf[data-handler=advanced] .form-group[data-maxlength] :input', inputHandler.checkLength );
    

    // Localsaving
    $(document).on('change', 'form.localStorage.waf[data-handler=advanced] :input', formHandler.saveToLocalStorage );

    // Fields
    $(document).on('change', 'form.waf[data-handler=advanced] [data-route] :input', fieldHandler.submit);
    $(document).on('click', 'form.waf[data-handler=advanced] .form-group[data-route] > .btn, form.waf[data-handler=advanced] .form-group[data-route] > button', fieldHandler.submit);
    $(document).on('complete', 'form.waf[data-handler=advanced] .form-group', fieldHandler.complete)


    // Fieldsets
    $(document).on('click', 'form.waf[data-handler=advanced] fieldset [type=submit]', fieldsetHandler.submit);
    $(document).on('complete', 'form.waf[data-handler=advanced] fieldset', fieldsetHandler.complete);

    // Mark which submit button
    
    // Forms
    // d('-- set form handlers');
    $(document).on('submit', 'form.waf[data-handler=advanced]', formHandler.submit );
    
    $(document).on('complete', 'form.waf[data-handler=advanced]', formHandler.complete);
    $(document).on('failed', 'form.waf[data-handler=advanced]', formHandler.complete);
    
    // Unload
    // $(window).on('beforeunload', unloadWarning);

    // Invalid inputs
    $(document).on( 'change', '.invalid *,.invalid', e => {
        // d('clear invalid');
        const $field = $(e.target).closest('.form-group')
        // d('field:',$field[0]);
        // d($field.find('.errors')[0])
        $field.find('.errors').remove();
        $(e.target).removeClass('invalid');
        $field.removeClass('invalid');
        
    });
}

module.exports = { initEventHandlers } 