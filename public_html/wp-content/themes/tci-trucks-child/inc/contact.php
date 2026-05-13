<?php
function rest_tci_contact() {
    register_rest_route( 'tci/v1', 'contact', array(
        array(
            // 'methods'             => 'GET',
            'methods'             => 'POST',
            'callback'            => 'tci_contact_post_rest',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_tci_contact' );

function tci_contact_post_rest( $request=null ) {
    $params = $request->get_params();
    $f = @$params['form'] ? $params['form'] : 'truck';
    // d('params:',$params);
    $form = new form( $f );
    $form->verifyRecaptcha();
    
    $params['from'] = $params['email'];
    $params['from_name'] = $params['yourname'];
    
    $formdata = get_formdata($f);
    $details = [];
    foreach( $formdata as $f=>$field ) {
        if( !is_array($field) ) continue;
        if( $f == 'submit' || $f == 'reset' || $field['type'] == 'hidden' ) continue;
        
        $value = html_entity_decode( @$params[$f] );
        $label = @$field['label'] ? $field['label'] : @$field['placeholder'];
        if( $label ) $details[] = "<strong>{$label}</strong>: $value";
    }
    
    $params['form_details'] = implode( '<br>', $details );
    d('DETAILS:',$params['form_details']);
    $sent = emt_send_action_email( [
        'action' => 'contact',
        'to' => @$params['to'] ? $params['to'] : get_option( 'admin_email' ),
        'reply_to' => $params['email']
    ],
    $params );

    if( @$params['mailchimp'] ) {
        $listId = get_option( 'mailchimp_listId' );
        $apiKey = get_option( 'mailchimp_apiKey' );
        mailchimp_join_email_list( $listId,$apiKey );
    }

    if( $sent ) $response = [
        'status' => 'success',
        'message' => __( 'Sent email!', 'tci' )
    ];
    else $response = [
        'status' => 'error',
        'message' => __( 'Your Email may not have been sent', 'tci' )
    ];
    wp_send_json($response);
}