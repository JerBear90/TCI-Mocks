<?php
function postlist_field( $field ){
    if( @$field['author'] == 'me' ) $field['author'] = get_current_user_id();
    $args = shortcode_atts([
        'post_type' => 'post',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'author' => ''
    ],$field);
    aad('Field',$field['label']);
    aad('Retrieving posts:',$args);
    $posts = get_posts( $args );
    // d('posts',count($posts));
    $field['type'] = 'select';
    $field['placeholder'] = '--';
    foreach( $posts as $post ) {
        $id = $post->ID;
        $title = $post->post_title;
        $field['options'][$id] = $title;
    }
    return $field;
}
add_filter( 'waf_postlist_field_data', 'postlist_field', 11 );
add_filter( 'waf_post_field_data', 'postlist_field', 11 );