<?php
function save_file( $file, $absname='' ){
    // Return if file does not exist
    if( is_string($file['tmp_name'][0]) ) if ( ! file_exists( $file['tmp_name'][0] ) ) return 'none';

    // Require wordpress file toosl
    require_once( ABSPATH . "wp-admin/includes/file.php" );

    // Upload dir
    $upload_dir = wp_upload_dir();

    // Greate unique wordpress filename
    $dir = $upload_dir['basedir'].'/user-uploads';
    if( !$absname ) $absname = tempnam( '/tmp', 'waf' );

    if( $file['content' ]) {
        
        list($type,$data) = explode(';', $file['content']);
        list(,$data) = explode(',', $file['content']);
        $filedata = base64_decode( $data );
        $file_type = str_replace( 'data:','',$type);

        touch($absname);
        // d('file',$absname,$filedata);
        file_put_contents( $absname, $filedata );
        return $absname;
    }
    return false;
}