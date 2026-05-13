const { initEventHandlers } = require('./initEventHandlers');
const renderForms = require('../ui/renderForms');

export const initializeForms = () => {
    initEventHandlers();
    
    renderForms();
}