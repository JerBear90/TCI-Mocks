<?php
function waf_verify_password( $user_id, $data ) {
    $user = get_userdata( $user_id );
    $password = $data['password'];
    $new_password = $data['new_password'];
    $confirm_password = $data['confirm_password'];
    
    if( $new_password || ( $data['user_email'] && $data['user_email'] != $user->data->user_email ) ) {
        if( !$password ) {
            $response = [
                'status' => 'error',
                'message' => __( 'You must provide your original password to update email or password.', 'waf' )
            ];
            wp_send_json($response);
            exit;
        }
        
        $hash = $user->data->user_pass;
        
        d($password,$hash);
        $verified = wp_check_password( $password, $hash, $user_id);
        if( !$verified ) {
            $response = [
                'status' => 'error',
                'message' => __( 'You original password is not correct.', 'waf' )
            ];
            wp_send_json($response);
            exit;
        }
    }
}