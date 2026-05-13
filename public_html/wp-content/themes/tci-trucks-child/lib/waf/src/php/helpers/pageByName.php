<?php
function get_page_by_name($page_name) {
	global $wpdb;
	$page_name_id = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '".$page_name."'");
	return $page_name_id ? get_post( $page_name_id ) : null;
}