<?php
function waf_rest_init_events() {
    register_rest_route( 'waf/v1', 'events', array(
        array(
            'methods'             => 'GET',
            'callback'            => 'waf_events',
            'permission_callback' => 'waf_permissions_check',
        )
    ));
}
add_action( 'rest_api_init', 'waf_rest_init_events' );

function waf_events() {
    extract( $_REQUEST );
    @session_start();
    $GLOBALS['noDebug'] = 1;
    header( 'Content-type:text/event-stream');
    header('Cache-Control: no-cache');
    // // d('session',$_SESSION);
    // $_SESSION['messages'] = [
    //     [
    //         'id' => 123,
    //         'action' => 'session-check',
    //         'message' => @session_id(),
    //     ]
    // ];
    if( is_array( @$_SESSION['messages'] ) ) 
        foreach( @$_SESSION['messages'] as $d=>$data ) {
            
            if( $data['message'] ) {
                echo 'id: '.$data['id']."\n";
                echo 'data: '.json_encode($data)."\n";
                echo 'event: '.$data['action']."\n\n";
            }
            unset($_SESSION['messages'][$d]);
        }
        session_write_close();
    exit;
}