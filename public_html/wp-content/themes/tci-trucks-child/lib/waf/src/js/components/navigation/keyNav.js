const {updateKeyNav} = require('../ui/updateKeyNav');
const {updateActive} = require('../ui/updateActive');
const $ = jQuery.noConflict();
const initKeyNav = () => {
    $(document).on( 'keyup', '.form-group[data-uuid] :input, .form-group .input', updateKeyNav );
    $(document).on( 'rendered', '*[data-uuid]', updateKeyNav );    
    $(document).on( 'focus', '.form-group[data-uuid] *', updateActive );    
}
module.exports = initKeyNav