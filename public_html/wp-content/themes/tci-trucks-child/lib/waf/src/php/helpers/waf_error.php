<?php
function waf_error( $message, $redirect='' ) {
    $str = g($message.'-message') ? g($message.'-message') : $message;
    $data = [
        'message' => $str,
        'status' => 'danger',
        'url' => $redirect
    ];
    d($data);
    wp_send_json($data);
    die;
}