<?php
function mailchimp_join_email_list( $listID, $apiKey, $params=[] ) {
	if( !$params ) $params = $_POST;
	
	if( $params['name'] ) {
		$fname = explode( ' ', $params['yourname'] )[0];
		$lname = explode( ' ', $params['yourname'] )[1];
		if( !$lname ) $lname = ' (via TCI )';
	} elseif( is_user_logged_in() ) {
		$user = wp_get_current_user();
		$fname = $user->data->first_name;
	}
	
	$email = $params['email'];
	if( !$fname ) $fname = 'Unknown User';
	if( $fname && $email ) {
		// MailChimp API credentials
		
		// MailChimp API URL
		$memberID = md5(strtolower($email));
		d('apikey:',$apiKey);
		$dataCenter = explode( '-', $apiKey )[1];
		d('data center:',$dataCenter);
		$url = 'https://' . $dataCenter . '.api.mailchimp.com/3.0/lists/' . $listID . '/members/' . $memberID;

		d('url: '.$url);
		// d($params);
		// d($fname,$lname);
		// member information
		$group = get_option( 'mailchimp_group' );
		$data = array(
			'email_address' => $email,
			'status'        => 'subscribed',
			'merge_fields'  => array(
				'FNAME'     => $fname,
				'LNAME'     => $lname
			)
		);
		if( $group ) {
			$data['interests'] = [$group=>true];
		}
		// d($data);
		$json = json_encode($data);
		d('json:');
		
		d('...');
		// send a HTTP POST request with curl
		$ch = curl_init($url);
		d('curl initted');
		curl_setopt($ch, CURLOPT_USERPWD, 'user:' . $apiKey);
		curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 10);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $json);
		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		d('code: '.$httpCode);
		$result = json_decode( $result, 1 );
		d($result);
		return $result;
	} 
}
