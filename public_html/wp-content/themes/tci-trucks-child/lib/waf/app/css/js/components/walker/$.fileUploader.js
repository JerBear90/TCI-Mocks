const $ = jQuery.noConflict();
const fieldSel = '.form-group.file';
const isAdvancedUpload = false;
$.fn.fileUploader = function () {
	// console.log("INITIALIZE FILE UPLOADER");
	let fcount = 0;
	const $form = $(this);

	// ajaxData = new FormData($form.get(0));
	// $form.data('ajaxData',ajaxData);

	// console.log('file input: ',$input);
	const $filelist = $('<ul class="filelist list-group p-0 mb-2"><li></li></ul>');
	const $buttons = $(
		`<div class="btn-group">
			<button class="btn btn-danger reset-uploader">Reset</button>
			<button class="btn btn-success save-file">Save</button>
		</div>`
	);



	const $fields = $form.find(fieldSel);
	// d('fields',$fields);
	if ($fields.length === 0) return;
	const $inputs = $form.find(fieldSel + ' :input');
	$fields.each(function () {
		$(this).data('files', []);
		const $list = $filelist.clone();
		$(this).append($list);
		$(this).append($buttons.clone());
		try {
			const fieldId = $(this).attr('class').match(/.*field-([^ ]*)/)[1];
			// d('fieldId',fieldId);
			if (currentFormValues) {
				const { files } = currentFormValues;
				// d('current',currentFormValues)
				if (files) if (files.length) {
					for (var f of files) {
						// console.log('file',f);
						if (f.field == fieldId) {
							// d('matches', fieldId )
							// showExisting(f,$list);	
						}
					}
				}
			}
		} catch (e) {
			console.log(e.message);
		}
	})

	showFile = function (e, f, fieldId) {
		const $field = $(e.target).closest(fieldSel);
		const $filelist = $field.find('.filelist');
		var $li = $('<li data-id="' + fieldId + '" class="list-group-item file-preview d-flex align-items-start flex-wrap">'
			+ '<div class="name col">'
			+ '<i class="status loading"></i>'
			+ '<strong>' + f.name + '</strong>'
			+ ' (' + (f.type || 'n/a') + ') - '
			+ bytesToSize(f.size)
			+ '</div>'
			+ '<a class="btn btn-sm btn-danger delete p-0 m-0">'
			+ '<i class="fa fa-close" data-index="' + fieldId + '"></i>'
			+ '</a>'
			+ '<div class="progress w-100 mt-3">'
			+ '<div class="progress-bar"></div>'
			+ '</div>'
			+ '</li>');
		$li.data('file', f);
		console.log($filelist[0], $li[0])
		$filelist.append($li);
		$field.find('.save-file').text('Save')
		$field.addClass('unsaved');
		var reader = new FileReader();

		reader.readAsDataURL(f);
		reader.onloadend = function () {
			$li.find('.status').removeClass('loading').addClass('fa fa-check');
			$li.find('.progress').remove();
			$form.find('input[type=submit]').prop('disabled', false);
			// console.log('uploaded file',f);
			const content = reader.result;
			// d('file',f);
			const field = $(e.target).getField();
			const $field = field.$field;
			const files = $field.data('files');
			// d('field',$field[0]);
			const fieldname = field.name;
			// d('field name',field,files);
			files.push({
				lastModified: f.lastModified,
				lastModifiedDate: f.lastModifiedDate,
				name: f.name,
				size: f.size,
				type: f.type,
				id: fieldId,
				field: fieldname,
				content
			});
			$field.find('input[type=file]').val('');
			// submitHandler( $(e.target)[0], false );
			// console.log('end',$form.find('input[type=submit]'));
		}
		reader.onprogress = (function ($current) {
			return function (e) {

				var percent = e.loaded / e.total * 100;
				// console.log('progress',percent);
				$current.find('.progress-bar').css('width', percent + '%');
				if (percent < 100) {
					$form.find('input[type=submit]').prop('disabled', 'disabled');
				}
			}
		})($li);
	}

	$inputs.on('change', function (e) {
		droppedFiles = e.target.files; // the files that were dropped
		const $input = $(this);
		// console.log(e);
		// console.log(droppedFiles);

		if (droppedFiles) {
			$.each(droppedFiles, function (i, file) {
				var name = $(this).attr('name').replace('[]', '');
				name += Math.random() * 100;
				// ajaxData.append( name, file );

				const attr = $input.data('rule-extension');
				if (attr) {
					const extensions = attr.split(',');
					const ext = file.name.split('.').pop();
					console.log('file ext', ext, 'available', extensions, 'index', extensions.indexOf(ext))
					if (extensions.indexOf(ext) === -1) {
						$input.after('<div class="alert alert-danger">Invalid file type</div>');
						$input.val('');
						return
					}
				}
				showFile(e, file, name);
				d('#input', $input[0]);
				$input.val('');
				fcount++;
				// console.log('append file',name,file);
			});
		}
	});

	$(document).on('click', '.filelist .delete', function (e) {
		// console.log('delete it');
		e.preventDefault();
		const $fofm = $(this).closest('form');
		const $field = $(this).closest(fieldSel);
		const files = $field.data('files');
		var $li = $(this).closest('li');
		const existing = $li.data('existing');
		if (existing) {
			const file = $li.data('file');
			d('delete existing file', file);
			const id = getFormId($form);
			console.log(document.cookie);
			console.log(window.apiNonce);
			console.log(JSON.stringify(file));
			renderDialog('Are you sure?', 'This file will be permanently deleted!', function () {
				formLoading($li);
				wp.deleteFile().delete(file)
					.then(response => {
						if (response.status == 'success') $li.remove();
					}).catch(e => {
						formError(e, $li);
					});
			});
		} else {
			var id = $li.data('id');
			$('[data-id="' + id + '"]').remove();
			// console.log('remove',id);
			const fileIndex = files.findIndex(f => f.id === id);
			d(fileIndex, files[fileIndex]);
			files.splice(fileIndex, 1);
		}
	})
	// Check for advanced upload support & add css class
	if (isAdvancedUpload) {
		$box.addClass('has-advanced-upload');

		var droppedFiles = false;

		$(this).on('drag dragstart dragend dragover dragenter dragleave drop', function (e) {
			e.preventDefault();
			e.stopPropagation();
		})
			.on('dragover dragenter', function () {
				$box.addClass('is-dragover');
			})
			.on('dragleave dragend drop', function () {
				$box.removeClass('is-dragover');
			})
			.on('drop', function (e) {
				droppedFiles = e.originalEvent.dataTransfer.files; // the files that were dropped
				// console.log(droppedFiles);
				// showFiles( droppedFiles );
				if (droppedFiles) {
					$.each(droppedFiles, function (i, file) {
						// console.log('append file');
						var name = $input.attr('name').replace('[]', '');
						name += Math.random() * 100;
						// ajaxData.append( name, file );
						showFile(file);
						fcount++;
					});
				}
			});
	} else {
		var iframeName = 'uploadiframe' + new Date().getTime();
		$iframe = $('<iframe name="' + iframeName + '" style="display: none;"></iframe>');

		$('body').append($iframe);
		$form.attr('target', iframeName);

		$iframe.one('load', function () {
			var data = JSON.parse($iframe.contents().find('body').text());
			$form
				.removeClass('is-uploading')
				.addClass(data.success == true ? 'is-success' : 'is-error')
				.removeAttr('target');
			if (!data.success) $errorMsg.text(data.error);
			$form.removeAttr('target');
			$iframe.remove();
		});
	}
}

function bytesToSize(bytes) {
	var sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
	if (bytes == 0) return '0 Byte';
	var i = parseInt(Math.floor(Math.log(bytes) / Math.log(1024)));
	return Math.round(bytes / Math.pow(1024, i), 2) + ' ' + sizes[i];
};
