require('./ui/jquery/$.processResponse');
// require('./ui/_/$.fileUploader');
require('bootstrap');

// const { initEventHandlers } = require('./controller/_/initialize');
const { initializeForms } = require('./controller/initializeForms');
// const Form = require('./inc/Form');
// const Field = require('./inc/Field');
// const Fieldset = require('./inc/Fieldset');
// const Input = require('./inc/Input');

// window.$ = jQuery.noConflict();
// window.initializeForms = initializeForms;
// module.exports = { Form, initEventHandlers }
window.forms = [];
// if (!window.formConfig) window.formConfig = {
//     'forms': {},
//     'templates': {},
//     'json': {}
// }

// LOAD Jquery plugins
require('./ui/jquery')
require('./components/');
const axios = require('./services/axios');
jQuery(document).ready( function($) {
    // d('initializing forms!');
    const path = '/wp-json/waf/v1/formConfig'
    const url = window.siteurl ? siteurl+path : path;

    if( window.formConfig ) initializeForms();
    else {
        axios.get(url).then( res => {
            window.formConfig = res.data;
            
            // d('formConfig returned:',formConfig,'from',url)
            initializeForms();
            $(document).trigger('forms-ready');
        });
    }
    // $('form[data-id=test]').submit();
});

// window.editors = [];
// window.$ = jQuery.noConflict();
// window.Field = Field;
// window.Form = Form;
// window.Input = Input
// window.Fieldset = Fieldset;
// console.log('field',window.Field);