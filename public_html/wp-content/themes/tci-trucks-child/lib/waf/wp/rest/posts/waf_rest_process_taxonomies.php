<?php
function waf_rest_process_taxonomies( $post_id, $taxonomy ) {
    foreach( $taxonomy as $tax=>$terms ) {
        if( $terms == 'null' ) $terms = '';
        // d($post_id,$terms,$tax);
        $tax_obj = get_taxonomy( $tax );
        if( $tax_obj->hierarchical ) {
            
            if( trim($terms) ) {
                $terms = explode( ',', $terms );
                foreach( $terms as $t=>$term ) {
                    // dd($term);
                    if( $term == 'null' || !$term ) continue;
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
                wp_delete_object_term_relationships( $post_id, $tax );
            }
        } else {
            // d($tax,$terms);
            
            if( $terms ) {
                if( !is_array($terms) ) $terms = explode(',',$terms);
                $terms = wp_set_object_terms( $post_id, $terms, $tax );
            } else {
                // d('--delete');
                wp_delete_object_term_relationships( $post_id, $tax );
            }
        }
        
        // if( $tax == 'job_listing_tag' ) d(wp_list_pluck(wp_get_object_terms($post_id,$tax),'name'));
    }
}
