<?php
function add_random_field_value( $data ) {
    $data['random'] = rand(0,1000);
    // d($data);
    return $data;
}
add_filter( 'waf_checkbox_field_data', 'add_random_field_value' );
add_filter( 'waf_checkboxes_field_data', 'add_random_field_value' );
add_filter( 'waf_radios_field_data', 'add_random_field_value' );