export const formatAddress = function (place) {
    if (place.formatted_address) return place.formatted_address;
    let address = '';
    if (place.address_components) {
        address = [
            (place.address_components[0] && place.address_components[0].short_name || ''),
            (place.address_components[1] && place.address_components[1].short_name || ''),
            (place.address_components[2] && place.address_components[2].short_name || '')
        ].join('\n');
    }
    return address;


    // if (!window.addressFormat) return place.name;
    // var address = addressFormat;
    // var parts = addressFormat.split(' ');
    // parts = parts.map(p => p.replace(/(^,)|(,$)/g, ""));
    // // d('parts', parts);
    // var details = {};
    // place.address_components.forEach(c => {
    //     var type = c.types[0];
    //     details[type] = c.long_name;
    // })
    // d(details);
    // parts.forEach(p => {
    //     var replace = details[p] ? details[p] : '';
    //     // d('replace', p, 'with', details[p]);
    //     address = address.replace(p, replace);
    // })
    // d("ADDRESS:", address);
    // return address;
}