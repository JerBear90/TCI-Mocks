const $ = require('../ui/jquery/$.getForm');
const Handlebars = require('handlebars');
class Input {
    constructor(field, form) {
        const context = field.context;
        this.field = field;
        this.form = form;
        this.context = context;
        // d('loading input',context)
        // d('value is',this.context.value,this.context.type);

        if (context.options) this.context.options = this.sanitizeOptions(context.options);

        // d('input',context);
        if (context.type === 'key-value') {
            // d('working key-value');
            if (!context.keys) context.keys = {}
            if (!context.keys.key) context.keys.key = 'key';
            if (!context.keys.value) context.keys.key = 'value';
            // d('sanitize now',context);
            context.value = this.sanitizeKeyvalues(context.value);
        }
        // console.log('field context',this.context.options);

    }
    get template() {
        const templates = formConfig.templates.inputs;
        const type = this.context.type || 'text';
        // d('type',type);
        return templates[type] ? templates[type] : templates['default'];
    }
    registerPartial() {
        const { container } = this.context;
        const context = { data: [], ...this.context }
        const partials = []
        // d('render input',this.template);
        const attrTemplate = this.form.getTemplate('attributes', 'helpers');
        if (context.options) context.options = this.sanitizeOptions(context.options);
        
        const values = container === 'duplicator-field' ? context.value || [''] : [context.value];
        // d('register partial',context.options,context.value)
        let input = '';
        for (var i in values) {
            const value = values[i]
            context.value = value;
            // console.log('row index',i,value,'for',this.context.type);
            // OuterHTML partial, if applicable
            if (context['outerhtml']) {
                const outerhtml = Handlebars.compile(context['outerhtml'])(context);
                Handlebars.registerPartial('outerhtml', outerhtml);
            } else if (context['html']) {
                // HTML partial, if applicable
                const html = Handlebars.compile(context['html'])(context);
                Handlebars.registerPartial('html', html);
            }

            // Input partial
            if (context.container === 'duplicator-field') {
                context.value = value;
                context.data.index = i;
                context.data.number = parseInt(i, 10) + 1;

                // console.log('render context',context.data,'value',value);
                // d('input partia data-index',i);
                const attributes = Handlebars.compile(attrTemplate)(context);
                Handlebars.registerPartial('attributes', attributes);
                context.attributes = attributes;

                const inputPartial = Handlebars.compile(this.template)(context);

                Handlebars.registerPartial('input', inputPartial);
                const duplicatorRow = this.form.getTemplate('duplicatorRow', 'helpers');
                // d(  'context',context);
                const row = Handlebars.compile(duplicatorRow)(context);
                // d('row',row,duplicatorRow);
                input += row;
            } else {
                const attributes = Handlebars.compile(attrTemplate)(context);
                context.attributes = attributes;
                Handlebars.registerPartial('attributes', attributes);
                // d('template',this.template,'context',context);
                input += Handlebars.compile(this.template)(context);
            }
        }
        // console.log('register patial',input);
        Handlebars.registerPartial('input', input);

    }
    sanitizeOptions(options) {
        const selected = (Array.isArray(this.field.value) ? [...this.field.value] : [this.field.value])
            .map(s => `${s}`);
        // console.log
        // if (this.field.name == 'features') d('selected', selected, 'options',options);
        const saneOptions = Object.entries(options).map((option, i) => {
            let sane;
            const [o, label] = option;
            // console.log('[OPTION]', option, o.label);
            if (typeof (label) === 'string') {
                const value = Array.isArray(options) ? label : o;
                sane = { value, label };
                // d('label',label,'value',value,'checked', value === selected, 'option',option,'o',o,'label',label );
                if (value == selected) sane.checked = true;
            } else {
                sane = label;
                // if (this.field.name == 'category') d('index:', selected, sane.value, selected.indexOf(sane.value));
                if (typeof (sane) == 'object' && sane != null) if (selected.indexOf(`${sane.value}`) > -1) sane.checked = true;
                // else d('sane', sane);
            }

            //    console.log('sane',sane);
            // console.log('sane option is ',sane);
            if (typeof (sane) == 'object' && sane != null) {
                if (sane.options) {
                    sane.options = this.sanitizeOptions(sane.options);
                }
                sane.index = i;
                sane.number = i + 1;
            }
            return sane;
        });
        // console.log('[OPTIONS] sanitized',saneOptions);
        // console.log('sanitized',options);
        // console.log('===');
        return saneOptions

    }
    sanitizeKeyvalues(value) {
        if (!value) return [];
        const sane = Object.entries(value).map((pair, index) => {
            const { keys } = this.context;
            const [key, value] = pair;
            const k = keys.key;
            const v = keys.value;
            // if( keys ) console.log('keys:',keys,k,value[k],v,value[v]);
            const sane = typeof (value) === 'string' && typeof (key) === 'string' ? { key, value, index } : { key: value[k], value: value[v], index }
            return sane;
        });
        // console.log('-- key values sanitized',sane,'from',value);
        return sane;
    }
    render() {
        if (context.options) this.context.options = this.sanitizeOptions(context.options);
        return Handlebars.compile(this.template)(this.context);
    }
}
module.exports = Input;