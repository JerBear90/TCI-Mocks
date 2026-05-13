const $ = require('../../ui/jquery');
const ui = require('./ui');
$.fn.locationInputs = () => {
    $(document).on('blur', '.form-group.location :input', ui.blurLocation);
    $(document).on('change', '.form-group.location :input',ui.clearLocation);
    $(document).on('change', '.form-group.location :input', e => {
        const field = $(e.target).getField();
        field.value = $(e.target).val();
    });
    $(document).on('click', '.form-group.location a.clear', e => {
        $(e.targte).val('');
        ui.clearLocation(e);
    });
    $(document).on('render', '.form-group.location', ui.renderLocation);
    $(document).on('place_change', '.form-group', (e,place) => { 
        $(this).trigger('pre_place_change');
        ui.placeChange(e,place);
    });
    $(document).on('map_update', '.form-group', ui.mapUpdate);
    $(document).on('map_click', '.form-group', ui.mapClick);
    $(document).on('click', '.form-group.location .locate', ui.locateMe);
}
jQuery(document).ready(function () {
    
    $(document).locationInputs()
})
