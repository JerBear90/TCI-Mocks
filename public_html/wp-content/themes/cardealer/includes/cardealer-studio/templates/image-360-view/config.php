<?php
defined( 'ABSPATH' ) || exit('restricted access');

return array(
    'title'            => esc_html__( '360° View', 'cardealer' ), // Required
    'demo_url'         => '',
    'type'             => 'block',                                 // Required
    'category'         => array(                                   // Required
        esc_html__( 'Image 360 View', 'cardealer' ),
    ),
    'tags'             => array(
        esc_html__( 'Image 360 View', 'cardealer' ),
        esc_html__( 'feature', 'cardealer' ),
    ),
);