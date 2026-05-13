<?php
function emt_options( $options ) {
    $options['smtp'] = [
        'title' => __( 'SMTP/Email Settings', 'jasper' ),
        'header' => [
            'type' => 'html',
            'html' => '<h3>'.__( 'Email SETTINGS', 'jasper' )
        ],
        'email_from_name' => [
            'label' => __( 'Email From Name', 'jasper' )
        ],
        'email_from_email' => [
            'label' => __( 'Email From Address', 'jasper' )
        ],
        'email_reply_to' => [
            'label' => __( 'Reply to address', 'jasper' )
        ],
        'header' => [
            'type' => 'html',
            'html' => '<h3>'.__( 'SMTP SETTINGS', 'jasper' )
        ],
        'smtp_enable' => [
            'type' => 'checkbox',
            'label' => __( 'Use SMTP Auth', 'jasper' )
        ],
        'smtp_secure' => [
            'label' => __( 'Use Secure SMTP', 'jasper' ),
            'type' => "checkbox"
        ],
        'smtp_host' => [
            'label' => __( 'SMTP Host', 'jasper' ),
        ],
        // 'smtp_port' => [
        //     'label' => __( 'SMTP Port', 'jasper' ),
        // ],
        'smtp_username' => [
            'label' => __( 'SMTP Username', 'jasper' ),
        ],
        'smtp_password' => [
            'label' => __( 'SMTP Password', 'jasper' ),
            'type' => 'password',
            'desc' => __( 'This should not be a personal email account', 'jasper' )
        ],
    ];
    return $options;
}
add_filter( 'theme_options', 'emt_options', 20 );