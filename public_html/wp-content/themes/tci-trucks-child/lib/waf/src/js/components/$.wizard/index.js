const $ = require('../../ui/jquery');
const { updateWizardProgress, nextWizardTab, previousWizardTab, renderInvalidTab, changeTab, completeWizard, focusWizardField } = require('./ui');

$.fn.wizardForm = () => {
    $(document).on('shown.bs.tab', updateWizardProgress);
    $(document).on('click', '.step-wizard a[data-toggle=tab]', updateWizardProgress);
    $(document).on('click', '.wizard.pager .next a', nextWizardTab);
    $(document).on('click', '.wizard.pager .previous a', previousWizardTab);
    $(document).on('invalid', 'form.waf.wizard', renderInvalidTab);
    $(document).on('complete', 'form.waf.wizard', completeWizard );
    $(document).on('click', 'form.waf.wizard .nav a', changeTab);
    $(document).on('click', 'form.waf.wizard .nav a,.timeline-step', changeTab);
    $(document).on('click','a[data-field]', focusWizardField );
}
$(document).ready(function () {
    $(document).wizardForm();
})