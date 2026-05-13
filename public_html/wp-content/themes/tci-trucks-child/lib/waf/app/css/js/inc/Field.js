const $ = require('../ui/jquery/$.getForm');
const axios = require('../services/axios');
const Input = require('./Input');
const Handlebars = require('handlebars');
const { sprintf } = require('sprintf-js');
const { parseJSON } = require('../utils/parseJSON')
const { getDataAttr } = require('../utils/getDataAttr');
const { EventEmitter } = require('events');
const { pathToName } = require('../utils/pathToName');
class Field extends EventEmitter {
    constructor(field, k, form, parent = null) {
        super();
        if (parent) {

            this.parent = parent;

        }
        let data;
        if (field.jquery) {
            const $input = field.find(':input');
            data = {
                name: $input.attr('name'),
                value: $input.val(),
                data: field.data()
            }
            this._field = field;
            if (data.data && !data.name) data.name = data.data.id;
            // d('create from jquery', data);

        } else data = { ...field }
        if (!form) form = new Form([data]);

        if (typeof (data) !== 'object') return;
        let value = '';
        if (data.name && k != data.name) k = data.name;
        // console.log('data ',k,data);

        this.name = k;
        // d('--bgin name attr', k);
        if (!data.nameAttr) data.nameAttr = pathToName(this.getPath(true));
        // d('name attr', data.nameAttr);
        // todo: improve on this?
        // if (!data.value) data.value = this.realValue;
        this.context = { ...data };
        if (!this.context.data) this.context.data = [];
        if (!this.context.conditions) this.context.conditions = [];
        // d('context', this.context, data);

        this.form = form;
        // console.log('context',this.context,this.context.value);

        // if( this.context.name === '6accounts' ) d('new data',this.context.name,this.context,{...this.context});
        this.input = new Input(this, form);

        if (!this.context.value && this.context.std) this.context.value = this.context.std;

        // Ready event
        this.emit('ready');

        // d('created field', this);
    }
    get jsonPath() {
        return this.getPath(true);
    }
    get path() {
        return this.getPath(false);
    }
    getPath(skipFieldsets = false) {
        const Fieldset = require('./Fieldset');
        // d("FIELDSET", this.name);
        // if (this.name == 'your_name') d('get field path', this.name);
        const path = [this.name];
        let parent = this.parent;
        while (parent) {
            // if (this.name == 'your_name') d('parent: ', parent.name, parent);
            // d('check fieldset:', parent instanceof Fieldset, 'nan', isNaN(parseInt(parent.name, 10)), 'skip:', skipFieldsets);
            if (parent instanceof Fieldset && isNaN(parseInt(parent.name, 10)) && skipFieldsets) {
                // d(parent.name, parent.context);
                if (parent.context.useInPath) path.push(parent.name);
            }
            else path.push(parent.name);
            parent = parent.parent;
        }

        // path.push(this.form.name);
        path.reverse()
        // if (this.name == 'your_name') d('path is:', path.join('.'))
        // d('-- found path', path.join('.'));
        return path.join('.');
    }
    set value(value) {
        this.context.value = value;
    }
    get realValue() {
        const { $input } = this;
        // d('set real value', this.path, $input);
        if ($input.length > 1) {
            if ($input.first().attr('type') == 'checkbox') {
                const values = [];
                $input.each(function () {
                    if ($(this).is('checked')) values.push($(this).val());
                })
                return values;
            }
            if ($input.first().attr('type') == 'radio')
                return this.$field.find("input:checked").val();
        } else if ($input.length == 1) return $input.val();
    }
    get value() {
        if (this.context.type == 'file') return this.context.files;
        d('value:',this.context.value);
        if (!this.context.value) this.context.value = this.realValue;
        d('real value:', this.realValue);
        // d('get value for field', this.context.value, this);
        return this.context.value;
    }
    get inputSrc() {
        const templates = formConfig.templates.inputs;
        const type = this.context.type;
        return templates[type] ? templates[type] : templates['default'];
    }
    get container() {
        const name = this.containerName;
        // d('container name',name, formConfig.templates.containers[name]);
        if (!formConfig.templates) return;
        return formConfig.templates.containers[name];
    }
    get containerName() {
        if (this._container) return this._container;
        const templates = formConfig.templates.containers;
        // d(formConfig);
        if (!templates) return;
        const { type, container } = this.context;
        if (container) if (templates[container]) return container;
        return templates[type] ? type : 'field';
    }
    set containerName(name) {
        return this._container = name;
    }

    keyboardNav() {
        const src = this.form.getTemplate('continue', 'helpers');
        // d('keyboard naving');
        if (src) {
            const html = Handlebars.compile(src)({});
            // this.context.append = html;
            return html;
        }

    }
    async submit() {
        const { name, value } = this;
        const data = { ...this.context.data }

        if (data.route) {
            let url = sprintf(data.route, value);
            const format = data.format || 'json';
            const _wpnonce = apiNonce;
            const headers = { "X-WP-Nonce": _wpnonce }
            data.value = value;
            const method = data.method || 'post';
            data.route = data.mode = data.method = data.value = data.name = data.field = undefined;
            const update = data.mode != 'param' ? { name: value, ...data } : {};
            if (data._id) {
                url = url.replace(/\/$/, "") + '/' + data._id;
            }
            if (format == 'formData')
                Object.entries(data).forEach(([k, v]) => update.append(k, v));

            // d(_wpnonce);
            // d(JSON.stringify(update));
            const promise = method == 'get' ? axios.get(url, { headers, params: update }) : axios[method](url, update, { headers })
            this.addLoading();
            this.disabled = true;
            promise
                .then(response => {
                    this.removeLoading();
                    const data = parseJSON(response.data);
                    // console.log('complete', data, response)
                    $('#debug').html(data.debug);
                    this.$field.trigger('complete', data.data);
                })
            // .catch((e, res) => {
            //     this.addLoading('danger', true);
            //     this.removeLoading();
            //     console.log('INCOMPLETE', data, res)
            //     this.form.message({ status: 'warning', message: e.message ? e.message : 'Unknown Error' }, false, this)
            //     if (res) this.$field.processResponse(res.data);
            //     return false;
            // });

        }
    }
    render(replace = null, validate = false) {
        // Get container
        const { type } = this.context;
        let container = type ? formConfig.templates.containers[name] : this.container;
        if (!container) container = this.container;

        // Set values from form
        this.context.showColon = this.form.args.showColon;
        this.context.showStar = this.form.args.showStar;
        if (this.context.edit == undefined) this.context.edit = this.form.args.edit;

        // Validation
        if (validate) this.context.invalid = this.invalid;
        else this.context.invalid = false;
        // d('render valitidity test',this.validate(), this.context.invalid);

        if (this.context.text && this.context.type == 'submit')
            this.context.value = this.context.text;
        // Data ATTr & input
        this.context.data.path = this.path;
        getDataAttr(this.context);
        this.input.registerPartial();
        this.input.context = this.context;

        if (this.context.css) {
            this.context.css = this.context.css.split('.this').join(`.${this.form.name} .form-group.${this.context.name}`);
            // console.log(this.context.css);
        }
        // return Handlebars.compile(container)(this.context);
        // d('render Template',this.context,container)
        const html = Handlebars.compile(container)(this.context);
        if (replace) $(replace).replaceWith(html);

        this.emit('rendered');
        return html;

    }
    removeLoading() {
        this.$field.find('.loading').remove();
    }
    get sel() {
        const path = this.path;
        const name = this.context.nameAttr;
        
        const sel = `*[data-path="${path}"]`;
        // d('gettting', this.name, this.path, sel)
        return $(sel).length ? $(sel) : `input[name=${name}]`;
        
    }
    get $append() {
        return $(this.sel + ' .append');
    }
    get $field() {
        if (this.name == 'city') {
            // d(this.sel);
            // d(this.form.$form);
        }
        return this._field ? this._field : $(this.form.$form).find(this.sel);
    }
    get $errors() {
        return this.$field.find('.errors');
    }
    get $first() {
        return this.$field.find(':input,.btn').first();
    }
    get $input() {
        let $el = this.$field.find(':input');
        if( $el.length == 0 ) $el = $(`input[name=${this.context.nameAttr}]`)
        // d('input:',$el[0]);
        return $el; 
    }
    get $last() {
        return this.$field.find(':input').last();
    }
    get $lastRow() {
        return this.$field.find('.input').last().find(':input').first()
    }
    get $text() {
        return this.$field.find('input[type=text]');
    }
    get $other() {
        return this.$field.find('input.other').first();
    }
    get requiredLength() {
        const required = this.context.required;
        const requiredLength = parseInt(required, 10);
        return isNaN(requiredLength) ? null : requiredLength;
    }
    get invalid() {
        if( !this.value ) this.value = this.realValue;
        const { required } = this.context;
        // d('required:',required,this);
        // console.log('required',true,'value',value,this.context)
        let invalid = false;

        // d('required',required,'value',value);
        if (required && !this.value) {
            // d('required without value', this.name)
            return true;
        }
        else if (this.context.match) {
            const { match } = this.context;
            const field = this.form.findByName(match);
            if (field.context.value != this.context.value) return true;
        }
        else if (typeof (value) === 'object') {
            if (this.requiredLength)
                if (!isNaN(this.requiredLength))
                    if (value.filter(v => v).length < required) return true;
        }
        // console.log('invalid',this.name,invalid);
        return invalid;

    }
    validate() {
        const { value, required } = this.context;
        const status = this.invalid ? 'invalid' : 'valid';

        const validation = {
            status,
            message: ''
        }
        const requiredLength = parseInt(required, 10);
        // d('value', value);
        if (Array.isArray(value)) {
            d('filter value', value);
            const filtered = value.filter(v => v);
            if (!isNaN(requiredLength) && filtered.length < requiredLength) {


                const remaining = (required - filtered.length);
                const errorData = [remaining]
                // d('errorData',errorData,'filtered',filtered);
                // d('FORM ARGS',this.form.args);
                if (this.context.errorPattern)
                    validation.message = sprintf(this.context.errorPattern, ...errorData);
            }
        }
        // d('FORM ARGS',this.form.args);
        if (validation.status == 'invalid' && !validation.message) {
            // d('add message');

            if (this.context.errorMessage)
                validation.message = this.context.errorMessage;
            else if (this.form.args.messages)
                if (this.form.args.messages.invalidField)
                    validation.message = this.form.args.messages.invalidField;
        }
        return validation;
    }
    renderValidation() {
        const validation = this.validate();
        // d('validation', validation);
        if (validation.status == 'invalid') {
            this.$field.addClass('invalid');
            this.$field.find(":input").not('button').addClass('invalid');
            // d('added invalid', this.$field[0])
            if (validation.message)
                this.$errors.html('<div class="alert alert-warning">' + validation.message + '</div>');
            // d(this.$errors[0]);
        } else {
            this.$field.removeClass('invalid');
            this.$field.find('.alert').remove();
        }
    }
    isRendered() {
        const $ = jQuery.noConflict();
        const { sel } = this;
        return $(sel).length;
    }
    addLoading(color = '', grow = false) {
        if (!color) color = this.form.args.loading.color;
        if (!grow) grow = this.form.args.loading.grow;
        d('color', color, 'grow', grow, this.$field[0]);
        const html = this.form.getLoading(color, grow);
        this.$field.find('.loading').remove();
        this.$field.append(html);
        this.disabled = true;
    }
    removeLoading() {
        this.$field.find('.loading').remove();
    }
}
module.exports = Field;