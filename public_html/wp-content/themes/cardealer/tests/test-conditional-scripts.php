<?php
/**
 * Test Suite 1: Conditional Script Loading
 *
 * Validates that script filter callbacks in CarDealer_Assets conditionally
 * enqueue scripts based on page context (Vehicle Detail Page, Inventory Page).
 *
 * Requirements: 1.1, 1.2, 3.2, 3.3, 4.2, 4.3, 4.4
 */

use PHPUnit\Framework\TestCase;

class Test_Conditional_Scripts extends TestCase {

	/**
	 * @var CarDealer_Assets
	 */
	private $assets;

	protected function setUp(): void {
		parent::setUp();
		$this->assets = new CarDealer_Assets();

		// Reset all test globals to default (false) state.
		$GLOBALS['test_is_singular']           = false;
		$GLOBALS['test_is_post_type_archive']  = false;
		$GLOBALS['test_is_page_template']      = false;
		$GLOBALS['test_wp_is_mobile']          = false;
		$GLOBALS['test_get_post_type']         = 'post';
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['test_is_singular'],
			$GLOBALS['test_is_post_type_archive'],
			$GLOBALS['test_is_page_template'],
			$GLOBALS['test_wp_is_mobile'],
			$GLOBALS['test_get_post_type']
		);
		parent::tearDown();
	}

	/**
	 * Helper: build a script_data array with action='register' for a given handle.
	 */
	private function make_script_data( $handle, $extra = array() ) {
		return array_merge(
			array(
				'handle'    => $handle,
				'src'       => CARDEALER_URL . '/js/test/' . $handle . '.js',
				'deps'      => array(),
				'ver'       => '1.0.0',
				'in_footer' => true,
				'action'    => 'register',
				'context'   => array( 'front' ),
			),
			$extra
		);
	}

	// ---------------------------------------------------------------
	// PhotoSwipe JS on Vehicle Detail Page
	// Validates: Requirements 1.1
	// ---------------------------------------------------------------

	/**
	 * Test PhotoSwipe JS is enqueued on Vehicle Detail Page (is_singular('cars') = true).
	 */
	public function test_photoswipe_js_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$script_data = $this->make_script_data( 'photoswipe' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'photoswipe' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test PhotoSwipe UI Default JS is enqueued on Vehicle Detail Page.
	 */
	public function test_photoswipe_ui_default_js_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$script_data = $this->make_script_data( 'photoswipe-ui-default' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'photoswipe-ui-default' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// PhotoSwipe JS remains registered on non-Vehicle Detail Page
	// Validates: Requirements 1.2
	// ---------------------------------------------------------------

	/**
	 * Test PhotoSwipe JS remains registered on a non-Vehicle Detail Page.
	 */
	public function test_photoswipe_js_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$script_data = $this->make_script_data( 'photoswipe' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'photoswipe' );

		$this->assertSame( 'register', $result['action'] );
	}

	/**
	 * Test PhotoSwipe UI Default JS remains registered on a non-Vehicle Detail Page.
	 */
	public function test_photoswipe_ui_default_js_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$script_data = $this->make_script_data( 'photoswipe-ui-default' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'photoswipe-ui-default' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Compare JS on Inventory Page
	// Validates: Requirements 4.2
	// ---------------------------------------------------------------

	/**
	 * Test Compare JS is enqueued on Inventory Page (is_post_type_archive('cars') = true).
	 */
	public function test_compare_js_enqueued_on_inventory_page() {
		$GLOBALS['test_is_post_type_archive'] = true;

		$script_data = $this->make_script_data( 'cardealer-compare' );
		$result = $this->assets->inventory_scripts( $script_data, 'cardealer-compare' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Compare JS on Vehicle Detail Page
	// Validates: Requirements 4.2
	// ---------------------------------------------------------------

	/**
	 * Test Compare JS is enqueued on Vehicle Detail Page.
	 */
	public function test_compare_js_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$script_data = $this->make_script_data( 'cardealer-compare' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'cardealer-compare' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Compare JS remains registered on unrelated page
	// Validates: Requirements 4.3
	// ---------------------------------------------------------------

	/**
	 * Test Compare JS remains registered on an unrelated page (no inventory, no detail).
	 */
	public function test_compare_js_remains_registered_on_unrelated_page() {
		$GLOBALS['test_is_singular']          = false;
		$GLOBALS['test_is_post_type_archive'] = false;
		$GLOBALS['test_is_page_template']     = false;

		$script_data = $this->make_script_data( 'cardealer-compare' );

		// Neither vehicle_detail_page_scripts nor inventory_scripts should enqueue it.
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'cardealer-compare' );
		$result = $this->assets->inventory_scripts( $result, 'cardealer-compare' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Compare JS preserves localize data and deps
	// Validates: Requirements 4.4
	// ---------------------------------------------------------------

	/**
	 * Test Compare JS preserves localize data (cardealer_compare_obj) and deps (jquery-ui-sortable)
	 * after conditional enqueue.
	 */
	public function test_compare_js_preserves_localize_data_and_deps() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$script_data = array(
			'handle'    => 'cardealer-compare',
			'src'       => CARDEALER_URL . '/js/frontend/compare.min.js',
			'deps'      => array( 'jquery-ui-sortable' ),
			'ver'       => CARDEALER_VERSION,
			'in_footer' => true,
			'action'    => 'register',
			'context'   => array( 'front' ),
			'localize'  => array(
				'cardealer_compare_obj' => array(
					'ajaxurl'     => 'https://example.com/wp-admin/admin-ajax.php',
					'compare_url' => '/compare/',
				),
			),
		);

		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'cardealer-compare' );

		$this->assertSame( 'enqueue', $result['action'] );
		$this->assertSame( array( 'jquery-ui-sortable' ), $result['deps'] );
		$this->assertArrayHasKey( 'localize', $result );
		$this->assertArrayHasKey( 'cardealer_compare_obj', $result['localize'] );
		$this->assertArrayHasKey( 'ajaxurl', $result['localize']['cardealer_compare_obj'] );
		$this->assertArrayHasKey( 'compare_url', $result['localize']['cardealer_compare_obj'] );
	}

	// ---------------------------------------------------------------
	// Cookies JS on Inventory Page
	// Validates: Requirements 3.2
	// ---------------------------------------------------------------

	/**
	 * Test Cookies JS is enqueued on Inventory Page.
	 */
	public function test_cookies_js_enqueued_on_inventory_page() {
		$GLOBALS['test_is_post_type_archive'] = true;

		$script_data = $this->make_script_data( 'jaaulde-cookies' );
		$result = $this->assets->inventory_scripts( $script_data, 'jaaulde-cookies' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test Cookies JS is enqueued on Vehicle Detail Page.
	 * Validates: Requirements 3.2
	 */
	public function test_cookies_js_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$script_data = $this->make_script_data( 'jaaulde-cookies' );
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'jaaulde-cookies' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Cookies JS remains registered on non-inventory/non-detail page
	// Validates: Requirements 3.3
	// ---------------------------------------------------------------

	/**
	 * Test Cookies JS remains registered on a non-inventory, non-detail page.
	 */
	public function test_cookies_js_remains_registered_on_unrelated_page() {
		$GLOBALS['test_is_singular']          = false;
		$GLOBALS['test_is_post_type_archive'] = false;
		$GLOBALS['test_is_page_template']     = false;

		$script_data = $this->make_script_data( 'jaaulde-cookies' );

		// Neither filter method should enqueue it.
		$result = $this->assets->vehicle_detail_page_scripts( $script_data, 'jaaulde-cookies' );
		$result = $this->assets->inventory_scripts( $result, 'jaaulde-cookies' );

		$this->assertSame( 'register', $result['action'] );
	}
}
