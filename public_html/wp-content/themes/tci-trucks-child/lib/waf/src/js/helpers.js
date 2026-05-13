function parseJSON(str) {
    if (typeof (str) == 'string') {
        let data, debug;
        try {
            data = JSON.parse(str);
        } catch (e) {

        }
        str = ` ${str} `

        if (!data) {
            try {
                const match = str.match(/\{(.*)\}/g);
                let data = match ? match.pop() : '';
                debug = match ? str.replace(data, '') : str + ' ';
                data = JSON.parse(data)
                console.log('DEBUG:', debug);
                return { data, debug }
            } catch (e) {
                data = {}
            }
        }
        
        return { data, debug }
    }
    else return { data: str, debug: '' };

}
(function($){
    $.fn.addLoading = function (color = 'primary', grow = false, append = false) {
        // d('messages',this.$messages,this.$form,html);
        if ($(this).find('.messages').length == 0)
            $(this).append('<div class="messages"></div>');
        const typeCls = grow ? 'spinner-grow' : 'spinner-border';
        const html = '<div class="text-' + color + ' ' + typeCls + '"></div>';
        if (append) $(this).find('.messages').append(html);
        else $(this).find('.messages').html(html);

    }
    $.fn.removeLoading = function() {
        $(this).find('.messages').html('');
    }
    $.fn.message = function (data) {
        let { status, message } = data;
        if ($(this).find('.messages').length == 0)
            $(this).append('<div class="messages"></div>');
            
        if (status == 'error') status = 'danger';
        d('message', message, status, $(this).find('.messages'));
        $(this).find('.messages').html('<div class="alert alert-' + status + '">' + message + '</div>');
        
    }
    $.fn.errorMessage = function(message) {
        $(this).message({status:'danger',message})
    }
    $.fn.successMessage = function (message) {
        $(this).message({ status: 'success', message })
    }
    $.fn.warningMessage = function (message) {
        $(this).message({ status: 'warning', message })
    }
    $.fn.validate = function( visible=':visible' ) {
        halt = 0;
        // d('validating the form!');
        $(this).find('*[data-condition-not] :input').trigger('validate');
        // d('inputs:', $(this).find(':input' + visible), ':input'+visible);
        $(this).find(':input' + visible ).each(function () {
            // Ignore disabled inputs
            if( $(this).attr('disabled') ) return;
            var $field = $(this).closest('.form-group');
            $field.removeAttr('invalid');
            $field.removeData('invalid');
            var name = $(this).attr('name');
            var value = $(this).val();
            // d('checking:',name,value);
            if( $(this).attr('type') == 'submit' || $(this).attr('type') == 'button' ) return;
            if ($(this).attr('type') == 'checkbox' && !$(this).is(':checked')) value = false;

            if ($(this).attr('type') == 'radio') {
                if ($(this).attr('required') || $(this).attr('aria-required')) {
                    if ($('input[name=' + name + ']:checked').length == 0) {
                        // $(this).addClass('invalid');
                        // $field.find('label').addClass('invalid');
                        d('invalid',name);
                        $field.attr('invalid',true);
                        if (!halt) halt = 1;
                    }
                }
            } else if( $(this).attr('type') == 'checkbox' && $(this).closest('.checkboxes').length > 0 ) {
                d('checkboxes!');
                var hasValue = $(this).closest('.checkboxes').find('input:checked').not(':disabled').length;
                if( ($(this).attr('required') || $(this).attr('aria-required')) && !hasValue ) {
                    // $field.find('input[type=checkbox]').addClass('invalid');
                    // $field.find('> label').addClass('invalid');
                    d('invalid groups', name);
                    $field.attr('invalid',true);
                    if (!halt) halt = 1;
                }
            } else if( ( value == '' && $(this).attr('type') != 'checkbox' ) || 
                ( $(this).attr('type') == 'checkbox' && !$(this).is(':checked')) && $(this).closest('.checkboxes').length == 0 
                ) {

                if ($(this).attr('required') || $(this).attr('aria-required')) {
                    // $(this).addClass('invalid');
                    // $field.addClass('invalid');
                    // $field.find('> label').addClass('invalid');
                    d('invalid single', name);
                    $field.attr('invalid',true);
                    if (!halt) halt = 1;
                }
                if( halt ) d("INVLAID:",name,'value:',value);
            }
        });

        $(this).find('input[type=file]').each(function () {
            let files = $(this)[0].files
            if ($.fn.getField) {
                const field = $(this).getField();
                if (field) {
                    if (field.context.files) files = field.context.files;
                }
            }
            if (files.length == 0 && $(this).attr('aria-required')) {
                // $(this).addClass('invalid');
                // $(this).closest('.form-group').find('label').addClass('invalid');
                $field.attr('invalid', true);
                if (!halt) halt = 1;
                d(' file halt: ' + halt);
            }
        });

        if ($(this).find('[invalid]').length > 0) halt = 1;
        // d('inavlidity:', halt );
        if (halt) return false;
        else return true;
    }


    $.fn.renderValidation = function() {
        var $form = $(this);
        $form.find('.errors').remove();
        var valid = $form.validate();

        if (!valid) {
            // Fill in invalid messages
            d('rendering invalid form');
            $form.find('[invalid]').each(function () {
                var $field = $(this).closest('.form-group');
                // d('field:',$field[0]);
                $field.addClass('invalid');
                
                var msg = $field.attr('data-invalid');
                // d('message:',msg);
                if (msg && $field.find('.errors').length == 0) {
                    
                    $field.append('<div class="alert alert-danger">' + msg + '</div>');
                    $form.find('[type=submit]').removeAttr('disabled');
                }
            });


            // Validator - allows for skipping required fields IF field provided by ".validator" has a note explaining
        
            // Focus on fisrt invalid field
            $form.find('.invalid').first().focus();
            d('focus on',$form.find('.invalid').first()[0])

            // Add invalid message
            // Add form error message
            var message = $form.data('invalid') || 'Please make sure all required fields are correct';
            if( message ) $form.errorMessage( message );

            // Trigger form complete event with invalid status
            $form.trigger('complete', { status: 'error', message: message });
            return false;
        }
        return true;
    }


    $.fn.processResponseSimple = function (response, context) {
        // Parse JSON if applicable
        // d('RESPONSE:', response.form.values[0]);
        var $this = $(this);
        if (typeof (response) == 'string') {
            var r = parseJSON(response);
            response = r.data;
        }
        if (typeof (response) == null || !response) return;
        // d('process',response);
        if (!context && response.context) context = response.context;
        if (!context) context = $(document);
        if (response.status == 'OK') response.status = 'success';
        // Get timeout time
        var timeout = $(this).data('timeout') ? $(this).data('timeout') : 1500;

        // update message
        if (response.message && !response.skipmessages) {
            d('--show message?',$(this)[0],response);
            $(this).message({status:response.status,message:response.message})
            $(this).find('[type=submit]').removeAttr('disabled');
        }

        // Arbitrary js
        if (response.pre_js) {
            try {
                eval(response.pre_js);
            } catch( e ) {
                d(e.message);
            }
        }

        // removable blocks
        if (response.remove) {
            for (var i in response.remove) {
                var item = response.remove[i];
                // html/outerhtml
                $(item).each(function () {
                    d("item:", $(this)[0]);
                    $(this).remove();
                });
            }
        }

        // Build DOM updates    
        const updates = [response];

        // Use the main item
        

        // merge with updates
        if( response.updates ) for( var update of response.updates ) updates.push(update);
        // d('updates:',updates);
        for( var update of updates ) {
            if (!update.skipmessages) var messages = $(update.selector, context).find('.messages').html();
            if (update.outerhtml) {
                $(update.selector, context).each(function () {
                    $(this)[0].outerHTML = update.outerhtml;
                });
            }

            if (update.html) {
                const html = update.html;
                const html_timeout = update.message ? timeout : 0;
                setTimeout(() => {
                    $(update.selector).html(html);
                    $(update.selector).find('[type=submit]').removeAttr('disabled');
                    $(update.selector).trigger('create');
                }, html_timeout);
            }
            if (update.append) {
                $(update.selector, context).append(update.append);
                $(update.selector, context).trigger('create');
            }
            if (update.prepend) {
                $(update.selector, context).prepend(update.prepend);
                $(update.selector, context).trigger('create');
            }
            if (update.after) {
                $(update.selector, context).after(update.after);
                $(update.selector, context).trigger('create');
            }
            if (update.before) {
                $(update.selector, context).before(update.before);
                $(update.selector, context).trigger('create');
            }


            // Add or remove classes
            if (update.removeClass) {
                d('remove class ' + update.removeClass);
                $(update.selector).removeClass(update.removeClass);
            }
            if (update.addClass) {
                d('add class ' + update.addClass);
                $(update.selector).addClass(update.addClass);
            }

            // Change value of a form input
            if (update.value) {
                $(update.selector, context).val(update.value);
            }
            // Change text
            if (update.text) {
                $(update.selector, context).text(update.text);
            }

            // Options on a select field
            if (update.options) {
                $(update.selector + ' option[value!=""]', context).remove();
                for (var i in update.options) {
                    var val = update.options[i];
                    $(update.selector).append('<option value="' + i + '">' + val + '</option>');
                }
                if (update.value) $(update.selector, context).val(update.value);
            }

            // Data attributes
            if (update.data) {
                for (var key in update.data) {
                    var value = update.data[key];
                    $(update.selector, context).data(key, value);
                }
            }

            // Click on a selector
            if (update.trigger) $(update.selector, context).trigger(update.trigger);
        }

        // Arbitrary js
        if (response.js) {
            d('eval js',response.js);
            eval(response.js);
        }

        // reload if needed
        if (response.reload) {
            $(this).addLoading('success',false,true);
            setTimeout(function () {
                window.location.reload()
            }, timeout);
        }

        // redirect if needed
        if (response.url) {
            $(this).addLoading('success', false, true);
            setTimeout(function () {
                window.location.href = response.url;
            }, timeout);
        }
    }
})(jQuery);