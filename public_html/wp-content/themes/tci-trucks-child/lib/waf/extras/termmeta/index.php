<?php
if( !function_exists('include_files') ) {
    function include_files( $dir ) {
        $files = scandir( $dir );
        
        foreach( $files as $file ) {
            if( $file == '.' || $file == '..' || $file == 'index.php' || $file == 'debug.php' ) continue;
            if( $file[0] == '_' ) continue;
            $ext = pathinfo( $file, PATHINFO_EXTENSION );
            
            $filename = $dir.'/'.$file;
            if( is_dir( $filename) ) include_files( $filename );
            else if( $ext == 'php' ) include $filename;
        }
    }
}
include_files( dirname(__FILE__) );