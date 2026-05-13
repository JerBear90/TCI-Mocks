<?php
function rest_user_login() {
    register_rest_route( 'waf/v1', 'loginUser', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_login_user',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_user_login' );

function waf_login_user( $data=null ) {
    if( !$data ) $data = $_REQUEST;
    $form = new form('login');
    $form->verifyRecaptcha( );
    
    if( !$data['user_login'] || !$data['user_password'] ) {
        $text = g('login_failed_message') ? g('login_failed_message') : 'LoginError: %s';
        waf_error( sprintf( $text,'Username or Password missing') );
    }
    $credentials = [
        'user_login' => $data['user_login'],
        'user_password' => $data['user_password'],
        'remember' => $data['rememberme']
    ];
    http_response_code(200);
    $signon = wp_signon( $credentials, g('use_secure_cookie') );
    // d('signon:',$signon);
    // die;
    // d('hello world');   
    if( @$signon->errors ) {
        
        list($error) = $signon->errors;
        $messages = [];
        
        foreach( $signon->errors as $error ) $messages[] = $error[0];
        $message = implode( '<br/>', $messages );
        if( !$message ) $message = 'Login Failed';
        $text = g('login_failed_message') ? g('login_failed_message') : 'LoginError: %s';
        
        $response = [
            'status' => 'danger',
            'message' => sprintf( $text,$message)
        ];
        wp_send_json( $response );
        die;
    } else if( $signon->ID ) {
        $page = g('myaccount_page') ? g('myaccount_page') : get_option('page_on_front');
        $page = apply_filters( 'waf_login_page', g('myaccount_page') );
        if( !$page ) $page = get_option( 'page_on_front' );
        
        $referer = $_SERVER['HTTP_REFERER'];
        if( $referer ) {
            $query_string = explode( '?', $referer )[1];
            $args = wp_parse_args( $query_string );
            if( $args['redirect_to'] ) $url = $args['redirect_to'];
        }

        $redirect_to = $data['redirect_to'] ? $data['redirect_to'] : $redirect_to = get_permalink ( $page );
        // d($data['redirect_to']);
        $response = [
            'status' => 'success',
            'message' => g('loggedin_message') ? g('loggedin_message') : 'Logged in',
            'url' => apply_filters( 'waf_login_url', $redirect_to ),
        ];
        
        wp_send_json( $response );
        die;
    } else {
        wp_send_json( [
            'status' => 'danger',
            'message' => g('unknown_error')
        ]);
    }
    
}