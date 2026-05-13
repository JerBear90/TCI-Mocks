<?php
function rest_posts_post() {
    register_rest_route( 'waf/v1', 'posts/(?P<post_type>[^/]+)', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_create_post',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_posts_post' );

function waf_create_post( $request ) {
    $data = $request->get_params();
    $data['post_type'] = $pt = $request->get_param('post_type');
    
    
    $redirect = @$data['redirect'];
    if( @$data['redirect'] ) unset( $data['redirect'] );
    
    $params = $request->get_params();
    $post_type = $params['post_type'];
    $meta = $tax = [];
    
    // d($data);
    if( !$data['post_type'] || !$data['post_title'] ) {
        wp_send_json( [
            'status' => 'danger',
            'message' => __( 'A Title and post type is required', 'waf' )
        ]);        
    }
    
    try {
        $id = waf_save_post( $data, $post_type );
        waf_process_file_upload($id);
        do_action( 'waf-save-post', $post_type, $id, $postdata, $meta, @$taxonomy );
    } catch( Exception $e ) {
        wp_send_json( [
            'status' => 'danger',
            'message' => $e->getMessage()
        ]);
    }
    
    
    
    $pto = get_post_type_object( $post_type );
    // d('post type:',$post_type,$pto);
    if( !$redirect ) {
        $redirect = site_url().'?p='.$id.'&post_type='.$post_type.'&preview=1';
    }
    d('added post',$id);
    // d(get_post($id));
    // die;
    // d(get_post($id));
    if( get_post_status($id) == 'auto-draft' ) $message = __( 'Updated draft', 'waf' );
    else $message = __( 'Created '.$pto->labels->singular_name.' "'.get_the_title($id).'"', 'waf' );
    $response = apply_filters( 'waf_post_response', [
        'message' => $message,
        'status' => 'success',
        'url' => $redirect,
        'id' => $id,
        'complete' => @$data['complete']
    ], $id, 'post', $data, true );
    // d('response:',$response);
    // die;
    // $rsponse = ['status'=>'success','message'=>'ok'];
    wp_send_json($response);
}