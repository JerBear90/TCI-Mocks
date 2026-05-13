<?php
define( 'WAFBASE', TCIDIR.'/lib/waf/plugin.php' );

define( 'WAFPATH', dirname(WAFBASE) );
define( 'WAFURL', str_replace( ABSPATH, site_url().'/', WAFPATH ) );

define( 'WAFNAMESPACE', 'waf/v1' );
// Form classes (Form,Field,Fieldset,Input)

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


// User Config
$wpd = wp_upload_dir();
$user_config = $wpd['basedir'].'/waf/config.php';
if( file_exists( $user_config ) ) include $user_config;

// Forms source
include 'src/php/index.php';

// WP Additions
include 'wp/inc/index.php';
include 'wp/rest/index.php';
include 'wp/filters/index.php';

// Extras
include 'extras/index.php';
