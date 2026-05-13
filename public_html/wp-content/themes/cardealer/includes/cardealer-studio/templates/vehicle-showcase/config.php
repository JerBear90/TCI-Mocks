<?php
defined( 'ABSPATH' ) || exit('restricted access');

return array(
    'title'            => esc_html__( 'Vehicle Showcase 1', 'cardealer' ), // Required
    'demo_url'         => '',
    'type'             => 'block',                                 // Required
    'category'         => array(                                   // Required
        esc_html__( 'Vehicle Showcase', 'cardealer' ),
    ),
    'tags'             => array(
        esc_html__( 'Vehicle Showcase', 'cardealer' ),
        esc_html__( 'feature', 'cardealer' ),
    ),
);