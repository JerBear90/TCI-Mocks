const $ = require('../../ui/$.getForm');
const Form = require('../../inc/Form');

const renderForm = function (e) {
    const $button = $(this);
    const uuid = $button.data('uuid');
    const edit = $button.data('edit');
    const $target = $button.data('target') || $button.parent().find('.editor');;

    const form = forms[uuid];
    d('uuid', uuid);
    if (form) {
        form.args.edit = edit;
        // d(form.args);
        $target.html('<div class="border p-5">' + form.render() + '</div>');
    }
}
const deleteForm = e => {

}
module.exports = { renderForm, deleteForm }