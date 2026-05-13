const $ = jQuery.noConflict();
export const updatePercent = e => {
    const form = $(e.target).getForm();
    
    // console.log('form is ',form.percent,'% complete');
    if( form ) if( form.percent ) {
        // d('update percenter',form.percent);
        $('.progress-percent .text').text( form.percent.toFixed(0) )
        $('.form-progress .progress-bar').width( form.percent+'%' );
    }
}