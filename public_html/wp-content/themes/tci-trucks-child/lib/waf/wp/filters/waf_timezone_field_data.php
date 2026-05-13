<?php
function waf_timezone_field_data( $data ) {
    for( $i=-12;$i<=14;$i+=0.5) {
        if( $i >= 0 ) $i = '+'.$i;
        // $key = "UTC$i";
        $value = "UTC $i";;
        $options[$i] = $value;
    }
    // d($options);
    $data['type'] = 'select';
    $data['options'] = $options;
    return $data;
}

add_filter( 'waf_timezone_field_data', 'waf_timezone_field_data' );