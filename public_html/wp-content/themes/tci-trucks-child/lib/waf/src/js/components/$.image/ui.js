const $ = require('../../ui/jquery');
const {parseJSON} = require('../../utils/parseJSON');
const axios = require('../../services/axios');
export const uploadProgress = (e, { file, event }) => {
    // d("upload progress", $(e.target)[0]);
    const field = $(e.target).getField();
    const percent = event.loaded / event.total * 100;
    field.percent = percent;
    // d('percent',percent);
    // d(file);
    let $percent = field.$field.find(` .progress-bar`);
    if( $percent.length == 0 ) {
        // field.$field.find('.img-upload').append('<div class="progress"><div class="progress-bar"></div>');
        $percent = field.$field.find(` .progress-bar`);
    }
    
    // d('percetn',$percent)
    $percent.width(percent + '%');
    if (percent <= 100) {
        field.context.disabled = true;
        const $submit = field.form.$form.find('input[type=submit]');
        if( $submit.length ) $submit.prop('disabled', 'disabled');
    } else {
        field.context.disabled = false;
        field.form.$form.find('input[type=submit]').removeProp('disabled');
    }
    // field.render(field.$field);
}
export const completeUpload = async(e, { file, reader }) => {
    // d('E', e.target, file, reader.conte)
    // d('complete');
    const field = $(e.target).getField();
    field.context.value = reader.result;
    field.value = reader.result;
    field.context.disabled = false;
    field.context.unsaved = true;
    field.form.changed = true;

    const html = field.render();
    const bd = $(html).find('.bd');
    d('html:',html,);
    field.$field.find('.bd').html( $(bd).html() );
}
export const startUpload = (e, { file }) => {
    // d('---start upload', $(e.target)[0], file);
    const field = $(e.target).getField();
    const reader = new FileReader();
    const rand = Math.floor(Math.random() * 1000);

    const data = {
        name: file.name,
        id: 'file-' + rand,
        loading: true,
        src: file
    }
    file.id = data.id;
    // field.context.files.push(data);
    // d('files:',field.context.files);
    // const html = field.render();
    // const $files = $(html).find('.filelist');
    // field.$field.find('.filelist').replaceWith($files);
    // field.$field.trigger('render');
    // field.addLoading();
    reader.readAsDataURL(file);
    reader.onloadend = event => field.$field.trigger('completeUpload', { file, reader, event })
    reader.onprogress = event => field.$field.trigger('uploadProgress', { file, reader, event })
}
export const fileChange = e => {
    const field = $(e.target).getField();
    const form = $(e.target).getForm();
    e.preventDefault();
    e.stopPropagation();
    // d('change file!', field, e.target);

    if (!field) {
        d('no field?');
        return;
    }

    // e.preventDefault();
    const droppedFiles = e.target.files; // the files that were dropped
    // d('files:', droppedFiles);
    if (droppedFiles) {
        $(droppedFiles).each((i, file) => {
            // name += Math.random() * 100;
            // ajaxData.append( name, file );
            const rules = field.context.rules;
            if( rules && rule !== null ) {
                const extensions = rules.split(',');
                const ext = file.name.split('.').pop();
                // console.log('file ext', ext, 'available', extensions, 'index', extensions.indexOf(ext))
                if (extensions.indexOf(ext) === -1) field.context.invalidExtension = true;
            }
            
            field.$field.find('.img-upload').append('<div class="progress"><div class="progress-bar" style="width:5%"></div>');
            field.$field.trigger('startUpload', { file })
        });
    }
}
export const deleteFile = async (e) => {
    e.preventDefault();
    const field = $(e.target).getField();
    const $f = field.$field;
    field.context.value = null;
    field.value = null;
    field.render( $f );
    
}
