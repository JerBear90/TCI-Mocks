<?php
function tweaks_emt_email_vars( $vars=[], $args=[] ) {
	extract( $args );
    
    // d('hello world!');
    
	if( $user_id ) {
		$user = get_userdata( $user_id );
        $registrationData = '';
        
        
		$vars = array_merge( (array)$vars, array(
			'display_name' => $user->data->display_name,
			'name' => $user->data->display_name,
			'first_name' => get_user_meta( $user_id, 'first_name', true ),
			'last_name' => get_user_meta( $user_id, 'last_name', true ),
			'email' => $user->data->user_email,
            'user_login' => $user->data->user_login,
        ) );
        
        foreach( $vars as $k=>$val ) {
            $parts = explode( '_', $k );
            foreach( $parts as $p=>$part ) $parts[$p][0] = strtoupper( $part[0] );
            $label = implode( ' ', $parts );
            $registrationData .= '<strong>'.$label.': </strong>'
                        .$val."<br>\n";
                        
            $vars['registration-data'] = $registrationData;
        }
    }
    
    
	return $vars;
}
add_filter( 'emt_email_vars', 'tweaks_emt_email_vars', 10, 2 );

function waf_emt_actions_field_data( $datum ) {
    $emt_actions = emt_get_actions_list();
    $datum['type'] = 'select';
    $datum['options'] = $emt_actions;
    return $datum;
}
add_filter( 'waf_emt_actions_field_data', 'waf_emt_actions_field_data', 100 );