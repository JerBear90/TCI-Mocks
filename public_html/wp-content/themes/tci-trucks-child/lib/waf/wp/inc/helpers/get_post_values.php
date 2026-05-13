<?php
function get_post_values( $data, $post_id ) {
    $post = get_post( $post_id );
    if( is_array($data) ) foreach( $data as $d=>$datum ) {
        if( $d == 'args' ) continue;
        $value = strpos( $d, 'post_' ) === 0 ? $post->$d : get_post_meta( $post_id, $d, true );
        // d('GET VALUE',$d,$value);
        if( $datum['type'] == 'fieldset' ) {
            $data[$d]['fields'] = get_post_values( $datum['fields'], $post_id );
        }
        else $data[$d]['value'] = $value;
    }
    // d($data);
    return $data;
}