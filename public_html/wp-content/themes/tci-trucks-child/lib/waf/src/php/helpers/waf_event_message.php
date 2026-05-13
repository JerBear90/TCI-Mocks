<?php
function waf_event_message( $post_id, $status, $message, $data=[] ) {
    global $currentMessage;
    @session_start();
    
    if( !@$_SESSION['messages'] ) $_SESSION['messages'] = [];
    // d('-- send message ',$message );
    $message = [
        'id' => uniqid(),
        'post_id' => $post_id,
        'status' => $status,
        'action' => @$_REQUEST['action'] ? $_REQUEST['action'] : @$data['action'],
        'message' =>  __( $message, 'woo-pusher' ),
        'data' => $data
    ];
    $_SESSION['messages'][] = $message;
    $currentMessage = $message;
    session_write_close();
    // set_transient( 'message-'.$user_id, )
}
// add_action( 'wp_ajax_waf-event', 'waf_event_message' );