<?php
function get_formdata( $slug ) {
    $data = [];
    $jsonPath = formTemplater::getJsonPath( $slug ); 
    if( !$jsonPath ) return [];
    $contents = file_get_contents( $jsonPath );
    if( $contents ) $data = json_decode( $contents, 1 );
    return $data;
}