<?php
// Save file as wordpress attachment
function save_attachment($file,$post_id='',$thumb=0, $deprecated_thumb=0) {
    if( $deprecated_thumb ) $thumb = $deprecated_thumb;
    require_once( ABSPATH . "wp-admin/includes/file.php" );
    $filename = $file['name'];
    // d('filename:',$filename);
    // Get file url
    $upload_dir = wp_upload_dir();
    // $filename = $file['name'];
    // $guid = $upload_dir['url'].'/'.@$filename;
    $absname = waf_save_file($file);
    

    // d("absname:",$absname,'existing',file_exists($absname));
    if( file_exists($absname) ) {
        chmod( $absname, 0644 );
        // Build attachment post details
        $basename = pathinfo( @$filename, PATHINFO_FILENAME );
        $title = pathinfo( $file['name'], PATHINFO_FILENAME );
        $ft = wp_check_filetype( $absname );
        
        $mime_type = $ft['type'];
        $attachment = array(
            'post_mime_type' => $ft['type'],
            'post_title' => $title,
            'post_name' => $basename,
            'post_content' => '',
            'post_status' => 'inherit',
            'post_parent' => $post_id,
            'post_mime_type' => $mime_type
        );
        // Insert attachment
        // d($attchment);
        $attach_id = wp_insert_attachment( $attachment, $absname );
    
        // Update image meta
        try {
            require_once( ABSPATH . "wp-admin/includes/image.php" );
            $attach_meta = wp_generate_attachment_metadata_custom( $attach_id, $absname );
            wp_update_attachment_metadata($attach_id, $attach_meta);
        } catch( Exception $e ) {
            d('error generating attachment metadata',$e->getMessage());
        }
    
        // Set thumbnail, if appropriate
        
        if($thumb) {
            // d('set thumb',$post_id,$attach_id);
            set_post_thumbnail( $post_id, $attach_id );
        }

        // d('attachment:',$attach_id,get_post($attach_id));
        return $attach_id;
    }
    
}