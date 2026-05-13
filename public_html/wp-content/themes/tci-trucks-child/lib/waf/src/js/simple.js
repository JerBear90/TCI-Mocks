const {parseJSON} = require('./utils/parseJSON');
window.d = console.log;
var $ = jQuery.noConflict();
jQuery(document).ready(function ($) {
    // d('Loaded WAF simple handler');
    
    // Handle field submission, if simple handler is specified
    $(document).on('submit', 'form[data-handler=simple]', ajaxSubmitForm);
    $(document).on('complete', 'form[data-handler=simple]', ajaxCompleteForm);

    // Handle buttons on simple form handler
    $(document).on('click', '.form-group[data-route] button, button[data-route], .btn[data-route]', ajaxSubmitField);
    $(document).on('change', '.form-group[data-route] :input', ajaxSubmitField);
    $(document).on('complete', '.form-group[data-route], button[data-route], .btn[data-route]', ajaxCompleteField);
    
    $(document).on('click', 'fieldset[data-route] button', ajaxSubmitField);
    $(document).on('complete', 'fieldset[data-route]', ajaxCompleteField);

    // Use the "submit" class on a field to handle it
    $(document).on('click', '.submitable button', ajaxSubmitField);
    $(document).on('complete', '.submitable', ajaxCompleteField);


    // Handle clearing invalid fields
    $(document).on('keyup','.form-group.invalid :input', clearInvalid );
    $(document).on('change', '.form-group.invalid :input', clearInvalid);

    // Handle "other" values
    $(document).on( 'click', 'select,input[type=checkbox]', changeOther );
    $(document).on('click', '.clear-other', clearOther);

    // Handle duplicator rows on simple forms
    $(document).on('click', 'form[data-handler=simple] .add-duplicator-row', addDuplicatorRow)
    $(document).on('click','form[data-handler=simple] .remove-duplicator-row', removeDuplicatorRow );
    
    $(document).on('click', '.duplicatable .add-duplicator-row', addDuplicatorRow)
    $(document).on('click', '.duplicatable .remove-duplicator-row', removeDuplicatorRow);

    $(document).on('change', '.form-group[data-toggle] :input', toggleElement);
    $(document).on('click', '.form-group[data-toggle] a, .form-group[data-toggle] .btn', toggleElement);
    $(document).on('render', '.form-group[data-toggle]', setToggleFields);
    // Enable submit buttons
    $('[type=submit]').removeAttr('disabled');

})
/* Simplified Form Submit Handler */
function ajaxSubmitForm(e) {
    if (e.isDefaultPrevented) {
        // d('-- form prevented');
        // return;
    }
    // d('submit form');
    if( $(this).hasClass('stripe') ) {
        var token = $('input[name=stripe_token]').val();
        var card = $('input[name=stripe_card]:checked').val();
        d('token:',token,'card:',card);
        if( !token && !card ) return;
    }
    e.preventDefault();
    const $form = $(this);
    if( $.fn.getForm ) {
        const form = $(e.target).getForm();
        if( form && !form.args.skipValidation ) if (form.invalid) return form.renderValidation(); 
        
    }
    
    // Prepare Data
    const method = $form.attr('method') || 'get';
    // d('method:',method);
    const url = $form.attr('action');
    const enctype = $form.attr('enctype')
    var data = new FormData();
    
    if( $(this).data('disabled') ) {
        d(' -- form disabled');
        return;
    }
    // if( enctype == 'multipart/form-data' ) {
        
        $form.find(':input').each( function(){
            const name = $(this).attr('name');
            let value = $(this).val();
            if( $(this).attr('disabled') ) {
                $(this).removeAttr('disabled');
                value = $(this).val();
                $(this).attr('disabled','disabled');
            }
            // d('input:',name,value);
            if( $(this).attr('type') == 'file' ) {
                // d('-- file');
                if( $(this).closest('.tus').length ) {
                    const field = $(this).getField();
                    if( field ) {
                        const files = field.context.files;
                        let i = -1;
                        if( files ) files.forEach( file => {
                            i++;
                            d(file);
                            d('name:',name);
                            data.append(name,JSON.stringify({name:file.name,url:file.url}));
                        });
                    }
                } else if( $(this)[0].files) for (var file of $(this)[0].files) {
                    // d(name, file, file.name);
                    data.append(name, file);
                }
            } else if ($(this).attr('type') == 'radio' || $(this).attr('type') == 'checkbox') {
                // d('radio:',$(this)[0]);
                if ($(this).is(':checked')) {
                    // d('add checkbox', name, value);
                    if( !value ) value = 1;
                    data.append(name, value);
                } else if ($(this).attr('type') == 'checkbox' ) {
                    // d('NOT CHECKED:', name, value,$(this)[0]);
                    data.append(name, '');
                }
            }
            else {
                // d('add field',name,value);
                data.append( name, value );
            }
        })   
        const formData = $form.data();
        Object.entries(formData).forEach( function([k,val]) {
            data.append(k, val);
        })
        var processData = false;
        var contentType = false;
    // } else {
    //     var data = $form.serialize();
    //     var processData = true;
    //     var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
    // }

    const _wpnonce = window.apiNonce ? apiNonce : null
    
    // Submit
    const headers = { "X-WP-Nonce": _wpnonce }
    // d('ajax submit form',method,data);
    
    $.ajax({
        url,
        data,
        headers,
        method,
        processData,
        contentType,
        context: $form,
        beforeSend: function () {
            $(this).find('[type=submit]').attr('disabled', true);
            // d('disable form', $(this).find('[type=submit]')[0]);
            // Validation & Loader
            if( !$form.validate() && !$form.data('skipvalidation') ) {
                d('--inavlid');
                return $form.renderValidation();
            }
            else $form.addLoading();
            // Presubmit to alter data or disable form
            $form.trigger('presubmit');

            // Check for disabled form
            if( $form.data('disabled') ) {
                d('--disabled');
                return false;
            }
            $form.data('disabled',true);
            // d('Submit form ' + method.toUpperCase() + '<' + url + '>');
            // d(data);
            $('input[name=stripe_token]').val('');
            return true;
        },
        complete:function(r) {
            $('input[name=stripe_token]').val('');
            const response = parseJSON(r.responseText);
            $(this).addLoading('success', false);
            $(this).removeData('disabled');
            // d(response);
            if( !response.data.url && !response.data.reload ) {
                // d('-- remove Disabled');
                $(this).find('[type=submit]').removeAttr('disabled');
            // return;
            }
            $(this).removeData('changed');
            $(this).removeAttr('disabled');
            // d('response:',response);
            $(this).trigger('complete', response.data);

            if (!response.data) {
                $(this).message({ status: 'warning', message: 'Server sent back empty response' }, false)
            }

            if( response.debug ) {
                console.log(response.debug);
                $('#debug').html(response.debug);
            }
        },
        // error:function(r) {
        //     d('error:',e,res);
        //     $(this).removeLoading();
        //     $(this).removeAttr('disabled');
        //     $(this).trigger('failure', response.data);
        //     return false;
        // }
    });
}
function ajaxCompleteForm(e,data) {
    if (e.isDefaultPrevented()) {
        d('--default prevented?');
        return;
    }
    $(this).removeLoading();
    if (data) {
        // if( data.status && !data.message ) data.message = data.status;
        // d('complete', data);
        $(this).removeLoading();
        $(this).processResponseSimple(data);
        
    }
}
function clearInvalid () {
    $(this).closest('.form-group').removeClass('invalid');
    $(this).closest('.form-group').removeAttr('invalid');
    $(this).closest('.form-group').find('.invalid-error').remove();
}



function ajaxSubmitField(e) {
    if ($(e.target).hasClass('add-duplicator-row')) return true;
    d('submit field?');
    e.preventDefault();
    e.stopPropagation();
    // d('clicked!;', $(e.target)[0]);
    
    const $form = $(this).closest('form');
    if( $form.length ) if( $form.data('handler') != 'simple' ) return true;
    let $field = $(this).data('route') ? $(this) : $(this).closest('[data-route]');
    if( $field.hasClass('tus') ) return;
    const data = $field.data();
    
    Object.entries(data).forEach( function( [k,val] ) {
        if( typeof val == 'object' ) data[k] = null;
    })

    // Prepare Data
    const method = data.method || 'get';
    d('method:',data.method,data);
    // d('method:', method);
    const url = data.route;
    
    // Confirm
    if( data.confirm && !data.confirmed ) {
        if( confirm(data.confirm) ) {
            data.confirmed = 1;
        } else {
            return;
        }
    }
    var processData = true;
    var contentType = 'application/x-www-form-urlencoded; charset=UTF-8';
    
    const _wpnonce = window.apiNonce ? apiNonce : null;

    // Submit
    const headers = { "X-WP-Nonce": _wpnonce }
    // d('field:',$field);
    $field.find(':input').each( function() {
        var name = $(this).attr('name');
        if( !name ) name = $(this).parent().data('name');
        if( $(this).attr('type') == 'checkbox' || $(this).attr('type') == 'radio' ) {
            if( $(this).is(':checked') ) var val = $(this).val();
        } else {
            var val = $(this).val();
        }
        // d('input:',$(this)[0]);
        data[name] = val;
    })

    // d('FIELD DATA:', url,$field[0],$field.data(),data);
    $.ajax({
        url,
        data,
        headers,
        method,
        processData,
        contentType,
        context: $field,
        beforeSend: function () {
            // Validation & Loader
            if (!$field.validate()) {
                d('--inavlid');
                return $field.renderValidation();
            }
            else $field.addLoading();
            // Presubmit to alter data or disable form
            $field.trigger('presubmit');

            // Check for disabled form
            if ($field.data('disabled')) {
                d('--disabled');
                return false;
            }
            $field.data('disabled', true);
            // d('Submit field ' + method.toUpperCase() + '<' + url + '>');
            // d(data);

            return true;
        },
        complete: function (r) {
            const response = parseJSON(r.responseText);
            $(this).addLoading('success', false);
            $(this).removeData('disabled');
            $(this).removeData('changed');
            $(this).removeAttr('disabled');
            // d('response:',response);
            $(this).trigger('complete', response.data);

            if( response.debug ) d('FIELD COMPLETE::debug:',response.debug);
            // d('data:',response.data);

            if (!response.data) {
                $(this).message({ status: 'warning', message: 'Server sent back empty response' }, false)
            }
        },
        // error:function(r) {
        //     d('error:',e,res);
        //     $(this).removeLoading();
        //     $(this).removeAttr('disabled');
        //     $(this).trigger('failure', response.data);
        //     return false;
        // }
    });
}
function ajaxCompleteField(e, data) {
    const $form = $(this).closest('form');
    if ($form.length) if ($form.data('handler') != 'simple') return true;
    // d('completing field',data);
    if (e.isDefaultPrevented()) {
        d('--default prevented?');
        return;
    }
    $(this).removeLoading();
    if (data) {
        $(this).removeLoading();
        $(this).processResponseSimple(data);
        // d('field completed');
    }
}
function clearInvalid() {
    $(this).closest('.form-group').removeClass('invalid');
    $(this).closest('.form-group').removeAttr('invalid');
}
function changeOther () {
    // d('this:',$(this));
    var val = $(this).val();
    // d('val:',val);
    if( val.toLowerCase() == 'other' && ($(this).is(':checked') || $(this)[0].tagName == 'SELECT' ) ) {
        
        var name = $(this).attr('name');
        var type = $(this).attr('type')
        
        var $field = 
            $('<div class="input-group d-flex align-items-center">'
                + '<input name="' + name + '" placeholder="Other" type="text" class="d-inline-block form-control" data-other="true">'
                + '<a href="" class="clear-other"><i class="fa fa-times"></i></a>'
            + '</div>');
        d($field);
        var $input = $field.find('input');
        // d('type:',type);
        if( type == 'checkbox') {
            $label = $(this).next('label');
            $field.data('label',$label.clone())
            $label.remove();
        }
        else {
            var selected = $(this).find('option[selected]').attr('value');
            $input.val( selected );
        }

        var $orig = $(this).clone();
        $(this).replaceWith($field);
        $field.data('source',$orig);
        $input.focus();
    }
}
function clearOther(e) {
    e.preventDefault();
    $parent = $(this).closest('.input-group').parent();
    var $field = $(this).closest('.input-group');
    var $orig = $field.data('source');
    
    
    var $label = $field.data('label');
    if( $label ) $field.after($label);
    $field.replaceWith($orig);
    
    var $input = $parent.find('input[type=checkbox]');
    $input.removeProp('checked');

}
function removeDuplicatorRow(e) {
    e.preventDefault();
    $(this).closest('.fieldset-container').remove();
}
function addDuplicatorRow(e) {
    e.preventDefault();
    
    var $fieldsets = $(this).parent().prev('.fieldsets');
    var $item = $fieldsets.find('.fieldset-container').last().clone();
    $item.find(':input').each( function() {
        d('this:',$(this)[0]);
        $f = $(this).closest('.form-group');
        var path = $f.data('path');
        d('f:',$f[0])
        d('path:',path);
        if( !path ) return;
        // d('path:',path);
        var parts = path.split('.');
        var name = parts[0];
        var nameAttr = $(this).attr('name');
        
        var index = parseInt( parts[1], 10 );
        var new_index = index+1;
        
        var find = name+'['+index+']';
        var replace = name + '[' + new_index + ']';
        if( nameAttr ) 
        var new_name = nameAttr.replace(find,replace);
        d('new name:',new_name,'find:',find,'replace',replace,'current:',name,'update:',new_name);

        var new_path = path.replace(index, new_index);
        $f.data(new_path);
        $f.attr('data-path',new_path);
        $(this).attr('name',new_name);
        $(this).val('');
        if( nameAttr ) {
            const $el = $(this).closest('.form-group');
            d('el:',$el[0]);
            $el.trigger('render');
        }
    })
    d('item:',$item[0],'fieldsets:',$fieldsets[0]);
    $fieldsets.append($item);
}

function toggleElement(e) {
    const $field = $(this).data('toggle') ? $(this) : $(this).closest('.form-group');
    d('field:',$field[0],'toggle',$(this).data)
    const data = $field.data();
    const toggle = data.toggle;
    const cls = data.class ? data.class : 'd-none';
d('toggle:',toggle,cls,data);
    $(toggle).toggleClass(cls);
}

function setToggleFields(e) {
    const $field = $(this).data('toggle') ? $(this) : $(this).closest('.form-group');
    const data = $field.data();
    const toggle = data.toggle;
    const cls = data.class ? data.class : 'd-none';
    const $check = $field.find('input[type=checkbox]');
    // d('init toggle',toggle,'checked:',$check.is(':checked'));
    if( cls == 'd-none' ) {
        if ($check.is(':checked')) $(toggle).removeClass(cls)
        else $(toggle).addClass(cls);
        
    } else {
        if ($check.is(':checked')) $(toggle).addClass(cls)
        else $(toggle).removeClass(cls);
    }
}