<?php
/**
 * Test Suite 2: Conditional Style Loading
 *
 * Validates that style filter callbacks in CarDealer_Assets conditionally
 * enqueue CSS based on page context (Vehicle Detail Page, WooCommerce pages,
 * blog pages, inventory pages).
 *
 * Requirements: 2.1, 2.2, 5.2, 5.3, 6.2, 6.3, 7.2, 7.3, 7.4, 8.2, 8.3
 */

use PHPUnit\Framework\TestCase;

class Test_Conditional_Styles extends TestCase {

	/**
	 * @var CarDealer_Assets
	 */
	private $assets;

	protected function setUp(): void {
		parent::setUp();
		$this->assets = new CarDealer_Assets();

		// Reset all test globals to default (false) state.
		$GLOBALS['test_is_singular']          = false;
		$GLOBALS['test_is_post_type_archive'] = false;
		$GLOBALS['test_is_page_template']     = false;
		$GLOBALS['test_wp_is_mobile']         = false;
		$GLOBALS['test_get_post_type']        = 'post';
		$GLOBALS['test_is_woocommerce']       = false;
		$GLOBALS['test_is_cart']              = false;
		$GLOBALS['test_is_checkout']          = false;
		$GLOBALS['test_is_account_page']      = false;
		$GLOBALS['test_is_product']           = false;
		$GLOBALS['test_is_author']            = false;
		$GLOBALS['test_is_category']          = false;
		$GLOBALS['test_is_home']              = false;
		$GLOBALS['test_is_single']            = false;
		$GLOBALS['test_is_tag']               = false;
		$GLOBALS['test_is_date']              = false;
		$GLOBALS['test_is_search']            = false;
		$GLOBALS['test_has_shortcode']        = false;
		$GLOBALS['test_post_content']         = '';
		$GLOBALS['test_the_id']               = 0;
	}

	protected function tearDown(): void {
		unset(
			$GLOBALS['test_is_singular'],
			$GLOBALS['test_is_post_type_archive'],
			$GLOBALS['test_is_page_template'],
			$GLOBALS['test_wp_is_mobile'],
			$GLOBALS['test_get_post_type'],
			$GLOBALS['test_is_woocommerce'],
			$GLOBALS['test_is_cart'],
			$GLOBALS['test_is_checkout'],
			$GLOBALS['test_is_account_page'],
			$GLOBALS['test_is_product'],
			$GLOBALS['test_is_author'],
			$GLOBALS['test_is_category'],
			$GLOBALS['test_is_home'],
			$GLOBALS['test_is_single'],
			$GLOBALS['test_is_tag'],
			$GLOBALS['test_is_date'],
			$GLOBALS['test_is_search'],
			$GLOBALS['test_has_shortcode'],
			$GLOBALS['test_post_content'],
			$GLOBALS['test_the_id']
		);
		parent::tearDown();
	}

	/**
	 * Helper: build a style_data array with action='register' for a given handle.
	 */
	private function make_style_data( $handle, $extra = array() ) {
		return array_merge(
			array(
				'handle'  => $handle,
				'src'     => CARDEALER_URL . '/css/test/' . $handle . '.css',
				'deps'    => array(),
				'ver'     => '1.0.0',
				'action'  => 'register',
				'context' => array( 'front' ),
			),
			$extra
		);
	}

	// ---------------------------------------------------------------
	// PhotoSwipe CSS on Vehicle Detail Page
	// Validates: Requirements 2.1
	// ---------------------------------------------------------------

	/**
	 * Test PhotoSwipe CSS is enqueued on Vehicle Detail Page.
	 */
	public function test_photoswipe_css_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$style_data = $this->make_style_data( 'photoswipe' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'photoswipe' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test PhotoSwipe Default Skin CSS is enqueued on Vehicle Detail Page.
	 */
	public function test_photoswipe_default_skin_css_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$style_data = $this->make_style_data( 'photoswipe-default-skin' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'photoswipe-default-skin' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// PhotoSwipe CSS remains registered on non-Vehicle Detail Page
	// Validates: Requirements 2.2
	// ---------------------------------------------------------------

	/**
	 * Test PhotoSwipe CSS remains registered on a non-Vehicle Detail Page.
	 */
	public function test_photoswipe_css_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$style_data = $this->make_style_data( 'photoswipe' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'photoswipe' );

		$this->assertSame( 'register', $result['action'] );
	}

	/**
	 * Test PhotoSwipe Default Skin CSS remains registered on a non-Vehicle Detail Page.
	 */
	public function test_photoswipe_default_skin_css_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$style_data = $this->make_style_data( 'photoswipe-default-skin' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'photoswipe-default-skin' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Timepicker CSS on Vehicle Detail Page
	// Validates: Requirements 5.2
	// ---------------------------------------------------------------

	/**
	 * Test Timepicker CSS is enqueued on Vehicle Detail Page.
	 */
	public function test_timepicker_css_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$style_data = $this->make_style_data( 'timepicker' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'timepicker' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Timepicker CSS remains registered on non-Vehicle Detail Page
	// Validates: Requirements 5.3
	// ---------------------------------------------------------------

	/**
	 * Test Timepicker CSS remains registered on a non-Vehicle Detail Page.
	 */
	public function test_timepicker_css_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$style_data = $this->make_style_data( 'timepicker' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'timepicker' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Contact Form CSS on Vehicle Detail Page
	// Validates: Requirements 6.2
	// ---------------------------------------------------------------

	/**
	 * Test Contact Form CSS is enqueued on Vehicle Detail Page.
	 */
	public function test_contact_form_css_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$style_data = $this->make_style_data( 'cardealer-contact-form' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'cardealer-contact-form' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Contact Form CSS remains registered on non-Vehicle Detail Page
	// Validates: Requirements 6.3
	// ---------------------------------------------------------------

	/**
	 * Test Contact Form CSS remains registered on a non-Vehicle Detail Page
	 * (without contact form shortcode).
	 */
	public function test_contact_form_css_remains_registered_on_non_detail_page() {
		$GLOBALS['test_is_singular'] = false;

		$style_data = $this->make_style_data( 'cardealer-contact-form' );

		// Run through vehicle_detail_page_styles (won't enqueue — not a detail page).
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'cardealer-contact-form' );
		// Run through additional_styles (won't enqueue — not singular, no shortcode).
		$result = $this->assets->additional_styles( $result, 'cardealer-contact-form' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// WooCommerce CSS on WooCommerce page
	// Validates: Requirements 7.2
	// ---------------------------------------------------------------

	/**
	 * Test WooCommerce CSS is enqueued on a WooCommerce page.
	 */
	public function test_woocommerce_css_enqueued_on_woocommerce_page() {
		$GLOBALS['test_is_woocommerce'] = true;

		$style_data = $this->make_style_data( 'cardealer-woocommerce' );
		$result = $this->assets->additional_styles( $style_data, 'cardealer-woocommerce' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test WooCommerce CSS is enqueued on cart page.
	 */
	public function test_woocommerce_css_enqueued_on_cart_page() {
		$GLOBALS['test_is_cart'] = true;

		$style_data = $this->make_style_data( 'cardealer-woocommerce' );
		$result = $this->assets->additional_styles( $style_data, 'cardealer-woocommerce' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test WooCommerce CSS is enqueued on checkout page.
	 */
	public function test_woocommerce_css_enqueued_on_checkout_page() {
		$GLOBALS['test_is_checkout'] = true;

		$style_data = $this->make_style_data( 'cardealer-woocommerce' );
		$result = $this->assets->additional_styles( $style_data, 'cardealer-woocommerce' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test WooCommerce CSS is enqueued on account page.
	 */
	public function test_woocommerce_css_enqueued_on_account_page() {
		$GLOBALS['test_is_account_page'] = true;

		$style_data = $this->make_style_data( 'cardealer-woocommerce' );
		$result = $this->assets->additional_styles( $style_data, 'cardealer-woocommerce' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// WooCommerce CSS remains registered on non-WooCommerce page
	// Validates: Requirements 7.4
	// ---------------------------------------------------------------

	/**
	 * Test WooCommerce CSS remains registered on a non-WooCommerce page
	 * (WooCommerce active but not on a WC page).
	 */
	public function test_woocommerce_css_remains_registered_on_non_wc_page() {
		// WooCommerce class exists (defined in bootstrap), but no WC page conditions are true.
		$GLOBALS['test_is_woocommerce']  = false;
		$GLOBALS['test_is_cart']         = false;
		$GLOBALS['test_is_checkout']     = false;
		$GLOBALS['test_is_account_page'] = false;

		$style_data = $this->make_style_data( 'cardealer-woocommerce' );
		$result = $this->assets->additional_styles( $style_data, 'cardealer-woocommerce' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Owl Carousel CSS on blog page
	// Validates: Requirements 8.2
	// ---------------------------------------------------------------

	/**
	 * Test Owl Carousel CSS is enqueued on a blog page (matching blog_scripts owl-carousel JS).
	 */
	public function test_owl_carousel_css_enqueued_on_blog_page() {
		$GLOBALS['test_is_home']       = true;
		$GLOBALS['test_get_post_type'] = 'post';

		$style_data = $this->make_style_data( 'owl-carousel' );
		$result = $this->assets->blog_styles( $style_data, 'owl-carousel' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	/**
	 * Test Owl Carousel CSS is enqueued on a single blog post page.
	 */
	public function test_owl_carousel_css_enqueued_on_single_blog_post() {
		$GLOBALS['test_is_single']     = true;
		$GLOBALS['test_get_post_type'] = 'post';

		$style_data = $this->make_style_data( 'owl-carousel' );
		$result = $this->assets->blog_styles( $style_data, 'owl-carousel' );

		$this->assertSame( 'enqueue', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Owl Carousel CSS remains registered on non-carousel page
	// Validates: Requirements 8.3
	// ---------------------------------------------------------------

	/**
	 * Test Owl Carousel CSS remains registered on a non-carousel page.
	 */
	public function test_owl_carousel_css_remains_registered_on_non_carousel_page() {
		// No blog, no inventory, no vehicle detail, no WooCommerce product.
		$GLOBALS['test_is_singular']          = false;
		$GLOBALS['test_is_post_type_archive'] = false;
		$GLOBALS['test_is_home']              = false;
		$GLOBALS['test_is_single']            = false;
		$GLOBALS['test_is_author']            = false;
		$GLOBALS['test_is_category']          = false;
		$GLOBALS['test_is_tag']               = false;
		$GLOBALS['test_is_date']              = false;
		$GLOBALS['test_is_search']            = false;
		$GLOBALS['test_is_product']           = false;
		$GLOBALS['test_get_post_type']        = 'page';

		$style_data = $this->make_style_data( 'owl-carousel' );

		// Run through all style filter methods that could enqueue owl-carousel.
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'owl-carousel' );
		$result = $this->assets->blog_styles( $result, 'owl-carousel' );
		$result = $this->assets->inventory_styles( $result, 'owl-carousel' );
		$result = $this->assets->additional_styles( $result, 'owl-carousel' );

		$this->assertSame( 'register', $result['action'] );
	}

	// ---------------------------------------------------------------
	// Owl Carousel CSS on Vehicle Detail Page
	// Validates: Requirements 8.2
	// ---------------------------------------------------------------

	/**
	 * Test Owl Carousel CSS is enqueued on Vehicle Detail Page.
	 */
	public function test_owl_carousel_css_enqueued_on_vehicle_detail_page() {
		$GLOBALS['test_is_singular'] = array( 'cars' );

		$style_data = $this->make_style_data( 'owl-carousel' );
		$result = $this->assets->vehicle_detail_page_styles( $style_data, 'owl-carousel' );

		$this->assertSame( 'enqueue', $result['action'] );
	}
}
