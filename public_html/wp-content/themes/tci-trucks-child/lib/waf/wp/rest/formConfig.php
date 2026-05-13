<?php
function form_config_rest_api() {
    $namespace = WAFNAMESPACE;
    register_rest_route( $namespace, '/formConfig.js', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'form_config_js',
            'permission_callback' => '__return_true'
        )
    ) );

    register_rest_route( WAFNAMESPACE, '/formConfig', array(
        array(
            'methods'             => WP_REST_Server::READABLE,
            'callback'            => 'form_config_rest',
            'permission_callback' => '__return_true',
            'args'                => array(

            )
        )
    ) );
}
add_action( 'rest_api_init', 'form_config_rest_api' );

function form_config_js( $request ) {
    header('Content-type: text/javascript');
    ?>
    var formConfig = <?php echo json_encode( getFormConfig() ); ?>
    <?php
    die;
}
function form_config_rest() {
    header('Content-type: application/json'); 
    ?>
    <?php echo json_encode( getFormConfig() ); ?>
    <?php
    die;
}