<?php
add_action( 'init', 'cardealer_extend_vc_shortcodes' );
/**
 * Extend vc shortcodes
 */
function cardealer_extend_vc_shortcodes() {
	require_once trailingslashit( CARDEALER_PATH ) . 'includes/vc/shortcodes/vc-row.php';
	require_once trailingslashit( CARDEALER_PATH ) . 'includes/vc/shortcodes/vc-row-inner.php';
	require_once trailingslashit( CARDEALER_PATH ) . 'includes/vc/shortcodes/vc-column.php';
	require_once trailingslashit( CARDEALER_PATH ) . 'includes/vc/shortcodes/vc-column-inner.php';
}

add_filter( 'vc_base_build_shortcodes_custom_css', 'ciyashop_parse_vc_shortcodes_custom_css', 12, 3 );
/**
 * Parse vc shortcodes custom css
 *
 * @param string $content .
 * @param string $post_id .
 * @param bool   $recur .
 */
function ciyashop_parse_vc_shortcodes_custom_css( $content, $post_id, $recur = false ) {
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}

	if ( ! $recur ) {
		$post = get_post( $post_id );
		if ( $post ) {
			$content = $post->post_content;
		}
	}

	$css = '';
	if ( ! preg_match( '/\s*(\.[^\{]+)\s*\{\s*([^\}]+)\s*\}\s*/', $content ) ) {
		return $css;
	}
	WPBMap::addAllMappedShortcodes();
	preg_match_all( '/' . get_shortcode_regex() . '/', $content, $shortcodes );

	foreach ( $shortcodes[2] as $index => $tag ) {

		$shortcode  = WPBMap::getShortCode( $tag );
		$attr_array = shortcode_parse_atts( trim( $shortcodes[3][ $index ] ) );

		if ( isset( $shortcode['params'] ) && ! empty( $shortcode['params'] ) ) {
			foreach ( $shortcode['params'] as $param ) {
				if ( isset( $param['type'] ) && 'css_editor' === $param['type'] && isset( $attr_array[ $param['param_name'] ] ) ) {
					if ( in_array( (string) $param['param_name'], array( 'element_css_md', 'element_css_sm', 'element_css_xs' ), true ) ) {
						continue;
					}
					$css .= $attr_array[ $param['param_name'] ];
				}
			}
		}

		if ( isset( $attr_array['cd_enable_responsive_settings'] ) && filter_var( $attr_array['cd_enable_responsive_settings'], FILTER_VALIDATE_BOOLEAN ) ) {
			if ( 'vc_row' === (string) $tag ) {
				if ( isset( $attr_array['element_css_md'] ) && ! empty( $attr_array['element_css_md'] ) ) {
					$css .= '@media (max-width: 1200px) {' . $attr_array['element_css_md'] . '}';
				}
				if ( isset( $attr_array['element_css_sm'] ) && ! empty( $attr_array['element_css_sm'] ) ) {
					$css .= '@media (max-width: 992px) {' . $attr_array['element_css_sm'] . '}';
				}
				if ( isset( $attr_array['element_css_xs'] ) && ! empty( $attr_array['element_css_xs'] ) ) {
					$css .= '@media (max-width: 767px) {' . $attr_array['element_css_xs'] . '}';
				}
			}
			if ( 'vc_row_inner' === (string) $tag ) {
				if ( isset( $attr_array['element_css_md'] ) && ! empty( $attr_array['element_css_md'] ) ) {
					$css .= '@media (max-width: 1200px) {' . $attr_array['element_css_md'] . '}';
				}
				if ( isset( $attr_array['element_css_sm'] ) && ! empty( $attr_array['element_css_sm'] ) ) {
					$css .= '@media (max-width: 992px) {' . $attr_array['element_css_sm'] . '}';
				}
				if ( isset( $attr_array['element_css_xs'] ) && ! empty( $attr_array['element_css_xs'] ) ) {
					$css .= '@media (max-width: 767px) {' . $attr_array['element_css_xs'] . '}';
				}
			}
			if ( 'vc_column' === (string) $tag ) {
				if ( isset( $attr_array['element_css_md'] ) && ! empty( $attr_array['element_css_md'] ) ) {
					$css .= '@media (max-width: 1200px) {' . $attr_array['element_css_md'] . '}';
				}
				if ( isset( $attr_array['element_css_sm'] ) && ! empty( $attr_array['element_css_sm'] ) ) {
					$css .= '@media (max-width: 992px) {' . $attr_array['element_css_sm'] . '}';
				}
				if ( isset( $attr_array['element_css_xs'] ) && ! empty( $attr_array['element_css_xs'] ) ) {
					$css .= '@media (max-width: 767px) {' . $attr_array['element_css_xs'] . '}';
				}
			}
			if ( 'vc_column_inner' === (string) $tag ) {
				if ( isset( $attr_array['element_css_md'] ) && ! empty( $attr_array['element_css_md'] ) ) {
					$css .= '@media (max-width: 1200px) {' . $attr_array['element_css_md'] . '}';
				}
				if ( isset( $attr_array['element_css_sm'] ) && ! empty( $attr_array['element_css_sm'] ) ) {
					$css .= '@media (max-width: 992px) {' . $attr_array['element_css_sm'] . '}';
				}
				if ( isset( $attr_array['element_css_xs'] ) && ! empty( $attr_array['element_css_xs'] ) ) {
					$css .= '@media (max-width: 767px) {' . $attr_array['element_css_xs'] . '}';
				}
			}
		}
	}

	foreach ( $shortcodes[5] as $shortcode_content ) {
		$css .= ciyashop_parse_vc_shortcodes_custom_css( $shortcode_content, $post_id, $recur = true );
	}

	return $css;
}
