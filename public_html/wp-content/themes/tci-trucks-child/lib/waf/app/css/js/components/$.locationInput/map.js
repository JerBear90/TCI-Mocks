const { formatAddress } = require('./formatAddress');
export const centerMap = (map, place) => {
    // If the place has a geometry, then present it on a map.
    if (place.geometry.viewport) {
        map.fitBounds(place.geometry.viewport);
    } else {
        map.setCenter(place.geometry.location);
        map.setZoom(17); // Why 17? Because it looks good.
    }
}

export const renderInfoWindow = (field, map, place, marker) => {
    // d('place', place, field);
    if (!place || !field) return;
    const $infoWindow = field.$field.find('.infowindow-content').clone();

    const infowindow = new google.maps.InfoWindow();
    infowindow.setContent($infoWindow[0]);
    $infoWindow.find('.place-icon').attr('src', place.icon);
    $infoWindow.find('.place-name').text(place.name);
    $infoWindow.find('.place-address').text(formatAddress(place));
    infowindow.open(map, marker);
    return infowindow;
}

export const addMarker = (map, place) => {
    // d('place', place);
    if (!map.markers) map.markers = []
    else map.markers.forEach((m, i) => {
        m.setMap(null);
        map.markers.splice(i, 1);
    })
    const marker = new google.maps.Marker({
        map: map,
        anchorPoint: new google.maps.Point(0, -29)
    });
    marker.setPosition(place.geometry.location);
    marker.setVisible(true);
    map.markers.push(marker);
    // d('makers', map.markers);
    return marker;
}


export const clickMap = (e, map) => {
    var geocoder = new google.maps.Geocoder;
    $('#wsf_form_grafter .form-p.map .error').remove();
    geocoder.geocode({ 'location': event.latLng }, (results, status) => {
        if (status === 'OK') {
            if (results[0]) field.$field
            const place = results[0];

        }
    });
}