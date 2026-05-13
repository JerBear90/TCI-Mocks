const $ = require('../ui/jquery/$.getForm');
const Handlebars = require('handlebars');
const Field = require('./Field');
const Fieldset = require('../inc');
const { getDataAttr } = require('../utils/getDataAttr');

class Duplicator extends Field {
    constructor(data, k, form, parent = null) {
        super(data, k, form);
        if (parent) this.parent = parent;
        const Fieldset = require('./Fieldset');
        this.form = form;
        this.name = k;
        this.orig = { ...data }
        this.orig.label = undefined;
        this.fields = [];
        this.context = [];
        if (!data.value) data.value = [{}];
        data.type = 'fieldset';

        // d('data value', data.value);
        if (!Array.isArray(data.value)) data.value = [data.value]

        data.value.forEach((v, index) => {
            // const name = [k, index].join('.');
            // d('create fieldset with name', name, v);
            data.name = index;

            // d('duplicator values', v);
            const values = Object.entries(v).map(([key, value]) => {
                const path = this.path + '.' + key;
                return {
                    path,
                    value
                }
            })
            data.value = values;
            const fieldset = new Fieldset(data, index, form, this);
            this.fields.push(fieldset);
        });
        if (data.value) this.values = data.value;
        this.context = {
            ...form.args,
            id: data.id,
            class: data.class,
            classes: data.classes,
            labels: data.labels,
            desc: data.desc, container: data.container,
            bdClass: data.bdClass,
            legend: data.legend,
            label: data.label,
            value: data.value,
            name: data.name,
            type: 'duplicator',
            data: {
                path: this.path,
                id:this.name
            }
        }

        // d(this.context);
        // d('creating new duplicator with', this.context)
        // d('created duplicator', this);
    }
    get value() {
        const values = []
        this.fields.forEach(fieldset => {
            // d('get value for fieldset', fieldset)
            values.push(fieldset.value);
        });
        // d('duplicator values', values);
        return values;
    }
    set value(data) {
        data.forEach((values, index) => {
            Object.entries(values).forEach(([name, value]) => {
                const path = [this.path, index, name].join('.');
                const field = this.form.findField(path);
                field.value = value;
            })
        });
    }
    removeRow(index) {
        d('remove:',index);
        const field = this.fields[index];
        if (!field) return false;
        const $el = $(field.sel);
        // d("el",$el[0],field);

        this.fields.splice(index, 1);
        return $el;
    }
    addRow() {
        const Fieldset = require('./Fieldset');
        const template = this.form.getTemplate('fieldset-duplicator', 'helpers');
        this.orig.useInPath = true;
        this.orig.name = undefined;
        const field = new Fieldset(this.orig, this.fields.length, this.form, this);

        field.allFields().forEach(field => field.context.value = undefined);
        this.fields.push(field);
        const context = {
            html: field.render(),
            index: this.fields.length - 1,
            classes: this.context.classes,
            labels: this.context.labels
        }
        // d('context:',this.context);
        return Handlebars.compile(template)(context);
    }
    get $fieldsets() {
        
        return $(this.sel).find('.fieldsets');
    }
    get template() {
        const container = this.context.container || 'duplicator';
        return this.form.getTemplate(container, 'containers');
    }
    render() {
        const context = this.context;
        getDataAttr(context);
        const fieldsets = [];
        this.fields.forEach((fieldset, index) => {
            fieldsets.push({
                index,
                html: fieldset.render()
            });
        });
        context.fieldsets = fieldsets;
        // d('render duplicator:',this.template)
        const html = Handlebars.compile(this.template)(context);
        // d('render fieldset',this.context.data)
        // d('html:',html);
        return html;
    }
}
module.exports = Duplicator;