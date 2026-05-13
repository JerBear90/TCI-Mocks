const {renderOther} = require('../ui/other');
const initOther = () => {
    const $ = jQuery.noConflict();
    
    $(document).on( 'change', 'form[data-uuid] select', renderOther );
    $(document).on( 'blur', 'form[data-uuid] input.other', renderOther );
    $(document).on( 'change', 'form[data-uuid] input[type=radio],input[type=checkbox]', renderOther );
    $(document).on( 'click', 'form[data-uuid] .toggle-other', renderOther );
}
module.exports = initOther;