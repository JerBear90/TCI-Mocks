<?php
function change_password_rest_api() {
    register_rest_route( 'waf/v1', '/changePassword', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_change_pass_rest',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'change_password_rest_api' );
/**
 * Reset the user pass after validation
 *
 * @author Aman Saini
 * @since  1.0
 * @return  Success/Error Message
 */
function waf_change_pass_rest(){
    @session_start();
    $user_id = get_current_user_id();
    $user = get_userdata($user_id);
    // dd('user:',$user_id);
    extract( $_REQUEST );
    $check = wp_authenticate_username_password( NULL, $user->data->user_login, $current_password );
    // d('checked:',$check);
    if( is_wp_error($check) ) {
        $error = array_pop( $check->errors );
        $response = [
            "status" => "danger",
            "message" => $error[0]
        ];
        wp_send_json($response);
    }

    if( !$user_password || !$current_password ) return false;
    if( $user_password ) {
        if( $user_password != $user_password2 ) {
            wp_send_json( ['status'=>'danger','message' => __( 'Passwords do not match', 'waf' )]);
            die;
        }
    }
    session_write_close();
    
    do_action( 'password_updated', $user_id );
    $credentials = [
        'user_login' => $user->data->user_login,
        'user_password' => $user_password
    ];

    wp_set_password( $user_password, $user_id );
    $signon = wp_signon( $credentials );
    wp_send_json([
        'status' => 'success',
        'user_id' => $user_id,
        'message' => __( 'Your password has been updated', 'waf' ),
        'reload' => true
    ]);
}