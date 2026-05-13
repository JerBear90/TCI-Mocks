<?php

function waf_rewrites() {
    add_rewrite_tag( '%wafView%', '([^/]+)' );
    add_rewrite_rule('formEditor', 'index.php?wafView=index', 'top');
    // flush_rewrite_rules();
}
add_action( 'init', 'waf_rewrites', 1 );

function waf_include($template) {
    global $wp_query;
    // d($wp_query);
    $view = @$wp_query->query_vars['wafView'];
    if( $view ) {
        if( !is_user_logged_in() ) {
            // wp_redirect( get_bloginfo('url').'/wp-login.php?redirect_to='.get_bloginfo('url').'/formEditor');
            // exit;
        }
        $dir = plugin_dir_path( dirname(__FILE__) );
        $template = "$dir/views/$view.php";
    }
    return $template;
}
add_filter('template_include', 'waf_include' );