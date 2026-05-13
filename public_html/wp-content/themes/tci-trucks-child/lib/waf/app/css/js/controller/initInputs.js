const $ = require('../utils/jquery');
const { changeInput, clearInput, focusInput } = require('../ui/input');
// const { updateDuplicator } = require('../ui/duplicator');
// const { focusInput } = require('../ui/input');
// const { updateActive } = require('../ui/updateActive');
const initInputs = () => {
    $(document).on('keyup change', '.form-group[data-uuid] :input', changeInput);
    $(document).on('click', '.form-group[data-uuid] .clear', clearInput);
    $(document).on('focus', '.form-group[data-uuid] :input', focusInput);
}
module.exports = initInputs