const $ = require('../../ui/jquery');
const {parseJSON} = require('../../utils/parseJSON');
const axios = require('../../services/axios');
export const uploadProgress = (e, { file, event }) => {
    d("upload progress", $(e.target)[0]);
    const field = $(e.target).getField();
    const percent = event.loaded / event.total * 100;
    field.percent = percent;
    // d('percent',percent);
    // d(file);
    const $percent = field.$field.find(`.filelist li[data-id=${file.id}] .progress-bar`);
    // d('percetn',$percent)
    $percent.width(percent + '%');
    if (percent <= 100) {
        field.context.disabled = true;
        const $submit = field.form.$form.find('input[type=submit]');
        if( $submit ) $submit.prop('disabled', 'disabled');
    } else {
        field.context.disabled = false;
        field.form.$form.find('input[type=submit]').removeProp('disabled');
    }
    // field.render(field.$field);
}
export const completeUpload = async(e, { file, reader }) => {
    // d('E', e.target, file, reader.conte)
    const field = $(e.target).getField();
    const { files } = field.context;

    const myFile = files.find(f => f.id == file.id)
    
    myFile.url = reader.result;
    myFile.loading = false;
    myFile.complete = true;
    

    field.form.$form.find('input[type=submit]').removeProp('disabled');

    field.context.disabled = false;
    field.context.unsaved = true;
    field.form.changed = true;
    
    if (field.$field.hasClass('autosave')) await saveFiles(e);
    const li = `li[data-id=${file.id}]`;
    const update = field.render();
    const $li = $(update).find(li).last();
    field.$f.find('.filelist '+li).replaceWith($li);

    
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
    d('files:',field.context.files);
    const html = field.render();
    const $files = $(html).find('.filelist');
    field.$field.find('.filelist').replaceWith($files);
    field.$field.trigger('render');

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
            if (!field.context.files) field.context.files = [];
            field.$field.trigger('startUpload', { file })
        });
    }
}
export const deleteFile = async (e) => {
    e.preventDefault();
    const field = $(e.target).getField();
    const { files, route } = field.context;
    // d('delete routr:',route);
    const $li = $(e.target).closest('[data-id]');
    const id = $li.data('id');
    // d("FILE-:",id.indexOf('file-'));
    // d('id:',id);
    if( id && route && !isNaN(id) ) {
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
                // d('response:',data,r);
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
    } else {
        const index = files.findIndex( f => f.id == id );
        // d('index:',index,'id:',id,"ids:",files.map(f=>f.id));
        if( index || index === 0 ) {
            field.context.files.splice(index,1);
            // d('files:',field.context.files);
            if( field.context.files.length == 0 ) field.context.files = [];
        }
        $li.remove();
    }
    field.$input.val('');
}

export const updateFile = async (e) => {
    e.preventDefault();
    
    const {id} = $(e.target).closest('li').data();
    const field = $(e.target).getField();
    const value = $(e.target).val();
    const name = $(e.target).attr('name');
    const route = field.context.route;
    
    if( field ) field.addLoading('text-danger spinner-border');
    // d('id:',id,'route:',route);    
    if (id && route) {
        const r = await axios.put(route + '/' + id, {name,value} )
            .catch(e => false);
        if (r) {
            const { data } = parseJSON(r.data);
            // d('response:',data,r);
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
export const saveFiles = async (e,field,upload) => {
    e.preventDefault();
    e.stopPropagation();
    if(!field ) field = $(e.target).getField();
    let formData = null;
    // d('field',field,$(e.target)[0]);
    let { files, route, mode } = field.context;
    // d('route:', route,'mode',mode);
    const $form = $(e.target).closest('form');
    if (!field.processed) field.processed = [];

    formData = new FormData();
    let filecount = 0;
    
    upload.file.url = upload.url;
    
    formData.append( field.name+'[0][url]', upload.url );
    formData.append( field.name+'[0][name]', upload.file.name );
    formData.append( field.name+'[0][type]', upload.file.type );
    formData.append( field.name+'[0][size]', upload.file.size );
    formData.append( field.name+'[0][error]', upload.file.error );
    formData.append('user_id',$('input[name=user_id').val());
    $form.find( ':input, textarea' ).each( function() {
        if( $(this).attr('type') == 'file' ) return;
        const name = $(this).attr('name');
        const value = $(this).val();
        d('name:',name);
        formData.append( name, value );
    })
    Object.entries($form.data()).forEach( ([name,value]) => {
        d('ddd name:',name); 
        formData.append(name,value);
    })
    console.log('upload',upload);
    // d('data:', JSON.stringify(formData));
    // d('saving files');
    // field.$field.trigger('render');
    
    const headers = mode == 'json' ? { 'Content-Type': 'text/json' } : { 'Content-Type': 'multipart/form-data' }
// d(files);
    
    d('save files',route);
    const res = await axios.post(route, formData, headers )
    // const res = await axios.post(route, [files] )
        .catch(e => false);
    // d('RES:', res);
    
    if (res.data) {
        
        if (res.data.status == 'success') {
            field.form.changed = false;
            // d('update', res.data.files, res.data, res);
            const updates = []
            d('succes!',res.data);
            $form.processResponse(res.data);
        }
    }
}
export const saveTag = e => {
    e.preventDefault();
    e.stopPropagation();
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