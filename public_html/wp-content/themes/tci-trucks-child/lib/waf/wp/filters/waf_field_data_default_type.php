<?php
function waf_field_data_default_type( $datum ) {
    if( !@$datum['type'] ) $datum['type'] = 'text';    
    return $datum;
}
add_filter( 'waf_field_data', 'waf_field_data_default_type');