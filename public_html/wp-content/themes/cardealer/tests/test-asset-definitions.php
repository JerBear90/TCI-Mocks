<?php
/**
 * Test Suite 3: Asset Definition Integrity
 *
 * Validates that asset definitions in CarDealer_Assets have the correct
 * default action values and that the sidebar CSS uses the $suffix variable.
 *
 * Requirements: 1.3, 2.3, 3.1, 4.1, 5.1, 6.1, 7.1, 8.1, 9.1, 9.2, 9.3
 */

use PHPUnit\Framework\TestCase;

class Test_Asset_Definitions extends TestCase {

	/**
	 * Helper: get a fresh CarDealer_Assets instance and extract scripts.
	 */
	private function get_scripts() {
		$assets = new CarDealer_Assets();
		$reflection = new ReflectionMethod( $assets, 'get_scripts' );
		$reflection->setAccessible( true );
		return $reflection->invoke( $assets );
	}

	/**
	 * Helper: get a fresh CarDealer_Assets instance and extract styles.
	 */
	private function get_styles() {
		$assets = new CarDealer_Assets();
		$reflection = new ReflectionMethod( $assets, 'get_styles' );
		$reflection->setAccessible( true );
		return $reflection->invoke( $assets );
	}

	// ---------------------------------------------------------------
	// Task 2.1: JS assets default to 'register'
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 1.3
	 * photoswipe JS should default to 'register'.
	 */
	public function test_photoswipe_js_defaults_to_register() {
		$scripts = $this->get_scripts();
		$this->assertArrayHasKey( 'photoswipe', $scripts );
		$this->assertSame( 'register', $scripts['photoswipe']['action'] );
	}

	/**
	 * Validates: Requirements 1.3
	 * photoswipe-ui-default JS should default to 'register'.
	 */
	public function test_photoswipe_ui_default_js_defaults_to_register() {
		$scripts = $this->get_scripts();
		$this->assertArrayHasKey( 'photoswipe-ui-default', $scripts );
		$this->assertSame( 'register', $scripts['photoswipe-ui-default']['action'] );
	}

	/**
	 * Validates: Requirements 3.1
	 * jaaulde-cookies JS should default to 'register'.
	 */
	public function test_jaaulde_cookies_js_defaults_to_register() {
		$scripts = $this->get_scripts();
		$this->assertArrayHasKey( 'jaaulde-cookies', $scripts );
		$this->assertSame( 'register', $scripts['jaaulde-cookies']['action'] );
	}

	/**
	 * Validates: Requirements 4.1
	 * cardealer-compare JS should default to 'register'.
	 */
	public function test_cardealer_compare_js_defaults_to_register() {
		$scripts = $this->get_scripts();
		$this->assertArrayHasKey( 'cardealer-compare', $scripts );
		$this->assertSame( 'register', $scripts['cardealer-compare']['action'] );
	}

	// ---------------------------------------------------------------
	// Task 2.2: CSS assets default to 'register'
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 2.3
	 * photoswipe CSS should default to 'register'.
	 */
	public function test_photoswipe_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'photoswipe', $styles );
		$this->assertSame( 'register', $styles['photoswipe']['action'] );
	}

	/**
	 * Validates: Requirements 2.3
	 * photoswipe-default-skin CSS should default to 'register'.
	 */
	public function test_photoswipe_default_skin_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'photoswipe-default-skin', $styles );
		$this->assertSame( 'register', $styles['photoswipe-default-skin']['action'] );
	}

	/**
	 * Validates: Requirements 5.1
	 * timepicker CSS should default to 'register'.
	 */
	public function test_timepicker_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'timepicker', $styles );
		$this->assertSame( 'register', $styles['timepicker']['action'] );
	}

	/**
	 * Validates: Requirements 6.1
	 * cardealer-contact-form CSS should default to 'register'.
	 */
	public function test_cardealer_contact_form_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'cardealer-contact-form', $styles );
		$this->assertSame( 'register', $styles['cardealer-contact-form']['action'] );
	}

	/**
	 * Validates: Requirements 7.1
	 * cardealer-woocommerce CSS should default to 'register'.
	 */
	public function test_cardealer_woocommerce_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'cardealer-woocommerce', $styles );
		$this->assertSame( 'register', $styles['cardealer-woocommerce']['action'] );
	}

	/**
	 * Validates: Requirements 8.1
	 * owl-carousel CSS should default to 'register'.
	 */
	public function test_owl_carousel_css_defaults_to_register() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'owl-carousel', $styles );
		$this->assertSame( 'register', $styles['owl-carousel']['action'] );
	}

	// ---------------------------------------------------------------
	// Task 2.3: Sidebar CSS uses $suffix variable
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 9.1, 9.2
	 * When SCRIPT_DEBUG is false (default in bootstrap), sidebar src should contain .min.css.
	 */
	public function test_sidebar_css_uses_min_suffix_when_script_debug_false() {
		// SCRIPT_DEBUG is defined as false in bootstrap.php
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'cardealer-sidebar', $styles );
		$this->assertStringContainsString( 'sidebar.min.css', $styles['cardealer-sidebar']['src'] );
	}

	/**
	 * Validates: Requirements 9.1, 9.3
	 * The sidebar src path should use the suffix variable pattern (not hardcoded).
	 * We verify by checking the src contains the expected path structure.
	 */
	public function test_sidebar_css_src_has_correct_path_structure() {
		$styles = $this->get_styles();
		$this->assertArrayHasKey( 'cardealer-sidebar', $styles );
		$src = $styles['cardealer-sidebar']['src'];
		$this->assertStringContainsString( '/css/frontend/sidebar', $src );
		$this->assertStringEndsWith( '.css', $src );
	}

	// ---------------------------------------------------------------
	// Property preservation: photoswipe JS
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 1.3
	 * photoswipe JS should preserve all original properties after action change.
	 */
	public function test_photoswipe_js_preserves_all_properties() {
		$scripts = $this->get_scripts();
		$ps = $scripts['photoswipe'];

		$this->assertSame( 'photoswipe', $ps['handle'] );
		$this->assertStringContainsString( '/js/library/photoswipe/photoswipe', $ps['src'] );
		$this->assertStringEndsWith( '.js', $ps['src'] );
		$this->assertSame( array(), $ps['deps'] );
		$this->assertSame( '4.1.2', $ps['ver'] );
		$this->assertTrue( $ps['in_footer'] );
		$this->assertSame( array( 'front' ), $ps['context'] );
		// Action should now be 'register' (changed from 'enqueue')
		$this->assertSame( 'register', $ps['action'] );
	}

	/**
	 * Validates: Requirements 4.1
	 * cardealer-compare JS should preserve localize data and deps after action change.
	 */
	public function test_cardealer_compare_js_preserves_localize_and_deps() {
		$scripts = $this->get_scripts();
		$compare = $scripts['cardealer-compare'];

		$this->assertSame( array( 'jquery-ui-sortable' ), $compare['deps'] );
		$this->assertArrayHasKey( 'localize', $compare );
		$this->assertArrayHasKey( 'cardealer_compare_obj', $compare['localize'] );
		$this->assertArrayHasKey( 'ajaxurl', $compare['localize']['cardealer_compare_obj'] );
		$this->assertArrayHasKey( 'compare_url', $compare['localize']['cardealer_compare_obj'] );
		$this->assertArrayHasKey( 'compare_nonce', $compare['localize']['cardealer_compare_obj'] );
	}
}
