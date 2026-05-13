<?php
// Form shortcode
function wsf_form_shortcode( $atts=array() ) {
	// d($atts);
	if( is_array($atts) ) {
		$id = $atts['id'].'.json';
		unset($atts['id']);
	}
	return get_form( $id, $atts );
}
add_shortcode( 'the_form', 'wsf_form_shortcode' );

// Contact form shortcode
function wsf_contact_form_shortcode( $atts=array() ) {
	return get_form( 'contact.json', $atts );
}
add_shortcode( 'contact_form', 'wsf_contact_form_shortcode' );