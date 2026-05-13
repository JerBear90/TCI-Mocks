<?php
function waf_success( $message, $redirect='' ) {
    $str = g($message.'-message') ? g($message.'-message') : $message;
    wp_send_json([
        'message' => $str,
        'status' => 'success',
        'url' => $redirect
    ]);
    die;
}