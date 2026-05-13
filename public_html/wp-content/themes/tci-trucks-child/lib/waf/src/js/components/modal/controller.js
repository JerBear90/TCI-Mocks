const $ = require('../../ui/$.getForm');
const { submitStripe, clearStripeToken, updateStripeFields } = require('../ui/stripe')
const { createStripe } = require('../utils/createStripe');

const initStripe = () => {
    // Form controllers
    $(document).on('rendered', '.form-group.stripe', createStripe);

    // DOM handlers
    // $(document).on( 'blur', '.form-group.stipe :input', submitStripe );
    $(document).on('presubmit', '.form-group.stripe', submitStripe);
    $(document).on('complete', 'form', clearStripeToken);
    $(document).on('click', 'input[type=submit]', clearStripeToken);
    if ($('.form-group.stripe :input').length)
        $(document).on('change', '.form-group.stripe :input', updateStripeFields);
}
module.exports = initStripe;