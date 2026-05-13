<?php
function waf_save_term_fields() {
    $builtIns = [
        'name','slug','term_group'
    ];
    $tax = @$_REQUEST['taxonomy'];
    $path = formTemplater::getJsonPath( 'taxonomies/'.$tax );
    
    $typedata = get_formdata( 'taxonomies/'.$tax );
    $alldata = get_formdata( 'taxonomies/all' );
    $formdata = array_merge( $alldata, $typedata );
    
    
    $update = [];
    foreach( $formdata as $k=>$field ) {
        $value = $_REQUEST[$k];
        $term_id = $_REQUEST['tag_ID'];
        $term_iddata = ['ID'=>$term_id];
        if( @$field['builtIn'] || in_array($k,$builtIns) ) $update[$k] = $value;
        else update_term_meta( $term_id, $k, $value );
        // d($k,$value);
        
    }
    if( !empty($update) ) {
        global $wpdb;
        $wpdb->update( $wpdb->terms, $update, ['term_id'=>$term_id] );
    }
}