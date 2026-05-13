<?php
function waf_form_actions( $data, $args ) {
	// d('hello world?');
	// d('action:',$args['action']);
	// d('format:',$args['format']);
	// d('url:',$args['url']);
	if( @$args['action']  && @$args['format'] != 'json' && ( strpos($args['url'], 'admin-ajax') || !$args['url'] ) ) {
		
		$nonce_key = @$args['nonceKey'] ? $args['nonceKey'] : $args['action'];
		
		$data['action'] = [
			'type' => 'hidden',
			'value' => @$args['action']
		];
		$data['_wpnonce'] = [
			'type' => 'hidden',
			'value' => wp_create_nonce($nonce_key)
		];
		// d($data['action']);
	}
	return $data;
}
add_filter( 'waf_form_data', 'waf_form_actions', 10, 2 );