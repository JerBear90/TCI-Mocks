<?php
/**
 * Test Suite 4: Google Fonts URL
 *
 * Validates that the google_fonts_url() method produces a correct Google Fonts
 * API v2 URL with ital,wght@ axis notation, display=swap, and separate family
 * parameters. Also validates that Redux active returns empty string.
 *
 * Requirements: 11.1, 11.2, 11.3, 11.4
 */

use PHPUnit\Framework\TestCase;

class Test_Google_Fonts extends TestCase {

	/**
	 * @var CarDealer_Assets
	 */
	private $assets;

	/**
	 * @var ReflectionMethod
	 */
	private $google_fonts_url_method;

	protected function setUp(): void {
		parent::setUp();
		$this->assets = new CarDealer_Assets();

		// Use reflection to access the private google_fonts_url() method.
		$reflection = new ReflectionClass( $this->assets );
		$this->google_fonts_url_method = $reflection->getMethod( 'google_fonts_url' );
		$this->google_fonts_url_method->setAccessible( true );
	}

	protected function tearDown(): void {
		// Clean up any globals we may have set.
		unset( $GLOBALS['car_dealer_options'] );
		parent::tearDown();
	}

	// ---------------------------------------------------------------
	// Test URL uses css2 endpoint
	// Validates: Requirement 11.1
	// ---------------------------------------------------------------

	/**
	 * Test that the Google Fonts URL uses the css2 endpoint.
	 */
	public function test_url_uses_css2_endpoint() {
		$url = $this->google_fonts_url_method->invoke( $this->assets );

		$this->assertStringContainsString( 'fonts.googleapis.com/css2', $url );
	}

	// ---------------------------------------------------------------
	// Test URL uses ital,wght@ axis notation for both font families
	// Validates: Requirement 11.2
	// ---------------------------------------------------------------

	/**
	 * Test that the URL uses ital,wght@ axis notation for Open Sans.
	 */
	public function test_url_uses_axis_notation_for_open_sans() {
		$url = $this->google_fonts_url_method->invoke( $this->assets );

		$this->assertStringContainsString(
			'Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800',
			$url
		);
	}

	/**
	 * Test that the URL uses ital,wght@ axis notation for Roboto.
	 */
	public function test_url_uses_axis_notation_for_roboto() {
		$url = $this->google_fonts_url_method->invoke( $this->assets );

		$this->assertStringContainsString(
			'Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,700;1,900',
			$url
		);
	}

	// ---------------------------------------------------------------
	// Test URL includes display=swap
	// Validates: Requirement 11.3
	// ---------------------------------------------------------------

	/**
	 * Test that the URL includes display=swap.
	 */
	public function test_url_includes_display_swap() {
		$url = $this->google_fonts_url_method->invoke( $this->assets );

		$this->assertStringContainsString( 'display=swap', $url );
	}

	// ---------------------------------------------------------------
	// Test URL contains two separate family= parameters
	// Validates: Requirement 11.2
	// ---------------------------------------------------------------

	/**
	 * Test that the URL contains two separate family= parameters (one per font).
	 */
	public function test_url_contains_two_family_parameters() {
		$url = $this->google_fonts_url_method->invoke( $this->assets );

		// Count occurrences of 'family=' in the URL.
		$count = substr_count( $url, 'family=' );
		$this->assertSame( 2, $count, 'Expected exactly two family= parameters in the URL.' );
	}

	// ---------------------------------------------------------------
	// Test Redux active returns empty string
	// Validates: Requirement 11.4
	// ---------------------------------------------------------------

	/**
	 * Test that when Redux is active and has custom fonts, google_fonts_url() returns empty string.
	 */
	public function test_redux_active_returns_empty_string() {
		// Temporarily define the Redux class if not already defined.
		$redux_was_defined = class_exists( 'Redux' );
		if ( ! $redux_was_defined ) {
			eval( 'class Redux {}' );
		}

		// Set up car_dealer_options with a body font-family (simulating Redux custom fonts).
		$GLOBALS['car_dealer_options'] = array(
			'opt-typography-body' => array(
				'font-family' => 'Lato',
			),
		);

		// Create a fresh instance so the constructor calls google_fonts_url() with Redux active.
		$assets = new CarDealer_Assets();

		// Use reflection to call google_fonts_url() directly.
		$reflection = new ReflectionClass( $assets );
		$method = $reflection->getMethod( 'google_fonts_url' );
		$method->setAccessible( true );

		$url = $method->invoke( $assets );

		$this->assertSame( '', $url, 'Expected empty string when Redux is active with custom fonts.' );

		// Clean up.
		unset( $GLOBALS['car_dealer_options'] );
	}
}
