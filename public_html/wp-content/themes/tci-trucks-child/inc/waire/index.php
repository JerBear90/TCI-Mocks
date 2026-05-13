<?php
function annual_table() {
    $fname = dirname(__FILE__).'/annual-variable.csv';
    $fh = fopen( $fname, 'r' );
    return get_csv_data($fh);
}

function stringency_table() {
    $fname = dirname(__FILE__).'/waire.csv';
    $fh = fopen( $fname, 'r' );
    return get_csv_data($fh);
}

function get_csv_data($fh) {
    $headers = fgetcsv($fh);
    $data = [];
    while( $line = fgetcsv($fh) ) {
        $item = [];
        foreach( $line as $i=>$value ) {
            $h = $headers[$i];
            $item[$h] = $value;
        }
        $data[] = $item;
    }
    return $data;
}
function waire_shortcode() {
    include 'template.php';
}
add_shortcode( 'waire', 'waire_shortcode' );