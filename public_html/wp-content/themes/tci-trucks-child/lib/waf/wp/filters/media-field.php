<?php
function waf_media_field_data( $data ) {
    @wp_enqueue_media();
    
    if( @is_string($datum['value']) ) {
        $datum['value'] = ['url'=>$datum['value']];
    }
    $data['preview'] = '<img src="'.@$data['value']['url'].'">';
    return $data;
}
add_filter( 'waf_media_field_data', 'waf_media_field_data' );