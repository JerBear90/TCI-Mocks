const $ = jQuery.noConflict();

export const getFormConfig = async () => {
    d('formconfig:', formConfig);
    if (formConfig) {
        window.formConfig = formConfig;
        d('update nonce', formConfig.nonce);
        // get form config with axios....
    }
    return formConfig;
}