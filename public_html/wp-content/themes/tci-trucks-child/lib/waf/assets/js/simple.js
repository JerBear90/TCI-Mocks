/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "/assets";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/simple.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/simple.js":
/*!**************************!*\
  !*** ./src/js/simple.js ***!
  \**************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("const {parseJSON} = __webpack_require__(/*! ./utils/parseJSON */ \"./src/js/utils/parseJSON.js\");\nwindow.d = console.log;\nvar $ = jQuery.noConflict();\njQuery(document).ready(function ($) {\n    // d('Loaded WAF simple handler');\n    \n    // Handle field submission, if simple handler is specified\n    $(document).on('submit', 'form[data-handler=simple]', ajaxSubmitForm);\n    $(document).on('complete', 'form[data-handler=simple]', ajaxCompleteForm);\n\n    // Handle buttons on simple form handler\n    $(document).on('click', '.form-group[data-route] button, button[data-route], .btn[data-route]', ajaxSubmitField);\n    $(document).on('change', '.form-group[data-route] :input', ajaxSubmitField);\n    $(document).on('complete', '.form-group[data-route], button[data-route], .btn[data-route]', ajaxCompleteField);\n    \n    $(document).on('click', 'fieldset[data-route] button', ajaxSubmitField);\n    $(document).on('complete', 'fieldset[data-route]', ajaxCompleteField);\n\n    // Use the \"submit\" class on a field to handle it\n    $(document).on('click', '.submitable button', ajaxSubmitField);\n    $(document).on('complete', '.submitable', ajaxCompleteField);\n\n\n    // Handle clearing invalid fields\n    $(document).on('keyup','.form-group.invalid :input', clearInvalid );\n    $(document).on('change', '.form-group.invalid :input', clearInvalid);\n\n    // Handle \"other\" values\n    $(document).on( 'click', 'select,input[type=checkbox]', changeOther );\n    $(document).on('click', '.clear-other', clearOther);\n\n    // Handle duplicator rows on simple forms\n    $(document).on('click', 'form[data-handler=simple] .add-duplicator-row', addDuplicatorRow)\n    $(document).on('click','form[data-handler=simple] .remove-duplicator-row', removeDuplicatorRow );\n    \n    $(document).on('click', '.duplicatable .add-duplicator-row', addDuplicatorRow)\n    $(document).on('click', '.duplicatable .remove-duplicator-row', removeDuplicatorRow);\n\n    $(document).on('change', '.form-group[data-toggle] :input', toggleElement);\n    $(document).on('click', '.form-group[data-toggle] a, .form-group[data-toggle] .btn', toggleElement);\n    $(document).on('render', '.form-group[data-toggle]', setToggleFields);\n    // Enable submit buttons\n    $('[type=submit]').removeAttr('disabled');\n\n})\n/* Simplified Form Submit Handler */\nfunction ajaxSubmitForm(e) {\n    if (e.isDefaultPrevented) {\n        // d('-- form prevented');\n        // return;\n    }\n    // d('submit form');\n    if( $(this).hasClass('stripe') ) {\n        var token = $('input[name=stripe_token]').val();\n        var card = $('input[name=stripe_card]:checked').val();\n        d('token:',token,'card:',card);\n        if( !token && !card ) return;\n    }\n    e.preventDefault();\n    const $form = $(this);\n    if( $.fn.getForm ) {\n        const form = $(e.target).getForm();\n        if( form && !form.args.skipValidation ) if (form.invalid) return form.renderValidation(); \n        \n    }\n    \n    // Prepare Data\n    const method = $form.attr('method') || 'get';\n    // d('method:',method);\n    const url = $form.attr('action');\n    const enctype = $form.attr('enctype')\n    var data = new FormData();\n    \n    if( $(this).data('disabled') ) {\n        d(' -- form disabled');\n        return;\n    }\n    // if( enctype == 'multipart/form-data' ) {\n        \n        $form.find(':input').each( function(){\n            const name = $(this).attr('name');\n            let value = $(this).val();\n            if( $(this).attr('disabled') ) {\n                $(this).removeAttr('disabled');\n                value = $(this).val();\n                $(this).attr('disabled','disabled');\n            }\n            // d('input:',name,value);\n            if( $(this).attr('type') == 'file' ) {\n                // d('-- file');\n                if( $(this).closest('.tus').length ) {\n                    const field = $(this).getField();\n                    if( field ) {\n                        const files = field.context.files;\n                        let i = -1;\n                        if( files ) files.forEach( file => {\n                            i++;\n                            d(file);\n                            d('name:',name);\n                            data.append(name,JSON.stringify({name:file.name,url:file.url}));\n                        });\n                    }\n                } else if( $(this)[0].files) for (var file of $(this)[0].files) {\n                    // d(name, file, file.name);\n                    data.append(name, file);\n                }\n            } else if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {\n                // d('radio:',$(this)[0]);\n                if ($(this).is(':checked')) {\n                    // d('add checkbox', name, value);\n                    if( !value ) value = 1;\n                    data.append(name, value);\n                } else if ($(this).attr('type') == 'checkbox' ) {\n                    // d('NOT CHECKED:', name, value,$(this)[0]);\n                    data.append(name, '');\n                }\n            }\n            else {\n                // d('add field',name,value);\n                data.append( name, value );\n            }\n        })   \n        const formData = $form.data();\n        Object.entries(formData).forEach( function([k,val]) {\n            data.append(k, val);\n        })\n        var processData = false;\n        var contentType = false;\n    // } else {\n    //     var data = $form.serialize();\n    //     var processData = true;\n    //     var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';\n    // }\n\n    const _wpnonce = window.apiNonce ? apiNonce : null\n    \n    // Submit\n    const headers = { \"X-WP-Nonce\": _wpnonce }\n    // d('ajax submit form',method,data);\n    \n    $.ajax({\n        url,\n        data,\n        headers,\n        method,\n        processData,\n        contentType,\n        context: $form,\n        beforeSend: function () {\n            $(this).find('[type=submit]').attr('disabled', true);\n            // d('disable form', $(this).find('[type=submit]')[0]);\n            // Validation & Loader\n            if( !$form.validate() && !$form.data('skipvalidation') ) {\n                d('--inavlid');\n                return $form.renderValidation();\n            }\n            else $form.addLoading();\n            // Presubmit to alter data or disable form\n            $form.trigger('presubmit');\n\n            // Check for disabled form\n            if( $form.data('disabled') ) {\n                d('--disabled');\n                return false;\n            }\n            $form.data('disabled',true);\n            // d('Submit form ' + method.toUpperCase() + '<' + url + '>');\n            // d(data);\n            $('input[name=stripe_token]').val('');\n            return true;\n        },\n        complete:function(r) {\n            $('input[name=stripe_token]').val('');\n            const response = parseJSON(r.responseText);\n            $(this).addLoading('success', false);\n            $(this).removeData('disabled');\n            // d(response);\n            if( !response.data.url && !response.data.reload ) {\n                // d('-- remove Disabled');\n                $(this).find('[type=submit]').removeAttr('disabled');\n            // return;\n            }\n            $(this).removeData('changed');\n            $(this).removeAttr('disabled');\n            // d('response:',response);\n            $(this).trigger('complete', response.data);\n\n            if (!response.data) {\n                $(this).message({ status: 'warning', message: 'Server sent back empty response' }, false)\n            }\n\n            if( response.debug ) {\n                console.log(response.debug);\n                $('#debug').html(response.debug);\n            }\n        },\n        // error:function(r) {\n        //     d('error:',e,res);\n        //     $(this).removeLoading();\n        //     $(this).removeAttr('disabled');\n        //     $(this).trigger('failure', response.data);\n        //     return false;\n        // }\n    });\n}\nfunction ajaxCompleteForm(e,data) {\n    if (e.isDefaultPrevented()) {\n        d('--default prevented?');\n        return;\n    }\n    $(this).removeLoading();\n    if (data) {\n        // if( data.status && !data.message ) data.message = data.status;\n        // d('complete', data);\n        $(this).removeLoading();\n        $(this).processResponseSimple(data);\n        \n    }\n}\nfunction clearInvalid () {\n    $(this).closest('.form-group').removeClass('invalid');\n    $(this).closest('.form-group').removeAttr('invalid');\n    $(this).closest('.form-group').find('.invalid-error').remove();\n}\n\n\n\nfunction ajaxSubmitField(e) {\n    if ($(e.target).hasClass('add-duplicator-row')) return true;\n    d('submit field?');\n    e.preventDefault();\n    e.stopPropagation();\n    // d('clicked!;', $(e.target)[0]);\n    \n    const $form = $(this).closest('form');\n    if( $form.length ) if( $form.data('handler') != 'simple' ) return true;\n    let $field = $(this).data('route') ? $(this) : $(this).closest('[data-route]');\n    if( $field.hasClass('tus') ) return;\n    const data = $field.data();\n    \n    Object.entries(data).forEach( function( [k,val] ) {\n        if( typeof val == 'object' ) data[k] = null;\n    })\n\n    // Prepare Data\n    const method = data.method || 'get';\n    d('method:',data.method,data);\n    // d('method:', method);\n    const url = data.route;\n    \n    // Confirm\n    if( data.confirm && !data.confirmed ) {\n        if( confirm(data.confirm) ) {\n            data.confirmed = 1;\n        } else {\n            return;\n        }\n    }\n    var processData = true;\n    var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';\n    \n    const _wpnonce = window.apiNonce ? apiNonce : null;\n\n    // Submit\n    const headers = { \"X-WP-Nonce\": _wpnonce }\n    // d('field:',$field);\n    $field.find(':input').each( function() {\n        var name = $(this).attr('name');\n        if( !name ) name = $(this).parent().data('name');\n        if( $(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio' ) {\n            if( $(this).is(':checked') ) var val = $(this).val();\n        } else {\n            var val = $(this).val();\n        }\n        // d('input:',$(this)[0]);\n        data[name] = val;\n    })\n\n    // d('FIELD DATA:', url,$field[0],$field.data(),data);\n    $.ajax({\n        url,\n        data,\n        headers,\n        method,\n        processData,\n        contentType,\n        context: $field,\n        beforeSend: function () {\n            // Validation & Loader\n            if (!$field.validate()) {\n                d('--inavlid');\n                return $field.renderValidation();\n            }\n            else $field.addLoading();\n            // Presubmit to alter data or disable form\n            $field.trigger('presubmit');\n\n            // Check for disabled form\n            if ($field.data('disabled')) {\n                d('--disabled');\n                return false;\n            }\n            $field.data('disabled', true);\n            // d('Submit field ' + method.toUpperCase() + '<' + url + '>');\n            // d(data);\n\n            return true;\n        },\n        complete: function (r) {\n            const response = parseJSON(r.responseText);\n            $(this).addLoading('success', false);\n            $(this).removeData('disabled');\n            $(this).removeData('changed');\n            $(this).removeAttr('disabled');\n            // d('response:',response);\n            $(this).trigger('complete', response.data);\n\n            if( response.debug ) d('FIELD COMPLETE::debug:',response.debug);\n            // d('data:',response.data);\n\n            if (!response.data) {\n                $(this).message({ status: 'warning', message: 'Server sent back empty response' }, false)\n            }\n        },\n        // error:function(r) {\n        //     d('error:',e,res);\n        //     $(this).removeLoading();\n        //     $(this).removeAttr('disabled');\n        //     $(this).trigger('failure', response.data);\n        //     return false;\n        // }\n    });\n}\nfunction ajaxCompleteField(e, data) {\n    const $form = $(this).closest('form');\n    if ($form.length) if ($form.data('handler') != 'simple') return true;\n    // d('completing field',data);\n    if (e.isDefaultPrevented()) {\n        d('--default prevented?');\n        return;\n    }\n    $(this).removeLoading();\n    if (data) {\n        $(this).removeLoading();\n        $(this).processResponseSimple(data);\n        // d('field completed');\n    }\n}\nfunction clearInvalid() {\n    $(this).closest('.form-group').removeClass('invalid');\n    $(this).closest('.form-group').removeAttr('invalid');\n}\nfunction changeOther () {\n    // d('this:',$(this));\n    var val = $(this).val();\n    // d('val:',val);\n    if( val.toLowerCase() == 'other' && ($(this).is(':checked') || $(this)[0].tagName == 'SELECT' ) ) {\n        \n        var name = $(this).attr('name');\n        var type = $(this).attr('type')\n        \n        var $field = \n            $('<div class=\"input-group d-flex align-items-center\">'\n                + '<input name=\"' + name + '\" placeholder=\"Other\" type=\"text\" class=\"d-inline-block form-control\" data-other=\"true\">'\n                + '<a href=\"\" class=\"clear-other\"><i class=\"fa fa-times\"></i></a>'\n            + '</div>');\n        d($field);\n        var $input = $field.find('input');\n        // d('type:',type);\n        if( type == 'checkbox') {\n            $label = $(this).next('label');\n            $field.data('label',$label.clone())\n            $label.remove();\n        }\n        else {\n            var selected = $(this).find('option[selected]').attr('value');\n            $input.val( selected );\n        }\n\n        var $orig = $(this).clone();\n        $(this).replaceWith($field);\n        $field.data('source',$orig);\n        $input.focus();\n    }\n}\nfunction clearOther(e) {\n    e.preventDefault();\n    $parent = $(this).closest('.input-group').parent();\n    var $field = $(this).closest('.input-group');\n    var $orig = $field.data('source');\n    \n    \n    var $label = $field.data('label');\n    if( $label ) $field.after($label);\n    $field.replaceWith($orig);\n    \n    var $input = $parent.find('input[type=checkbox]');\n    $input.removeProp('checked');\n\n}\nfunction removeDuplicatorRow(e) {\n    e.preventDefault();\n    $(this).closest('.fieldset-container').remove();\n}\nfunction addDuplicatorRow(e) {\n    e.preventDefault();\n    \n    var $fieldsets = $(this).parent().prev('.fieldsets');\n    var $item = $fieldsets.find('.fieldset-container').last().clone();\n    $item.find(':input').each( function() {\n        d('this:',$(this)[0]);\n        $f = $(this).closest('.form-group');\n        var path = $f.data('path');\n        d('f:',$f[0])\n        d('path:',path);\n        if( !path ) return;\n        // d('path:',path);\n        var parts = path.split('.');\n        var name = parts[0];\n        var nameAttr = $(this).attr('name');\n        \n        var index = parseInt( parts[1], 10 );\n        var new_index = index+1;\n        \n        var find = name+'['+index+']';\n        var replace = name + '[' + new_index + ']';\n        if( nameAttr ) \n        var new_name = nameAttr.replace(find,replace);\n        d('new name:',new_name,'find:',find,'replace',replace,'current:',name,'update:',new_name);\n\n        var new_path = path.replace(index, new_index);\n        $f.data(new_path);\n        $f.attr('data-path',new_path);\n        $(this).attr('name',new_name);\n        $(this).val('');\n        if( nameAttr ) {\n            const $el = $(this).closest('.form-group');\n            d('el:',$el[0]);\n            $el.trigger('render');\n        }\n    })\n    d('item:',$item[0],'fieldsets:',$fieldsets[0]);\n    $fieldsets.append($item);\n}\n\nfunction toggleElement(e) {\n    const $field = $(this).data('toggle') ? $(this) : $(this).closest('.form-group');\n    d('field:',$field[0],'toggle',$(this).data)\n    const data = $field.data();\n    const toggle = data.toggle;\n    const cls = data.class ? data.class : 'd-none';\nd('toggle:',toggle,cls,data);\n    $(toggle).toggleClass(cls);\n}\n\nfunction setToggleFields(e) {\n    const $field = $(this).data('toggle') ? $(this) : $(this).closest('.form-group');\n    const data = $field.data();\n    const toggle = data.toggle;\n    const cls = data.class ? data.class : 'd-none';\n    const $check = $field.find('input[type=checkbox]');\n    // d('init toggle',toggle,'checked:',$check.is(':checked'));\n    if( cls == 'd-none' ) {\n        if ($check.is(':checked')) $(toggle).removeClass(cls)\n        else $(toggle).addClass(cls);\n        \n    } else {\n        if ($check.is(':checked')) $(toggle).addClass(cls)\n        else $(toggle).removeClass(cls);\n    }\n}\n\n//# sourceURL=webpack:///./src/js/simple.js?");

/***/ }),

/***/ "./src/js/utils/parseJSON.js":
/*!***********************************!*\
  !*** ./src/js/utils/parseJSON.js ***!
  \***********************************/
/*! exports provided: parseJSON */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, \"parseJSON\", function() { return parseJSON; });\nconst $ = jQuery;\nconst parseJSON = str => {\n\tif (typeof (str) == 'string') {\n\t\tlet data, debug;\n\t\ttry {\n\t\t\tdata = JSON.parse(str);\n\t\t} catch( e ) {\n\n\t\t}\n\t\tstr = ` ${str} `\n\t\t\n\t\tif( !data) { \n\t\t\ttry { \n\t\t\t\tconst match = str.match(/\\{(.*)\\}/g);\n\t\t\t\tlet data = match ? match.pop() : '';\n\t\t\t\tdebug = match ? str.replace(data, '') : str + ' ';\n\t\t\t\tdata = JSON.parse(data)\n\t\t\t\treturn {data,debug}\n\t\t\t} catch (e) {\n\t\t\t\tdata = {}\n\t\t\t}\n\t\t}\n\t\treturn { data, debug }\n\t}\n\telse return { data: str, debug: '' };\n\n}\n\n\n//# sourceURL=webpack:///./src/js/utils/parseJSON.js?");

/***/ })

/******/ });