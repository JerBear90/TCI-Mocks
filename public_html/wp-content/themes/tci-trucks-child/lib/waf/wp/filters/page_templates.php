<?php
function waf_page_template_field_data( $data ) {
    $templates = get_page_templates();
    $data['placeholder'] = __( '(None)', 'r9tv' );
    foreach( $templates as $template=>$t ) {
        $options[$t] = $template;
        
    }
    d(json_encode($options));
    $data['options'] = $options;
    $data['type'] = 'select';
    return $data;
}
add_filter( 'waf_page_template_field_data', 'waf_page_template_field_data' );