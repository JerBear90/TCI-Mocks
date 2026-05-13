<?php
function json_parser_init() {
    
    // if( is_admin() ) return;
    $data = file_get_contents( "php://input" );
    $data = json_decode( $data, 1 );
    
    // die;
    $_REQUEST = array_merge( (array)$_REQUEST, (array)$data );
    $_POST = array_merge( (array)$_POST, (array)$data );
    
    foreach( $_REQUEST as $key=>$val ) {
        $array = '';
        if( !is_array($val) ) {
            $val = stripslashes($val);
            $array = json_decode( $val, 1 );
        }
        if( is_array($array) ) $_REQUEST[$key] = $_POST[$key] = $array;
    }
}
add_action( 'rest_api_init', 'json_parser_init', 1 );
// add_action( 'wp', 'json_parser_init', 1 );
// add_action( 'init', 'json_parser_init', 5 );