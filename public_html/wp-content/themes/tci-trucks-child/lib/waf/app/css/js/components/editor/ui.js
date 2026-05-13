const $ = require('../../utils/jquery');
const FieldEditor = require('../../inc/FieldEditor');
const Field = require('../../inc/Field');
// const $ = require('jquery');

const showEditor = function (e) {
    const $form = $(this).closest('form');
    $form.find('.field-editor').each(hideEditor);

    const $field = $(this).closest('.form-group');
    const field = $field.getFieldObject();
    const uuid = field.uuid;

    // console.log('field to edit',field);
    const editor = window.editors[uuid] || new FieldEditor(field);
    // console.log('editor',editor);
    const $editor = editor.render();
    $field.replaceWith($editor);

    window.editors[uuid] = editor;
    $('body').addClass('editor-open');
}
const closeEditor = (e, save = false) => {
    e.preventDefault();
    d('close editor');
    const $editor = $(e.target).closest('.field-editor');
    const editor = $(e.target).getEditorObject();

    if (editor) {
        const context = save ? editor.context : editor.orig;
        context.edit = true;
        editor.field.context = context;
        const html = editor.field.render();
        $editor.replaceWith($(html));
        return false;
    }
}
const hideEditor = (i, el) => {
    const editor = $(el).getEditorObject();
    const field = editor.field;
    // console.log($editor);

    const context = editor.field.context;
    context.edit = true;
    field.context = context;
    field.render($editor);
    return false;
}
const saveEditor = e => closeEditor(e, true);
const cancelEditor = e => closeEditor(e, false);
const editorChange = e => {
    // if( e.isDefaultPrevented() ) return;
    const $editor = $(e.target).closest('.field-editor');
    const editor = $(e.target).getEditorObject();
    const field = editor.field;
    // console.log('preview',$(this).closest('.editor-field-preview').length);
    if ($(e.target).closest('.editor-field-preview').length) return false;


    const property = $(e.target).closest('.form-group').data('key');
    // d('editor',editor);
    const orig = editor.context[property];
    let value = field.context.value;

    if (field.context.type === 'key-value') {
        const k = field.context.keys.key;
        const v = field.context.keys.value;
        // console.log('key values:',k,v);
        value = value.map(val => {
            const object = { index: val.index }
            object[k] = val.key;
            object[v] = val.value;
            return object;
        });
    }
    // console.log('value:',value,property);
    editor.setValue(property, value);

    if (property === 'type') {
        if (orig != value) $editor.find('fieldset[name=specific]').html(editor.renderSpecific());
    }
    if (!editor.context.value) editor.context.value = editor.context.std;
    // console.log('context',editor.context);
    $editor.find('.editor-field-preview').html(editor.field.render());
}
const saveForm = e => {
    e.preventDefault();
    d('saving form');

    const $form = $(e.target).closest('form');
    // console.log($form[0],$form.data());
    const uuid = $form.data('uuid');
    const form = window.forms[uuid];
    d('forms:', form, forms, uuid);
    d(form.save());
}
module.exports = { showEditor, saveEditor, cancelEditor, saveEditor, editorChange, saveForm }