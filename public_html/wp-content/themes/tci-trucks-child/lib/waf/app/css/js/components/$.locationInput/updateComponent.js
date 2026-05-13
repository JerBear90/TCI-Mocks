const $ = require('../../ui/jquery');
export const updateComponent = (id, component, place) => {
    const form = $(this).getForm();
    const $el = $('input[name=' + id + ']');
    let value;
    d(place.geometry.location);
    if( (component == 'lat' || component == 'lng') ){
        value = place.geometry.location[component]
    } else {
        let {name,key} = component.split('.');
        if( !key ) key = 'long';
        d('key:',key);
        const found = place.address_components.find(c => c.types.indexOf(name) > -1);
        if( found ) value = found[key+'_name']
    }
    d('setting',value,'to',component)
    $el.val(value).change();
}