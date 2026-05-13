const $ = require('../../ui/jquery');
const { saveTag, fileChange, startUpload, updateFile, uploadProgress, completeUpload, deleteFile, resetFiles, saveFiles } = require('./ui');
const { tusChange } = require('./tus');
const fieldSel = '.form-group.file';

$.fn.fileUploader = function () {
    // d('found uploader:',$(fieldSel));
    $(document).on('change', fieldSel + '.tus input[type=file]', tusChange);

    $(document).on('startUpload', fieldSel + ':not(.tus)', startUpload);
    // $(document).on('uploadProgress', fieldSel + ':not(.tus)', uploadProgress);
    // $(document).on('completeUpload', fieldSel + ':not(.tus)', completeUpload);


    // $(document).on('click', fieldSel + ' .filelist li .delete', deleteFile);
    // $(document).on('change', fieldSel + ' .filelist li :input', updateFile);

    // $(document).on('click', fieldSel + ' .reset-uploader', resetFiles);
    // $(document).on('click', fieldSel + ' .save-uploader', saveFiles);
    // $(document).on('click', fieldSel + '.tus .save-uploader', tusChange);
    // $(document).on('change', fieldSel + ' input.tag', saveTag);
}
$(document).ready(function ($) {
    $(document).fileUploader();
});