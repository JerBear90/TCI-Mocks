<?php
function taxonomy_field( $field ) {
    $args = shortcode_atts([
        'taxonomy' => 'category',
        'hide_empty' => false,
        'number' => 0,
        'parent' => ''
    ],$field);;
    
    $tax = get_taxonomy( $args['taxonomy'] );
    $field_key = @$field['field'];
    if( $tax && !$field_key ) $field_key = $tax->hierarchical ? 'term_id' : 'slug';
    $terms = get_terms( $args );
    // d('found:',count($terms));
    if( $terms instanceof WP_Error ) {
        $field['type'] = 'info';
        $field['info'] = '-- Invalid Taxonomy '.$field['taxonomy'];
        return $field;
    }
    foreach( $terms as $term ) {
        $name = html_entity_decode( @$term->name );
        // if( @$term->parent )
        //    $options[$term->$field_key]= '— '.$name;
        // else 
        if( $field_key && $name ) 
            $options[@$term->$field_key]= $name;
    }
    if( !@$field['placeholder'] ) $field['placeholder'] = '--';
    $field['type'] = @$field['mode'] ? $field['mode'] : 'select';
    $field['wp-type'] = 'taxonomy';
    $field['options'] = @$options;
    
    // d($field);
    return $field;
}
add_filter( 'waf_taxonomy_field_data', 'taxonomy_field' );