<?php
function waf_page_link( $slug ) {
    echo waf_get_page_link( $slug );
}
function waf_get_page_link( $slug ) {
    $page = g($slug.'-page');
    
    if( !$page ) return '#';
    $permalink = get_permalink( $page );
    return $permalink ? $permalink : '#';
}