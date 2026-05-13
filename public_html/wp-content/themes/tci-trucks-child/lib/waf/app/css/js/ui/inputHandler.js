const $ = require('./jquery');
const change = e => {
    if (e.isDefaultPrevented()) return false;

    const field = $(e.target).getField();
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
    field.value = $(e.target).val();
    field.form.changed = true;
    // updatePercent(e);

}
const clear = e => {
    e.preventDefault();
    const field = $(e.target).getFieldObject();
    field.context.value = '';
    field.$text.val('');
    field.$text.focus();
}

const focus = e => {
    const form = $(e.target).getForm();
    const field = $(e.target).getTopField();
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
module.exports = { change, clear, focus }