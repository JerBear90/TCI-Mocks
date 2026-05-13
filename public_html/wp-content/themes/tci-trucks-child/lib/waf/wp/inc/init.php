<?php
function waf_init() {
    // d('hello world');
    $rand = is_devel() ? rand(0,1000) : 19;
    
    wp_register_script( 'wsf-functions', WAFURL.'/src/old/functions.js?r='.$rand, array('jquery') );
    wp_register_script( 'jquery.form', WAFURL.'/src/old/jquery.form.js?r='.$rand, array('jquery') );
    wp_register_script( 'wsf', WAFURL.'/src/old/forms.js?r='.$rand, array('jquery', 'wsf-functions', 'jquery.form') );
    wp_register_style( 'wsf', WAFURL.'/src/old/forms.css?r='.$rand, array('jquery', 'wsf-functions', 'jquery.form') );

    wp_register_style( 'toastr', WAFURL.'/assets/toastr/toastr.min.css?r='.$rand );

    wp_register_style( 'waf-bootstrap', WAFURL.'/assets/css/bootstrap.min.css' );
    wp_register_style( 'waf-fa', WAFURL.'/assets/fontawesome/css/all.min.css' );
    
    wp_register_script( 'waf', WAFURL.'/assets/js/all.js?r='.$rand, array('jquery') );
    wp_register_script( 'waf-helpers', WAFURL.'/assets/js/helpers.js?r='.$rand, array('jquery') );
    wp_register_script( 'waf-simple', WAFURL.'/assets/js/simple.js?r='.$rand, array('jquery','waf-helpers') );

    wp_enqueue_script( 'waf-simple' );
    if( @$_GET['post'] && @$_GET['action'] == 'edit' ) {
        wp_enqueue_style('waf-bootstrap');
        wp_enqueue_script('waf-simple');
        // wp_enqueue_script('waf');
    }
    if( is_admin() ) return;
    wp_enqueue_style( 'waf-bootstrap' );
    wp_enqueue_style( 'waf-fa' );
    wp_enqueue_script( 'waf' );
    // wp_enqueue_script( 'waf', 'http://brainy:8080/app/js/bundle.js', ['jquery'] );

    wp_enqueue_style( 'toastr' );
}
add_action( 'init', 'waf_init' );