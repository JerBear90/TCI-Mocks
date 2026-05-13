const initFile = () => {
    $(document).on( 'change', 'input[type=file]', function(evt) {
        $f = $(this).closest('.form-group');
        $f.removeClass('invalid');
        $f.find('.invalid').removeClass('invalid');

        // Remove the "clear file" input
        $f.find('input.clear').remove();
        var files = evt.target.files; // FileList object

        // files is a FileList of File objects. List some properties.
        var html = '';
        //$f.find('ul.filelist').remove();
        $f.find('ul.filelist').html('');

        for (var i = 0, f; f = files[i]; i++) {
            html +=
                '<li id="file-'+i+'" class="list-group-item p-1 file-preview">'
                    +'<strong>' + f.name + '</strong>'
                    +' (' + ( f.type || 'n/a' ) + ') - '
                    + bytesToSize( f.size )
                    +'<a class="oi oi-circle-x close rounded-circle text-danger" data-dismiss="alert">'
                        +''
                    +'</a>'
                + '</li>';
            html = '<ul class="filelist list-group">' + html + '</ul>';
            $f.find('ul.filelist').html( html );

            var reader = new FileReader();

            reader.readAsDataURL( f );
            reader.onprogress = (function($f) {
                return function(evt) {
                    // evt is an ProgressEvent.
                    if (evt.lengthComputable) {
                        var percentLoaded = Math.round((evt.loaded / evt.total) * 100);
                        // Increase the progress bar length.
                        if (percentLoaded <= 100) {
                            var $bar = $('.progress-bar',$f);
                            $bar.attr('aria-valuenow',percentLoaded);
                            $bar.css('width',percentLoaded+'%');
                        }

                    }
                }
            });
            reader.onloadend = (function($f,file) {
                return function(e) {
                    var preview;

                    if( file.type.indexOf( 'image' ) === 0 ) preview = '<img src="'+e.target.result+'" class="img-preview">';
                    else if( file.type.indexOf( 'audio' ) === 0 )preview = '<audio controls class="w-100"><source src="'+e.target.result+'" type="'+file.type+'"></audio>';
                    var $name = $('.name',$f);
                    $name.prepend(preview);
                }
            })($f,f)

        }
    });

}
module.exports = initFile;