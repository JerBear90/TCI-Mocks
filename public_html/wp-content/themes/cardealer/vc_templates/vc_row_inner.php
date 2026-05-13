<?php // phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * VC row inner file
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
 * @var $css
 * @var $el_id
 * @var $equal_height
 * @var $content_placement
 * @var $content - shortcode content
 * Shortcode class
 * @var $this WPBakeryShortCode_VC_Row_Inner
 */
$el_class                    = '';
$equal_height                = '';
$content_placement           = '';
$css                         = '';
$el_id                       = '';
$pgscore_background_position = '';
$overlay_html                = '';
$element_css_md              = '';
$element_css_sm              = '';
$element_css_xs              = '';
$disable_element             = '';
$output                      = '';
$after_output                = '';

$atts = vc_map_get_attributes( $this->getShortcode(), $atts );
extract( $atts ); // phpcs:ignore WordPress.PHP.DontExtract.extract_extract

$el_class    = $this->getExtraClass( $el_class );
$css_classes = array(
	'vc_row',
	'wpb_row',
	// deprecated.
	'vc_inner',
	'vc_row-fluid',
	$el_class,
	vc_shortcode_custom_css_class( $css ),
);

if ( isset( $element_css_md ) && ! empty( $element_css_md ) ) {
	$css_classes[] = vc_shortcode_custom_css_class( $element_css_md );
}
if ( isset( $element_css_sm ) && ! empty( $element_css_sm ) ) {
	$css_classes[] = vc_shortcode_custom_css_class( $element_css_sm );
}
if ( isset( $element_css_xs ) && ! empty( $element_css_xs ) ) {
	$css_classes[] = vc_shortcode_custom_css_class( $element_css_xs );
}

// MPC Fix.
// Remove Overlay settings if Massive Addons is active.
global $mpc_paths;
if ( $mpc_paths ) {
	$pgscore_enable_overlay = false;
	$pgscore_half_overlap   = false;
}

if ( empty( $pgscore_bg_type ) ) {
	$css_classes[] = 'row-background-light';
} else {
	$css_classes[] = $pgscore_bg_type;
}

if ( 'yes' === $disable_element ) {
	if ( vc_is_page_editable() ) {
		$css_classes[] = 'vc_hidden-lg vc_hidden-xs vc_hidden-sm vc_hidden-md';
	} else {
		return '';
	}
}

if ( vc_shortcode_custom_css_has_property(
	$css,
	array(
		'border',
		'background',
	)
) ) {
	$css_classes[] = 'vc_row-has-fill';
}

if ( ! empty( $atts['gap'] ) ) {
	$css_classes[] = 'vc_column-gap-' . $atts['gap'];
}

if ( ! empty( $equal_height ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-equal-height';
}

if ( ! empty( $content_placement ) ) {
	$flex_row      = true;
	$css_classes[] = 'vc_row-o-content-' . $content_placement;
}

if ( ! empty( $flex_row ) ) {
	$css_classes[] = 'vc_row-flex';
}
$wrapper_styles     = array();
$wrapper_attributes = array();
// build attributes for wrapper.
if ( ! empty( $el_id ) ) {
	$wrapper_attributes[] = 'id="' . ( ( preg_match( '/^\d/', $el_id ) === 1 ) ? esc_attr( 'vc_row_' . $el_id ) : esc_attr( $el_id ) ) . '"';
}


if ( ! empty( $pgscore_half_overlap ) && 'true' === $pgscore_half_overlap ) {
	$css_classes[] = 'vc_row-half_overlap';
};
$css_class            = preg_replace( '/\s+/', ' ', apply_filters( VC_SHORTCODE_CUSTOM_CSS_FILTER_TAG, implode( ' ', array_filter( array_unique( $css_classes ) ) ), $this->settings['base'], $atts ) );
$wrapper_attributes[] = 'class="' . esc_attr( trim( $css_class ) ) . '"';


if ( ! empty( $pgscore_background_position ) ) {
	$wrapper_styles['background-position'] = 'background-position:' . str_replace( '-', ' ', $pgscore_background_position ) . ' !important;';
}

if ( ! empty( $wrapper_styles ) && is_array( $wrapper_styles ) ) {
	$wrapper_styles       = implode( ' ', array_filter( array_unique( $wrapper_styles ) ) );
	$wrapper_attributes[] = 'style="' . esc_attr( $wrapper_styles ) . '"';
}
$wrapper_attributes = implode( ' ', array_filter( array_unique( $wrapper_attributes ) ) ); // WPCS: sanitization ok.

$output .= '<div ' . $wrapper_attributes . '>';

if ( ( isset( $pgscore_enable_overlay ) && 'true' === $pgscore_enable_overlay ) && ( isset( $pgscore_overlay_color ) && ! empty( $pgscore_overlay_color ) ) ) {
	$overlay_style = 'background-color:' . $pgscore_overlay_color . ';';
	$output       .= '<div class="vc_row-background-overlay" style="' . esc_attr( $overlay_style ) . '"></div>';
}

$output .= wpb_js_remove_wpautop( $content );
$output .= '</div>';
$output .= $after_output;

return $output;
