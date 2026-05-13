const $ = jQuery.noConflict();
const DOM = {
    stripeToken:"input[name=stripe_token]",
}
const MSGS = {
    sortFailed: 'Failed to save sort order',
    default: 'Failed',
    noCard: 'Error Processing card',
    cardVerified: 'Card verified via stripe',
    confirmSaveState: 'Settings are unsaved - continue anyway?'
}

export const submitStripe = e => {
    const $ = jQuery.noConflict();
    const field = $(e.target).getFieldObject();
    const form = $(e.target).getForm();
    


    
    // d('submitting stripe form',$(e.target),field,form);
    // UI.formLoader(DOM.checkoutForm);
    const stripe = field.stripe;
    if( stripe ) {
        e.preventDefault();
        if( field.context.value ) return false;


        const value = {}
        form.disabled = true;
        form.addLoading();
        stripe.createToken(field.card)
            .then( (res,err) => {
                
                if( res.error ) return form.message( {status:'Danger','message':res.error.message} );
                // field.context.value = res.token.id;
                value.token = res.token.id;
                field.$field.find(':input').each( function() {
                    var name = $(this).attr('name');
                    value[name] = $(this).val();
                });
                d('token',res.token.id);
                field.context.value = value;
                form.disabled = false;
                d('stripe value',value,field.context);    
                form.submit();
            })
            .catch( e => {
                form.message( {message:`Stripe Error: ${e.message}`, status:'error'} );
            });
    }
    return false;
}

export const clearStripeToken = e => {
    const form = $(e.target).getForm();
    const stripeFields = form.allFields().filter( f => f.context.type === 'stripe' );

    stripeFields.forEach( f => {
        d('stripe fields',f);
        f.context.value = '' 
    });
}