<?php
function waf_save_post( $data, $post_type='', $new=false, $admin=false ) {
    global $wpdb;
    $post_id = $data['ID'];
    unset($data['ID']);
    // d('save data',$data);
    // die;
    // $postdata = ['ID' => $post_id ];
    $postdata = [];
    $taxonomy = [];
    $meta = [];

    $post_type = get_post_type( $post_id );
    $original_status = get_post_status( $post_id );
    if( !$post_id && $new=false ) {
        
        throw new Execption( __( 'No Post id', 'jasper' ) );
    }
    
    // Process meta queries
    foreach( $data as $k=>$value ) {
        if( is_string($value) ) $value = stripslashes( $value );
        if( strpos( $k, 'meta_' ) === 0 ) {
            $key = str_replace( 'meta_', '', $k );
            $meta[$key] = $value;
        } elseif( strpos( $k, 'taxonomy_' ) === 0 ) {
            $key = str_replace( 'taxonomy_', '', $k );
            $taxonomy[$key] = $value;
        } elseif( strpos( $k, 'post_') === 0 ) {
            $postdata[$k] = $value;
        } elseif( $k != 'files' ) {
            $meta[$k] = $value;
        }
    }
    // d('post id:',$post_id);
    
    if( @$data['wizard'] && !$admin ) {
        
        if( $new ) {
            if( $data['complete'] ) $postdata['post_status'] = g('default-'.$post_type.'-status');
            else $postdata['post_status'] = 'auto-draft';
        } else {
            
            if( $data['complete'] && (get_post_status($post_id) == 'auto-draft' || get_post_status($post_id) == 'draft' ) ) {
                $postdata['post_status'] = g('default-'.$post_type.'-status');
            } elseif( $data['draft'] ) {
                $postdata['post_status'] = 'draft';
            } else
                unset($postdata['post_status']);
        }
    }
    
    if( $admin && @$_REQUEST['publish'] ) $postdata['post_status'] = 'publish';
    elseif( $admin && @$_REQUEST['post_status']) {
        $postdata['post_status'] = $_REQUEST['post_status'];
    }
    // d($postdata);
    // die;

    if( $post_id ) wp_update_post( $postdata );
    else $post_id = wp_insert_post( $postdata );
    // $post_id = @$postdata['ID'];
    // unset(@$postdata['ID']);
    // d('postdata:',$postdata,$data);
    // die;
    $wpdb->update( $wpdb->posts, $postdata, ['ID'=>$post_id]);
// d($meta);
    
    foreach( $meta as $key=>$value ) {
        
        if( $value && $value != 'false' ) update_post_meta( $post_id, $key, $value );
        else delete_post_meta( $post_id, $key );
        // if( $key == 'locations' ) d('value:',$post_id,$key,$value,get_post_meta($post_id,$key,true));
    }

    // die;
    foreach( $taxonomy as $tax=>$terms ) {
        // d($post_id,$terms,$tax);
        $tax_obj = get_taxonomy( $tax );
        if( $tax_obj->hierarchical ) {
            $terms = explode( ',', $terms );
            foreach( $terms as $t=>$term ) {
                if( $term && !is_numeric($term) ) {
                    // d('--add term',$term);
                    $new = wp_insert_term($term,$tax);
                    // d($new);
                    if( is_wp_error($new) ) $terms[$t] = $new->error_data['term_exists'];
                    else $terms[$t] = $new['term_id'];
                    // d($terms[$t]);
                }
            }
            $terms = implode( ',', $terms );
            $terms = wp_set_post_terms( $post_id, $terms, $tax );
        } else {
            if( !is_array($terms) ) $terms = explode(',',$terms);
            $terms = wp_set_object_terms( $post_id, $terms, $tax );
        }
        
        // d(wp_get_object_terms($post_id,$tax));
    }
    
    // Process meta queries
    
    // die;
    // d($_FILES,$_REQUEST);
    // die;
    // Images/attachments
    $filedata = @$data['images'];
    if( $filedata ) {
        // d($file);
        foreach( $filedata as $i=>$item) {
            // d($i,$item);
            $item = json_decode( stripslashes($item), 1 );
            $post_id = $item['id'];
            // d($post_id,$item);
            $filedata[$post_id] = $item;
            unset($filedata[$i]);
        }
    }
    
    waf_rest_process_taxonomies( $post_id, $taxonomy );
    waf_process_file_upload( $post_id );
    
    do_action( 'waf-save-post', $post_id, $new, $original_status, $postdata, $meta, $taxonomy );
    return $post_id;
}