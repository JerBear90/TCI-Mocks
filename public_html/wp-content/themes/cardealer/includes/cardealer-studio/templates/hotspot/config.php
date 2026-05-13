<?php
defined( 'ABSPATH' ) || exit('restricted access');

return array(
    'title'            => esc_html__( 'Hotspot', 'cardealer' ), // Required
    'demo_url'         => '',
    'type'             => 'block',                                 // Required
    'category'         => array(                                   // Required
        esc_html__( 'Hotspot', 'cardealer' ),
    ),
    'tags'             => array(
        esc_html__( 'Hotspot', 'cardealer' ),
        esc_html__( 'feature', 'cardealer' ),
    ),
);
