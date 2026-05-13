const $ = jQuery.noConflict();
const tus = require('tus-js-client');
const {saveFiles} = require('./ui');
const Form = require('../../inc/Form');
export const tusChange = evt => {
    const $field = $(evt.target).closest('.form-group');
    let field = $(evt.target).getField();
    d('-- tus change',field);
    if( !field ) {
        const form_id = $(this).closest('form').attr('name');
        
        const data = $field.data();
        const form = new Form(form_id);
        // d('form:',form_id,form);
        field = form.fields.find( f => f.path == data.path );
        d('field:',field);
        
        // d('tus field:',field,'form:',form_id,form)
    }
    // d('tus upload',field);  
    // $(evt.target).closest('form').data('disabled','disabled');
    evt.preventDefault();
    // d('tus change!');
    // d(evt.target.files);
    // d('field:',field);
    Object.entries(evt.target.files).forEach( ([i,file]) => {
        

        // Create a new tus upload
        const rand = Math.floor(Math.random() * 1000);
        const data = {
            name: file.name,
            id: 'file-' + rand,
            loading: true
        }
        
        // d('field:',field.context);
        if (!field.context.files) field.context.files = [];
        field.context.simple = false;
        const files = field.context.files;
        files.push(data);
        
        // d('files:',files);
        field.value = files;
        const update = field.render();
        d('update:',update);
        const $li = $(update).last().find('li').last();
        // d('li:',$li[0]);
        field.$field.find('.filelist').append($li);
    
        const upload = new tus.Upload(file, {
            endpoint: field.context.tus,
            retryDelays: [0, 3000, 5000, 10000, 20000],
            metadata: {
                filename: file.name,
                filetype: file.type,
                id: data.id
            },
            onError: function (e) {
                // console.log("Failed because: " + error)
                // const $li = field.$field.find(`.filelist li[data-id=${data.id}]`);
                // d('li',$li);
                // $li.find('.progress').remove();
                // $li.addClass('error');
                // setTimout( $li.remove, 1000 );
            },
            onProgress: function (bytesUploaded, bytesTotal) {
                const percent = (bytesUploaded / bytesTotal * 100).toFixed(2)
                // d(bytesUploaded, bytesTotal, percent + "%")
                field.percent = percent;
                // d('data:::::',data);
                const $progressBar = field.$field.find(`.filelist li[data-id=${data.id}] .progress-bar`);
                $progressBar.width(percent + '%');
                // d($progressBar[0],percent);
                const $submit = field.form.$form.find('[type=submit]');
                if (percent < 100) {
                    field.context.disabled = true;
                    // if ($submit) $submit.prop('disabled', 'disabled');
                } else {
                    field.context.disabled = false;
                    // if ($submit) $submit.removeProp('disabled', 'disabled');
                }
                var $filelist = field.$field.find('.filelist');
                if( $filelist.length > 1 ) $filelist.last().remove();
            },
            onSuccess: function (e) {
                console.log("Download %s from %s", upload.file.name, upload.url);
                // d('filed',files,field.context);
                
                const file = files.find( f=>f.id == data.id );
                field.value = files;
                
                if( file ) {
                    file.complete = true;
                    file.uploading = false;
                    file.progress = 100;
                    file.url = upload.url;
                }
                // d(data);
                const li = `li[data-id=${data.id}]`;
                const update = field.render();
                const $li = $(update).find(li).last();
                field.$f.find('.filelist '+li).replaceWith($li);
                field.$f.find('.filelist '+li).remove();

                const $progress = field.$field.find(`.filelist li[data-id=${data.id}] .progress`);
                d('progress:', $progress);
                $progress.replaceWith('')
                // d('url:',upload.url);
                // field.render(field.$field);
                
                d('field:',field.$field[0])
                field.context.route = $field.data('route');
                saveFiles(evt,field,upload);
                // else field.$field.trigger('render');

                const incomplete = files.filter(f => !f.complete);
                d('incomplete:', incomplete)
                if( incomplete.length == 0 ) {
                    d("COMPLETE!");
                    const $submit = field.form.$form.find('[type=submit]');
                    $(evt.target).closest('form').removeData('disabled');
                    $submit.removeAttr('disabled');
                    
                    
                }
            },
            onComplete: function(e) {
                $(evt.target).closest('form').removeData('disabled');
            }
        })
        upload.start()
    });

    // Start the upload
    
}

