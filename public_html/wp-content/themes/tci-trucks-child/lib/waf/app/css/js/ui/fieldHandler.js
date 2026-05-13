const $ = require('./jquery');
const Field = require('../inc/Field');
const submit = e => {
    let field = $(e.target).getFieldObject()
    const $field = $(e.target).closest('.form-group');
    if (!field) field = new Field($field);
    const data = $field.data();
    if (!data) return;

    // d("SUBMIT FIELD", data);
    if (field.context.data.route) {
        field.submit();
    }
}
const complete = function (e, data) {
    $form = $(this).closest('form');
    $(this).processResponse(data, $form);
    e.stopPropagation();
}
const submitValue = e => {
    const field = $(e.target).getField();
    d('field:',field);
    if( !field ) return true;
    const submits = field.form.allFields().filter( f => f.context.type == 'submit' );
    submits.forEach( s => s.value = null);
    field.value = true;
    return true;
}
module.exports = { submit, complete, submitValue }