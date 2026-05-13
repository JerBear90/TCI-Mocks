<?php
/**
 * Sample data functions.
 *
 * @package cardealer/sample-data
 * @since   6.0.0
 */

/**
 * Get imported base sample.
 *
 * @return string
 */
function cardealer_get_imported_sample() {
	$imported_sample      = null;
	$imported_base_sample = get_option( 'cardealer_imported_base_sample', '' );

	if ( ! empty( $imported_base_sample ) ) {
		$imported_sample = $imported_base_sample;
	} else {
		$default_sample_data = get_option( 'pgs_default_sample_data' );
		if ( ! empty( $default_sample_data ) ) {
			$imported_samples = cardealer_json_decode( $default_sample_data, array( 'associative' => true ) );
			if ( ! empty( $imported_samples ) && is_array( $imported_samples ) ) {
				$imported_sample = $imported_samples[0];
				if ( 'default' === $imported_sample ) {
					$imported_sample = 'classic-vc';
				}elseif ( 'default-elementor' === $imported_sample ) {
					$imported_sample = 'classic-el';
				}
			}
		}
	}

	return $imported_sample;
}

/**
 * Get sample items.
 *
 * @return array
 */
function cardealer_get_samples_items() {
	$builder_type     = cardealer_get_default_page_builder();
	$samples          = cardealer_samples();
	$imported_sample  = cardealer_get_imported_sample();
	$filtered_samples = array();

	// Check if $imported_sample matches a specific key and add 'is_imported' accordingly.
	if (
		isset( $samples[ $imported_sample ] )
		&& 'base' === $samples[ $imported_sample ]['sample_type']
		&& $samples[ $imported_sample ]['builder_type'] === $builder_type
	) {
		$samples[ $imported_sample ]['is_imported'] = 'yes';

		$filtered_samples = array_filter(
			$samples,
			function( $sample, $key ) use ( $imported_sample ) {
				return (
					$key === $imported_sample
					|| (
						( isset( $sample['sample_type'] ) && 'sub_sample' === $sample['sample_type'] )
						&& ( isset( $sample['sample_parent'] ) && $sample['sample_parent'] === $imported_sample )
					)
				);
			},
			ARRAY_FILTER_USE_BOTH
		);
	} else {
		// If $imported_sample is not found, treat it as blank and return samples with sample_type = 'base'.
		$filtered_samples = array_filter(
			$samples,
			function( $sample ) use ( $builder_type ) {
				return isset( $sample['sample_type'] ) && 'base' === $sample['sample_type'] && $sample['builder_type'] === $builder_type;
			}
		);
	}

	return $filtered_samples;
}

if ( ! function_exists( 'cardealer_samples_set_data_paths' ) ) {
	/**
	 * Add "data_dir" and "data_url" paths in each sample data.
	 *
	 * @see cardealer_samples_set_data_paths()
	 *
	 * @param array  $item1 Item variable.
	 * @param string $key store key.
	 */
	function cardealer_samples_set_data_paths( &$item1, $key ) {
		if ( ! isset( $item1['data_dir'] ) ) {
			$sample_data_path  = get_parent_theme_file_path( 'includes/sample_data' );
			$item1['data_dir'] = trailingslashit( trailingslashit( $sample_data_path ) . str_replace( '-elementor', '', $key ) );
		}
		if ( ! isset( $item1['data_url'] ) ) {
			$sample_data_url   = get_parent_theme_file_uri( 'includes/sample_data' );
			$item1['data_url'] = trailingslashit( trailingslashit( $sample_data_url ) . str_replace( '-elementor', '', $key ) );
		}
	}
}
