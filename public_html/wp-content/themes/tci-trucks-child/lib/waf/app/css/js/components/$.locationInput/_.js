function initMap() {
    if (!window.google) return;

    // map.controls[google.maps.ControlPosition.TOP_RIGHT].push(card);
    // d('input', input);


    // Bind the map's bounds (viewport) property to the autocomplete object,
    // so that the autocomplete requests use the current map bounds for the
    // bounds option in the request.
    // if (map ) autocomplete.bindTo('bounds', map);

    // Set the data fields to return when the user selects a place.





    d('value', place.formatted_address);
    // var address = formatAddress(place);
    // field.$input.val(address);
    if (!place.geometry) {
        // User entered the name of a Place that was not suggested and
        // pressed the Enter key, or the Place Details request failed.
        console.log("No details available for input: '" + place.name + "'");
        return;
    }

    if (map) {
        infowindow.close();
        marker.setVisible(false);

        // If the place has a geometry, then present it on a map.
        if (place.geometry.viewport) {
            map.fitBounds(place.geometry.viewport);
        } else {
            map.setCenter(place.geometry.location);
            map.setZoom(17); // Why 17? Because it looks good.
        }
        marker.setPosition(place.geometry.location);
        marker.setVisible(true);

        var address = '';
        if (place.address_components) {
            address = [
                (place.address_components[0] && place.address_components[0].short_name || ''),
                (place.address_components[1] && place.address_components[1].short_name || ''),
                (place.address_components[2] && place.address_components[2].short_name || '')
            ].join('\n');
        }

        infowindowContent.children['place-icon'].src = place.icon;
        infowindowContent.children['place-name'].textContent = place.name;
        infowindowContent.children['place-address'].textContent = address;
        infowindow.open(map, marker);
    }
}

// Sets a listener on a radio button to change the filter type on Places
// Autocomplete.
if (map && window.radioButton) {
    function setupClickListener(id, types) {
        var radioButton = document.getElementById(id);
        radioButton.addEventListener('click', function () {
            autocomplete.setTypes(types);
        });
    }

    setupClickListener('changetype-all', []);
    setupClickListener('changetype-address', ['address']);
    setupClickListener('changetype-establishment', ['establishment']);
    setupClickListener('changetype-geocode', ['geocode']);

    document.getElementById('use-strict-bounds')
        .addEventListener('click', function () {
            console.log('Checkbox clicked! New state=' + this.checked);
            autocomplete.setOptions({ strictBounds: this.checked });
        });
}
