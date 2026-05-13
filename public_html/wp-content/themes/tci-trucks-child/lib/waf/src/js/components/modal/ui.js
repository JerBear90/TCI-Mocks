const $ = jQuery.noConflict();
export const submitModal = e => {
    const $modal = $(e.target).closest('.modal');
    const $form = $modal.find('form');
    if( $form.length ) $form.submit();
}