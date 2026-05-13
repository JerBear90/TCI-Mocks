<?php
function rest_posts_delete() {
    register_rest_route( 'waf/v1', 'posts/(?P<id>[\d]+)', array(
        array(
            'methods'             => WP_REST_Server::DELETABLE,
            'callback'            => 'waf_delete_post',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_posts_delete' );

function waf_delete_post( $request ) {
    $data = $_REQUEST;
    $redirect = @$data['redirect'] ? $data['redirect'] : '/';
    if( @$data['redirect'] ) unset( $data['redirect'] );

    $params = $request->get_params();
    $id = $params['id'];
    $post = get_post($id);
    $post_title = $post->post_title;

    $deleted =  wp_delete_post( $id );
    // $deleted = true;
    if( !$deleted ) {
        waf_error('Error deleting post');
    }
    $redirect = apply_filters( 'waf_delete_redirect', $redirect, $post->post_type, $post );
    $response = [
        'message' => __( 'Deleted '.$post_title.'!', 'waf' ),
        'status' => 'success',
        'remove' => ['#post-'.$id],
        'url' => $redirect
    ];
    // d($response);
    wp_send_json($response);
    
}