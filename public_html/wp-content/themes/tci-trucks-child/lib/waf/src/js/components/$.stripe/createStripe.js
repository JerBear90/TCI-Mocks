const $ = jQuery.noConflict();
let stripe = window.stripe;
export const createStripe = e => {
    const field = $(e.target).getFieldObject();


    const key = stripePublicKey;
    if( key && !stripe ) {
        stripe = Stripe(stripePublicKey);
    } else if ( !key ) {
        d("NO STRIPE PUBLIC KEY");

        return false;
    }
    
    if( stripe ) {
        const elements = stripe.elements();
        const card = elements.create('card', {
            iconStyle: 'solid',
            style: {
                invalid: {
                    iconColor: '#e85746',
                    color: '#e85746',
                }
            },
            classes: {
                base: 'form-control',
            },
        });
        
        card.mount( field.$field.find('.card')[0] );
        setTimeout( function() {
            field.removeLoading();
        },field.context.timeout || 1500 );

        field.card = card;
        field.stripe = stripe;
    }
    return false;
}