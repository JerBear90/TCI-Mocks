const $ = require('../../utils/jquery');
const { scrollSpeed } = require('../../config');
const { scrollTo } = require('../../utils/scrollTo');
const { addDuplicatorRow } = require('../ui/duplicator');
const { updateKeyNav } = require('../ui/updateKeyNav');
const walkNext = e => {
    console.log('walk next', e.isDefaultPrevented());
    if (e.isDefaultPrevented()) return false;
    e.preventDefault();
    e.stopPropagation();
    const $formBD = $(e.target).closest('.form-bd > .bd');
    // d('el',$(e.target)[0]);
    const form = $(e.target).getForm();
    const field = $(e.target).getTopField();
    form.active = field;
    const $next = field.$field.nextAll('.form-group, fieldset');
    // d('next',$next.length,$next);
    $next.remove();

    // d('form',form,$(e.target)[0]);
    // console.log('form',form);
    const html = form.walk();
    d(html);
    if (html) {
        const $html = $(html);
        const uuid = $html.data('uuid');
        $formBD.append($html);
        const field = form.findField(uuid);
        $(e.target).blur();
        setTimeout(() => {
            scrollTo(field);
        }, 250);
        updateKeyNav(e);
        // d('WALK TO INPUT',field.$input[0],field,field.$input,field.$field);
        // field.$input.focus();
    }
}
const keyWalkNext = e => {
    // console.log('keypress',e.keyCode,e);
    const form = $(e.target).getForm();
    const field = $(e.target).getTopField();

    if (!field) return;
    let invalid = false;
    if (field) if (field.invalid) invalid = true;
    // d('current field',field);
    if (e.keyCode === 13) {
        // d("field",field,field.$field,'invalid:',invalid);
        if (invalid) {
            e.preventDefault();
            field.renderValidation();
            // d('invalid field');
            return field.$field.find('.invalid :input').focus();
        } else {
            d("FIELD VALID, continue");
            walkNext(e);
        }

    }
}
module.exports = { walkNext, keyWalkNext }