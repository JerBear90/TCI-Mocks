<?php
function waf_process_user_data( &$data, &$userdata, &$meta ) {
    unset( $data['password'] );
    unset( $data['new_password'] );
    unset( $data['confirm_password'] );
    foreach( $data as $k=>$value ) {
        if( strpos( $k, 'meta_' ) === 0 ) {
            $key = str_replace( 'meta_', '', $k );
            $meta[$key] = $value;
        } elseif( strpos( $k, 'user_') === 0 ) {
            $postdata[$k] = $value;
        } elseif( $k != 'files' ) {
            $meta[$k] = $value;
        }
    }
}