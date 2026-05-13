<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Extend vc column inner
 *
 * @package CiyaShop
 */

add_action( 'init', 'ciyashop_extend_vc_column_inner', 1000 );
/**
 * Extend vc column inner
 */
function ciyashop_extend_vc_column_inner() {
	if ( ! function_exists( 'vc_add_params' ) ) {
		return;
	}

	// Get shortcode config.
	$shortcode_tmp = vc_get_shortcode( 'vc_column_inner' );

	$replacing_params = array(
		'css'   => array(
			'type'       => 'css_editor',
			'heading'    => esc_html__( 'Large Devices (&#8805;1200px)', 'cardealer' ),
			'param_name' => 'css',
			'group'      => esc_html__( 'Design Options', 'cardealer' ),
		),
		'el_id' => array(
			'type'        => 'el_id',
			'heading'     => esc_html__( 'Row ID', 'cardealer' ),
			'param_name'  => 'el_id',
			'description' => sprintf(
				/* translators: $s: Set Link */
				esc_html__( 'Enter row ID (Note: make sure it is unique and valid according to <a href="%s" target="_blank">w3c specification</a>)', 'cardealer' ),
				'http://www.w3schools.com/tags/att_global_id.asp'
			)
			. '<br><span class="ciyashop-red">Important : If Row ID starts with number (while generated automatically), it will be prefixed with "<strong>vc_row_</strong>".</span>',
			'settings'    => array(
				'auto_generate' => true,
			),
		),
	);

	foreach ( $shortcode_tmp['params'] as $param_key => $param_data ) {
		if ( array_key_exists( $param_data['param_name'], $replacing_params ) ) {
			$shortcode_tmp['params'][ $param_key ] = $replacing_params[ $param_data['param_name'] ];
		}
	}

	// Design Options.
	$design_options = array(
		array(
			'type'        => 'checkbox',
			'heading'     => esc_html__( 'Enable Responsive Settings?', 'cardealer' ),
			'param_name'  => 'cd_enable_responsive_settings',
			'group'       => esc_attr__( 'Design Options', 'cardealer' ),
			'description' => esc_html__( 'Select this checkbox to set different value in responsive views. If any value is not passed in responsive settings, it will use default or value set from it\'s higher settings.', 'cardealer' ),
		),
		array(
			'type'             => 'css_editor',
			'heading'          => esc_html__( 'Medium Devices (&#8805;992px)', 'cardealer' ),
			'param_name'       => 'element_css_md',
			'group'            => esc_attr__( 'Design Options', 'cardealer' ),
			'abc'              => 'xyz',
			'edit_field_class' => 'css_editor_padding_margin_border',
			'dependency'       => array(
				'element' => 'cd_enable_responsive_settings',
				'value'   => 'true',
			),
		),
		array(
			'type'             => 'css_editor',
			'heading'          => esc_html__( 'Small Devices (&#8805;768px)', 'cardealer' ),
			'param_name'       => 'element_css_sm',
			'group'            => esc_attr__( 'Design Options', 'cardealer' ),
			'edit_field_class' => 'css_editor_padding_margin_border',
			'dependency'       => array(
				'element' => 'cd_enable_responsive_settings',
				'value'   => 'true',
			),
		),
		array(
			'type'             => 'css_editor',
			'heading'          => esc_html__( 'Extra Small Devices (<768px)', 'cardealer' ),
			'param_name'       => 'element_css_xs',
			'group'            => esc_attr__( 'Design Options', 'cardealer' ),
			'edit_field_class' => 'css_editor_padding_margin_border',
			'dependency'       => array(
				'element' => 'cd_enable_responsive_settings',
				'value'   => 'true',
			),
		),
	);

	// Background Options.
	$background_options = array(
	);

	// Overlay Options.
	$overlay_options = array(
	);

	// Overlay Options.
	$half_overlap_options = array(
	);

	// Text alignment.
	$text_alignment = array(
	);

	// MPC Fix
	// Remove Overlay settings if Massive Addons is active.
	global $mpc_paths;
	if ( $mpc_paths ) {
		$overlay_options      = array();
		$half_overlap_options = array();
		$text_alignment       = array();
	}

	// Merge Parameters.
	$shortcode_tmp['params'] = array_merge(
		$shortcode_tmp['params'],
		$design_options,
		$background_options,
		$text_alignment,
		$overlay_options,
		$half_overlap_options
	);

	// VC doesn't like even the thought of you changing the shortcode base, and errors out, so we unset it.
	unset( $shortcode_tmp['base'] );

	// Update the actual parameter.
	vc_map_update( 'vc_column_inner', $shortcode_tmp );
}
