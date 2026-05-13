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
let stripe;
export const renderStripe = e => {
    // d('target:',$(e.target)[0]);
    const field = $(e.target).getField();
    // d('field:', field);
    const form = $(e.target).getForm();
    // d('form:',form);
    const key = window.stripePublicKey;
    if (key && !stripe) {
        stripe = Stripe(stripePublicKey);
    } else if (!key) {
        d("NO STRIPE PUBLIC KEY");
        return false;
    }

    if (stripe) {
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

        card.mount( $(e.target).find('.card')[0] );
        setTimeout(function () {
            field.removeLoading();
        }, $(e.target).data('timeoute') || 1500);

        $(e.target).data('card',card);
        $(e.target).data('stripe',stripe);
        $(e.target).closest('form').addClass('stripe');
    }
    return false;
}
export const toggleCardInput = e => {
    const val = $(e.target).val();
    const $field = $(e.target).closest('.form-group');
    const $card = $field.find('.card');
    
    if( val ) {
        d('-- hide card',$card[0]);
        $card.addClass('d-none');
    } else {
        $card.removeClass('d-none');
        $field.trigger('render');
    }
}
export const submitStripe = e => {
    const $ = jQuery.noConflict();
    const $card = $(e.target).find('.card');
    if( !$card.is(':visible') )return;
    
    
    const form = $(e.target).getForm();
    d('invalid:',form.invalid);
    const $form = $(e.target);
    if (form.invalid) return form.renderValidation();
    const $field = $(e.target).find('.form-group.stripe');
    const field = form.fields.find( f=> f.context.type == 'stripe' );
    // d('field:::::',field);
    
    
    // d('submitting stripe form',$(e.target),field,form);
    // UI.formLoader(DOM.checkoutForm);
    const stripe = $field.data('stripe');
    $form.removeData('disabled');
    if( stripe ) {
        const token = $(e.target).find('input[name=stripe_token]').val();
        // d('field:',field.value,field.name);
        if( token ) return;


        const value = {}
        form.disabled = true;
        form.addLoading();
        
        const card = $field.data('card');
        $form.data('disabled','disabled');
        // d('form:',$form[0],'data:',$form.data());
        const message = $form.data('stripe');
        form.message({status:'info',message:message,mode:'inline'})
        // d('processing stripe',message)
        stripe.createToken(card)
            .then( (res,err) => {
                d('res:',res,res.error);
                if( res.error ) return form.message( {status:'danger','message':res.error.message,mode:'inline'} );
                // field.context.value = res.token.id;
                value.token = res.token.id;
                
                $field.find(':input').each( function() {
                    var name = $(this).attr('name');
                    value[name] = $(this).val();
                });
                d('token',res.token.id);
                $field.find('input[name=stripe_token]').val(JSON.stringify(value));
                form.disabled = false;
                
                $form.removeData('disabled');
                const message= $form.data('stripe-complete');
                d('-- commplete',message);
                form.message({status:'success',message:message})
                $form.find('[type=submit],.next a').click();
            })
            .catch( err => {
                d('-- error',err.message);
                e.preventDefault();
                $(e.target).closest('.form-group').data('invalid');
                form.message( {message:`Stripe Error: ${err.message}`, status:'danger'} );
            });
    }
}

export const clearStripeToken = e => {
    const form = $(e.target).getForm();
    const stripeFields = form.allFields().filter( f => f.context.type === 'stripe' );

    stripeFields.forEach( f => {
        // d('stripe fields',f);
        f.context.value = '' 
    });
}