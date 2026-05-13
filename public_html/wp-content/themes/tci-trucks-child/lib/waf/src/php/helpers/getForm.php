<?php
// The submit function
// include 'actions/contact.php';
// include 'helpers.php';
// include 'form.class.php';

// 'the_form' template tag to draw the form
function the_form( $data='', $args=[] ) {
	echo get_form( $data, $args );	
}

// 'get_form' function to retrieve a form
function get_form( $data='', $args=[] ) {
	// create a new form
	$form = new form( $data, $args );
	
	// get the form
	return $form->render( false );
}