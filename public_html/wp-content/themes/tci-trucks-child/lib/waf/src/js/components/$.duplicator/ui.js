const $ = require('../../ui/jquery');
// const { walkNext } = require('../walker/ui');
// const { updateKeyNav } = require('../navigation/updateKeyNav');
const Duplicator = require('../../inc/Duplicator');

const addDuplicatorRow = (e) => {
    e.stopPropagation();
    e.preventDefault();
    
    const field = $(e.target).getDuplicator();
    // d('addrow', $(e.target)[0],field);
    let value = field.context.value;
    // console.log('$field:',$field,'field:',field,'data:',$field.data());
    if (!value) value = ['']

    
    const html = field.addRow();
    
    // d('fieldsets:',field.$fieldsets);
    field.$fieldsets.append(html);
    var $row = field.$fieldsets.find('fieldset').last();
    d('row:',$row[0]);
    $row.trigger('render');
    $row.find('.form-group').each( function() {
        d('redner:',$(this)[0])
        $(this).trigger('render');
    });

    field.renderValidation();
    return false;
}
const removeDuplicatorRow = (e) => {
    e.preventDefault();
    const field = $(e.target).getFieldObject();
    if (!field) {
        d("NO FIELD");
        return;
    }
    const index = $(e.target).data('index');
    // d('index', index);
    const $el = field.removeRow(index);
    // d('el', $el[0]);
    $el.closest('.fieldset-container').remove();
    return false;
}
const duplicatorKeypress = e => {
    // e.stopPropogation();
    // d("duplicator key event",e.keyCode);
    if (e.keyCode === 13) {
        const field = $(e.target).getFieldObject();
        // d('field.requiredLength',field.requiredLength,'value',field.context.value.length,field.context.value);
        const $next = field.$field.find('.input').next('.input input').first();
        d('next', $next[0]);
        if ($next.length) {
            e.preventDefault();
            e.stopPropagation();
            return $next.focus();
        }
        if (field.context.value.length < field.requiredLength)
            addDuplicatorRow(e);
    }
}
module.exports = { addDuplicatorRow, removeDuplicatorRow, duplicatorKeypress }