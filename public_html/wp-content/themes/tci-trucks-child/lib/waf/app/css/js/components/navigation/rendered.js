const { updateKeyNav } = require('../ui/updateKeyNav');
// const {updateActive} = require('../ui/updateActive');
const { updatePercent } = require('../ui/updatePercent');
const $ = jQuery.noConflict();
const renderedHandler = () => {
    $(document).on('rendered', '*[data-uuid]', updateKeyNav);
    $(document).on('rendered focus click', '*[data-uuid]', updatePercent);
}
module.exports = renderedHandler