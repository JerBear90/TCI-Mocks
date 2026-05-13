<?php
function waf_truck_form_data( $data ) {
    global $post;
    ini_set('display_errors','on');
    // d($post);
    if( !@$post ) return;
    // $locations = wp_get_object_terms( $post->ID, 'location' );
    // $contact = wp_get_object_terms( $post->ID, 'contact' );
    // $email = get_post_meta( $post->ID, 'Email', true );
    // $email = get_term_meta( $contact[0]->term_id, 'email', true );
    $email = get_option( 'admin_email' );
    $data['to'] = [
        'type' => 'hidden',
        'label' => 'to',
        'value' => $email
    ];
    $data['truck'] = [
        'type' => 'hidden',
        'label' => 'Truck',
        'value' => htmlentities('<a href="'.get_permalink($post->ID).'" target="_blank">'.$post->post_title.'</a>')
    ];
    $data['subject'] = [
        'type' => 'hidden',
        'value' => sprintf( __( 'Inquiry about %s', 'tci' ), $post->post_title )
    ];
    if( !@get_option('mailchimp_apiKey') ) unset($data['mailchimp']);
    return $data;
}
add_filter( 'waf_truck_form_data', 'waf_truck_form_data' );