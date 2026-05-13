jQuery(document).ready( async($) => {
    // monitorFormConfig();

    if( !window.formConfig ) {
        console.log('get form config');
        await getFormConfig();
        console.log('form config',formConfig);
    }
    initEventHandlers();
    renderFormsList('body');
});