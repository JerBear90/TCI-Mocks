require('./ui/jquery/$.processResponse');
// require('./ui/_/$.fileUploader');
// require('bootstrap');
const toastr = require('toastr');
// const { initEventHandlers } = require('./controller/_/initialize');
const { initializeForms } = require('./controller/initializeForms');
const renderForms = require('./ui/renderForms');
// const Form = require('./inc/Form');
// const Field = require('./inc/Field');
// const Fieldset = require('./inc/Fieldset');
// const Input = require('./inc/Input');

// window.$ = jQuery.noConflict();
// window.initializeForms = initializeForms;
// module.exports = { Form, initEventHandlers }
window.forms = [];
if (!window.formConfig) window.formConfig = {
    'forms': {},
    'templates': {},
    'json': {}
}


const axios = require('./services/axios');
jQuery(document).ready( function($) {
    // d('initializing forms!');
    if( $.fn.tooltip ) $('.tooltip').tooltip();
    const path = '/wp-json/waf/v1/formConfig'
    const url = window.siteurl ? siteurl+path : path;
    // d('get formcofnig', url);
    const headers = { "X-WP-Nonce": window.apiNonce }
    axios.get(url,{headers}).then( res => {
        // d('initial listings form:',window.formConfig.forms.listing);
        res.data.forms = {...res.data.forms,...window.formConfig.forms}
        window.formConfig = res.data;
        
        // d('formConfig returned:',formConfig,'from',url)
        renderForms();
        $(document).trigger('forms-ready');
    }).catch( e => {
        renderForms();
        
    });
    initializeForms();
    // $('form[data-id=test]').submit();
});

// LOAD Jquery plugins
require('./ui/jquery')
require('./components');

window.toastr = toastr;
window.handlebars = require('handlebars');
window.parseJSON = require('./utils/parseJSON').parseJSON;

// window.editors = [];
// window.$ = jQuery.noConflict();
// window.Field = Field;
// window.Form = Form;
// window.Input = Input
// window.Fieldset = Fieldset;
// console.log('field',window.Field);