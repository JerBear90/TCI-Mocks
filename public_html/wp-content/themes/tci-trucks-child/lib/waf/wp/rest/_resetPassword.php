<?php
function reset_password_rest_api() {
    register_rest_route( 'waf/v1', '/resetPassword', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_reset_pass_rest',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'reset_password_rest_api' );
/**
 * Reset the user pass after validation
 *
 * @author Aman Saini
 * @since  1.0
 * @return  Success/Error Message
 */
function waf_reset_pass_rest(){
    
    extract( $_REQUEST );
    $user = get_user_by( 'login', $login );
    $user_id = $user->ID;
    
    $check = check_password_reset_key( $key, $login );
    // $check = is_devel();
    // d('checked:',$check);
    // d($_REQUEST);
    if( !$login ) {
        $error = array_pop( $check->errors );
        $response = [
            "status" => "danger",
            "message" => 'No user login provided'
        ];
        wp_send_json($response);
    }
    if( is_wp_error($check) || !$check ) {
        $error = array_pop( $check->errors );
        $response = [
            "status" => "danger",
            "message" => $error[0]
        ];
        wp_send_json($response);
    }

    if( !$pass1 || !$pass2 ) {
        wp_send_json( ['status'=>'danger','message' => __( 'Please Fill in all fields', 'waf' )]);
        return false;
    }
    if( $pass1 ) {
        if( $pass1 != $pass2 ) {
            wp_send_json( ['status'=>'danger','message' => __( 'Passwords do not match', 'waf' )]);
            die;
        }
    }
    
    
    do_action( 'password_updated', $user_id );
    $credentials = [
        'user_login' => $login,
        'user_password' => $pass1
    ];

    $set = wp_set_password( $pass1, $user_id );
    $signon = wp_signon( $credentials );
    
    $url = add_query_arg( 'loggedin', time(), site_url() );
    $response = [
        'status' => 'success',
        'user_id' => $user_id,
        'message' => __( 'Your password has been updated', 'waf' ),
        'url' => $url
        // 'reload' => true
    ];
    wp_send_json($response);
}