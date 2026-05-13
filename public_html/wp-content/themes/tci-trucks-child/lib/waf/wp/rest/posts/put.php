<?php
function rest_posts_put() {
    register_rest_route( 'waf/v1', '/posts/(?P<id>[\d]+)', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_update_post',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_posts_put' );

function waf_update_post( $request ) {
    $data = $_REQUEST;
    
    // d('putting?');
    $redirect = @$data['redirect'];
    if( @$data['redirect'] ) unset( $data['redirect'] );

    $params = $request->get_params();
    $post_id = $params['id'];
    $original_status = get_post_status( $post_id );
    $data['ID'] = $params['id'];
    $meta = $taxonomy = [];
    if( !$post_id ) {
        wp_send_json([
            'status' => 'danger',
            'message' => "Can't find post (no post id provided)"
        ]);
    }
    $postdata = ['ID' => $post_id ];
    $post_type = get_post_type( $post_id );
    if( !$post_id ) {
        wp_send_json( [
            'status' => 'danger',
            'message' => __( 'No post id', 'waf' )
        ]);        
    }
    try {
        waf_save_post( $data );
    } catch( Exception $e ) {
        wp_send_json( [
            'status' => 'danger',
            'message' => $e->getMessage()
        ]);
    }
    
    $pto = get_post_type_object( $post_type );
    // d('post type:',$pto,$post_type);
    // die;
    
    if( !$redirect ) {
        if( get_post_status($post_id) == 'publish' ) $redirect = get_permalink( $post_id );
        else $redirect = site_url().'?p='.$post_id.'&post_type='.$post_type.'&preview=1';
    }

    $response = apply_filters( 'waf_post_response', [
        'message' => sprintf( __( 'Updated %s!', 'waf' ), $pto->labels->singular_name ),
        'status' => 'success',
        // 'url' => $redirect
        'url' => $redirect,
        'id' => $id,
        'complete' => @$data['complete']
    ], $post_id, 'put', $data, false );
    
    wp_send_json($response);
}