const $ = require('../../ui/jquery');
const updateConfirmation = e => {
    e.preventDefault();
    e.stopPropagation();
    const value = $(e.target).data('value') ? 'yes' : 'no';
    const field = $(e.target).getField();
    field.value = value;
    d('value:', value);
    if (field.context.other && value != 'other') {
        field.context.other = false;
        field.render(field.$field);
    } else if (!field.context.other && value == 'other') {
        field.context.other = true;
        field.render(field.$field);
    }
}
const keyUpdateConfirmation = e => {

    // d('confirmation keypress',e);
}
module.exports = { updateConfirmation, keyUpdateConfirmation }