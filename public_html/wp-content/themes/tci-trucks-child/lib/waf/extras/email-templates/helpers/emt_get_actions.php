<?php
// Get EMT Actions
function emt_get_actions() {
	// Define email actions
	$common_variables = array(
		'user_login' => 'User Login',
		'first_name' => 'First Name',
		'email' => 'Email',
		'display_name' => 'Display Name',
		'siteurl' => 'Site Url'
    );
    $common_variables = apply_filters( 'emt_common_variables', $common_variables );
	
	// Add common variables
	$emt_actions = [];
	$emt_actions = apply_filters( 'emt_actions', $emt_actions );
	foreach( $emt_actions as $e=>$emt_action ) {
		if( @$emt_action['common'] ) {
			$variables = array_merge( $common_variables, (array)$emt_action['variables'] );
			$emt_actions[$e]['variables'] = $variables;
		}
		
	}
	return $emt_actions;
}