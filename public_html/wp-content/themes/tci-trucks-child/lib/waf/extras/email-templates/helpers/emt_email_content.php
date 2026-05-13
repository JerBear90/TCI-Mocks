<?php
// Filter paragraphs on email content
function emt_email_content( $content ) {
	$content = str_replace( '<p>', '<p class="c5"><span class="c7">', $content );
	$content = str_replace( '</p>', '</span></p>', $content );//<br style="height: 10px;"/>',
	
	$content = str_replace( '<div style="text-align: center;">', 
		'<div style="text-align:center;" class="c5"><span class="c7">', $content );
	$content = str_replace( '</div>', '</span></div>', $content );//<br style="height: 10px;"/>',
	return $content;
}
add_filter( 'emt_email_content', 'emt_email_content' );