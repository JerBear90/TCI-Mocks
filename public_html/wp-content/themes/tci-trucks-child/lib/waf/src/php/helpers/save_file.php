<?php
function waf_save_file( $file, $filepath='' ){
    
    // Return if file does not exist
    // d('saving file?');
    if( @$file['path'] && !$file['tmp_name'] ) $file['tmp_name'] = $file['path'];
    if( is_string(@$file['tmp_name']) ) if ( ! file_exists( $file['tmp_name'] ) ) {
        d('-- cannot find temp file');
        return 'none';
    }

    // Require wordpress file toosl
    require_once( ABSPATH . "wp-admin/includes/file.php" );

    // Upload dir
    $upload_dir = wp_upload_dir();

    // Greate unique wordpress filename
    $dir = $upload_dir['path'];

    $filename = wp_unique_filename($dir, $file['name']);
    // d('created filename:',$dir,$file['name'],$filename);
    // Get file path
    if( !$filepath ) $filepath = $dir.'/'.$filename;

    // Get file url
    $guid = $upload_dir['url'].'/'.$filename;

    // Move the file
    
    if( @$file['content' ]) {
        
        list($type,$data) = explode(';', $file['content']);
        list(,$data) = explode(',', $file['content']);
        $filedata = base64_decode( $data );
        $file_type = str_replace( 'data:','',$type);

        touch($filepath);
        
        file_put_contents( $filepath, $filedata );
        return $filepath;
    } elseif( $file['tmp_name'] ) {
        
        $renamed = copy( $file['tmp_name'], $filepath );
        // d('file',$file['tmp_name'],$filepath,$renamed);
        if( file_exists($filepath) ) return $filepath;
    }
    return false;
}