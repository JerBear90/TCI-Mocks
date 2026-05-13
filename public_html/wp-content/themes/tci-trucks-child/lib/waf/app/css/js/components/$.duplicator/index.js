const $ = require('../../ui/jquery');
const { addDuplicatorRow, removeDuplicatorRow, duplicatorKeypress } = require('./ui');

$.fn.duplicator = function () {
    $(document).on('keypress', '.form-group.duplicator :input', duplicatorKeypress)
    $(document).on('click', '.add-duplicator-row', addDuplicatorRow);
    $(document).on('click', '.remove-duplicator-row', removeDuplicatorRow);
}

jQuery(document).ready(function () {
    $(document).duplicator()
})