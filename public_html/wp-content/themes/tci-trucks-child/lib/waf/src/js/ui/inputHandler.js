const $ = require('./jquery');
const render = e => {
    const field = $(e.target).getField();
    const val = $(e.target).val();

    if( val && field) field.value = val;

}
const rangeChange = e => {
    const val = $(e.target).val();
    const field = $(e.target).getField();
    if (!field) return;
    if (field.disabled) return;
    const { min, minLabel, max, maxLabel } = field;
    if (val == min && minLabel) field.$field.find('.value').text(minLabel);
    else if (val == max && maxLabel) field.$field.find('.value').text(maxLabel);
    else field.$field.find('.value').text(val);
    
}

const checkLength = e => {
    // d('-- check field length');
    // if (e.isDefaultPrevented()) return false;
    const field = $(e.target).getField();
    let maxLength = field.context.data.maxLength;
    if (!field) return;
    
    // if (field.disabled) return;
    if( maxLength ) {
        // d('max:', field.context.maxLength);
        let length = field.value.length;
        // d(length + '/' + maxLength);
        field.$field.find('.length .value').text(length);
        if (e.keyCode == 46 || e.keyCode == 8) return true;
        if (length >= maxLength) {
            e.preventDefault();
            return false;
        }
        
    }
}
const change = e => {
    if (e.isDefaultPrevented()) return false;
    const field = $(e.target).getField();
    if( !field ) return;
    if( field.disabled ) return;
    field.value = field.realValue;
    // d('set field value',field.realValue);
    return true;
    
    // d('change!', field);
    if (!field) return;
    const $field = field.$field;

    if (field.context.type == "checkboxes") {
        field.value = [];
        d('checkboxes!');
        const value = [];
        $field.find('input').each(function () {
            if ($(this).is(':checked')) value.push($(this).val());
        })
        field.value = value;
        return;
    }

    if ($(e.target).val()) {
        $field.removeClass('invalid');
        $field.find(':input').removeClass('border boder-danger');
        $field.find('.alert').remove();
    }
    // field.value = $(e.target).val();
    d('set to', $(e.target).val());
    field.form.changed = true;
    // updatePercent(e);

}
const clear = e => {
    e.preventDefault();
    const field = $(e.target).getFieldObject();
    if (!field) return;
    if (field.disabled) return;
    field.context.value = '';
    field.$text.val('');
    field.$text.focus();
}

const focus = e => {
    const form = $(e.target).getForm();
    const field = $(e.target).getTopField();
    if (!field || !form) return;
    if (field.disabled) return;
    form.active = field;


    // if (form.args.keyboardNav) {
    //     if (isMobile()) {
    //         const scrollTop = $(e.target).offset().top - 60;
    //         $('html,body').stop();
    //         // $('html,body').scrollTop( scrollTop );
    //     }
    //     const $next = field.$field.nextAll('.form-group, fieldset');
    //     // d('next',$next.length,$next);
    //     $next.remove();
    // }
}
module.exports = { change, clear, focus, render, rangeChange, checkLength }
