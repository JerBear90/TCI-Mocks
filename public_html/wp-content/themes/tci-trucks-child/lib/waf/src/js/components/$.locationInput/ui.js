const $ = require('../../ui/jquery');
const { centerMap, addMarker, renderInfoWindow } = require('./map');
const { updateComponent } = require('./updateComponent');
export const renderMap = e => {
    const field = $(e.target).getField();
    const $map = field.$field.find('.input-map');
// d('map el:',$map[0])
    const map = new google.maps.Map($map[0], {
        center: { lat: -33.8688, lng: 151.2195 },
        zoom: 10
    });
    map.addListener('click', event => {
        // d('map click!');
        field.$field.trigger('map_click', { map, event });
    })
    return map;
}
export const renderLocation = e => {
    // d('--render location',window.google)
    if( !window.google ) return;
    
    
    const field = $(e.target).getField();
    // d('render location for',field,$(e.target)[0])
    if (!field) return;
    const input = $(e.target).find('input')[0]
    // d('render location?', field,input);

    const autocomplete = new google.maps.places.Autocomplete(input);

    autocomplete.setFields(['address_components', 'geometry', 'icon', 'name']);
    // d('autocomplete:',autocomplete)
    const map = field.context.map ? renderMap(e, autocomplete) : null;
    field.context.map = map;
    // d('add autocomplete', field, input, map);
    autocomplete.addListener('place_changed', () => {
        const place = autocomplete.getPlace();
        field.$field.trigger('place_change', place);
        field.$field.find('.clear').show();
        if (map) {
            field.$field.trigger('map_update', { map, place });
        }
        d('place:',place);

    });
    const geocoder = new google.maps.Geocoder;
    const value = field.value;
    if( value ) {
        geocoder.geocode({ 'address':value }, (results, status) => {
            if( results ) {
                const place = results[0];
                if (place && status == 'OK') {
                    // d('found place:', place);
                    field.$field.trigger('place_change', place);
                    field.$field.trigger('map_update', { map, place });
                }
            }
        });
    }
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
    if( field.$input.attr('disabled') ) return;
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
    e.preventDefault();
    const field = $(e.target).getField();
    // d('palce:',place.formatted_address);
    if (place.formatted_address) {
        const val = place.formatted_address;
        // d('val:',val);
        field.$input.val(val);
        field.value = val;
    }
    field.$input.change();
    // d('field', field.value);

    if (field.context.components) {
        Object.entries(field.context.components).find(([id, component]) => {
            updateComponent(id, component, place, e)
        });
        // d('PLACE', place);
    }
    if (field.context.use) updateComponent(field.context.nameAttr, field.context.use, place, e);
}

export const locateMe = e => {
    e.preventDefault();
    e.stopPropagation();
    const field = $(e.target).getField();
    
    navigator.geolocation.getCurrentPosition(position => {
        const geocoder = new google.maps.Geocoder();
        var latlng = new google.maps.LatLng(position.coords.latitude, position.coords.longitude);

        geocoder.geocode({ 'latLng': latlng }, function (results, status) {
            // d('results',results);
            if( results.length ) {
                field.$input.val( results[0].address_components.formatted_address );
                const map = field.context.map;
                const place = results[0];
                // d(place);
                field.$field.trigger('place_change',results[0]);

                // d('update:', place, e.target);
                field.$field.trigger('map_update', {map,place});
                const marker = addMarker(map, place);
                renderInfoWindow(field, map, place, marker);
                centerMap(map, place);
            }
        });

    })
    return false;
}

export const clearLocation = e => {
    e.preventDefault();
    const field = $(e.target).getField();
    field.$field.find('a.clear').hide();
    if( !field.$input.val() ) if (field.context.components) {
        Object.entries(field.context.components).find(([id, component]) => {
            d('update');
            updateComponent(id, component );
        });   
    }
}

export const blurLocation = e => {
    const field = $(e.target).getField();
    if (field.context.components) {
        Object.entries(field.context.components).find(([id, component]) => {
            const $component = $('input[name='+component+']');
            if( !$component.val() ) return $(e.target).val('');
        });
    }
}