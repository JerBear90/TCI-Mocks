const $ = jQuery.noConflict();
const { Field, Fieldset, Duplicator } = require('../inc');
const uuidv1 = require('uuid/v1');
const Handlebars = require('handlebars');
const toastr = require('toastr');
const { getDataAttr } = require('../utils/getDataAttr');
const { scrollTo } = require('../utils/scrollTo');
const { EventEmitter } = require('events');
const axios = require('../services/axios')
const { parseJSON } = require('../utils/parseJSON');

class Form extends EventEmitter {
    constructor(data = {}, args = {}) {
        super();
        let formArgs = {}
        this.fields = []
        this.render = this.render.bind(this);
        const defaults = {
            'ajax': true,						// Process submission using ajax
            'edit': false,                      // Enable form editor
            'container': 'form', 				// Template to use for form container
            'url': window.adminajax ? adminajax : '/wp-admin/admin-ajax.php',						// Form submit url
            'enctype': '',					// Form encoding type (use multipart/form-data for uploads)
            'enableUploads': false, 			// Convienence arg to set enctype to multipart/form-data to enable file uploads
            'target': '',						// Form target
            'showColon': true,				// Show ':' in labels,
            'showStar': true,					// Show '*' on required fields

            'class': '',						// Form Class,
            'classes': {
                'error': 'border border-danger'
            },
            'id': '',							// For CSS ID.  Use "false" for no id, "" for default
            'action': 'contact',				// Form action input, for ajax
            'method': 'post',					// Form Method,
            'callback': 'getRequestValue',	// Callback function to fill in form values
            'timeout': 1500,					// Wait time for reloads
            'title': '',
            'data': {},					// Additional data specific to this action,
            'action_args': {}, 			// Action specific settings
            'effects': {}, 				// Form submit effects (fadeOut, clear, reset, etc.)
            'messages': {},				// Messages based on form status
            'oneClick': false,				// One click fields
            'debug': true,
            'renderMode': 'html',           // How to render the form: html
            'keyboardNav': false,
            'toastr': true,
            'inlineMessages': true,
            'format': 'json',
            'loading': {
                color: 'primary',
                grow: false
            }
        }
        this.args = { ...defaults, ...args };
        // console.log("FIRST TRY ARGS:",this.args,"FROM:",args,"defaults",defaults);
        if (typeof (data) === 'string') [data, formArgs] = this.loadFromString(data);
        else {
            formArgs = data.args;
            delete (data.args);
        }

        Object.entries(data).forEach(el => {
            const [k, d] = el;
            if (k === 'args' || d === null) return;
            else if (typeof (d) == 'string' && k === 'submit' && d) {
                this.fields.push(new Field({ type: 'submit', value: d }, 'submit', this));
            }

            else if (typeof (d) === 'string') args[k] = d;
            else if (d.type === 'duplicator') this.fields.push(new Duplicator(d, k, this));
            else if (d.type === 'fieldset') this.fields.push(new Fieldset(d, k, this));
            else if (typeof (d) == 'object') this.fields.push(new Field(d, k, this));
        });
        if (data.submit && data.submit !== null && typeof (data.submit) === 'string') {
            this.fields.push(new Field({ type: 'submit', value: args.submit }, 'submit', this));
        }

        const submit = this.fields.findIndex(f => f.context.type === 'submit');

        if (submit === -1 && data.submit !== null) {
            this.fields.push(new Field({ type: 'submit', value: 'Submit' }, 'submit', this));
        }

        this.args = { ...defaults, ...formArgs, ...args }
        // console.log("ARGS:",this.args,"FROM:",args,"formArgs",formArgs,"defaults",defaults);

        this.name = this.args.form;
        this.allFields = this.allFields.bind(this);

        if (!this.uuid) this.uuid = uuidv1();
        this.args.data.uuid = this.uuid;
        this.emit('ready', this);
        // d('created form', this);
    }
    loadFromString(slug) {
        // console.log('[Form][loadFromString()]',slug)
        let data = { ...formConfig.forms[slug] }

        // Show error message if no valid json found
        if (!data) {
            if (!this.args.debug) return false;
            data = formConfig.forms.error;
            if (!data) data = {}
        }
        // d('args',data.args,data);
        // const data = JSON.parse( src );
        // collect args
        try {
            const args = {
                form: slug,
                id: 'hf_' + slug,
                actions: 'hf_' + slug,
                ...(data.args)
            }
            delete (data.args);
            return [data, args];
        } catch (e) {
            console.log('erro:', e.message);
            return [];
        }

    }
    set(fieldName, value) {
        // console.log('name',fieldName);
        const field = this.findByName(fieldName);
        if (field) field.set(value);
    }
    getTemplate(slug, type) {
        // d('type', type, 'slug', slug, formConfig.templates)
        return formConfig.templates[type][slug];
    }
    renderFields() {
        const { renderMode } = this.args;
        if (renderMode == 'html') {
            return this.fields.reduce((html, field) => {
                // console.log(field);

                html += field.render();
                if (field instanceof Fieldset) {

                }
                return html;
            }, '');
        }
        else if (renderMode === 'walk')
            return this.walk();
    }
    nextField() {
        const l = this.fields.length - 1;
        const field = this.active ? this.active : this.fields[l];
        const i = this.fields.findIndex(f => f.path === field.path);
        if (i == -1) return false;
        return this.fields[i + 1];
    }
    get previous() {
        const { active } = this;
        if (active) {
            const i = this.fields.findIndex(f => f.path === active.path);
            if (i > 0) return this.fields[i - 1];
        }
        return null;
    }
    get percent() {
        const { fields, active } = this;
        const percentStart = parseInt(this.args.percentStart, 10) || 0;
        const percentEnd = parseInt(this.args.percentEnd, 10) || 100;
        if (!active) {
            // d('No active');
            return;
        }
        const index = fields.findIndex(f => f.path === active.path)
        const ratio = index / fields.length;
        const range = percentEnd - percentStart;
        const percent = (ratio * range) + percentStart;
        // d('percent',percent,'ration',ratio,'range',range,'index:',index,'of:',fields.length);
        return percent;
    }
    get next() {
        const { active } = this;

        if (active) {
            const i = this.fields.findIndex(f => f.path === active.path);
            // d('active index',i);
            const l = this.fields.length;
            if (i < l) return this.fields[i + 1];
        }
        return null;
    }
    get sel() {

        return `form[data-uuid=${this.uuid}]`;
    }
    set $form($el) {
        this._$form = $el;
    }
    get $form() {
        // d('sel', this.sel);
        // d(this._$form);
        return this._$form ? this._$form : $(this.sel);
    }
    get $messages() {
        return $(this.sel + ' .messages ');
    }
    get $bd() {
        return $(this.sel + ' .bd');
    }
    walk() {
        let field = this.active;
        let next;
        // d("WALKING!");
        if (this.next) if (this.next.isRendered()) {
            // console.log('next field',this.next);
            return scrollTo(this.next);
        }
        if (!field) {
            next = this.fields[0];
        } else {
            // console.log('render next');
            if (field.context.next) {
                const { value } = field.context;
                d('value', value, 'next', field.context.next)
                if (typeof (field.context.next) === 'string') {
                    d('string field');
                    next = this.findByName(field.context.next);
                } else {
                    const name = field.context.next[`${value}`];
                    d('find by name', name);
                    if (name) next = this.findByName(name);
                    else next = this.nextField();
                }
            } else {
                // d('find next field');
                next = this.nextField();
            }
        }
        // d('next field',next);
        if (next) {
            // console.log('walking',this.toRender,field);
            const html = next.render(null, false);

            this.active = next;
            return html;
        }
    }
    render($el) {
        const $ = jQuery.noConflict();

        const fields = this.renderFields();
        // d('fields',fields);
        Handlebars.registerPartial('fields', fields);//'<div id="editor-form-fields-holder"></div>' );
        const context = {
            name: this.args.form,
            percent: this.percent,
            fields: this.fields.filter(f => f instanceof Fieldset)
                .map((f, index) => {
                    index++;
                    return {
                        index,
                        name: f.context.name,
                        id: f.context.id,
                        title: f.context.legend
                    }
                }),
            ...this.args
        };
        // d("REDNERING:", context.fields);
        // console.log('form context',context);
        getDataAttr(context);
        const src = this.getTemplate(this.args.container, 'containers');
        const html = Handlebars.compile(src)(context);


        if ($el) {
            $el.replaceWith($(html));
            // $el.trigger('rendered');
            // d(`ABOUT TO RENDER [data-uuid=${this.uuid}]`);
            try {
                // this.active.$input.change();
                // console.log('active',this.active);
                this.active = this.fields[0];
                this.active.$field.trigger('rendered');
            } catch (e) {
                console.log('could not focus input:', e.message);
            }

            // this.message({status:'success',message:'test'})
        }

        this.emit('rendered');
        $(this.$form[0]).trigger('rendered');
        return html;
        // console.log('RENDERED FORM FIELDS');
        // console.log('-----');
        // console.log('-----');
    }
    allFields(set = this, fields = [], duplicators = true) {
        set.fields.forEach(field => {
            if (field.context.type === 'fieldset') this.allFields(field, fields, duplicators);
            if (field.context.type === 'duplicator' && duplicators) this.allFields(field, fields, duplicators);
            fields.push(field);
        })
        return fields;
    }
    get walkFields() {
        if (!this._walkFields) this._walkFields = [...this.fields]
        return this._walkFields;
    }
    findField(path) {
        const allFields = this.allFields();
        // d('find field', path);
        // d('all fields',allFields.map( f => f.uuid+' '+f.context.name ) );
        return allFields.find(f => f.path === path);
    }
    findByName(name) {
        const allFields = this.allFields();
        // d('all fields',allFields.map( f => f.uuid+' '+f.context.name ) );
        return allFields.find(f => f.name === name);
    }
    attachFields(set = this) {

        const container = set.context ? set.context.type : 'form';
        const sel = `${container}[data-uuid=${set.uuid}]`;
        const $set = $(sel);
        // console.log('attachFields',sel,$set[0]);

        // console.log('-- attach ',set,`to ${container}[data-id=${set.name}]`,$set,'as',container);
        $set.data(`${container}`, set);
        // $set.css('border','1px solid red');
        set.fields.forEach(field => {
            const container = field.containerName;
            const sel = `*[data-uuid=${field.uuid}]`;
            d("field", sel);
            const $el = $(sel, $set);
            // d('field',field,field.uuid);
            // console.log('-- attach ',field,`to [data-id=${field.name}]`,$el,'as',container);
            $el.data(`${container}`, field);
            // $el.css('border','1px solid red');
            // $el.css('color','1px solid red');
            if (field.context.type === 'fieldset') this.attachFields(field);
            else if (field.input) {
                const $input = $el.find('> :input, > .input');
                d($input[0]);
                $input.data('input', field.input)
            }
            d('----');
            // $el.find('> :input, > .input').css('border','#f00');
            // $el.find('> :input, > .input').css('color','#f00');
        });
    }
    get invalid() {
        return this.fields.reduce((invalid, f) => (invalid || f.invalid), false);
    }
    presubmit() {
        const fields = this.allFields();
        fields.forEach(field => {
            field.$field.trigger('presubmit');
        })
        // d('$FORM',this.$form);
        this.$form.trigger('presubmit');
    }
    renderValidation() {
        const labels = []
        this.allFields().forEach(field => {
            field.renderValidation()
            // d('INSTANCE: ', field instanceof Field, field.name)
            if (field.invalid && !(field instanceof Fieldset) && !(field instanceof Duplicator)) {
                const label = field.context.label ? field.context.label : field.context.placeholder
                const name = field.name;
                labels.push(label ? label : name);
            }
        });
        // d("INVLIAD:", labels);
        const messageStr = this.args.messages.invalid;
        const message = sprintf(messageStr, labels.join(', '));
        this.message({
            status: 'danger',
            message
        })
        if (this.invalid) this.$form.trigger('invalid');
    }
    set value(values) {
        const fields = this.allFields();
        fields.forEach(field => {
            const data = values.find(v => v.path = field.path);
            if (field instanceof Fieldset) field.value = values;
            else if (data) field.value = data.value;
        })
    }
    async submit(extend) {
        const { format } = this.args;
        // d('submit', this, extend);
        if (this.disabled && !window.showdebug) {
            // d('disabled form!')
            return;
        }
        const me = this;
        // d('submitting form');
        this.presubmit();

        // d('this form',this.$form[0])
        this.$form.trigger('presubmit');

        if (this.disabled && !window.showdebug) {
            d('disabled on presubmit');
            return;
        }
        if (this.extend) {
            extend = this.extend
            delete (this.extend);
        }
        // d('extend',extend);
        // d('is invalid: ', this.invalid);
        if (this.invalid) {
            this.renderValidation();
            if (!window.showDebug) return;
        }
        else this.addLoading();

        // get data by submit method
        let data = this.getFormJSON();

        if (format == 'json') data = { ...data, ...extend }
        else if (format == 'formData') data = this.getFormData(extend)
        else if (format == 'serialize') data = this.$form.serialize();

        let { method, url, form } = this.args;
        const _wpnonce = window.apiNonce ? apiNonce : null;

        // Log ot console so it can be copied to postman for debug


        // if( format === 'json' ) {
        const id = this.args.apiId;
        if (id) url = sprintf(url, id);

        d(`[Sumbit] (${form}) <${url}> [${method.toUpperCase()}] USING ${format}`);
        d('DATA:', data);
        // d(data);

        const headers = { "X-WP-Nonce": _wpnonce }

        const promise = method.toLowerCase() == 'get' ? axios.get(url, { headers, params: data }) : axios[method](url, data, { headers })

        promise
            .then(response => {
                this.addLoading('success', false);
                this.disabled = false;
                const data = parseJSON(response.data);
                // d("COMPLETE", this.$form[0], data);
                this.$form.trigger('complete', data);
                // d('data', data);
                if (!data) {
                    this.message({ status: 'warning', message: 'Server sent back empty response' }, false)
                }
            })
            .catch((res) => {
                this.removeLoading('danger', true);
                // console.log(res);
                // d(res.messages[0]);
                this.$form.trigger('complete',res)
                this.disabled = false;
                return false;
            });
        // } else {
        //     axios.post( url, data, (e,response)=>  {
        //         const res = parseJSON(response);
        //         console.log('submitted',res);
        //         this.$form.trigger('complete',res);
        //     }).
        //     catch
        // }


    }

    removeLoading() {
        this.$messages.find('.loading').remove();
        this.disabled = false;
    }
    addLoading(color, grow, append) {
        if (!color) color = this.args.loading.color;
        if (!grow) grow = this.args.loading.grow;

        const html = this.getLoading(color, grow);
        this.$messages.html(html);
        this.disabled = true;
    }
    appendLoading(color, grow) {
        if (!color) color = this.args.loading.color;
        if (!grow) grow = this.args.loading.grow;

        const html = this.getLoading(color, grow);
        this.$messages.append(html);
        this.disabled = true;
    }
    getLoading(color = 'primary', grow = false) {
        // d('adding loading',this.$messages[0])
        const context = {
            color,
            grow
        }
        const template = this.getTemplate('loading', 'helpers');
        if (template) return Handlebars.compile(template)(context);
    }
    formDataSubmit() {
        const formData = new FormData();
        this.getFormData(this.fields, formData);


        var request = new XMLHttpRequest();
        request.open("POST", this.args.url);
        // console.log('formData',formData);
        request.send(formData);

    }
    getFormData(extend) {
        const formData = new FormData();
        this.allFields().forEach(field => {
            const value = field.value;
            formData.append(field.name, value);
        })
        if (extend) {
            Object.entries(extend).forEach(([k, value]) => {
                if (value) formData.append(k, value);
            });
        }
        return formData;

        fields.forEach(field => {
            const { value } = field.context;
            // d('formData',field.name,value,field.context,'type',typeof(value))
            if (field instanceof Fieldset) {
                d('-- process fieldset values');
                this.getFormData(field.fields, formData);
            }
            else if (typeof (value) == 'object') {
                formData.append(field.name, JSON.stringify(value));
            } else {
                if (value) d('FORMDATA APPEND', field.name, value);
                formData.append(field.name, value);
            }
        });
        formData.append('action', this.args.action);
        formData.append('_wpnonce', apiNonce);
        return formData;
    }
    getFormJSON(fields = this.fields, data = {}) {

        fields.forEach(field => {
            // d('get json', field.value, field.name, field.jsonPath)
            if (field instanceof Fieldset)
                if (field.context.useInPath || !isNaN(parseInt(field.name, 10)))
                    data[field.jsonPath] = field.value;
                else {
                    d('not included fieldset', field);
                    data = { ...data, ...field.value }
                }
            else
                data[field.jsonPath] = field.value;
        });
        let files = [];
        this.allFields().forEach(field => {
            const currentFiles = field.context.files;
            // d('currentFiles', currentFiles);
            if (files && currentFiles) files = [...files, ...currentFiles];
        })
        // console.log('files', files);
        if (files.length) data.files = files;
        if (this.args.action) data.action = this.args.action;
        return data;
    }
    getContext(fields, data = []) {
        Object.entries(fields).forEach(([f, field]) => {
            // console.log('getting context',f,field);
            if (f != field.name) f = field.name;
            if (field.type === 'fieldset ') data[f] = this.getContext(field, data);
            else data.push(field.context);
        });
        return data;
    }
    message(data, clear = true, field = null) {
        if (clear) this.removeLoading();
        if (!data.message) {
            d('-- not message');
            return;
        }
        if (!data.status) data.status = 'success';

        let { status, message } = data;
        if (status == 'error') status = 'danger';
        const tslug = status == 'danger' ? 'error' : status;
        if (this.args.toastr && toastr[tslug]) {
            d('toast it up',data,status,message);
            toastr[tslug](message);
        }

        if (this.args.inlineMessages) {
            if (clear && field) field.$errors.html('');
            else if (clear) this.$messages.html('');
            if (field) field.$errors.html(`<div class="alert alert-${status}">${message}</div>`);
            else this.$messages.append(`<div class="alert alert-${status}">${message}</div>`);
        }
        return false;
    }
    connect(el) {
        return false;
        const $form = $(el);
        // d('connect');
        $form.data('uuid', this.uuid);
        $form.attr('data-uuid', this.uuid);
        const allFields = this.allFields(this, [], false);

        const connectField = (field, $ctx) => {
            const $context = $ctx.length ? $ctx : $form;
            const { type, name } = field.context;

            // d('find in context of', $ctx, field);
            const sel = `${type == 'fieldset' ? '.fieldset' : '.form-group'}[data-id=${name}]`
            // d('sel',sel);
            const $field = $(sel, $context);
            const val = $field.find(':input').val();
            if (val) field.context.value = val
            if ($field.length == 0 && field.context.type != 'submit') {
                d("FORM ERROR: cannot connect", field.name, sel);
            }
            // else if (type == 'fieldset') d('-- connect to', $field[0], field.uuid);
            $field.data('uuid', field.uuid);
            $field.attr('data-uuid', field.uuid);
            // d('vall',val,field.$input[0],field.$field[0],'find',field.$field.find(':input,.input,.btn'));
            field.emit('rendered');
            $field.trigger('rendered');
            // d('connect',$field[0],sel,field);

            if (field.context.type == 'duplicator') {
                // d("CONNECT DUPLICATOR", field.context.name);
                const $fieldsets = $field.find('.fieldset-wrap > .fieldset');
                // d('fieldsets', field.fields);
                let i = -1;
                $fieldsets.each(function () {
                    i++;
                    const fieldset = field.fields[i];
                    // d('fieldset', i);
                    // $(this).data('uuid', fieldset.uuid);
                    // $(this).attr('data-uuid', fieldset.uuid);

                    // console.log('fieldset index', i, $(this), 'fieldset', fieldset);
                    // d('All fields:', fieldset.allFields(), fieldset);
                    fieldset.allFields().forEach(subfield => {
                        connectField(subfield, $(this))
                    });
                })
                return;
            }

            if (field.context.type == 'checkboxes' || field.context.type == 'taxonomy') {
                field.context.value = [];
            }
        }
        allFields.forEach(connectField);
    }


    setValues(data) {
        if (data)
            this.fields.forEach(field => {
                if (field.context.type == 'submit') return;
                let s = field.context.subkey;
                const k = field.name;

                field.context.value = s ? data[s][k] : data[k];
                field.input.context = field.context;
                if (field.context.type == 'info')
                    field.context.info = field.context.value;
                // d('field',k,field,field.context.value);
                field.input.context = field.context;
                if (field.context.options) {
                    d('POPTIONS', field.input.context);
                    // field.input.sanitizeOptions();
                }
            });
    }
    save() {
        // d('saving forms');
        return [this.getContext(this.fields), this.args];
    }
}
window.Form = Form;
module.exports = Form;