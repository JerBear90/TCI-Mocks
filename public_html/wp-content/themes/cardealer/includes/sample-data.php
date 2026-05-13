<?php
require_once trailingslashit( CARDEALER_PATH ) . 'includes/sample_data/sample-data-items.php';
require_once trailingslashit( CARDEALER_PATH ) . 'includes/sample_data/sample-data-additional-items.php';
require_once trailingslashit( CARDEALER_PATH ) . 'includes/sample_data/sample-data-functions.php';

add_filter( 'redux/options/car_dealer_options/sections', 'cardealer_sample_data_v6_notice' );
function cardealer_sample_data_v6_notice( array $sections ): array {
	if ( defined( 'CDHL_VERSION' ) && version_compare( CDHL_VERSION, '6.0.0', '<' ) ) {
		foreach ( $sections as $index => $section ) {
			if ( isset( $section['id'] ) && 'sample_data' === $section['id'] ) {
				$update_url = add_query_arg(
					array(
						'page'          => 'theme-plugins',
						'plugin_status' => 'update',
					),
					admin_url( 'themes.php' )
				);
				ob_start();
				?>
				<span class="notice-title" style="padding-top: 0;float: left;margin-top: 0;font-weight: 600;margin-bottom: 0;"><?php echo esc_html__( 'Important Notice', 'cardealer' ); ?></span>
				<span style="margin: 0.5em 0.5em 0 0;clear: both;float: left;"><?php echo esc_html__( 'Update bundle plugin to latest provided version to display the sample data.', 'cardealer' ); ?></span>
				<span style="margin: 0.5em 0.5em 0 0;clear: both;float: left;"><a href="<?php echo esc_url( $update_url ); ?>"><?php echo esc_html__( 'Begin updating plugins', 'cardealer' ); ?></a></span>
				<?php
				$notice = ob_get_clean();
				$section['fields'] = array(
					array(
						'id'       => 'sample-data-v6-notice',
						'type'     => 'info',
						'style'    => 'critical',
						'title'    => '',
						'titlex'    => esc_html__( 'Important Note', 'cardealer' ),
						'desc'     => $notice,
					),
				);
				$sections[ $index ] = $section;
			}
		}
	}

	return $sections;
}

