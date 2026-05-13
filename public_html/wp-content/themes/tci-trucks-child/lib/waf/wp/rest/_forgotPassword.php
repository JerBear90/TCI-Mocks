<?php
function forgot_password_rest_api() {
    register_rest_route( 'waf/v1', '/forgotPassword', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_forgot_pass_rest',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'forgot_password_rest_api' );
/**
 * Reset the user pass after validation
 *
 * @author Aman Saini
 * @since  1.0
 * @return  Success/Error Message
 */
function waf_forgot_pass_rest(){
    $user_login = $_REQUEST['user_login'];
    $user = get_user_by( 'login', $user_login );
    if( !$user ) $user = get_user_by_email( $user_login );
    
    if( !$user ) {
        wp_send_json([
            'status' => 'danger',
            'message' => sprintf( __( 'Cannot find user %s', 'r9tv' ), $user_login )    
        ]);
    }
    $user_login = $user->user_login;
    $key = get_password_reset_key( $user );
    // d($key);
    if( is_wp_error($key) ) {
        $error = array_shift( $key->errors );
        wp_send_json([
            'status' => 'error',
            'message' => $error[0]
        ]);
    }
    if( function_exists('emt_send_action_email') ) {
        if( g('reset-password-page') )  $url = get_permalink( get_option('reset-password-page') );
        else $url = network_site_url( 'wp-login.php', 'login' );
        $url = add_query_arg( ['action' => 'rp','key'=>$key,'login'=>$user->user_login], $url );
        $vars = [
            'key' => $key,
            'password_url' => $url
        ];
        $result = emt_send_action_email( [
            'user_id' => $user->ID,
            'action' => 'forgot-password'
        ],$vars
        );
 d('email vars:',$vars);
        echo $url;
        if( $result ) 
        wp_send_json([
            'status' => 'success',
            'message' => __( 'Please check your email for your password reset link', 'r9tv' )
        ]);
    }

    wp_send_json([
        'status' => 'danger',
        'message' => __( 'Error sending password reset email', 'r9tv' )
    ]);
}