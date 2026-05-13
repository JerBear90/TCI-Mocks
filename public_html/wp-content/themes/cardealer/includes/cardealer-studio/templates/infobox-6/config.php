<?php
defined( 'ABSPATH' ) || exit('restricted access');

return array(
    'title'            => esc_html__( 'Infobox 6', 'cardealer' ), // Required
    'demo_url'         => '',
    'type'             => 'block',                                 // Required
    'category'         => array(                                   // Required
        esc_html__( 'Infobox New', 'cardealer' ),
    ),
    'tags'             => array(
        esc_html__( 'Infobox', 'cardealer' ),
        esc_html__( 'feature', 'cardealer' ),
    ),
);
