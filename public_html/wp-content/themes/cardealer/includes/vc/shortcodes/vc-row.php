<?php
/**
 * VC row
 *
 * @package CiyaShop
 */
add_action( 'init', 'ciyashop_extend_vc_row', 1000 );
/**
 * Extend vc row
 */
function ciyashop_extend_vc_row() {
	if ( ! function_exists( 'vc_add_params' ) ) {
		return;
	}

	// Get shortcode config.
	$shortcode_new = vc_get_shortcode( 'vc_row' );

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

	foreach ( $shortcode_new['params'] as $param_key => $param_data ) {
		if ( array_key_exists( $param_data['param_name'], $replacing_params ) ) {
			$shortcode_new['params'][ $param_key ] = $replacing_params[ $param_data['param_name'] ];
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
		array(
			'type'       => 'dropdown',
			'heading'    => esc_html__( 'Select Row Background Type', 'cardealer' ),
			'param_name' => 'cd_bg_type',
			'value'      => array(
				esc_attr__( 'Light background', 'cardealer' ) => 'row-background-light',
				esc_attr__( 'Dark background', 'cardealer' )  => 'row-background-dark',
			),
			'group'      => esc_attr__( 'Background options', 'cardealer' ),
		),
	);

	// Overlay Options.
	$overlay_options = array(
		array(
			'type'       => 'checkbox',
			'heading'    => esc_html__( 'Enable Overlay?', 'cardealer' ),
			'param_name' => 'cd_enable_overlay',
			'group'      => esc_attr__( 'Background options', 'cardealer' ),
		),
		array(
			'type'        => 'colorpicker',
			'heading'     => esc_html__( 'Overlay color', 'cardealer' ),
			'param_name'  => 'cd_overlay_color',
			'description' => esc_html__( 'Select overlay color.', 'cardealer' ),
			'dependency'  => array(
				'element' => 'cd_enable_overlay',
				'value'   => 'true',
			),
			'group'       => esc_attr__( 'Background options', 'cardealer' ),
		),
		array(
			'type'        => 'cd_number_min_max',
			'heading'     => esc_html__( 'Overlay Opacity', 'cardealer' ),
			'param_name'  => 'cd_overlay_opacity',
			'value'       => '80',
			'min'         => '0',
			'max'         => '100',
			'suffix'      => '%',
			'group'       => esc_attr__( 'Background options', 'cardealer' ),
			'dependency'  => array(
				'element' => 'cd_enable_overlay',
				'value'   => 'true',
			),
			'description' => esc_html__( 'Enter value between 0 to 100 (0 is maximum transparency, while 100 is minimum)', 'cardealer' ),
		),
	);

	// Half Overlay Options.
	$half_overlap_options = array(
	);

	// MPC Fix
	// Remove Overlay settings if Massive Addons is active.
	global $mpc_paths;
	if ( $mpc_paths ) {
		$overlay_options      = array();
		$half_overlap_options = array();
	}

	// Merge Parameters.
	$shortcode_new['params'] = array_merge(
		$shortcode_new['params'],
		$design_options,
		$background_options,
		$overlay_options,
		$half_overlap_options
	);

	// VC doesn't like even the thought of you changing the shortcode base, and errors out, so we unset it.
	unset( $shortcode_new['base'] );

	// Update the actual parameter.
	vc_map_update( 'vc_row', $shortcode_new );
}
