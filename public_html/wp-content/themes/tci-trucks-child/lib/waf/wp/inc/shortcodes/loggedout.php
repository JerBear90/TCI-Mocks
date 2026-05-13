<?php
function logged_out_shortcode( $atts=[], $content='' ) {
    if( !is_user_logged_in() ) return $content;
}
add_shortcode( 'loggedout', 'logged_out_shortcode' );

// apply_filters( 'the_content', 'do_shortcode' );