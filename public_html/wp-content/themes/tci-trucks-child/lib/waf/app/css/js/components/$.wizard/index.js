const $ = require('../../ui/jquery');
const { updateWizardProgress, nextWizardTab, previousWizardTab, renderInvalidTab, changeTab } = require('./ui');

$.fn.wizardForm = () => {
    $(document).on('shown.bs.tab', updateWizardProgress);
    $(document).on('click', '.step-wizard a[data-toggle=tab]', updateWizardProgress);
    $(document).on('click', 'ul.wizard.pager .next a', nextWizardTab);
    $(document).on('click', 'ul.wizard.pager .previous a', previousWizardTab);
    $(document).on('invalid', 'form.waf.wizard', renderInvalidTab);
    $(document).on('click', 'form.waf.wizard .nav a', changeTab);
}
$(document).ready(function () {
    $(document).wizardForm();
})