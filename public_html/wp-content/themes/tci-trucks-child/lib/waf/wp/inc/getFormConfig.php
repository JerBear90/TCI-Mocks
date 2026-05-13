<?php
function getFormConfig( $admin=false, $inHeader=false ) {
    global $noDebug, $formConfiging;
    ob_start();
    $noDebug = 1;
    $formConfiging = 1;
    $path = dirname(dirname(__FILE__)); 
    $jsonPath = $path.'/json/';
    $templatePath = $path.'/templates/';
    $formsPath = $path.'/forms/';

    
    $formConfig = [
        'templates' => [],
        'forms' => [],
        // 'templates' => getDirContents( $templatePath ),
        // 'forms' => getDirContents( $formsPath ),
        'nonce' => wp_create_nonce( 'wp_rest' ),
        'eventUrl' => get_bloginfo('url').'/wp-json/waf/v1/events?_wpnonce='.wp_create_nonce('wp_rest'),
        'ajaxUrl' => get_admin_url().'/admin-ajax.php'
    ];
    // if( $admin ) $formConfig['json'] = getDirContents( $jsonPath );
    $jsonPaths = formTemplater::getJsonPaths();
    $templatePaths = formTemplater::getTemplatePaths();

    foreach( $jsonPaths as $dir ) {
        // d($dir);
        $forms = getDirContents( $dir );
        $formConfig['forms'] = array_merge( $forms, $formConfig['forms'] );
    }

    if( !$inHeader ) {
        foreach( $templatePaths as $dir ) {
            $templates = getDirContents( $dir );
            unset($templates['classes']);
            $formConfig['templates'] = array_merge_recursive( $templates, $formConfig['templates'] );
        }
        foreach( $formConfig['templates'] as $type=>$templates ) {
            if( is_array($templates) ) foreach( $templates as $t=>$template ) {
                
                if( is_array($template) ) $formConfig['templates'][$type][$t] = array_pop( $template );
                // if( $t == 'pricing-table' ) d($type,$t,$formConfig['templates'][$type][$t]);
            }
        }
    }
    // d('forms:',$formConfig['forms']);
    foreach( $formConfig['forms'] as $f=>$data ) {
        if( !is_array($data) ) {
            unset( $formConfig['forms'][$f] );
            continue;
        }
        
        if( @$inHeader && !@$data['args']['headerConfig'] ) {
            d("-- unset $f");
            unset( $formConfig['forms'][$f] );
            continue;
        }
        
        $id = @$data['args']['form'] ? $data['args']['form'] : $f;

        $data = apply_filters( 'waf_form_data', $data, $data['args'] );
        // d("FORM:.$f",$data);
        $data = apply_filters( 'waf_'.$id.'_form_data', $data, $data['args'] );
// d($data);
        $data['args'] = apply_filters( 'waf_form_args', $data['args'], $data );
        $data['args'] = apply_filters( 'waf_'.$id.'_form_args', $data['args'], $data );
        // d('waf_'.$id.'_form_args');

        // $data = apply_filters( 'waf_formConfig_data', $data );
        $formConfig['forms'][$f] = $data;
    }
    // echo json_encode( $formConfig['forms']['edit-user']['args'] );
    $noDebug = 0;
    ob_end_clean();
    
    return $formConfig;
}

?>