<?php
function tci_config( $file, $key='' ) {
    $path = TCIDIR.'/config/'.$file.'.json';
    $data = json_decode( file_get_contents( $path ), 1 );
    if( $key ) return $data[$key];
    else return $data;
}

function tciRender( $slug, $vars=[], $echo = true ) {
    // Get filename
    // td('slug:',$slug);
    $slug = str_replace( '.php', '', $slug );
    $path = locate_template( $slug.'.php', false );
    if( is_array($vars) ) extract( $vars );
    // td('vars:',$vars,$is_testing);
    // d('path:',$path);
    ob_start();
    if( file_exists($path) ) include $path;
    else d('-- Template not found',$slug);
    
    $html = ob_get_contents();
    ob_end_clean();
    
    if( $echo ) echo $html;
    else return $html;
}