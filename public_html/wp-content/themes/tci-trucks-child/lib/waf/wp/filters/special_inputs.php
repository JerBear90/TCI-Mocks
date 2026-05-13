<?php
function special_inputs_data( $data, $form ){
    // d('process fields',$data);
    if( !$form ) $form = $data;
    if( is_array($data) ) foreach( $data as $d=>$datum ) {
        if( !is_array($datum) || $d == 'args' ) continue;
        if( @$datum['type'] == 'fieldset' || @$datum['type'] == 'duplicator' ) {
            // d($datum);
            $datum['fields'] = special_inputs_data( @$datum['fields'], $data );
        }
        // d("FILTER",'waf_'.@$datum['type'].'_field_data');
        if( !@$datum['name'] ) $datum['name'] = $d;
        $datum = apply_filters( 'waf_'.@$datum['type'].'_field_data', $datum, $form );
        if( @$datum ) $datum = apply_filters( 'waf_field_data', $datum, $data );
        
        if( @$datum ) $data[$d] = $datum;
        else unset($data[$d]);
    }
    return $data;
}
add_filter( 'waf_form_data','special_inputs_data', 100, 2 );