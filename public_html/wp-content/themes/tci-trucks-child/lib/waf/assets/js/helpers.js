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
/******/ 	return __webpack_require__(__webpack_require__.s = "./src/js/helpers.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./src/js/helpers.js":
/*!***************************!*\
  !*** ./src/js/helpers.js ***!
  \***************************/
/*! no static exports found */
/***/ (function(module, exports) {

eval("function parseJSON(str) {\n    if (typeof (str) == 'string') {\n        let data, debug;\n        try {\n            data = JSON.parse(str);\n        } catch (e) {\n\n        }\n        str = ` ${str} `\n\n        if (!data) {\n            try {\n                const match = str.match(/\\{(.*)\\}/g);\n                let data = match ? match.pop() : '';\n                debug = match ? str.replace(data, '') : str + ' ';\n                data = JSON.parse(data)\n                console.log('DEBUG:', debug);\n                return { data, debug }\n            } catch (e) {\n                data = {}\n            }\n        }\n        \n        return { data, debug }\n    }\n    else return { data: str, debug: '' };\n\n}\n(function($){\n    $.fn.addLoading = function (color = 'primary', grow = false, append = false) {\n        // d('messages',this.$messages,this.$form,html);\n        if ($(this).find('.messages').length == 0)\n            $(this).append('<div class=\"messages\"></div>');\n        const typeCls = grow ? 'spinner-grow' : 'spinner-border';\n        const html = '<div class=\"text-' + color + ' ' + typeCls + '\"></div>';\n        if (append) $(this).find('.messages').append(html);\n        else $(this).find('.messages').html(html);\n\n    }\n    $.fn.removeLoading = function() {\n        $(this).find('.messages').html('');\n    }\n    $.fn.message = function (data) {\n        let { status, message } = data;\n        if ($(this).find('.messages').length == 0)\n            $(this).append('<div class=\"messages\"></div>');\n            \n        if (status == 'error') status = 'danger';\n        d('message', message, status, $(this).find('.messages'));\n        $(this).find('.messages').html('<div class=\"alert alert-' + status + '\">' + message + '</div>');\n        \n    }\n    $.fn.errorMessage = function(message) {\n        $(this).message({status:'danger',message})\n    }\n    $.fn.successMessage = function (message) {\n        $(this).message({ status: 'success', message })\n    }\n    $.fn.warningMessage = function (message) {\n        $(this).message({ status: 'warning', message })\n    }\n    $.fn.validate = function( visible=':visible' ) {\n        halt = 0;\n        // d('validating the form!');\n        $(this).find('*[data-condition-not] :input').trigger('validate');\n        // d('inputs:', $(this).find(':input' + visible), ':input'+visible);\n        $(this).find(':input' + visible ).each(function () {\n            // Ignore disabled inputs\n            if( $(this).attr('disabled') ) return;\n            var $field = $(this).closest('.form-group');\n            $field.removeAttr('invalid');\n            $field.removeData('invalid');\n            var name = $(this).attr('name');\n            var value = $(this).val();\n            // d('checking:',name,value);\n            if( $(this).attr('type') == 'submit' || $(this).attr('type') == 'button' ) return;\n            if ($(this).attr('type') == 'checkbox' && !$(this).is(':checked')) value = false;\n\n            if ($(this).attr('type') == 'radio') {\n                if ($(this).attr('required') || $(this).attr('aria-required')) {\n                    if ($('input[name=' + name + ']:checked').length == 0) {\n                        // $(this).addClass('invalid');\n                        // $field.find('label').addClass('invalid');\n                        d('invalid',name);\n                        $field.attr('invalid',true);\n                        if (!halt) halt = 1;\n                    }\n                }\n            } else if( $(this).attr('type') == 'checkbox' && $(this).closest('.checkboxes').length > 0 ) {\n                d('checkboxes!');\n                var hasValue = $(this).closest('.checkboxes').find('input:checked').not(':disabled').length;\n                if( ($(this).attr('required') || $(this).attr('aria-required')) && !hasValue ) {\n                    // $field.find('input[type=checkbox]').addClass('invalid');\n                    // $field.find('> label').addClass('invalid');\n                    d('invalid groups', name);\n                    $field.attr('invalid',true);\n                    if (!halt) halt = 1;\n                }\n            } else if( ( value == '' && $(this).attr('type') != 'checkbox' ) || \n                ( $(this).attr('type') == 'checkbox' && !$(this).is(':checked')) && $(this).closest('.checkboxes').length == 0 \n                ) {\n\n                if ($(this).attr('required') || $(this).attr('aria-required')) {\n                    // $(this).addClass('invalid');\n                    // $field.addClass('invalid');\n                    // $field.find('> label').addClass('invalid');\n                    d('invalid single', name);\n                    $field.attr('invalid',true);\n                    if (!halt) halt = 1;\n                }\n                if( halt ) d(\"INVLAID:\",name,'value:',value);\n            }\n        });\n\n        $(this).find('input[type=file]').each(function () {\n            let files = $(this)[0].files\n            if ($.fn.getField) {\n                const field = $(this).getField();\n                if (field) {\n                    if (field.context.files) files = field.context.files;\n                }\n            }\n            if (files.length == 0 && $(this).attr('aria-required')) {\n                // $(this).addClass('invalid');\n                // $(this).closest('.form-group').find('label').addClass('invalid');\n                $field.attr('invalid', true);\n                if (!halt) halt = 1;\n                d(' file halt: ' + halt);\n            }\n        });\n\n        if ($(this).find('[invalid]').length > 0) halt = 1;\n        // d('inavlidity:', halt );\n        if (halt) return false;\n        else return true;\n    }\n\n\n    $.fn.renderValidation = function() {\n        var $form = $(this);\n        $form.find('.errors').remove();\n        var valid = $form.validate();\n\n        if (!valid) {\n            // Fill in invalid messages\n            d('rendering invalid form');\n            $form.find('[invalid]').each(function () {\n                var $field = $(this).closest('.form-group');\n                // d('field:',$field[0]);\n                $field.addClass('invalid');\n                \n                var msg = $field.attr('data-invalid');\n                // d('message:',msg);\n                if (msg && $field.find('.errors').length == 0) {\n                    \n                    $field.append('<div class=\"alert alert-danger\">' + msg + '</div>');\n                    $form.find('[type=submit]').removeAttr('disabled');\n                }\n            });\n\n\n            // Validator - allows for skipping required fields IF field provided by \".validator\" has a note explaining\n        \n            // Focus on fisrt invalid field\n            $form.find('.invalid').first().focus();\n            d('focus on',$form.find('.invalid').first()[0])\n\n            // Add invalid message\n            // Add form error message\n            var message = $form.data('invalid') || 'Please make sure all required fields are correct';\n            if( message ) $form.errorMessage( message );\n\n            // Trigger form complete event with invalid status\n            $form.trigger('complete', { status: 'error', message: message });\n            return false;\n        }\n        return true;\n    }\n\n\n    $.fn.processResponseSimple = function (response, context) {\n        // Parse JSON if applicable\n        // d('RESPONSE:', response.form.values[0]);\n        var $this = $(this);\n        if (typeof (response) == 'string') {\n            var r = parseJSON(response);\n            response = r.data;\n        }\n        if (typeof (response) == null || !response) return;\n        // d('process',response);\n        if (!context && response.context) context = response.context;\n        if (!context) context = $(document);\n        if (response.status == 'OK') response.status = 'success';\n        // Get timeout time\n        var timeout = $(this).data('timeout') ? $(this).data('timeout') : 1500;\n\n        // update message\n        if (response.message && !response.skipmessages) {\n            d('--show message?',$(this)[0],response);\n            $(this).message({status:response.status,message:response.message})\n            $(this).find('[type=submit]').removeAttr('disabled');\n        }\n\n        // Arbitrary js\n        if (response.pre_js) {\n            try {\n                eval(response.pre_js);\n            } catch( e ) {\n                d(e.message);\n            }\n        }\n\n        // removable blocks\n        if (response.remove) {\n            for (var i in response.remove) {\n                var item = response.remove[i];\n                // html/outerhtml\n                $(item).each(function () {\n                    d(\"item:\", $(this)[0]);\n                    $(this).remove();\n                });\n            }\n        }\n\n        // Build DOM updates    \n        const updates = [response];\n\n        // Use the main item\n        \n\n        // merge with updates\n        if( response.updates ) for( var update of response.updates ) updates.push(update);\n        // d('updates:',updates);\n        for( var update of updates ) {\n            if (!update.skipmessages) var messages = $(update.selector, context).find('.messages').html();\n            if (update.outerhtml) {\n                $(update.selector, context).each(function () {\n                    $(this)[0].outerHTML = update.outerhtml;\n                });\n            }\n\n            if (update.html) {\n                const html = update.html;\n                const html_timeout = update.message ? timeout : 0;\n                setTimeout(() => {\n                    $(update.selector).html(html);\n                    $(update.selector).find('[type=submit]').removeAttr('disabled');\n                    $(update.selector).trigger('create');\n                }, html_timeout);\n            }\n            if (update.append) {\n                $(update.selector, context).append(update.append);\n                $(update.selector, context).trigger('create');\n            }\n            if (update.prepend) {\n                $(update.selector, context).prepend(update.prepend);\n                $(update.selector, context).trigger('create');\n            }\n            if (update.after) {\n                $(update.selector, context).after(update.after);\n                $(update.selector, context).trigger('create');\n            }\n            if (update.before) {\n                $(update.selector, context).before(update.before);\n                $(update.selector, context).trigger('create');\n            }\n\n\n            // Add or remove classes\n            if (update.removeClass) {\n                d('remove class ' + update.removeClass);\n                $(update.selector).removeClass(update.removeClass);\n            }\n            if (update.addClass) {\n                d('add class ' + update.addClass);\n                $(update.selector).addClass(update.addClass);\n            }\n\n            // Change value of a form input\n            if (update.value) {\n                $(update.selector, context).val(update.value);\n            }\n            // Change text\n            if (update.text) {\n                $(update.selector, context).text(update.text);\n            }\n\n            // Options on a select field\n            if (update.options) {\n                $(update.selector + ' option[value!=\"\"]', context).remove();\n                for (var i in update.options) {\n                    var val = update.options[i];\n                    $(update.selector).append('<option value=\"' + i + '\">' + val + '</option>');\n                }\n                if (update.value) $(update.selector, context).val(update.value);\n            }\n\n            // Data attributes\n            if (update.data) {\n                for (var key in update.data) {\n                    var value = update.data[key];\n                    $(update.selector, context).data(key, value);\n                }\n            }\n\n            // Click on a selector\n            if (update.trigger) $(update.selector, context).trigger(update.trigger);\n        }\n\n        // Arbitrary js\n        if (response.js) {\n            d('eval js',response.js);\n            eval(response.js);\n        }\n\n        // reload if needed\n        if (response.reload) {\n            $(this).addLoading('success',false,true);\n            setTimeout(function () {\n                window.location.reload()\n            }, timeout);\n        }\n\n        // redirect if needed\n        if (response.url) {\n            $(this).addLoading('success', false, true);\n            setTimeout(function () {\n                window.location.href = response.url;\n            }, timeout);\n        }\n    }\n})(jQuery);\n\n//# sourceURL=webpack:///./src/js/helpers.js?");

/***/ })

/******/ });