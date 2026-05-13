<?php
function waf_process_file_upload($post_id) {
    // Images/attachments
    // die;
    if( !empty(@$_FILES) ) {
        // d($_FILES);
        $files = waf_remap_files( $_FILES );
        $i=0;
        foreach( $files as $file ) {
            $i++;
            $f = @$file['field'];
            $featured = $i==1 ? true : false;
            // d('file:',$file,'featured:',$featured);
            try {
                $attach_id = save_attachment( $file, $post_id, $featured );
                // d($file,$post_id,$featured,$attach_id);
            } catch( Exception $e ) {
                d("Error saving attachment:",$e->getMessage());
            }
        }
    }
}