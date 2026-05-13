const $ = jQuery.noConflict();
export const renderOther = e => {
    e.preventDefault();
    e.stopPropagation();
    
    // const $field = $(e.target).closest('.form-group');
    const field = $(e.target).getFieldObject();
    // d('field',field);
    if( !field ) return;
    const {value} = field.context;
    if( value ) if( typeof(value) == 'string' ) if( value.toLowerCase() == 'other' ) {
        // console.log("RENDER OTHER???",value);
        field.context.other = true;
        // d('field',field.$field);
        field.render( field.$field );
        field.context.value = '';
        if( field.$other.length ) field.$other.focus();
    } else if ( field.context.other && !value ) {
        field.context.other = false;
        field.render( field.$field );
    }
}