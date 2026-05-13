const $ = require('../../ui/jquery');
const {parseJSON} = require('../../utils/parseJSON');
const axios = require('../../services/axios');
export const uploadProgress = (e, { file, event }) => {
    d("upload progress", $(e.target)[0]);
    const field = $(e.target).getField();
    const percent = event.loaded / event.total * 100;
    field.percent = percent;
    field.$field.find(`.filelist li[data-id=${file.id}] .progress-bar`).width(percent + '%');
    if (percent < 100) {
        field.context.disabled = true;
        const $submit = field.form.$form.find('input[type=submit]');
        if( $submit ) $submit.prop('disabled', 'disabled');
    } else {
        field.context.disabled = false;
        field.form.$form.find('input[type=submit]').removeProp('disabled');
    }
    field.render(field.$field);
}
export const completeUpload = async(e, { file, reader }) => {
    // d('E', e.target, file, reader.conte)
    const field = $(e.target).getField();
    const { files } = field.context;

    const myFile = files.find(f => f.id == file.id)
    
    myFile.content = reader.result;
    myFile.loading = false;
    myFile.complete = true;

    field.form.$form.find('input[type=submit]').removeProp('disabled');

    field.context.disabled = false;
    field.context.unsaved = true;
    field.form.changed = true;
    
    if (field.$field.hasClass('autosave')) await saveFiles(e);
    // field.render(field.$field);
    // field.$field.trigger('render');

    
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
    field.context.files.push(data);
    field.render(field.$field);
    // field.$field.trigger('render');

    reader.readAsDataURL(file);
    reader.onloadend = event => field.$field.trigger('completeUpload', { file, reader, event })
    reader.onprogress = event => field.$field.trigger('uploadProgress', { file, reader, event })
}
export const fileChange = e => {
    const field = $(e.target).getField();
    const form = $(e.target).getForm();
    e.preventDefault();
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
            if (rules) {
                const extensions = rules.split(',');
                const ext = file.name.split('.').pop();
                // console.log('file ext', ext, 'available', extensions, 'index', extensions.indexOf(ext))
                if (extensions.indexOf(ext) === -1) field.context.invalidExtension = true;
            }
            if (!field.context.files) field.context.files = [];
            field.$field.trigger('startUpload', { file })
        });
    }
}
export const deleteFile = async (e) => {
    e.preventDefault();
    const field = $(e.target).getField();
    const { files, route } = field.context;
    const $li = $(e.target).closest('[data-id]');
    const id = $li.data('id');
    if (id) {
        const file = files.find(f => f.id == id);
        // if( !file ) return;
        d(id);
        d(field);
        console.log(document.cookie);
        console.log(window.apiNonce);
        // console.log(JSON.stringify(file));
        // renderDialog('Are you sure?', 'This file will be permanently deleted!', e => {
        field.addLoading('text-danger spinner-border');
        let deleted = true;
        if( id && route ) {
            const r = await axios.delete(route + '/' + id)
                .catch(e => false );
            if( r ) { 
                const {data} = parseJSON(r.data);
                d('response:',data,r);
                deleted = data.status == 'success';
            } else deleted =false;
        }
        if (deleted) {
            const i = files.findIndex(f => f.id == id);
            files.splice(i, 1);
            field.context.files = files;
            field.removeLoading();
            $li.remove();
        } else {
            field.removeLoading();
        }
    }
}
export const resetFiles = e => {
    e.preventDefault();
    const field = $(e.target).getField();
    const { files } = field.context;
    if (!files) return field.context.files = [];
    files.filter(f => {
        if (!f.uploaded) $(`li[data-id="${f.id}"`).remove();
        return f.uploaded
    });
    field.render(field.$field);
}
export const saveFiles = async (e,field) => {
    // e.preventDefault();
    if(!field ) field = $(e.target).getField();
    let formData = null;
    // d('field',field,$(e.target)[0]);
    let { files, route, mode } = field.context;
    d('route:', route,'mode',mode);
    if (!field.processed) field.processed = [];
    if( mode == 'json' ) {
        formData = {
            files: field.value,
            user_id: $('input[name=user_id').val()
        }
    } else {
        formData = new FormData();
        let filecount = 0;
        files.forEach( (file,i) => {
            // d('src:',file.src);
            if( field.processed.find(p => file.id == p ) || !file.src ) return;
            formData.append( 'files['+field.name+']['+i+']', file.src );
            filecount++;
            field.processed.push(file.id);
        });
        formData.append('user_id',$('input[name=user_id').val());
        d('-- setup form')
    }
    d('data:', JSON.stringify(formData));
    d('saving files');
    // field.$field.trigger('render');
    
    const headers = mode == 'json' ? { 'Content-Type': 'text/json' } : { 'Content-Type': 'multipart/form-data' }
// d(files);
    

    const res = await axios.post(route, formData, headers )
    // const res = await axios.post(route, [files] )
        .catch(e => false);
        
    if (res.data) {
        if (res.data.status == 'success') {
            field.form.changed = false;
            // d('update', res.data.files, res.data, res);
            field.context.files = res.data.files;
            // field.render(field.$field);
            // d('saved files:',res.data);

            // field.$field.trigger('complete');
            field.render(field.$field);
            field.$field.trigger('render');
            field.$field.find('.filelist').eq(1).remove();
            field.$field.find('input[type=file]').eq(1).remove();
            window.field = field;
        }
    }
}
export const saveTag = e => {
    const field = $(e.target).getField();
    const { files } = field.context;

    const name = $(e.target).attr('name');
    const id = $(e.target).data('id');
    const value = $(e.target).val();

    const file = files.find(f => {
        f[name] == undefined;
        return f.id == id
    });
    // d('file', file, name, value);
    if (file) file[name] = true;
    // d(file, files);
}