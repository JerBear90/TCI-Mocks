const $ = require('../../ui/jquery');
const { centerMap, addMarker, renderInfoWindow } = require('./map');
const { updateComponent } = require('./updateComponent');
export const renderMap = e => {
    const field = $(e.target).getField();
    const $map = field.$field.find('.input-map');

    const map = new google.maps.Map($map[0], {
        center: { lat: -33.8688, lng: 151.2195 },
        zoom: 13
    });
    map.addListener('click', event => {
        // d('map click!');
        field.$field.trigger('map_click', { map, event });
    })
    return map;
}
export const renderLocation = e => {
    if( !window.google ) return;
    // d('render location?');
    // d('--render location')
    const field = $(e.target).getField();
    if (!field) return;
    const input = $(e.target).find('input')[0]


    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
    const map = field.context.map ? renderMap(e, autocomplete) : null;
    d('add autocomplete', field, input, map);
    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        field.$field.trigger('place_change', place);
        if (map) {
            field.$field.trigger('map_update', { map, place });
        }

    });
}

export const mapUpdate = (e, { map, place }) => {
    // d('update:', place, e.target);
    const field = $(e.target).getField();
    const marker = addMarker(map, place);
    if (field) {
        // d('field');
        renderInfoWindow(field, map, place, marker);
    }
    centerMap(map, place);
}


export const mapClick = (e, { map, event }) => {
    // d('-indeed')
    const field = $(e.target).getField();
    // d('field',field);
    const geocoder = new google.maps.Geocoder;
    $('#wsf_form_grafter .form-p.map .error').remove();
    geocoder.geocode({ 'location': event.latLng }, (results, status) => {
        const place = results[0];
        if (place && status == 'OK') {
            // d('place', place);
            field.$field.trigger('place_change', place);
            field.$field.trigger('map_update', { map, place });
        }
    });
}

export const placeChange = function (e, place) {
    // d(place);
    const field = $(e.target).getField();
    d('place', place);
    if (place.formatted_address) {
        const val = place.formatted_address;
        field.$input.val(val);
    }
    field.$input.change();
    // d('field', field.value);

    if (field.context.components) {
        Object.entries(field.context.components).find(([id, component]) => {
            updateComponent(id, component, place)
        });
        // d('PLACE', place);
    }
    if (field.context.use) updateComponent(field.context.nameAttr, field.context.use, place);
}