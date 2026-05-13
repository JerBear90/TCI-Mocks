<?php
function waf_process_user_avatar( $user_id, $data ) {
    $files = waf_remap_files( $_FILES );
    
    // Note: There is only expected to be one file
    foreach($files as $f=>$file ) {
        $avatar_id = save_attachment( $file );
        $url = wp_get_attachment_image_src( $avatar_id )[0];
        update_user_meta( $user_id, $f.'-id', $avatar_id );
        update_user_meta( $user_id, $f, $url );
        break;
    }
}