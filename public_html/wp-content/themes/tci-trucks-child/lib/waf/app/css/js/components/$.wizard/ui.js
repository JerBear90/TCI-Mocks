const $ = jQuery.noConflict();
const _buttonText = e => {
    const $current = _current();
    const $next = $current.closest('li').next('li').find('a');

    const $button = $('ul.wizard.pager .next a');
    // d('button', $button);
    const text = $next.length ? $button.data('next') : $button.data('save');
    $button.text(text);
}


// $('#rootwizard .finish').click(function () {
//     $('#success-modal').modal();
// });
const _current = () => {
    let $current = $('.step-wizard a[data-toggle=tab].active')
    if ($current.length == 0) $current = $('.step-wizard a[data-toggle=tab]').first();
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
    const $current = _current();
    const $next = $current.closest('li').next('li').find('a');
    // if ($next.length == 0) {
    //     const $form = $(e.target).closest('form');
    //     $form.submit();
    // }

    $next.trigger('click');
}
export const changeTab = function (e) {
    const $context = $(this).closest('form');
    const sel = $(this).attr('href');
    // d('change', sel, $(sel)[0], 'friends', $(sel).siblings());
    $(sel, $context).siblings().removeClass('active ');
    $(sel, $context).addClass('active');
}

export const previousWizardTab = e => {
    const $current = _current();
    const $previous = $current.closest('li').prev('li').find('a');
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
