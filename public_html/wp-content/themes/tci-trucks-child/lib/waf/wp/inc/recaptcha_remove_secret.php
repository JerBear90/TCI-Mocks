<?php
function recaptcha_remove_secret( $data ) {
    foreach( $data as $f=>$field ) {
        if( !is_array($field) ) continue;
        if( $field['type'] == 'recaptcha2' || $field['type'] == 'recaptcha3' ) {
            unset( $field['secret_key'] );
            $data[$f] = $field;
        }
    }
    return $data;
}
add_filter( 'waf_formConfig_data', 'recaptcha_remove_secret' );