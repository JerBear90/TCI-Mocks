const $ = require('../../ui/jquery');
export const updateComponent = (id, component, place, e) => {
    const form = $(e.target).getForm();
    const field = $(e.target).getField();
    // d(place);
    if( field ) if( field.disabled ) return;
    
    // d('field:',field);
    
    let $el;
    var $fieldset = $(e.target).closest('fieldset')
    if( field.context.scope == 'fieldset' ) {
        const sel = '.'+id+' :input';
        $el = $fieldset.find(sel)
        // d('el:', $el[0], sel);
    } else {
        const sel = id.indexOf('.') > -1 ? '[data-path="' + id + '"] input' : 'input[name=' + id + ']';
        // d('sel:',sel);
        $el = $(sel)
    }
    
    
    let value;
    if( !place ) return $el.val('').change();
    // d(place.geometry.location);
    // d('component:',id,component)
    let { name, key } = component.split('.');
    if (!key) key = 'long';
    if (!name) name = component;

    if( (component == 'lat' || component == 'lng') ){
        value = place.geometry.location[component]
    } else if ( component == 'address' ) {
        const st = place.address_components.find(c => c.types.indexOf('street_number') > -1);
        const route = place.address_components.find(c => c.types.indexOf('route') > -1);
        if( st && route ) value = st[key+'_name']+' '+route[key+'_name'];
    
    } else {
        const found = place.address_components.find(c => c.types.indexOf(name) > -1 );
        
        if( found ) value = found[key+'_name']
    //  d("---------")   
    }
    // d('el:', $el[0],value);
    $el.val(value)
    
    const componentField = $el.getField();
    // d('-- update',value);
    // d('field:', componentField,$el[0],value);
    if (componentField) {
        // d(componentField.$f[0]);
        componentField.value = undefined;
        if( componentField.context.type == 'info' ) {
            componentField.context.info = value;
            componentField.$f.find('.alert').text(value);
        }
    }
}