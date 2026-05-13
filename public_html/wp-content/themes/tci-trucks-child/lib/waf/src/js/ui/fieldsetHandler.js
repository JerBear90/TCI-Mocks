const $ = require('./jquery');
const Field = require('../inc/Field');
const submit = e => {
    // d('submit field');
    e.preventDefault();
    let field = $(e.target).getFieldset();
    if( !field ) return true;
    let $field = field.$field;
    // d('field:',field,$field[0]);
    if (!field) field = new Field($field);
    if (field) field.disabled = true;
    const data = $field.data();
    if (!data) {
        d('-- no data');
        return;
    }

    if (data.confirm) {
        if (!confirm(data.confirm)) return false;
    }
    const name = field.name;
    const value = field.value;
    data[name] = value;


    d("SUBMIT FIELD", data);
    
    if (field.context.data.route) {
        field.submit();
    }
}
const complete = function (e, data) {

    e.stopPropagation();
    $el = $(e.target).closest('fieldset');
    $(e.target).processResponse(data, $el);
    const field = $el.getFieldset();
    if( field ) field.disabled = false;
}
const submitValue = e => {
    const field = $(e.target).getField();
    // d('field',field);
    if (!field) return true;
    if (field.disabled) return;
    // if( field.context.type == 'submit' ) return;

    const submits = field.form.allFields().filter(f => f.context.type == 'submit');
    submits.forEach(s => s.value = null);
    if (field.context.method) field.form.method = field.context.method;
    if (field.context.route) field.form.route = field.context.route;

    field.value = true;
    return true;
}
module.exports = { submit, complete, submitValue }