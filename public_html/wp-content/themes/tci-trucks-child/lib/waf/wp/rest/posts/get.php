<?php

function rests_post_get() {
    register_rest_route( 'waf/v1', 'posts/(?P<post_type>[^/]+)', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'waf_get_posts',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
    register_rest_route( 'waf/v1', 'posts', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'waf_get_posts',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rests_post_get' );

add_action( 'pre_get_posts', function($args) {
    // d($args);
    return $args;
});
function waf_get_posts( $request ) {
    $request_data = $request->get_params();
    // d($_REQUEST);
    
    $params = $request->get_params();
    $request_data['post_type'] = $params['post_type'];
    // d($request_data, $post_type);
    // die;
    
    // Process meta queries
    $args = waf_get_query_args( $request_data );
    // d($args);
    ob_start();
    $q = new WP_Query($args);
    // d('posts:',$q->have_posts(),'all:',$q->posts);
    
    // d($_REQUEST);
    // dd('args',$args);
    
//  d($args);
    if( @$request_data['template'] ) $request_data['template'] = str_replace( '.php', '', $request_data['template'] );
    if( @$request_data['columns'] ) $column_class = 'mb-3 col-md-'.(12/$request_data['columns']);
    if( @$column_class ) $request_data['wrap'] = 'row';

    
    if( $q->have_posts() ) {
        echo apply_filters( 'waf_response_text', "<h3>Found $q->found_posts Results</h3>", $q );
        while( $q->have_posts() ) : $q->the_post();
            $post_type = get_post_type();
            if( $request_data['template'] ) {
                $path = locate_template( $request_data['template'].'.php', false );
                // d($path);
                $template = locate_template( $request_data['template'].'.php', false );
            }
            else $template = locate_template( 'archive-item-'.$post_type.'.php', false );
            $template = apply_filters( 'waf_search_item', $template, $post_type );
            // d('template',$template,$post_type);
            if( file_exists($template ) ) include $template;
            else d("-- template not found for ",$post_type, $template);
        endwhile;
        wp_pagenavi( array( 'query' => $q ) );
    } else {
        echo '<div id="noposts" class="alert alert-info text-center w-100 align-self-start">'.__('No Posts Found','waf').'</div>';
    }
    $html = ob_get_contents();
    ob_end_clean();
    // echo $html;
    //    d($request_data);
    //    die;
    if( !trim($html) ) $html = '<div class="alert alert-primary">'.__( 'No Results Found', 'waf' ).'</div>';
    $response = apply_filters( 'get_post_response', [
        'status' => 'success',
        'message' => g('unknown_error'),
        'selector' => @$request_data['selector'] ? $request_data['selector'] : '#postlist',
        'html' => $html,
        'total' => (int)$q->found_posts,
        'page_count' => (int)$q->max_num_pages
    ], @$request_data['post_type'], $q );    
    $show = $response;
    unset($show['html']);
    // d($show);
    // d($response);
    wp_send_json($response);
}