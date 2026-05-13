<?php
function waf_get_query_args( $data ) {
    
    $args = [
        'post_type' => $data['post_type'],
        'post_parent' => @$data['post_parent'],
        'post_status' => 'publish',
        'posts_per_page' => @$data['posts_per_page'] ? $data['posts_per_page'] : get_option( 'posts_per_page' ),
        'paged' => @$data['page'] ? $data['page'] : 1,
        'orderby' => 'date',
        'order' => 'desc'
    ];

    if( @$data['s'] ) $args['s'] = $data['s'];
    foreach( $data as $k=>$value ) {
        if( !$value ) continue;
        if( strpos( $k, 'meta_' ) === 0 ) {
            $key = str_replace( 'meta_', '', $k );
            // d($k,$value);
            if( is_array($value) ) {
                if( !$args['meta_query'] ) $args['meta_query'] = [];
                // d($k,$value);
                if( $value['min'] ) {
                    $args['meta_query'][] = [
                        'key' => $key,
                        'value' => $value['min'],
                        'type' => 'numeric',
                        'compare' => '>='
                    ];
                }
                if( $value['max'] ) {
                    $args['meta_query'][] = [
                        'key' => $key,
                        'value' => $value['max'],
                        'type' => 'numeric',
                        'compare' => '<='
                    ];
                }
            }
            else {
                if( !$args['meta_query'] ) $args['meta_query'] = [];
                $args['meta_query'][] = [
                    'key' => $key,
                    'value' => $value,
                    'compare' => 'EXISTS'
                ];
            }
        }
    }

    // taxonomy queries
    // dd($args);
    foreach( $data as $k=>$value ) {
        // d($k);
        if( !$value ) continue;
        $k = str_replace( 'tax_', 'taxonomy_', $k );
        if( strpos( $k, 'taxonomy_' ) === 0 ) {
            $key = str_replace( 'taxonomy_', '', $k );
            // d('ke',$k);
            $tax = get_taxonomy( $key );
            
            if( @!$args['tax_query'] ) $args['tax_query'] = [];
            $args['tax_query'][] = [
                'taxonomy' => $key,
                'field' => $tax->hierarchical ? 'term_id' : 'slug',
                'terms' => $value,
                'operator' => 'IN'
            ];
        }
    }
    $args = apply_filters( 'waf_post_args', $args );
    return  $args;
}