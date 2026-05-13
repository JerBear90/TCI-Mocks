const $ = jQuery.noConflict();
export const updateKeyNav = e => {
    const form = $(e.target).getForm();
    
    // d('update key nav',form.args.keyboardNav);
    if( form ) 
    if( form.args.keyboardNav ) {
        const field = form.active;
        if( field.context.type == 'submit' || field.context.type == 'button' || field.context.type == 'container' ) return;
        const $parent = field.$field;

        const $btns = $parent.find('.btn, input[type=submit]');
        const $inputs = $parent.find(':input:not(button)');
        // d('field',field,'$parent',$parent);
        if( $btns.length > 0 && $inputs.length == 0 ) return false;
        
        // d(field.validate());
        if(!field.invalid && $inputs.length > 0 ) {
            field.$append.html( field.keyboardNav() );
        }
        else if( field.invalid ) {
            field.$append.html( '' );
        }
    }
}