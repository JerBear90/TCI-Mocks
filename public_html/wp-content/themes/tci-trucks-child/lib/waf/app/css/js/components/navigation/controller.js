const {previousField,nextField} = require('../ui/navigation');
const initNavigation = () => {
    const $ = jQuery.noConflict();
    // d('init naviagation);')
    $(document).on( 'click', '*[data-action=previousField]', previousField );
    $(document).on( 'click', '*[data-action=nextField]', nextField );
}
module.exports = initNavigation;