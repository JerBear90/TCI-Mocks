<?php
/**
 * Template Name: Compare Vehicles
 * Description: A page template that display cars to be campared.
 *
 * @package CarDealer
 * @author  Potenza Global Solutions
 */
$is_iframe = cardealer_is_iframe();

$compare_showcase = cardealer_is_compare_showcase();
$compare_ids      = cardealer_get_compare_ids();
$compare_data     = array();

if ( ! $is_iframe ) {
	get_template_part( 'template-parts/compare/header', null, array(
		'compare_showcase' => $compare_showcase,
	) );
} else {
	// get_header('compare');
	wp_head();
}

foreach ( $compare_ids as $vehicle_id ) {
	$vehicle_post = get_post( $vehicle_id );

	if ( empty( $vehicle_post ) ) {
		$vehicle_post = cardealer_post_faker( $vehicle_id, 'invalid' );
	}

	$compare_data[] = array(
		'vehicle_id' => $vehicle_id,
		'is_vehicle' => 'cars' === $vehicle_post->post_type,
		'post_title' => ( 'cars' === $vehicle_post->post_type ) ? $vehicle_post->post_title : esc_html__( 'Invalid Vehicle', 'cardealer' ),
		'post_type'  => $vehicle_post->post_type,
		'post'       => $vehicle_post,
	);
}

$compare_count = count( $compare_data );

if ( $compare_showcase && empty( $compare_data ) ) {
	get_template_part( 'template-parts/compare/no-compare-data' );
} else {
	if ( ! $compare_showcase ) {
		for ( $cols = 1; $cols <= 4; $cols++ ) {
			$random_id    = wp_generate_password( 8, false ); // Generate an 8-character random ID.
			$vehicle_post = cardealer_post_faker( $random_id, 'select_vehicle', array(
				'post_title' => esc_html__( 'Select Vehicle', 'cardealer' ),
			) );
			$compare_data[] = array(
				'vehicle_id' => $random_id,
				'is_vehicle' => 'cars' === $vehicle_post->post_type,
				'post_title' => $vehicle_post->post_title,
				'post_type'  => $vehicle_post->post_type,
				'post'       => $vehicle_post,
			);
		}
	}
	$column_counts   = count( $compare_data );

	$compare_content_tmpl = 'template-parts/compare/content-compare';

	get_template_part( $compare_content_tmpl, null, array(
		'compare_showcase' => $compare_showcase,
		'compare_ids'      => $compare_ids,
		'compare_count'    => $compare_count,
		'column_counts'    => $column_counts,
		'compare_data'     => $compare_data,
	) );
}

if ( ! $is_iframe ) {
	get_template_part( 'template-parts/compare/footer' );
} else {
	// get_footer('compare');
	wp_footer();
}
