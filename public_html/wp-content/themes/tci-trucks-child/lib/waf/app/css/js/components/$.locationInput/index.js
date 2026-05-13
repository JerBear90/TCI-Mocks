const $ = require('../../ui/jquery');
const { renderLocation, mapUpdate, mapClick, placeChange } = require('./ui');
$.fn.locationInputs = () => {
    $(document).on('render', '.form-group.location', renderLocation);
    $(document).on('place_change', '.form-group', placeChange);
    $(document).on('map_update', '.form-group', mapUpdate);
    $(document).on('map_click', '.form-group', mapClick);
}
jQuery(document).ready(function () {
    $(document).locationInputs()
})