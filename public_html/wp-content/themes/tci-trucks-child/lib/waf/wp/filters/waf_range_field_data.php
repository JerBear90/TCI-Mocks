<?php
function waf_range_field_data( $data ) {
    $data['valueStr'] = @$data['value'] ? $data['value'] : $data['minLabel'];
    // d($data['value']);
    if( $data['value'] ) $data['valueStr'] = $data['value'];
    if( !$data['value'] ) $data['value'] = "0".$data['value'];
    
    return $data;
}
add_filter( 'waf_range_field_data', 'waf_range_field_data' );