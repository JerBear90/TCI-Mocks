import { parseJSON } from '../../utils/parseJSON';
const Form = require('../../inc/Form');
const $ = jQuery.noConflict();
$.fn.processResponse = function (response, context) {
    // Parse JSON if applicable
    var $this = $(this);
    if (typeof (response) == 'string') {
        var r = parseJSON(response);
        response = r.data;
    }
    if (typeof (response) == null || !response) return;
    // d('process',response);
    if (!context && response.context) context = response.context;
    if (!context) context = $(document);
    if (response == null) return;
    if (response.status == 'OK') response.status = 'success';

    // Get timeout time

    var timeout = $(this).attr('data-timeout') ? $(this).attr('data-timeout') : 1500;

    if (window.location.protocol == "https:") {
        var f_url = "http://";
        var replace = "https://";
        var find = new RegExp(f_url, 'g');

        if (response.html) response.html = response.html.replace(find, replace);
        if (response.outerhtml) response.outerhtml = response.outerhtml.replace(find, replace);
    }
    if (response.form) {
        const { id, values } = response.form;
        const form = new Form(id);
        form.value = values;
        try {
            const html = form.render();
            d('RESPONSE:', response);
            if (response.tabbed) {
                const $tabs = $(this).closest('.tabs')
                d($tabs[0]);
                if ($tabs.length) {
                    const $nav = $tabs.find('.nav');
                    const $tabcontent = $tabs.find('.tab-content').first();
                    const active = $('.tab-pane.active').attr('id');
                    const id = 'tab-' + Math.ceil(Math.random() * 1000);
                    $tabcontent.find('.tab-pane.show').removeClass('active show');
                    const $tabpane = $(`<div role="tabpanel" class="tab-pane fade in show active" id="${id}">
                                            <a class="btn btn-danger pull-right btn-sm close text-white" data-dismiss="tab">
                                                <i class="la la-close"></i>
                                            </a>
                                        ${html}
                                        </div>`)
                    $tabcontent.append($tabpane);
                    const $li = $(`<li role="presentation">
                            <a href="#${id}" id="${id}-tab" aria-controls="${id}" role="tab" data-toggle="tab">
                                <i class="${response.tabIcon}" aria-hidden="true"></i>
                                ${response.legend}
                            </a>
                        </li>`);
                    $li.data('previous', active);
                    $nav.append($li);



                    $li.find('a').tab('show');
                    d("TAB FORM:", $tabpane.find('form')[0]);
                    const $form = $tabpane.find('form');
                    $form.attr('data-rendered', true);
                    $form.data('rendered', true);
                    $form.render(form);
                }
            } else {
                $(response.selector).data('html', $(response.selector).html());
                $(response.selector).html(html);
            }
        } catch (e) {
            console.log('form render error:', e.message, form)
        }

    }
    // update message
    if (response.message && !response.skipmessages) {
        // $(this).find('.messages').removeClass('loading').html( '<div class="'+response.status+'">'+response.message+'</div>' );
        if ($(this).find('.messages').length == 0)
            $(this).append('<div class="messages w-100 mt-1"></div>');
        $(this).find('.loading').remove();
        $(this).find('.messages').html('<div class="alert alert-' + response.status + '">' + response.message + '</div>');
        // d('messages', $(this)[0], $(this).find('.messages')[0], response.message, response.status);
    }

    // Arbitrary js
    if (response.pre_js) {
        eval(response.pre_js);
    }

    // removable blocks
    if (response.remove) {
        for (var i in response.remove) {
            var item = response.remove[i];
            // html/outerhtml
            d("REMOVE",item);
            $(item).remove();
        }
    }

    // Update any html selectors
    if ($(response.selector).length > 0) {


        if (!response.skipmessages) var messages = $(response.selector, context).find('.messages').html();
        if (response.outerhtml) {
            $(response.selector, context).each(function () {
                $(this)[0].outerHTML = response.outerhtml;
            });
        }

        if (response.html) {
            const html = response.html;
            const html_timeout = response.message ? timeout : 0;
            setTimeout(() => {
                $(response.selector).html(html);
                $(response.selector).trigger('create');
            }, html_timeout);
        }
        if (response.append) {
            $(response.selector, context).append(response.append);
            $(response.selector, context).trigger('create');
        }
        if (response.prepend) {
            $(response.selector, context).prepend(response.prepend);
            $(response.selector, context).trigger('create');
        }
        if (response.after) {
            $(response.selector, context).after(response.after);
            $(response.selector, context).trigger('create');
        }
        if (response.before) {
            $(response.selector, context).before(response.before);
            $(response.selector, context).trigger('create');
        }


        // Add or remove classes
        if (response.removeClass) {
            d('remove class ' + response.removeClass);
            $(response.selector).removeClass(response.removeClass);
        }
        if (response.addClass) {
            d('add class ' + response.addClass);
            $(response.selector).addClass(response.addClass);
        }

        // Change value of a form input
        if (response.value) {
            $(response.selector, ctx).val(response.value);
        }
        // Change text
        if (response.text) {
            $(response.selector, ctx).text(response.text);
        }

        // Options on a select field
        if (response.options) {
            $(response.selector + ' option[value!=""]', ctx).remove();
            for (var i in response.options) {
                var val = response.options[i];
                $(response.selector).append('<option value="' + i + '">' + val + '</option>');
            }
            if (response.value) $(response.selector, ctx).val(response.value);
        }

        // Data attributes
        if (response.data) {
            for (var key in response.data) {
                var value = response.data[key];
                $(response.selector, context).data(key, value);
            }
        }

        // Click on a selector
        if (response.trigger) $(response.selector, ctx).trigger(response.trigger);
    }

    // Process Tabs
    if (response.tabs) {
        if (response.selector) $(response.selector).addTabs(response.tabs);
    }

    // new html code chunks
    if (response.updates) {
        for (var i in response.updates) {
            var itemData = parseJSON(response.updates[i]);
            var item = itemData.data;
            if (item == null) continue;
            if (item.context == false) var ctx = document;
            else ctx = context;

            // Arbitrary js
            if (item.pre_js) {
                d(item.pre_js);
                eval(item.pre_js);
            }

            // html/outerhtml
            if (item.outerhtml) {
                $(item.selector, ctx).replaceWith(item.outerhtml);
            } else if (item.html) {
                $(item.selector, ctx).html(item.html);
            }

            // append/prepend
            if (item.append) $(item.selector, ctx).append(item.append);
            if (item.prepend) $(item.selector, ctx).prepend(item.prepend);
            if (item.after) $(item.selector).after(item.after);
            if (item.before) $(item.selector, ctx).before(item.before);

            // update jquery mobile
            $(item.selector, ctx).trigger('create');

            // Click on a selector
            if (item.trigger) $(item.selector, ctx).trigger(item.trigger);

            // Add or remove classes
            if (item.removeClass) {
                d('remove class ' + item.removeClass);
                $(item.selector).removeClass(item.removeClass);
            }
            if (item.addClass) {
                d('add class ' + item.addClass);
                $(item.selector).addClass(item.addClass);
            }

            // Change value of a form input
            if (item.value != undefined) {
                $(item.selector, ctx).val(item.value);
            }

            // Options for a select field
            if (item.options) {
                $(item.selector + ' option[value!=""]', ctx).remove();
                for (var i in item.options) {
                    var val = item.options[i];
                    $(item.selector, ctx).append('<option value="' + i + '">' + val + '</option>');
                }
                if (item.value) $(item.selector, ctx).val(item.value);
            }

            // Click on a selector
            if (item.trigger) {
                d('---');
                d('tiggering on:');
                d($(item.selector, ctx));
                d(item.trigger);
                d('--');
                $(item.selector, ctx).trigger(item.trigger);
            }

            // Data attributes
            if (item.data) {
                for (var key in item.data) {
                    var value = item.data[key];
                    $(item.selector, context).data(key, value);
                }
            }

            // Change text
            if (item.text) {
                $(item.selector, ctx).text(item.text);
            }
        }
    }

    // reload if needed
    if (response.reload) {
        const form = $this.getForm();
        form.appendLoading()
        setTimeout(function () { 
            window.location.reload() 
        }, timeout);
    }

    // redirect if needed
    if (response.url) {
        const form = $this.getForm();
        form.appendLoading()
        setTimeout(function () { 
            window.location.href = response.url 
            d('form:',form);
        }, timeout);
    }

    // open new tab
    if (response.open) {
        d(response.open);
        setTimeout(window.open(response.open), timeout);
    }

    // fadeOut if needed
    if (response.fadeOut) {
        var opacity = $.isNumberic(response.fadeOut) ? response.fadeOut : 0.6;
        $(this).find('.bd').animate({ opacity: fadeout }, timeout)
            .find(':input').attr('disabled', 'disabled');
        //$(this).find('.messages').css('position','absolute').animate({bottom:'35%'}, 3000 );
    }

    // Clear Form if needed
    if (response.clear) {
        $(this).clearForm();
        $(this).find('.tinymce .content, .tinymce textarea').html('');
    }

    // Reset Form if needed
    if (response.reset) {
        d('reset form');
        $(this)[0].reset();
        $(this).find('.tinymce .content, .tinymce textarea').html('');
    }

    if (response.errors) {
        // var html = '';
        // var f = response.errors.length;
        // for( var i =0; i<f; i++ ) {
        //     var error = response.errors[i];
        //     if( error.message == undefined ) error.message = '';
        //     html += '<div class="error">'+error.message+'</div>';
        //     $(error.selector).addClass('invalid').first().focus();
        // }
        // $(this).find('.messages').html(html);
    }

    // Add Classes to selectors
    if (response.addClass) {
        for (var i in response.addClass) {
            var item = response.addClass[i];

            $(item.selector).addClass(item.cls);
        }
    }

    // 'Flash' selector classes
    if (response.flashClass) {
        if (typeof (response.flashClass == 'object')) {
            for (var i in response.flashClass) {
                var item = response.flashClass[i];
                if (item.time) time = item.time;
                else time = timeout;
                d(item);
                $(item.selector).addClass(item.cls);
            }
            setTimeout(function () {
                for (var i in response.flashClass) {
                    var item = response.flashClass[i];
                    $(item.selector).removeClass(item.cls);
                }
            }, time);
        } else {
            var sel = response.selector;
            var cls = response.flashClass;
            $(sel).addClass(cls);
            d('stringy');
            setTimeout(function () { $(sel).removeClass(cls) }, timeout);
        }
    }

    /*
    // Add any tabs
        if( response.tabs ) {
            $('.tabbed-nav').addTabs(response);
        }
    */
    /*
    // colorbox
    if( response.colorbox ) {
        setTimeout( function() {
            jQuery.colorbox.close();
        },1600);
    }
    */

    // Arbitrary js
    if (response.js) {
        eval(response.js);
    }

    $(document).trigger('update');
}