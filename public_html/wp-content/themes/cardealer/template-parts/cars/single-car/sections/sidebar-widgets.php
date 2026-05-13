<?php
$sidebar_position = cardealer_get_cars_details_page_sidebar_position();
if ( 'no' !== $sidebar_position ) {
	$custom_sidebar = get_post_meta( get_the_ID(), 'custom_sidebar', true );
	if ( $custom_sidebar ) {
		dynamic_sidebar( $custom_sidebar );
	} else {
		dynamic_sidebar( 'detail-cars' );
	}
}
