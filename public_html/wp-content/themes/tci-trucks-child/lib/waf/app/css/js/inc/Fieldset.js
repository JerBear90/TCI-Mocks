const $ = require('../ui/jquery/$.getForm');
const Handlebars = require('handlebars');
const Field = require('./Field');
const Duplicator = require('./Duplicator');
const { getDataAttr } = require('../utils/getDataAttr');

class Fieldset extends Field {
    constructor(data, k, form, parent = null) {
        super(data, k, form);
        this.fields = [];
        if (parent) {
            this.parent = parent;

        }
        if (data.fields) {
            Object.entries(data.fields).forEach(([key, datum]) => {

                if (datum.name) key = datum.name;
                if (datum.type === 'fieldset') this.fields.push(new Fieldset(datum, key, form, this));
                else if (datum.type === 'duplicator') this.fields.push(new Duplicator(datum, key, form, this));
                else if (typeof (datum) === 'object') this.fields.push(new Field(datum, key, form, this));
            })
            Object.entries(data).forEach(([d, datum]) => {
                if (typeof (datum) === 'string') this.context[d] = datum;
            })
        }

        if (data.value) {
            // console.log('fieldset has value', data.value);
            this.value = data.value;
        }
        this.allFields = this.allFields.bind(this);
        // d('created fieldset', this);
    }
    get value() {
        let values = {}
        this.fields.forEach(field => {
            if (field instanceof Fieldset)
                if (field.context.useInPath || !isNaN(parseInt(field.name, 10)))
                    values[field.jsonPath] = field.value;
                else {
                    d('not included fieldset', field);
                    values = { ...values, ...field.value }
                }
            else
                values[field.jsonPath] = field.value;
        });
        // d('fieldset values', values);
        return values;
    }
    set value(values) {
        if (!values) return;
        // d('value to set', values);
        this.fields.forEach(f => {
            if (!Array.isArray(values)) {
                d("NOT ARRAY", values);
                // return;
            }
            const data = values.find(v => v.path == f.path);
            if (data) f.value = data.value;
            if (f instanceof Fieldset) f.value = values;
            else if (data) f.value = data.value;
        });
    }
    get $fieldset() {
        return $field;
    }
    get $field() {
        const $ = jQuery.noConflict();

        return $(this.sel);
    }
    get $append() {
        return $(this.sel + ' > .bd >.append');
    }
    get $fields() {
        return $('.form-group', this.sel);
    }

    get template() {
        const container = this.context.container || 'fieldset';
        return this.form.getTemplate(container, 'containers');
    }
    get invalid() {
        return this.fields.reduce((invalid, f) => (invalid || f.invalid), false);
    }
    allFields() {
        // d('ALL FIELDS', this);
        return this.form.allFields(this);
    }
    renderValidation() {

    }
    registerPartial() {
        const html = this.fields.reduce((html, field) => {
            if (this.context['fieldClass']) {
                field.context['class'] = `${field.context['class']} ${this.context['fieldClass']}`;
            }
            if (this.context['inputClass']) {
                field.context['inputClass'] = `${field.context['inputClass']} ${this.context['inputClass']}`;
                field.input.context['inputClass'] = `${field.input.context['class']} ${this.context['inputClass']}`;
            }

            html += field.render();
            return html;
        }, '');
        Handlebars.registerPartial('fields', html);
    }
    renderFields() {
        return this.fields.reduce((html, field) => {
            if (this.context['fieldClass']) {
                field.context['class'] = `${field.context['class']} ${this.context['fieldClass']}`;
            }
            if (this.context['inputClass']) {
                field.context['inputClass'] = `${field.context['inputClass']} ${this.context['inputClass']}`;
                field.input.context['inputClass'] = `${field.input.context['class']} ${this.context['inputClass']}`;
            }
            html += field.render();
            return html;
        }, '');
    }
    render() {
        this.registerPartial();
        getDataAttr(this.context);
        // d('context', this.context);
        const html = Handlebars.compile(this.template)(this.context);
        // d('render fieldset',this.context.data)
        return html;
        const $fieldset = $(html);


        $fieldset.data('fieldset', this);
        this.$dom ? this.$dom.replaceWith($fieldset) : this.$dom = $fieldset;
        return $fieldset;
        if (fields.length) {
            let $last = fields.shift();
            $holder.replaceWith($last);
            // console.log($last[0]);
            fields.forEach($field => {
                // console.log($field[0]);
                $last.after($field);
                $last = $field;
            });
        }
        // console.log('RENDERED FIELDSET');
        // console.log('-----');
        return $fieldset;
    }
}
module.exports = Fieldset;