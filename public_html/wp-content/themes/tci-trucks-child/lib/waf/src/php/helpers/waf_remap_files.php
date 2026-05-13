<?php
function waf_remap_files( $input='' ) {
    // d($input);
    if( !$input ) $input = $_FILES;
    $files = [];
    foreach( $input as $f=>$keys ) {
        foreach( $keys as $key=>$values ) {
            if( !is_array($values) ) return $input;
            foreach( $values as $i=>$value ) {
                if( !$files[$i] ) $files[$i] = [];
                // if( !$files[$f][$i] ) $files[$f][$i] = [];
                $files[$i][$key] = $value;
            }
        }
    }
    return $files;
}