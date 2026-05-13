const $ = jQuery.noConflict();
// const $ = jQuery;
$.fn.getForm = function( ) {
    const $el = $(this).prop('tagName') === 'form' ? $(this) : $(this).closest('form');
    // d($(this).closest('form'));
    
    return $el.data('form');
}
$.fn.getTopField = function () {
    const field = $(this).getFieldObject();
    const fieldset = $(this).getFieldsetObject();
    // console.log('this',$(this)[0],'field',field,'fieldset',fieldset);
    return fieldset ? fieldset : field;
}
$.fn.getFieldsetObject = $.fn.getFieldset = function () {
    d('get fieldset?');
    const $el = $(this).prop('tagName') === 'fieldset' ? $(this) : $(this).closest('fieldset');
    d('find fieldset:',$el[0]);
    const path = $el.data('path');
    const form = $(this).getForm();
    // d('found form: this',$(this)[0],'field',$el[0],'$form',$form,'form',form);
d('path:',path);
    if (form) return form.findField(path);
}

$.fn.getDuplicator = $.fn.getFieldset = function () {
    const $el = $(this).hasClass('duplicator') ? $(this) : $(this).closest('.duplicator.form-group');
    // d('el:',$el[0]);
    // d('path:',path);
    const path = $el.data('path');
    const form = $(this).getForm();
    // d('form:',form);
    // d('found form: this',$(this)[0],'field',$el[0],'$form',$form,'form',form);

    if (form) return form.findField(path);
}

$.fn.getField = $.fn.getFieldObject = function () {
    const Field = require('../../inc/Field');
    const $el = $(this).hasClass('form-group') ? $(this) : $(this).closest('.form-group[data-path]');
    if ($el.data('field')) return $el.data('field');

    let field;
    const form = $el.getForm();
    const path = $el.data('path');

    if (form) field = form.findField(path);

    if (!field && $el.data('route')) {
        // d('new field from', $el[0]);
        field = new Field($el);
    }
    if (field) {
        // d('found field:',field);
        $el.data('field', field);
        return field;
    }
}
$.fn.getEditorObject = function () {
    d(" NO GET EDITOR OBJECT FUNCTION");
    return null;
}
module.exports = $;