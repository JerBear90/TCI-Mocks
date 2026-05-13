const $ = jQuery.noConflict();
const {submitModal} = require('../ui/modal');

const initSubmitModal = () => $(document).on( 'click', '.modal-footer .btn-primary', submitModal );
module.exports = initSubmitModal;