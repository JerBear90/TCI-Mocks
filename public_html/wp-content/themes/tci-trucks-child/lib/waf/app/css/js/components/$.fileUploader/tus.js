const $ = jQuery.noConflict();
const tus = require('tus-js-client');
const {saveFiles} = require('./ui');
export const tusChange = evt => {
    const file = evt.target.files[0];
    const field =$(evt.target).getField();

    // Create a new tus upload
    const rand = Math.floor(Math.random() * 1000);
    const data = {
        name: file.name,
        id: 'file-' + rand,
        loading: true
    }
    field.context.files.push(data);
    field.render(field.$field);
    
    const upload = new tus.Upload(file, {
        endpoint: field.context.tus,
        retryDelays: [0, 3000, 5000, 10000, 20000],
        metadata: {
            filename: file.name,
            filetype: file.type,
            id: data.id
        },
        onError: function (error) {
    
        },
        onProgress: function (bytesUploaded, bytesTotal) {
            const percent = (bytesUploaded / bytesTotal * 100).toFixed(2)
            // d(bytesUploaded, bytesTotal, percent + "%")
            field.percent = percent;
            const $progressBar = field.$field.find(`.filelist li[data-id=${data.id}] .progress-bar`);
            $progressBar.width(percent + '%');
            if (percent < 100) {
                field.context.disabled = true;
                const $submit = field.form.$form.find('input[type=submit]');
                if ($submit) $submit.prop('disabled', 'disabled');
            } else {
                field.context.disabled = false;
                field.form.$form.find('input[type=submit]').removeProp('disabled');
            }
        },
        onSuccess: function (e) {
            console.log("Download %s from %s", upload.file.name, upload.url);
            const file = field.context.files.find( f=>f.id == data.id );
            const $progress = field.$field.find(`.filelist li[data-id=${data.id}] .progress`);
            $progress.replaceWith('<div class="text-success">Uploaded Successfully! Thank you!</div>')
            if( file ) {
                file.complete = true;
                file.uploading = false;
                file.progress = 100;
                file.url = upload.url;
            }
            // d('url:',upload.url);
            field.render(field.$field);
            
            // d('field:',field.$field[0])
            if (field.$field.hasClass('autosave')) saveFiles(evt,field);
            // else field.$field.trigger('render');
        },
        onComplete: function(e) {
            
        }
    })

    // Start the upload
    upload.start()
}

