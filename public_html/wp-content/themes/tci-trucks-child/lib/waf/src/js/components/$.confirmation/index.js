const $ = require('../../ui/jquery');
const { updateConfirmation, keyUpdateConfirmation } = require('./ui');

$.fn.confirmation = function () {
    $(document).on('click', '.form-group.confirmation button', updateConfirmation);
    $(document).on('keypress', '.form-group.confirmation', keyUpdateConfirmation);
}
$(document).ready(function ($) {
    $(document).confirmation();
});