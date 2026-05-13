<?php
function waf_path_to_name( $path ) {
    $parts = explode('.', $path );
    // d($path,$parts);
    $first = array_shift($parts);
    if( count($parts) == 0 ) return $first;
    $name = $first.'['.implode( '][', $parts ).']';
    // d('name:',$name);
    return $name;
}