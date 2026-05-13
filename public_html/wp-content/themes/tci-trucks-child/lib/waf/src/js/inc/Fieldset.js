
const $ = require('../ui/jquery/$.getForm');
const Handlebars = require('handlebars');
const Field = require('./Field');
const Duplicator = require('./Duplicator');
const { getDataAttr } = require('../utils/getDataAttr');


const axios = require('../services/axios');
const { sprintf } = require('sprintf-js');
const { parseJSON } = require('../utils/parseJSON')

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
                    values[field.name] = field.value;
                else {
                    // d('not included fieldset', field);
                    values = { ...values, ...field.value }
                }
            else
                values[field.name] = field.value;
        });
        // d('fieldset values', values);
        return values;
    }
    set value(values) {
        // d('set value',values);
        if (!values) return;
        // d('value to set', values);
        this.fields.forEach(f => {
            // if( f.context.type == 'submit' ) return;
            if (!Array.isArray(values)) {
                d("NOT ARRAY", values);
                return;
            }
            d()
            const data = values.find(v => v.path == f.path);
            if( f instanceof Fieldset) f.value = values;
            else if (data) {
                d('setting to',data);
                f.value = data.value;
                d("SET", f.name, data.value);
            }
            else f.value = '';
        });
    }
    get $fieldset() {
        return this.$field;
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
        if( this.context.messages ) {
            var invalid = this.context.messages.invalid
            
            if( invalid ) {
                this.message({status:'error',message:invalid,mode:this.context.messages.mode});     
            }
        }
        // d('rendering fieldset validation');
        this.fields.forEach( field => {
            // d('field:',field.name);
            // if( field.context.type == 'fieldset' ) return;
            field.renderValidation()
        });
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
        this.context.data.path = this.path;
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

    // submit
    async submit() {
        // d('submit fieldset!');
        const { name, value } = this;
        let data = { ...this.context.data }
        // d('data:',data,data);
        if (data.route) {
            let url = sprintf(data.route, value);
            const format = data.format || 'formData';
            const _wpnonce = apiNonce;
            const headers = { "X-WP-Nonce": _wpnonce }
            data.value = value;
            const method = data.method || 'post';
            data.route = data.mode = data.method = data.value = data.name = data.field = data.id = data.path = undefined;
            let update = format == 'json' ? { ...data } : new FormData();
            if (data._id) {
                url = url.replace(/\/$/, "") + '/' + data._id;
            }
            
            
            this.allFields().forEach( field => {
            // Object.entries(data).forEach(([k, v]) => {
                var k = field.context.nameAttr;
                var v = field.realValue;
                // d(k,v);
                
                if (format == 'formData') {
                
                    if (field.$input.attr('type') == 'file' ) {
                        // d('file???');
                        if (field.$input[0].files) for (var file of field.$input[0].files) {
                            // d('add file', file);
                            update.append(field.context.nameAttr, file, file.name);
                        }
                    } else {
                        // d('append',k,v);
                        update.append(k, v);
                    }
                } else {
                    update[k] = v;
                }
            });
            // d('data.mode:', format);
            // d('data:', update);
            // d(_wpnonce);
            d(url,JSON.stringify(update));
            // d('headers:',headers);
            const promise = method == 'get' ? axios.get(url, { headers, params: update }) : axios[method](url, update, { headers })
            this.addLoading();
            this.disabled = true;
            promise
                .then(response => {
                    // d('completed', response.data.form.values[0]);
                    this.removeLoading();
                    // console.log('complete', data, response)
                    $('#debug').html(data.debug);
                    d('field:',this.$field[0])
                    this.$field.trigger('complete', response.data);
                    this.disabled = false;
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
}
module.exports = Fieldset;