<?php
function get_form_metalist( $formdata, $data=[] ) {
    if( is_string($formdata) ) $formdata = get_formdata($formdata);
    foreach( $formdata as $f=>$field ) {
        if( $f == 'args' || $f == 'submit' || @$field['type'] == 'hidden' || !@is_array($field) ) continue;
        if( @$field['fields'] ) {
            $data = get_form_metalist( $field['fields'], $data );
        }
        elseif( !@$field['private'] ) {
            if( @$field['options'] && @$field['value'] ) {
                $key = $field['value'];
                if( @$field['post_type'] ) {
                    $link = get_permalink( $field['value'] );
                    $title = get_the_title( $field['value'] );
                    $field['value'] = '<a href="'.$link.'">'.$title.'</a>';
                } elseif( isset($field['options'][$key]) ) {
                    $field['value'] = $field['options'][$key];
                }
                
            }
            $data[$f] = [
                'label' => @$field['label'],
                'value' => @$field['value']
            ];
        }
    }
    return $data;
}