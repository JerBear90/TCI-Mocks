const $ = require('../../ui/$.getForm');
const { walkNext, keyWalkNext } = require('../ui/walk');
const initWalk = () => {
    $(document).on('keypress', '.waf-fullscreen-container', keyWalkNext);
    $(document).on('click', '.waf-fullscreen-container .form-bd .btn', walkNext);
}
module.exports = initWalk;