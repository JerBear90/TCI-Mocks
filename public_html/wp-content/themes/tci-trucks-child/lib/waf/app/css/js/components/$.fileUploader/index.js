const $ = require('../../ui/jquery');
const { saveTag, fileChange, startUpload, uploadProgress, completeUpload, deleteFile, resetFiles, saveFiles } = require('./ui');
const { tusChange } = require('./tus');
const fieldSel = '.form-group.file';

$.fn.fileUploader = function () {
    $(document).on('change', fieldSel + ':not(.tus) input', fileChange);
    $(document).on('change', fieldSel + '.tus input', tusChange);

    $(document).on('startUpload', fieldSel, startUpload);
    $(document).on('uploadProgress', fieldSel, uploadProgress);
    $(document).on('completeUpload', fieldSel, completeUpload);


    $(document).on('click', fieldSel + ' .filelist li .delete', deleteFile);
    $(document).on('click', fieldSel + ' .reset-uploader', resetFiles);
    $(document).on('click', fieldSel + ' .save-uploader', saveFiles);
    // $(document).on('click', fieldSel + '.tus .save-uploader', tusChange);
    $(document).on('change', fieldSel + ' input.tag', saveTag);
}
$(document).ready(function ($) {
    $(document).fileUploader();
});