const $ = require('../ui/$.getForm');
const Form = require('./Form');
const Field = require('./Field');
const Fieldset = require('./Fieldset');
const Input = require('./Input');
const Handlebars = require('handlebars');
class FieldEditor extends Field {
    constructor(field) {
        super(field.context, null, field.form);

        this.context.edit = false;
        this.field = field;
        field.context = this.context;
        this.orig = { ...this.context }
        this.containerName = 'fieldEditor';
        this.json = formConfig.json.field;

        const type = this.context.type;
        const specific = formConfig.json.fields[type];

        if (specific) this.json.form.specific = JSON.parse(specific)

        this.editorForm = new Form(this.json.form, { edit: false });

    }

    registerPartials(fields) {
        if (!fields) return;
        return fields.map(field => {
            const k = field.name;
            Handlebars.registerPartial(k, $(field.render()).html());
        });
    }

    registerSpecific() {
        const type = this.context.type;
        const specific = formConfig.json.fields[type];

        if (specific) {
            let data = JSON.parse(specific);
            data.name = 'specific';
            Object.entries(data.fields).forEach(([k, field]) => {
                field.edit = false;
                field.data = { ...field.data, key: field.key }
                field.value = this.context[field.key];
            });
            const field = new Fieldset(data, 'specific', this.form);
            Handlebars.registerPartial('specific', field.render());
            // return {name:'specific',$field}
            // d('addit specific',field);
            this.editorForm.fields.push(field);
            // d(this.editorForm);
        } else {
            // d('nothing specific');
            Handlebars.registerPartial(k, '');
        }

        return '';
    }
    render($el = null) {
        // console.log('rendered editor');
        d('field', $(this.field.render()).html())
        Handlebars.registerPartial('field', $(this.field.render()).html());
        this.registerPartials(this.editorForm.fields);
        this.registerSpecific();
        this.context.uuid = this.field.uuid;
        const container = formConfig.templates.containers['fieldEditor'];
        // d(container,formConfig.templates);
        const html = $(Handlebars.compile(container)(this.context));
        // partials.forEach( ({$field,name}) => $field ? $html.find(`#field-editor-partial-${name}`).replaceWith($field) : '' );

        if ($el) $el.replaceWith($(html));
        return html;
    }
    setValue(k, val) {
        this.context[k] = val;
    }
}
module.exports = FieldEditor;