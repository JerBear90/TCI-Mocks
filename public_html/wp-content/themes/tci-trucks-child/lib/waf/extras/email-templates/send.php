<?php
/*
function extra_emt_email_vars( $vars, $args ) {
	extract( $args );
	if( $user_id ) {
		$user = get_userdata( $user_id );

		$emt_actions = get_emt_actions();
		$var_keys = array_keys( $emt_actions[$action]['variables'] );
		foreach( $var_keys as $key ) {
			switch $key {
				case 'subscription_link' :

					break;
f
				case 'subscription_id' :

					break;
				default :
					$vars[$key] = get_user_meta( $user_id, $key, true );
			}
		}

	}
	return $vars;
}
add_filter( 'emt_email_vars', 'extra_emt_email_vars', 10, 2 );
*/

/* =================================
	Email Sending Functions

	* function emt_send_action_email: Send an email using a template
		- args: "action", "user_id", "to"
			- email action (assign template to action)
			- user_id can be used instead of "to" to send to user's email
		- vars: variables available to email template
			- {display_name}, {first_name}, {last_name}, {email}, {user_login} available to all
	* function get_email_assignments: Get all Email posts assigned to action
	* function emt_get_email_content: Get email content from email post template
	* function emt_content_add_template: Add html template to email content
	* function emt_send_email: Send email using a email post template (provide post_id)
	* function emt_write_log: Log to text file (/email_logs)
			(automatic using above send functions)
	* function tweaks_log_email: Log to WP database table {prefix}_subscriptions_emails
			(used in subscriptions-table.php)
	* function tweaks_get_email_log: Get email log from {prefix}_subscriptions_emails
			(used in subscriptions-table.php)
	================================= */

// Send an email action
function emt_send_action_email( $args, $vars=array() ) {
	$status = null;
	$args = wp_parse_args( $args, array(
		'action' => '',
		'user_id' => '',
		'to' => ''
	));

	// extract args for convienence
	extract( $args );
	
	$vars = apply_filters( 'emt_email_vars', $vars, $args );
	// d($args,$vars);
	// Get emails assigned to this action
	$assigned = get_email_assignments( $action );
d('assigned:',$assigned);
	if( is_array($assigned) ) {
		if( !$to ) {
			if( $user_id ) {
				$user = get_userdata( $user_id );
				$to = $user->data->user_email;
			}
		}
// d('to:',$to);
		if( $to ) {
			foreach( $assigned as $post_id ) {
				// d('send it',$to,$post_id,$vars,$args);
				$status = emt_send_email( $to, $post_id, $vars, $args );
			}
		}

	}
	return $status;
}

// Get email assignments for a reservation action
function get_email_assignments( $action ) {
	global $wpdb;
	$q = "SELECT * FROM {$wpdb->postmeta} WHERE meta_key='emt_action' AND meta_value='$action'";
	$args = [
		'meta_key' => 'emt_action',
		'meta_value' => $action,
		'post_type' => 'email'
	];
	$results = get_posts( $args );
	$assignments = wp_list_pluck( $results, 'ID' );
	return apply_filters( 'emt_email_assignments', $assignments );
}

// Get content for email from template
function emt_get_email_content( $template, $vars=array(), $args=array() ) {
	global $wpdb;

	$id = $template;
	if( $id && get_post_status( $id ) == 'publish' ) {
		// get content
		if( !@$content ) {
			$template_post = get_post( $id );
			$content = $template_post->post_content;
		}
		// d('post:',$template_post);
		$content = apply_filters( 'the_content', $content, $id );

		// get subject
		$subject = @$_REQUEST['subject'];
		if( !$subject ) $subject = get_post_meta( $id, 'emt_subject', true );

		// Get vars
		$vars = apply_filters( 'emt_email_variables', $vars );
		
		if( is_array($vars) ) {
			foreach( $vars as $key=>$value ) {
				$keys[] = '{'.$key.'}';
				if( is_string($value) ) $values[] = stripslashes($value);
				else $values[] = $value;
			}
		}

		// Get the email template
		$email_template = get_post_meta( $id, 'emt_html_template', true );
		if( !$email_template) $email_template = 'simple.html';
		if( !strpos( $email_template, '/') === 0 ) 
			$email_template = dirname(__FILE__).'/html-templates/'.$email_template;
		// d('email template:',$email_template);
		// build the body
		$content = wpautop( $content );
		
		$content = apply_filters( 'emt_email_content', $content, $vars, $args );

		ob_start();
		eval( '?>'.file_get_contents( $email_template ) );
		$body = ob_get_contents();
		ob_end_clean();

		$body = str_ireplace( '{content}', $content, $body );

		// Replace variables
		$subject = @stripslashes( html_entity_decode( str_ireplace( $keys, $values, $subject ) ) );
		$body = str_ireplace( $keys, $values, $body );

		// If no variables, remove curly brackets
		if( !is_array($vars) ) {
			$body = str_replace( array('{','}'),'',$body );
		}
		
		return array( 'subject' => $subject, 'body' => $body );
	}
	return [];
}

// Add content to template
function emt_content_add_template( $content, $email_template='' ) {
	// Get the email template
	if( !$email_template) $email_template = 'simple.html';
	$email_template = dirname(__FILE__).'/html-templates/'.$email_template;

	// build the body
	$content = wpautop( $content );
	$content = apply_filters( 'emt_email_content', $content );

	ob_start();
	eval( '?>'.file_get_contents( $email_template ) );
	$body = ob_get_contents();
	ob_end_clean();
	$body = str_ireplace( '{content}', $content, $body );

	// Replace variables
	$body = str_ireplace( $keys, $values, $body );
	return $body;
}

// Send the email
function emt_send_email( $to, $template, $vars=array(), $args=array() ) {
	global $wpdb;
	// get template
	d('sending email',$to );

	if( is_devel() ) $to = DEVEL_EMAIL;

	d("to: $to, template: $template");
	if( !$to ) {
		return false;
	}

	$email_content_arr = emt_get_email_content( $template, $vars, $args );
	// echo $email_content_arr['body'];
	// d($vars);
	extract( $email_content_arr );
	// d($email_content_arr,$vars);
	// die;

	if( !$body ) {
		d('--no email body');
		return;
	}
	if( $body ) {
		// get from
		$default_name = g('email_from_name') ? g('email_from_name') : get_bloginfo('name');
		$from_name = @$args['from_name'] ? $args['from_name'] : $default_name;
		$default = g('email_from_email') ? g('email_from_email') : get_option( 'admin_email' );
		$reply_to = @$args['reply_to'] ? $args['reply_to'] : get_option( 'email_reply_to' );
// d('default:',$default);
		$from = $default;
// d('from:',$from);
		// Additional Headers
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=utf-8' . "\r\n";
		$headers .= "From: $from_name <$from>\r\n";
		d($headers);
		// Get files
		if( !@$template_name ) $template_name = $template;

		//$template = apply_filters( 'email_template', $template, $vars, $member );
		//$files = apply_filters( 'emt_email_files', array(), $template_name, $member, $vars	 );

		// Send email
		
		
		try {
			d('-- send to',$to);
			wp_mail( $to, $subject, $body, $headers );
			$message = 'success';
		} catch (Exception $e) {
			$message = 'failure:'.$e->errorMessage();
			d($e->errorMessage());
		}

		$log = '['.date('D M d Y, h:ia', current_time('timestamp') ).'], From: '.$from_name.'<'.$from.'>, '.
				'Subject: '.$subject.', To: '.$to.', BCC: '.@$bcc.', Status: '.$message;
				// d($log);
		emt_write_log( $log );
		if( $message == 'success' ) {
			return 'success';
		} else {
			return 0;
		}
	}
}

/*
function get_templates_email_files( $files=array(), $template='', $member='' ) {
	if( !is_numeric($template) && is_array($_POST['attachments']) ) return array_merge( $files, $_POST['attachments'] );

	if( is_numeric($template) ) {
		b('admin');
		$post_id = $template;

		$args = array(
			'post_type' => 'any',
			'post_status' => 'any',
			'post_parent' => $post_id,

		);
		$posts = get_posts( $args );

		if( count($posts) > 0 ) {
			foreach( $posts as $post ) {
				$title = get_the_title($post->ID);
				$fname = get_attached_file( $post->ID );
				$files[$title] = $fname;
				d($title);
				d($fname);
			}
		}
		b('cblog');
	}
	return $files;
}
add_filter( 'get_emt_email_files', 'get_templates_email_files', 10, 3 );
*/

// Write to log file
function emt_write_log( $log ) {
	// Month based filename
	$month = date( 'Y-M');

	// Real or Test log
	if( is_devel() ) $fname = ABSPATH.'email-log/dev-test/'.$month.'.txt';
	else $fname = ABSPATH.'email-log/'.$month.'.txt';

	// Created if not exists
	if( !file_exists( $fname ) ) {
		@touch($fname);
		@chmod($fname, 0600);
	}

	if( file_exists( $fname ) ) {
		// Write to log file
		$f = @fopen( $fname, 'a' );
		@fwrite( $f, $log."\n" );
		@fclose( $f );
	}
}

// Log email to wp database
function tweaks_log_email( $data  ) {
	global $wpdb;
	$table = $wpdb->prefix.'subscription_emails';
	$wpdb->insert( $table, $data );
	return $wpdb->last_error;
}

// Get email log from wp database
function tweaks_get_email_log( $email ) {
	global $wpdb;
	$table = $wpdb->prefix.'subscription_emails';
	$q = "SELECT *,UNIX_TIMESTAMP(date) as date FROM $table WHERE email='$email'";
	return $wpdb->get_results( $q );
}
?>
