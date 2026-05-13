<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * VC Column inner file
 *
 * @package CiyaShop
 */

if ( ! defined( 'ABSPATH' ) ) {
	wp_die( '-1' );
}

/**
 * Shortcode attributes
 *
 * @var $atts
 * @var $el_class
 * @var $el_id
 * @var $width
 * @var $css
 * @var $offset
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Column_Inner
 */
$el_class                    = '';
$width                       = '';
$el_id                       = '';
$css                         = '';
$offset                      = '';
$pgscore_background_position = '';
$overlay_html                = '';
$element_css_md              = '';
$element_css_sm              = '';
$element_css_xs              = '';
$output                      = '';
$atts                        = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$width = wpb_translateColumnWidthToSpan( $width );
$width = vc_column_offset_class_merge( $offset, $width );

$custom_css  = array();
$css_classes = array(
	$this->getExtraClass( $el_class ),
	'wpb_column',
	'vc_column_container',
	$width,
);

if ( isset( $element_css_md ) && ! empty( $element_css_md ) ) {
	$custom_css[] = vc_shortcode_custom_css_class( $element_css_md );
}
if ( isset( $element_css_sm ) && ! empty( $element_css_sm ) ) {
	$custom_css[] = vc_shortcode_custom_css_class( $element_css_sm );
}
if ( isset( $element_css_xs ) && ! empty( $element_css_xs ) ) {
	$custom_css[] = vc_shortcode_custom_css_class( $element_css_xs );
}

// MPC Fix.
// Remove Overlay settings if Massive Addons is active.
global $mpc_paths;
if ( $mpc_paths ) {
	$pgscore_enable_overlay = false;
	$pgscore_half_overlap   = false;
}

if ( empty( $pgscore_bg_type ) ) {
	$css_classes[] = 'col-background-light';
} else {
	$css_classes[] = $pgscore_bg_type;
}


if ( vc_shortcode_custom_css_has_property(
	$css,
	array(
		'border',
		'background',
	)
) ) {
	$css_classes[] = 'vc_col-has-fill';
}

if ( ! empty( $alignment ) ) {
	$css_classes[] = 'text-' . $alignment;
}


$wrapper_attributes = array();

$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( $css_classes ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';

if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . ( ( preg_match( '/^\d/', $el_id ) === 1 ) ? esc_attr( 'vc_col_' . $el_id ) : esc_attr( $el_id ) ) . '"';
}


if ( ! empty( $pgscore_half_overlap ) && 'true' === $pgscore_half_overlap ) {
	$css_classes[] = 'vc_row-half_overlap';
};

if ( ! empty( $pgscore_background_position ) ) {
	$wrapper_styles['background-position'] = 'background-position:' . str_replace( '-', ' ', $pgscore_background_position ) . ' !important;';
}

if ( ! empty( $wrapper_styles ) && is_array( $wrapper_styles ) ) {
	$wrapper_styles       = implode( ' ', array_filter( array_unique( $wrapper_styles ) ) );
	$wrapper_attributes[] = 'style="' . esc_attr( $wrapper_styles ) . '"';
}

$custom_css[] = trim( vc_shortcode_custom_css_class( $css ) );

$custom_css = implode( ' ', array_filter( array_unique( $custom_css ) ) );

$wrapper_attributes = implode( ' ', array_filter( array_unique( $wrapper_attributes ) ) ); // WPCS: sanitization ok.

$output .= '<div ' . $wrapper_attributes . '>';

if ( ( isset( $pgscore_enable_overlay ) && 'true' === $pgscore_enable_overlay ) && ( isset( $pgscore_overlay_color ) && ! empty( $pgscore_overlay_color ) ) ) {
	$overlay_style = 'background-color:' . $pgscore_overlay_color . ';';
	$output       .= '<div class="vc_row-background-overlay" style="' . esc_attr( $overlay_style ) . '"></div>';
}

$inner_column_class = 'vc_column-inner ' . esc_attr( $custom_css );

$output .= '<div class="' . $inner_column_class . '">';
$output .= '<div class="wpb_wrapper">';
$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';
$output .= '</div>';
$output .= '</div>';

return $output;
