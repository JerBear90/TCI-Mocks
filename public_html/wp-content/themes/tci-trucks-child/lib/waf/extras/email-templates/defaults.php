<?php
function default_emt_email_vars( $vars, $args ) {
	if( $args['user_id'] ) {
		$user_id = $args['user_id'];
		$usermeta = get_user_meta( $user_id );
		
		$user = get_userdata( $user_id );
		$role = get_role( $user->roles[0] )->name;
		
		$vars = array_merge( (array)$vars, array(
			'display_name' => $user->data->display_name,
			'name' => $user->data->display_name,
			'first_name' => get_user_meta( $user_id, 'first_name', true ),
			'last_name' => get_user_meta( $user_id, 'last_name', true ),
			'email' => $user->data->user_email,
			'user_login' => $user->data->user_login,
			'role' => $role,
			'url' => get_bloginfo('url'),
			'site' => get_bloginfo('name')
		) );
		
		// d(html_entity_decode($vars['formdata']));
	}
	return $vars;
}
add_filter( 'emt_email_vars', 'default_emt_email_vars', 10, 2 );

function waf_emt_actions( $actions ) {
    $actions = [
        'forgot-password' => [
			'title' => __( 'Recover Password', 'waf' ),
			'variables' => [
				'url' => __( 'Password Reset URL', 'waf' )
			]
        ],
        'contact' => [
            'title' => __( 'Contact', 'waf' ),
            'variables' => [
                'details' => __( 'Contact Form Details', 'waf' ),
                'from' => __( 'From Email', 'waf' ),
                'from_name' => __( 'From Name', 'waf' )
            ]
        ]
    ];
	
    // d('actions:',$actions);
    return $actions;
}
add_filter( 'emt_actions', 'waf_emt_actions' );

function waf_contact_emt_email_vars( $vars, $args ) {
	if( $args['action'] == 'contact' ) {
		$formdata = get_formdata('contact');
		$details = '';

		foreach( $formdata as $f=>$field ) {
			if( !is_array($field) || $f == 'args' )  continue;
			$value = @$_REQUEST[$f];
			$label = @$field['label'] ? $field['label'] : @$field['placeholder'];
			if( @$field['fields'] ) {
				
				foreach( $field['fields'] as $ff=>&$ffield ) {
					
					$label = @$ffield['label'] ? $ffield['label'] : @$ffield['placeholder'];
					$value = @$_REQUEST[$ff];
					if( $value && $label ) {
						// d($label,$value);
						$details .= '<strong>'.$label.': </strong>'.$value."<br>";
					}
				}
			}
			if( $value && $label ) {
				// d($label,$value);
				$details .= '<strong>'.$label.': </strong>'.$value."<br>";
			}
		}
		$vars['from_name'] = @$_REQUEST['name'];
		$vars['from'] = @$_REQUEST['email'];
		$vars['details'] = $details;
		$args['reply_to'] = $vars['from'];

	}
	// dd($vars);
	// die;
	return $vars;
}
add_filter( 'emt_email_vars', 'waf_contact_emt_email_vars', 100, 2 );