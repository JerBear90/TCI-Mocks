<?php
// Menu Item
function emt_email_all_init() {
	add_submenu_page( 'edit.php?post_type=email', 'Email All', 'Email All', 'manage_options', 'email-all', 'emt_email_all_page' );
}
add_action( 'admin_menu', 'emt_email_all_init' );

// Form
function emt_email_all_page() {
	$form = array(
		'action' => 'email-all',
		'class' => 'email-all',
		'subject' => array(
			'type' => 'text',
			'label' => 'Subject',
			'required' => true
		),
		'body' => array(
			'type' => 'tinymce',
			'label' => 'Body',
			'input_id' => 'email-body'
		),
		'html_template' => array(
			'label' => 'HTML  Template',
			'type' => 'select',
			'options' => emt_get_html_template_list(),
			'required' => true
		),
		'test' => array(
			'type' => 'checkbox',
			'label' => 'Send Test Only',
		),
		'test_email' => array(
			'type' => 'email',
			'label' => 'Test Email'
		),
		'submit' => 'Send'
	);
	the_form( $form );
	echo '<div id="messages"></div>';
}

// Form Submission
function emt_ajax_email_all() {
	extract( $_POST );
	global $wpdb;
	if( wp_verify_nonce( $_wpnonce, 'email-all' ) ) {
		
		$body = emt_content_add_template( $_POST['email-body'], $html_template );
		if( $test ) {
			d('test it');
			if( !$test_email ) {
				$response = array(
					'status' => 'error',
					'message' => 'Provide Test Email',
					'invalid' => array(
						'input[name=test_email]'
					)
				);
				echo json_encode( $response );
				exit;
			}
			$message = tweaks_send_email( $body, $subject, $test_email );
			if( $message == 'success' ) {
				$response = array(
					'status' => 'success',
					'message' => 'Sent Test Email'
				);
			} else {
				$response = array(
					'status' => 'error',
					'message' => $message
				);
			}
		} else {
			
			// $users = get_users( array( 'role__in' => ( 'subscriber' ), 'meta_key' => 'customer_id' ) );
			$q = "SELECT user_email FROM {$wpdb->users} AS u"
						." JOIN {$wpdb->prefix}subscriptions AS s"
						." ON u.ID=s.user_id";
			$emails = wp_list_pluck( $wpdb->get_results($q), 'user_email' );
			$emails = array_unique( $emails );
			
			
			foreach( $emails as $email ) {
				d($email);
				if( !is_steve() ) tweaks_send_email( $body, $subject, $email );
			}
			$response = array( 
				'status' => 'success',
				'message' => 'Send to '.count($emails).' members'
			);
		}
		
	}
	echo json_encode( $response );
	exit;
}
add_action( 'wp_ajax_email-all', 'emt_ajax_email_all' );
