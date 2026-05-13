const $ = require('../../utils/jquery');
const { showEditor, saveEditor, cancelEditor, editorChange, saveForm } = require('../ui/editor');
const initEditor = () => {

    $(document).on('click', '.form-group a.edit', showEditor);
    $(document).on('click', '.save-field-editor', saveEditor);
    $(document).on('click', '.cancel-field-editor', cancelEditor);
    $(document).on('click', '.cancel-form-editor', cancelEditor);
    $(document).on('click', '.save-form-editor', saveForm);

    $(document).on('change keyup', '.field-editor :input', editorChange);
}

module.exports = initEditor