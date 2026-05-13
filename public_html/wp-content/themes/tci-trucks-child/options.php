<?php
function tci_options( $options ) {
    return [
        'general' => [
            'title' => 'General',
            'trucks_feed' => [
                'label' => 'Used Trucks Feed',
                'std' => 'https://sales.tcitransportation.com/wp-json/wp/v2/cars?_embed'
            ],
            'jobs_feed' => [
                'label' => 'Latest Jobs Feed',
                'std' => 'https://careers.tcitransportation.com/wp-json/tci/v1/jobs'
            ],
            'job_site' => [
                'label' => 'Jobs Site Url',
                'std' => 'https://careers.tcitransportation.com'
            ]
        ]
    ];
}
add_filter( 'theme_options', 'tci_options' );