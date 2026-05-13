<?php
function waf_redirect_url( $data ) {
    global $formConfiging;
    // d('configin:',$formConfiging);
    // if( $formConfiging ) return $data;
    // dd($_REQUEST);
    if( @$_REQUEST['redirect_to'] ) {
        $data['redirect_to'] = ['type'=>'hidden','value'=>$_REQUEST['redirect_to'] ];
    } elseif( !@$data['redirect_to']['value'] ) {
        $data['redirect_to'] = [
            'type' => 'hidden',
            'value' => get_current_url()
        ];
    }
    // dd($data['redirect_to']);
    return $data;
}
add_filter( 'waf_login_form_data', 'waf_redirect_url' );
add_filter( 'waf_register_form_data', 'waf_redirect_url' );