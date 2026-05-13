<?php
function waf_rest_user_api_init() {
    register_rest_route( 'waf/v1', 'user', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_rest_update_user',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
    register_rest_route( 'waf/v1', 'user/(?P<id>[\d]+)', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_rest_update_user',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'waf_rest_user_api_init' );

function waf_rest_update_user( $request ) {
    $data = $request->get_params();
    $user_id = $data['id'] ? $data['id'] : get_current_user_id();
    
    if( $user_id ) if( $user_id != get_current_user_id() && !current_user_can('edit_users') ) {
        return false;
    }
    $user = get_userdata($user_id);
    // d($user);

    $redirect = @$data['redirect'];
    if( @$data['redirect'] ) unset( $data['redirect'] );
    $meta = [];
    $userdata = ['ID' => $user_id];
    
    // Verify password fields
    waf_verify_password( $user_id, $data);

    // Process meta queries
    waf_process_user_data( $data, $userdata, $meta );
    
    // d($postdata);
    wp_update_user( $userdata );
    
    // Update avatar 
    waf_process_user_avatar( $user_id, $data );

    // d("POST ADDED",$id,get_post($id));
    foreach( $meta as $key=>$value ) {
        if( $value && $value != 'false' ) update_user_meta( $user_id, $key, $value );
        else delete_user_meta( $user_id, $key );
    }

    // waf_process_user_file_upload($id);
    do_action( 'waf-save-user', $user_id, $userdata, $meta );

    if( !$redirect ) {
        $redirect = add_query_arg( 'updated-user',true,site_url() );
    }
    // d(get_post($id));
    // die;
    $response = apply_filters( 'waf_user_response', [
        'message' => __( 'Updated User!', 'waf' ),
        'status' => 'success',
        'url' => $redirect
    ], $id, $user, $data, true );
    // d('response:',$response);
    // $rsponse = ['status'=>'success','message'=>'ok'];
    wp_send_json($response);
}