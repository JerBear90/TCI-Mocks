<?php
function rests_post_get_edit() {
    global $noDebug;
    $noDebug = 0;
    register_rest_route( 'waf/v1', 'posts/edit/(?P<id>[\d]+)', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'waf_get_edit_post',
            'permission_callback' => '__return_true',
            'args'                => array(

            ),
        )
    ));
}
add_action( 'rest_api_init', 'rests_post_get_edit' );

function waf_get_edit_post( $request ) {
    $data = $_REQUEST;
    $params = $request->get_params();
    $id = $params['_id'];
    // d($params);
    $_REQUEST['id'] = $id;
    // d($data, $post_type);
    $post = get_post( $id );
    
    $form = new form( $post->post_type );
    
    $form->args['method'] = 'put';
    $form->args['url'] = trailingslashit( $form->args['url'] ).$id;
    $fields = $form->allFields();
    
    
    foreach( $fields as $field ) {
        $value = '';
        // d($field->name);
        if( $field->context['type'] == 'fieldset' || $field->context['type'] == 'submit' ) continue;
        $k = $field->context['name'];
        // d($field->context);
        if ( $k == 'ID' ) {
            $value = $id;
        } elseif( $field->context['type'] == 'file' ) {
            $images = cann_get_post_images( $id );
            $value = [];
            foreach( $images as $image ) {
                $value[] = [
                    'id' => $image->ID,
                    'url' => wp_get_attachment_image_src( $image->ID )[0],
                    'name' => $image->post_title
                ];
            }
            // d('FILES:',$value);
        } elseif( strpos( $k, 'meta_' ) === 0 ) {
            $key = str_replace( 'meta_', '', $k );
            $value = get_post_meta( $id, $key, true );
        } elseif( $field->context['taxonomy'] ) {
            $key = $field->context['taxonomy'];
            $tax = get_taxonomy( $key );
            if ($tax ) {
                $f = $tax->hierarchical ? 'term_id' : 'slug';
                $terms = wp_get_post_terms( $id, $key );
                $value = wp_list_pluck( $terms, $f );

                // d('tax',$id,$key,$value);
            }
        } elseif( strpos( $k, 'post_') === 0 ) {
            $key = $k;
            $value = $post->$k;
            // d('post value',$k,$value);
        } elseif( $k != 'files' ) {
            $key = $k;
            $value = get_post_meta( $id, $k, true );
        }
        if( $value instanceof WP_Error ) {
            // d($k,$key,$wp->error['errors']);
        }elseif( $value ){
            if( is_object($field) ) $values[] = [ 'path' =>  $field->getPath(), 'value' => $value ];
            else d('no field:',$field);
        }
    }
    
    $values = apply_filters( 'waf_post_values', $values, $form, $id );
    // d($values);    
    // d($data);
    
    $response = apply_filters( 'waf_post_edit_form_response', [
        'status' => 'danger',
        'message' => g('unknown_error'),
        'selector' => 'body',
        'tabbed' => $form->args['tabbed'],
        'tabIcon' => $form->args['tabIcon'], 
        'legend' => $form->args['legend'],
        'form' => [
            'url' => '/wp-json/waf/v1/posts/'.$id,
            'method' => 'post',
            'id' => $form->args['form'],
            'values' => $values
        ]
    ], $post_type );
    // d('hello?');
    // d($post_type,$response['form']['values']);
    wp_send_json( $response );
}