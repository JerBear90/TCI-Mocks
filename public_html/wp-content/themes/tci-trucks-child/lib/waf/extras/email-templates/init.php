<?php
/*
	Site specific config for Email Template System
*/

// Filter paragraphs on email content
// Admin init - EMT metabox
function emt_admin_init() {
	global $metaboxes;
	// Get list of email actions for metabox
	$emt_actions = emt_get_actions_list();
	$html_templates = emt_get_html_template_list();
	$id = @$_GET['post'];
	
	// Setup metabox
	$metaboxes['email'] = array(
		'setup' => array(
			'title' => 'Email Template Setup',
			'type' => 'metabox',
			'position' => 'side',
			'priority' => 'high',
			'emt_subject' => array(
				'type' => 'text',
				'label' => 'Email Subject'
			),
			'emt_action' => array(
				'type' => 'select',
				'label' => 'Email Action',
				'options' => $emt_actions
			),
			'emt_variables' => array(
				'name' => '',
				'class' => 'emt_variables',
				'type' => 'html',
				'label' => 'Available Variables',
				'html' => '<div class="value">Select an Action to see available variables</div>'
			),
			'emt_html_template' => array(
				'type' => 'select',
				'label' => 'HTML Template',
				'placeholder' => '',
				'options' => $html_templates
			)
		),
		'test' => array(
			'title' => 'Send Test Email',
			'type' => 'metabox',
			'position' => 'side',
			'priority' => 'high',
			'to' => array(
				'name' => '',
				'class' => 'to',
				'type' => 'email',
				'label' => 'Email'
			),
			'send' => array(
				'type' => 'submit',
				'label' => 'Send'
			),
			'messages' => array(
				'type' => 'html',
				'class' => '',
				'label' => 'Save post before testing!',
				'html' => '<div class="messages"></div>'
			)
		)
	);
	
	if( $id ) {
		$emt_action = get_post_meta( $id, 'emt_action', true );
		if( $emt_action ) $metaboxes['email']['setup']['emt_variables']['html'] = emt_get_variables_list( $emt_action );
	}
	// d($metaboxes);
}
add_action( 'admin_init', 'emt_admin_init' );
