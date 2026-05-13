const $ = require('../../ui/jquery');
const { startUpload, fileChange, uploadProgress, completeUpload, deleteFile } = require('./ui');
const fieldSel = '.form-group.image';

$.fn.imageFieldUploader = function () {fileChange
    // d('found uploader:',$(fieldSel));
    
    $(document).on('startUpload', fieldSel, startUpload);
    $(document).on('uploadProgress', fieldSel, uploadProgress);
    $(document).on('completeUpload', fieldSel, completeUpload);


    $(document).on('click', fieldSel + ' .remove', deleteFile);
    $(document).on('change', fieldSel + ' :input', fileChange);
}
$(document).ready(function ($) {
    $(document).imageFieldUploader();
});