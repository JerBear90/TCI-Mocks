<?php
function rest_post_order_api_init() {
    register_rest_route( 'waf/v1', 'postOrder', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_rest_order_posts',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_post_order_api_init' );

function waf_rest_order_posts( $request ) {
    $user_id = get_current_user_id();
    $data = $request->get_params();
    $items = $data['order'];
    foreach( $items as $order=>$item ) {
        if( current_user_can('edit_post',$item) ) {
            $update = ['ID'=>$item, 'menu_order'=>$order];
            d('update:',$update);
            wp_update_post( $update);
        }  
    }
    wp_send_json($response);
}