<?php
// Echo Current URL
function the_current_url( $query_string=true ) {
	echo get_current_url( $query_string );
}
// Get Current URL
function get_current_url( $query_string=true ) {
	if( $query_string ) $request_uri = $_SERVER["REQUEST_URI"];
	else list( $request_uri ) = explode( '?', $_SERVER["REQUEST_URI"] );
	
	if( @$_SERVER['HTTPS'] ) return 'https://'.$_SERVER["HTTP_HOST"] . $request_uri;
	else return 'http://'.$_SERVER["HTTP_HOST"] . $request_uri;
}