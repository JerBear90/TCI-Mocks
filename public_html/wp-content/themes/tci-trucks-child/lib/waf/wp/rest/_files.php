<?php
function rest_tmp_files() {
    register_rest_route( 'waf/v1', 'files', array(
        array(
            'methods'             => WP_REST_Server::CREATABLE,
            'callback'            => 'waf_upload_tmpfile',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rest_tmp_files' );

function waf_upload_tmpfile() {
    $files = $_REQUEST['files'];
    $d = wp_upload_dir();
    // geodir_temp
    foreach( $files as &$file ) {
        if( !$file['content'] ) continue;
        $tmpname = $d['basedir'].'/geodir_temp/waf-'.uniqid();
        touch($tmpname,0664);
        $saved = waf_save_file( $file, $tmpname );
        
        $url = str_replace( $d['basedir'],$d['baseurl'], $tmpname );
        $file['url'] = $url;
        unset($file['content'],$file['complete']);
    //     d($file);
    }
    wp_send_json([
        'status' => 'success',
        'files' => $files
    ]);
}