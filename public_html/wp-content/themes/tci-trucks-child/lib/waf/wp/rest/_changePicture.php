<?php
function change_picture_rest_api_init() {
    register_rest_route( 'waf/v1', '/changePicture', array(
        array(
            'methods'             => 'POST',
            'callback'            => 'waf_rest_change_picture',
            'permission_callback' => 'is_user_logged_in',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'change_picture_rest_api_init' );
/**
 * Reset the user pass after validation
 *
 * @author Aman Saini
 * @since  1.0
 * @return  Success/Error Message
 */
function waf_rest_change_picture(){
   $file = [];
//    foreach( $_FILES['avatar'] as $k=>$values ) $file[$k] = $values[0];
    $file = $_FILES['avatar'];
    $user_id = get_current_user_id();
    
    $avatar_id = save_attachment( $file );
    update_user_meta( $user_id, 'avatar', $avatar_id );
    // d('avatar id',$avatar_id);
    if( $avatar_id ) 
        wp_send_json([
            'status' => 'success',
            'message' => __( 'Your profile picture has been changed', 'r9tv' ),
            'selector' => '.avatar-holder',
            'html' => get_avatar( $avatar, 50 )
        ]);

    wp_send_json([
        'status' => 'danger',
        'message' => __( 'There was an error changing your profile picture', 'r9tv' )
    ]);
}