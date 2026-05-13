<?php
/**
 * PHPUnit bootstrap file for CarDealer Theme tests.
 *
 * Loads Composer autoload, initializes WP_Mock, defines required constants,
 * and stubs WordPress/theme functions so class-cardealer-assets.php can be
 * loaded without a live WordPress environment.
 */

// Load Composer autoloader.
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

// Initialize WP_Mock.
WP_Mock::bootstrap();

// Define WordPress/theme constants used by CarDealer_Assets.
if ( ! defined( 'CARDEALER_URL' ) ) {
	define( 'CARDEALER_URL', 'https://example.com/wp-content/themes/cardealer' );
}

if ( ! defined( 'CARDEALER_VERSION' ) ) {
	define( 'CARDEALER_VERSION', '4.0.0' );
}

if ( ! defined( 'SCRIPT_DEBUG' ) ) {
	define( 'SCRIPT_DEBUG', false );
}

/*
 * Stub theme helper functions that are called during file load / constructor.
 * These are defined in other theme files that we don't load in tests.
 */
if ( ! function_exists( 'cardealer_get_featured_vehicles_list_style' ) ) {
	function cardealer_get_featured_vehicles_list_style() {
		return 'style-1';
	}
}

if ( ! function_exists( 'cardealer_campare_page_url' ) ) {
	function cardealer_campare_page_url() {
		return '/compare/';
	}
}

if ( ! function_exists( 'cardealer_campare_type' ) ) {
	function cardealer_campare_type() {
		return 'cookie';
	}
}

if ( ! function_exists( 'cardealer_vehicle_compare_select_vehicles_post_per_page' ) ) {
	function cardealer_vehicle_compare_select_vehicles_post_per_page() {
		return 10;
	}
}

if ( ! function_exists( 'cardealer_get_google_maps_api_key' ) ) {
	function cardealer_get_google_maps_api_key() {
		return '';
	}
}

if ( ! function_exists( 'cardealer_get_goole_api_keys' ) ) {
	function cardealer_get_goole_api_keys( $key = '' ) {
		return '';
	}
}

if ( ! function_exists( 'cardealer_is_year_range_active' ) ) {
	function cardealer_is_year_range_active() {
		return false;
	}
}

if ( ! function_exists( 'cardealer_get_cars_currency_symbol' ) ) {
	function cardealer_get_cars_currency_symbol() {
		return '$';
	}
}

if ( ! function_exists( 'cardealer_get_cars_currency_placement' ) ) {
	function cardealer_get_cars_currency_placement() {
		return 'left';
	}
}

if ( ! function_exists( 'cardealer_cars_filter_methods' ) ) {
	function cardealer_cars_filter_methods() {
		return 'ajax';
	}
}

if ( ! function_exists( 'cardealer_is_tax_page' ) ) {
	function cardealer_is_tax_page() {
		return false;
	}
}

if ( ! function_exists( 'cardealer_get_blog_layout' ) ) {
	function cardealer_get_blog_layout() {
		return 'standard';
	}
}

if ( ! function_exists( 'cardealer_get_banner_type' ) ) {
	function cardealer_get_banner_type() {
		return 'image';
	}
}

if ( ! function_exists( 'cardealer_get_video_link' ) ) {
	function cardealer_get_video_link() {
		return '';
	}
}

/*
 * Stub WordPress core functions that are called at file-load time
 * (inside get_scripts/get_styles arrays and the constructor).
 */
if ( ! function_exists( 'add_query_arg' ) ) {
	function add_query_arg( $args, $url = '' ) {
		if ( is_array( $args ) ) {
			$query = http_build_query( $args );
			return $url . '?' . $query;
		}
		return $url;
	}
}

if ( ! function_exists( 'get_option' ) ) {
	function get_option( $option, $default = false ) {
		return $default;
	}
}

if ( ! function_exists( 'admin_url' ) ) {
	function admin_url( $path = '' ) {
		return 'https://example.com/wp-admin/' . ltrim( $path, '/' );
	}
}

if ( ! function_exists( 'wp_create_nonce' ) ) {
	function wp_create_nonce( $action = '' ) {
		return 'test_nonce_' . $action;
	}
}

if ( ! function_exists( 'esc_html__' ) ) {
	function esc_html__( $text, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( '_x' ) ) {
	function _x( $text, $context, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'esc_html_x' ) ) {
	function esc_html_x( $text, $context, $domain = 'default' ) {
		return $text;
	}
}

if ( ! function_exists( 'add_filter' ) ) {
	function add_filter( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'add_action' ) ) {
	function add_action( $tag, $callback, $priority = 10, $accepted_args = 1 ) {
		return true;
	}
}

if ( ! function_exists( 'apply_filters' ) ) {
	function apply_filters( $tag, $value ) {
		return $value;
	}
}

if ( ! function_exists( 'is_rtl' ) ) {
	function is_rtl() {
		return false;
	}
}

if ( ! function_exists( 'did_action' ) ) {
	function did_action( $tag ) {
		return 0;
	}
}

if ( ! function_exists( 'sanitize_text_field' ) ) {
	function sanitize_text_field( $str ) {
		return $str;
	}
}

if ( ! function_exists( 'wp_unslash' ) ) {
	function wp_unslash( $value ) {
		return $value;
	}
}

if ( ! function_exists( 'rawurlencode' ) ) {
	// rawurlencode is a PHP built-in, but just in case.
}

if ( ! function_exists( 'home_url' ) ) {
	function home_url( $path = '' ) {
		return 'https://example.com' . $path;
	}
}

if ( ! function_exists( 'wp_enqueue_script' ) ) {
	function wp_enqueue_script( $handle ) {
		return true;
	}
}

if ( ! function_exists( 'is_admin' ) ) {
	function is_admin() {
		return false;
	}
}

if ( ! function_exists( 'get_permalink' ) ) {
	function get_permalink( $post = 0 ) {
		return 'https://example.com/?p=' . $post;
	}
}

if ( ! function_exists( 'get_post_type_archive_link' ) ) {
	function get_post_type_archive_link( $post_type ) {
		return 'https://example.com/' . $post_type . '/';
	}
}

if ( ! function_exists( 'get_taxonomy' ) ) {
	function get_taxonomy( $taxonomy ) {
		return false;
	}
}

if ( ! function_exists( 'is_tax' ) ) {
	function is_tax( $taxonomy = '' ) {
		return false;
	}
}

if ( ! function_exists( 'cardealer_get_default_sort_by' ) ) {
	function cardealer_get_default_sort_by() {
		return 'date';
	}
}

if ( ! function_exists( 'cardealer_get_cars_list_layout_style' ) ) {
	function cardealer_get_cars_list_layout_style() {
		return 'view-grid';
	}
}

/*
 * Stub the PGS_Assets class that CarDealer_Assets instantiates in its constructor.
 */
if ( ! class_exists( 'PGS_Assets' ) ) {
	class PGS_Assets {
		public function __construct( $prefix = '' ) {
			// No-op stub for testing.
		}
	}
}

/*
 * Stub WPBakeryVisualComposerAbstract — not loaded in tests.
 * (We do NOT define it so class_exists returns false, matching non-WPBakery environments.)
 */

/*
 * Stub WooCommerce class — defined so class_exists('WooCommerce') returns true in tests.
 * WooCommerce page conditions (is_woocommerce, is_cart, etc.) are controlled via $GLOBALS stubs.
 */
if ( ! class_exists( 'WooCommerce' ) ) {
	class WooCommerce {}
}

/*
 * Controllable stubs for WordPress conditional functions.
 * Tests set $GLOBALS['test_*'] to control return values.
 */
if ( ! function_exists( 'is_singular' ) ) {
	function is_singular( $post_types = '' ) {
		if ( isset( $GLOBALS['test_is_singular'] ) ) {
			if ( is_array( $GLOBALS['test_is_singular'] ) ) {
				if ( is_array( $post_types ) ) {
					return ! empty( array_intersect( $post_types, $GLOBALS['test_is_singular'] ) );
				}
				return in_array( $post_types, $GLOBALS['test_is_singular'], true );
			}
			return (bool) $GLOBALS['test_is_singular'];
		}
		return false;
	}
}

if ( ! function_exists( 'is_post_type_archive' ) ) {
	function is_post_type_archive( $post_types = '' ) {
		if ( isset( $GLOBALS['test_is_post_type_archive'] ) ) {
			if ( is_array( $GLOBALS['test_is_post_type_archive'] ) ) {
				return in_array( $post_types, $GLOBALS['test_is_post_type_archive'], true );
			}
			return (bool) $GLOBALS['test_is_post_type_archive'];
		}
		return false;
	}
}

if ( ! function_exists( 'is_page_template' ) ) {
	function is_page_template( $template = '' ) {
		if ( isset( $GLOBALS['test_is_page_template'] ) ) {
			if ( is_string( $GLOBALS['test_is_page_template'] ) ) {
				return $template === $GLOBALS['test_is_page_template'];
			}
			return (bool) $GLOBALS['test_is_page_template'];
		}
		return false;
	}
}

if ( ! function_exists( 'wp_is_mobile' ) ) {
	function wp_is_mobile() {
		return isset( $GLOBALS['test_wp_is_mobile'] ) ? (bool) $GLOBALS['test_wp_is_mobile'] : false;
	}
}

if ( ! function_exists( 'get_post_type' ) ) {
	function get_post_type( $post = null ) {
		return isset( $GLOBALS['test_get_post_type'] ) ? $GLOBALS['test_get_post_type'] : 'post';
	}
}

if ( ! function_exists( 'get_current_screen' ) ) {
	function get_current_screen() {
		if ( isset( $GLOBALS['test_current_screen'] ) ) {
			return $GLOBALS['test_current_screen'];
		}
		$screen = new stdClass();
		$screen->id = '';
		return $screen;
	}
}

if ( ! function_exists( 'get_page_template_slug' ) ) {
	function get_page_template_slug( $post = null ) {
		return isset( $GLOBALS['test_page_template_slug'] ) ? $GLOBALS['test_page_template_slug'] : '';
	}
}

if ( ! function_exists( 'wp_strip_all_tags' ) ) {
	function wp_strip_all_tags( $string, $remove_breaks = false ) {
		return strip_tags( $string );
	}
}

if ( ! function_exists( 'wp_script_is' ) ) {
	function wp_script_is( $handle, $list = 'enqueued' ) {
		return false;
	}
}

if ( ! function_exists( 'is_single' ) ) {
	function is_single( $post = '' ) {
		return isset( $GLOBALS['test_is_single'] ) ? (bool) $GLOBALS['test_is_single'] : false;
	}
}

if ( ! function_exists( 'is_author' ) ) {
	function is_author( $author = '' ) {
		return isset( $GLOBALS['test_is_author'] ) ? (bool) $GLOBALS['test_is_author'] : false;
	}
}

if ( ! function_exists( 'is_category' ) ) {
	function is_category( $category = '' ) {
		return isset( $GLOBALS['test_is_category'] ) ? (bool) $GLOBALS['test_is_category'] : false;
	}
}

if ( ! function_exists( 'is_home' ) ) {
	function is_home() {
		return isset( $GLOBALS['test_is_home'] ) ? (bool) $GLOBALS['test_is_home'] : false;
	}
}

if ( ! function_exists( 'is_tag' ) ) {
	function is_tag( $tag = '' ) {
		return isset( $GLOBALS['test_is_tag'] ) ? (bool) $GLOBALS['test_is_tag'] : false;
	}
}

if ( ! function_exists( 'is_date' ) ) {
	function is_date() {
		return isset( $GLOBALS['test_is_date'] ) ? (bool) $GLOBALS['test_is_date'] : false;
	}
}

if ( ! function_exists( 'is_search' ) ) {
	function is_search() {
		return isset( $GLOBALS['test_is_search'] ) ? (bool) $GLOBALS['test_is_search'] : false;
	}
}

if ( ! function_exists( 'comments_open' ) ) {
	function comments_open( $post_id = null ) {
		return false;
	}
}

if ( ! function_exists( 'is_product' ) ) {
	function is_product() {
		return isset( $GLOBALS['test_is_product'] ) ? (bool) $GLOBALS['test_is_product'] : false;
	}
}

if ( ! function_exists( 'wp_deregister_style' ) ) {
	function wp_deregister_style( $handle ) {
		return true;
	}
}

if ( ! function_exists( 'wp_add_inline_style' ) ) {
	function wp_add_inline_style( $handle, $data ) {
		return true;
	}
}

/*
 * Controllable stubs for WooCommerce conditional functions.
 * Tests set $GLOBALS['test_*'] to control return values.
 */
if ( ! function_exists( 'is_woocommerce' ) ) {
	function is_woocommerce() {
		return isset( $GLOBALS['test_is_woocommerce'] ) ? (bool) $GLOBALS['test_is_woocommerce'] : false;
	}
}

if ( ! function_exists( 'is_cart' ) ) {
	function is_cart() {
		return isset( $GLOBALS['test_is_cart'] ) ? (bool) $GLOBALS['test_is_cart'] : false;
	}
}

if ( ! function_exists( 'is_checkout' ) ) {
	function is_checkout() {
		return isset( $GLOBALS['test_is_checkout'] ) ? (bool) $GLOBALS['test_is_checkout'] : false;
	}
}

if ( ! function_exists( 'is_account_page' ) ) {
	function is_account_page() {
		return isset( $GLOBALS['test_is_account_page'] ) ? (bool) $GLOBALS['test_is_account_page'] : false;
	}
}

if ( ! function_exists( 'has_shortcode' ) ) {
	function has_shortcode( $content, $tag ) {
		if ( isset( $GLOBALS['test_has_shortcode'] ) ) {
			if ( is_array( $GLOBALS['test_has_shortcode'] ) ) {
				return in_array( $tag, $GLOBALS['test_has_shortcode'], true );
			}
			return (bool) $GLOBALS['test_has_shortcode'];
		}
		return false;
	}
}

if ( ! function_exists( 'get_post_field' ) ) {
	function get_post_field( $field, $post = null ) {
		if ( isset( $GLOBALS['test_post_content'] ) && 'post_content' === $field ) {
			return $GLOBALS['test_post_content'];
		}
		return '';
	}
}

if ( ! function_exists( 'get_the_ID' ) ) {
	function get_the_ID() {
		return isset( $GLOBALS['test_the_id'] ) ? $GLOBALS['test_the_id'] : 0;
	}
}

// Now require the class under test.
// The file will auto-instantiate via $GLOBALS['cardealer_assets'] = cardealer_assets();

// Set up global $wp and $wp_query objects needed by get_scripts() localize data.
global $wp, $wp_query;
$wp = new stdClass();
$wp->request = '';
$wp_query = new stdClass();
$wp_query->query_vars = array();

require_once dirname( __DIR__ ) . '/includes/classes/class-cardealer-assets.php';
