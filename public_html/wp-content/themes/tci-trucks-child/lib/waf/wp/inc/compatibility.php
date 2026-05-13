<?php
// Use WSF form args filters
function waf_wsf_form_args($data,$args) {
    return apply_filters( 'wsf_form_args', $data, $args );
}

// User WSF form data filters
function waf_wsf_form_data($data,$args) {
    return apply_filters( 'wsf_form_data', $data, $args );
}
add_filter( 'waf_form_data', 'waf_wsf_form_data', 1, 2 );