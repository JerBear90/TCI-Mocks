<?php
function waf_form_invalid_message_args( $args ) {
    $args['data']['invalid'] = @$args['messages']['invalid'];
    return $args;
}
add_filter( 'waf_form_args', 'waf_form_invalid_message_args' );