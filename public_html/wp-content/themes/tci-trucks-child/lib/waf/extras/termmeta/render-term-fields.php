<?php
function waf_render_term_fields( $t ) {
    $tax = is_object($t) ? $t->taxonomy : $t;
    if( is_object($t) ) $term_id = $t->term_id;
    
    $path = formTemplater::getJsonPath( 'taxonomies/'.$tax );
    if( $path ) $formdata = get_formdata( 'taxonomies/'.$tax );
    else $formdata = get_formdata( 'taxonomies/all' );

    $formdata = apply_filters( 'waf_form_data', $formdata, @$formdata['args'] );
    $formdata = apply_filters( 'waf_taxonomies/tax_'.$tax.'_form_data', $formdata, @$formdata['args'] );
    foreach( $formdata as $f=>&$field ) {
        $field['container'] = $field['type'] == 'checkbox' ? 'admin-table-checkbox' : 'admin-table-field';
        $field['inputClass'] = 'widefat';
        if( $term_id ) {
            $term = get_term($term_id,$tax);
            if( @$field['builtIn'] ) $field['value'] = $term->$f;
            else $field['value'] = get_term_meta( $term_id, $f, true );
            // d('value:',$field['value']);
        }
    }
    // d(get_term_meta($term_id));
    $formdata['submit'] = false;
    return the_form( $formdata );  
    
}