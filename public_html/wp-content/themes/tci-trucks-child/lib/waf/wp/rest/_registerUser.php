<?php
function register_user_rest_api() {
    register_rest_route( 'waf/v1', '/registerUser', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_register_user',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'register_user_rest_api' );

function waf_register_user() {
    
    $data = [];
    // wp_create_user();
    // $wp_user = get_userdata( $user->user_id );
    $data = $_REQUEST;
    $form = new form('register');
    $form->verifyRecaptcha( );
    
    if( $data['user_login'] && $data['user_email'] && $data['user_password'] ) {
        
        if( $data['user_password'] != $data['user_password2'] ) {
            wp_send_json( ['status'=>'danger','message' => 'Passwords do not matchs']);
            die;
        }
        if( $data['display_name'] ) {
            list( $first, $last ) = explode( ' ', $data['display_name'] );
            if( !$data['first_name'] ) $data['first_name'] = $first;
            if( !$data['last_name'] ) $data['last_name'] = $last;
        } else {
            $data['display_name'] = $data['first_name'].' '.$data['last_name'];
        }
        $userdata = [
            'user_login' => $data['user_login'],
            'user_email' => $data['user_email'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'display_name' => $data['display_name'],
            'user_pass' => $data['user_password'],
            'role' => 'member',
        ];
        $user_id = wp_insert_user( $userdata );
        
        if( $user_id->errors ) {
            foreach( $user_id->errors as $e=>$error ) $errors .= implode( "<br>",$error );
            wp_send_json([
                'status' => 'danger',
                'message' => '<strong>Error: </strong>'.$errors
            ]);
        }
    } else {
        $message = g( 'missing_userdata_message' ) ? g( 'missing_userdata_message' ) : 'Missing user data';
        wp_send_json([
            'status' => 'danger',
            'message' => $message
        ]);
    }
    
    if( $user_id ) {
        $wp_user = get_userdata( $user_id );
        wp_set_auth_cookie( $user_id, true );
        wp_set_current_user( $user_id );
        do_action( 'wp_login', $wp_user->user_login, $wp_user );
        
        // $url = get_bloginfo('url');
        wp_send_json( [
            'status' => 'success',
            'message' => __( "Successfully registered!", 'waf' ),
            'url' => apply_filters( 'waf_register_url', '' ),
            'reload' => true
        ]);
        exit;
    }
}