const $ = jQuery.noConflict();
export const updateActive = e => {
    try {
        const form = $(e.target).getForm();
        const field = $(e.target).getTopField();
        // d('form',form,'field',field);
        // d('active',field.context.name,$(e.target)[0]);
        if (field) form.active = field;
        // d("ACTIVE",form.active)
    } catch (e) {

    }
}