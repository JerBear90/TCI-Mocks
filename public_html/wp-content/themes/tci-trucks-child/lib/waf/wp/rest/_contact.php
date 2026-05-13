<?php
function waf_rest_init_contact() {
    register_rest_route( 'waf/v1', 'contact', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_rest_contact',
            'permission_callback' => '__return_true',
        )
    ));
}
add_action( 'rest_api_init', 'waf_rest_init_contact' );

function waf_rest_contact() {
    extract( $_REQUEST );
    @session_start();
    // $GLOBALS['noDebug'] = 1;
    // d($_POST);
    
    $sent = emt_send_action_email([
        'to' => @$to,
        'user_id' => @$user_id,
        'action' => 'contact'
    ]);

    if( @$_REQUEST['copy'] ) {
       $sent = emt_send_action_email([
            'to' => @$email,
            'action' => 'contact'
        ]); 
    }
    $form = get_formdata('contact');
    // d($form);
    $messages = $form['args']['messages'];
    if( $sent ) {
        $response = [
            'status' => 'success',
            'message' => @$messages['success'] ? @$messages['success'] : __( "Sent Email", 'jasper' )
        ];
    } else {
        $response = [
            'status' => 'error',
            'message' => @$messages['error']? @$messages['error'] : __( "Failed to send Email", 'jasper' )
        ];
    }
    session_write_close();
    wp_send_json($response);
    exit;
}