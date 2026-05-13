<?php
function waf_activate() {
    global $wpdb;
    $sql = 
    ["CREATE TABLE `wp_truck_details` (
        `ID` int(11) NOT NULL,
        `post_id` int(11) NOT NULL,
        `updated` datetime DEFAULT CURRENT_TIMESTAMP,
        `listing_id` bigint(20) NOT NULL,
        `Manufacturer` varchar(64) NOT NULL,
        `Model` varchar(64) NOT NULL,
        `Year` int(11) NOT NULL,
        `truckCondition` text NOT NULL,
        `VINSerialNumber` varchar(128) NOT NULL,
        `DisplayOnSite` varchar(12) NOT NULL,
        `data` text NOT NULL,
        `Price` int(11) DEFAULT NULL,
        `StockNumber` varchar(64) NOT NULL,
        `Vendor` varchar(64) NOT NULL
        );"];
    foreach( $sql as $q ) {
        $wpdb->query( $q );
        if( $wpdb->last_error ) d('error: '.$wpdb->last_error);
    }
    d($wpdb->last_error);
    d('updated database?');
    waf_rewrites();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'waf_activate' );
// add_action( 'init', 'waf_activate' );
add_action( 'init', function() {
    if( @$_GET['create_db_table'] ) {
        waf_activate();
        die;
    }
});