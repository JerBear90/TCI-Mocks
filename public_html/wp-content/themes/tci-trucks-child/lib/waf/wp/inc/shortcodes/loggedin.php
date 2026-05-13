<?php
function logged_in_shortcode( $atts=[], $content='' ) {
    if( is_user_logged_in() ) return $content;
}
add_shortcode( 'loggedin', 'logged_in_shortcode' );