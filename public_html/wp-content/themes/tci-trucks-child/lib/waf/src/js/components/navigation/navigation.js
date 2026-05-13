const $ = jQuery.noConflict();
const { scrollTo } = require('../../utils/scrollTo');
const { updateActive } = require('../ui/updateActive');

export const previousField = e => {
    e.preventDefault();
    const form = $(e.target).getForm();
    // d('form',form);
    const field = form.previous;
    // d('find previous',field);
    if (field) {
        scrollTo(field);
        updateActive();
    }
}
export const nextField = e => {
    const form = $(e.target).getForm();
    const field = form.next;
    // d('find next',field);
    e.preventDefault();
    // if( field ) scrollTo(field);
    form.walk();
    updateActive();
}