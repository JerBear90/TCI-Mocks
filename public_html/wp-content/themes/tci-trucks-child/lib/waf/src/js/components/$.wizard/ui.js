const $ = jQuery.noConflict();
const _buttonText = e => {
    const $current = _current();
    const $next = $current.next('a');

    const $button = $('.wizard.pager .next a');
    // d('button', $button);
    const text = $next.length ? $button.data('next') : $button.data('save');
    $button.text(text);
}


// $('#rootwizard .finish').click(function () {
//     $('#success-modal').modal();
// });
const _current = () => {
    const sel = '.step-wizard a.active';
    let $current = $(sel).last();
    // d('current tab', $current[0],sel);
    if ($current.length == 0) {
        $current = $('.step-wizard a[data-toggle=tab]').first();
        $current.addClass('active');
    }
    // d('current tab', $current[0]);
    return $current;
}

export const updateWizardProgress = e => {
    const $navigation = $(e.target).closest('ul');
    // d($navigation);
    const $li = $(e.target).closest('li');
    // d('li', $li);
    const index = $navigation.find('li').index($li[0]);
    const total = $navigation.find('li').length;
    const current = index + 1;

    const percent = (current / total) * 100;
    // d('current percent:', percent, 'index:', index);
    $('#rootwizard .progressbar').css({
        width: percent + '%'
    });
    
    _buttonText();
}

export const nextWizardTab = e => {
    e.preventDefault();
    // d("CLICK NEXT!!!!");
    const $current = _current();
    const $next = $current.next('a');
    // d('current:',$current[0]);
    const sel = $current.attr('href')
    // d('sel:',sel);
    const $fieldset = $(sel);
    // d('path:',$fieldset.data('path'));
    const form = $fieldset.getForm();
    
    const path = $fieldset.data('name');
    // d(form,path);
    const fieldset = form.findField(path);
// d('fieldset:',$fieldset[0],sel,fieldset);
    // d('current:',$current[0],'next:',$next[0]);
    
    if( fieldset.invalid ) {
        // d('-- invalid tab');
        fieldset.renderValidation();
        const $first = $fieldset.find('.invalid :input:visible').first();
        // d('first:',$first[0]);
        $first.focus();
        return false;
    }
    const $next_fieldset = $($next.attr('href'));
    
    if ($next.length == 0 || $next_fieldset.data('finish') ) {
        const $form = $(e.target).closest('form');
        $form.submit();
        return;
    }

    $next.trigger('click');
}

export const completeWizard = function(e,response) {
    const r = parseJSON(response);
    
    if( r.data.status == 'success' ) {
        const $tab = $('[data-finish]');
        // d('complete wizard:',$tab[0]);
        if( $tab.length ) {
            const sel = '#' + $tab.attr('id');  
            const $a = $('a[href='+sel+']');
            $tab.data('completed',true);
            
            $a.data('completed',true);
            $a.click();
            $tab.siblings().data('disabled',true);
        }
        
    }
}
export const changeTab = function (e) {
    e.preventDefault();
    const $context = $(this).closest('form');
    const sel = $(this).attr('href');
    const id = sel.replace('#', '')
    const $form = $('#rootwizard form');
    
    // d('change', sel, $(sel)[0], 'friends', $(sel).siblings());
    const $tab = $(sel, $context);
    if (!$tab.hasClass('active' ) ) {
        d('-- scroll to?');
        $(window).scrollTop( $form.offset().top -150 );
    }
    // d('tab:',$tab[0],'data:',$tab.data());
    if( ($tab.data('finish') && !$tab.data('completed')) || $tab.data('disabled') ) {
        d('-- cancel finish tab',$tab[0],$tab.data());
        return;
    }
    $tab.siblings().removeClass('active ');
    $tab.addClass('active');
    

    const $pager = $('.pager');
    // d('pager:',$pager[0]);
    $pager.data('tab',sel);

    // d('pager:',$pager[0],'this:',$(e.target)[0]);
    if (sel) {
        $pager.data('tab', id);
        $pager.attr('data-tab', id);

        var i = 0;
        $('a[href='+sel+']').siblings('a').addClass('active');
        var $next = $(e.target).first();
        if( $next[0].tagName != 'A' ) $next = $next.closest('a').first();
        const $next_tab = $( $next.next().attr('href') );
        if( $next_tab.data('finish') ) {
            // d('-- complete text');
            const complete = $form.data('complete');
            const $next = $('.pager .next a');
            const $previous = $('.pager .previous a');
            // $previous.hide();
            if( !$next.data('text' ) ) $next.data('text', $next.text() );
            $next.text( complete );
        } else {
            const $next = $('.pager .next a');
            const $previous = $('.pager .previous a');
            $previous.show();
            const text = $next.data('text');
            // d('tetx:',text);
            $next.text(text);
        }
        $next.addClass('active');
        // d('start:',$next[0])
        while ($next = $next.next()) {
            i++;
            // d('next:', $next[0]);
            if (!$next || !$next.length) break;
            $next.removeClass('active');
            if (i == 10) break;
        }
    }
}

export const previousWizardTab = e => {
    const $current = _current();
    const $previous = $current.prev('a');
    if ($previous.length == 0) return;

    // d('previous', $previous[0]);
    $previous.trigger('click');
    _buttonText();
}
export const renderInvalidTab = e => {
    const $form = $(e.target).closest('form');
    // d('form', $form);
    const $invalid = $form.find('.invalid');
    // d("INVALID:", $invalid[0]);
    const $tab = $invalid.closest('.tab-pane');
    if ($tab.length) {
        const tabId = $tab.attr('id');
        const $a = $(`a[href=#${tabId}]`);
        $a.click();
        setTimeout($invalid.find(':input').focus(), 0);
    }
}

export const focusWizardField = e => {
    e.preventDefault();
    const $el = $(e.target)[0].tagName == 'A' ? $(e.target ) : $(e.target).closest('a');
    const field = $el.data('field');
    d('field:', field, $el.data(), $el[0]);
    const $field = $('.form-group[data-path="' + field + '"]');
    const $tab = $field.closest('.tab-pane');
    const id = $tab.attr('id');
    const $link = $('a[href=#' + id + ']')
    d('link:', $link[0], id);
    $link.click();
    d('field:', $field[0]);
    $field.find(':input').first().focus();
}