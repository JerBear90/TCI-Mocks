<?php
/**
 * Test Suite 5: Static File Verification
 *
 * Validates that flaticon.css has been modernized (only .woff and .ttf formats,
 * no .eot/.svg references, no webkit media query block, all icon classes preserved)
 * and that header.php has a clean <html> tag with language_attributes() and no IE
 * conditional comments while preserving all head content.
 *
 * Requirements: 10.1, 10.2, 10.3, 10.4, 12.1, 12.2, 12.3
 */

use PHPUnit\Framework\TestCase;

class Test_Static_Files extends TestCase {

	/**
	 * @var string Absolute path to the theme root directory.
	 */
	private static $theme_root;

	/**
	 * @var string Contents of flaticon.css.
	 */
	private $flaticon_css;

	/**
	 * @var string Contents of header.php.
	 */
	private $header_php;

	public static function setUpBeforeClass(): void {
		self::$theme_root = dirname( __DIR__ );
	}

	protected function setUp(): void {
		parent::setUp();

		$flaticon_path = self::$theme_root . '/css/frontend/flaticon.css';
		$this->assertFileExists( $flaticon_path, 'flaticon.css must exist.' );
		$this->flaticon_css = file_get_contents( $flaticon_path );

		$header_path = self::$theme_root . '/header.php';
		$this->assertFileExists( $header_path, 'header.php must exist.' );
		$this->header_php = file_get_contents( $header_path );
	}

	// ---------------------------------------------------------------
	// Flaticon CSS: @font-face contains only .woff and .ttf formats
	// Validates: Requirement 10.1
	// ---------------------------------------------------------------

	/**
	 * Test that the @font-face rule contains .woff format.
	 */
	public function test_flaticon_font_face_contains_woff() {
		$this->assertMatchesRegularExpression(
			'/url\([^)]*Flaticon\.woff[^)]*\)\s*format\(\s*["\']woff["\']\s*\)/',
			$this->flaticon_css,
			'@font-face should contain a .woff source.'
		);
	}

	/**
	 * Test that the @font-face rule contains .ttf format.
	 */
	public function test_flaticon_font_face_contains_ttf() {
		$this->assertMatchesRegularExpression(
			'/url\([^)]*Flaticon\.ttf[^)]*\)\s*format\(\s*["\']truetype["\']\s*\)/',
			$this->flaticon_css,
			'@font-face should contain a .ttf source.'
		);
	}

	// ---------------------------------------------------------------
	// Flaticon CSS: no .eot or .svg references in @font-face
	// Validates: Requirement 10.2
	// ---------------------------------------------------------------

	/**
	 * Test that the @font-face rule has no .eot references.
	 */
	public function test_flaticon_font_face_no_eot() {
		$this->assertDoesNotMatchRegularExpression(
			'/url\([^)]*\.eot[^)]*\)/',
			$this->flaticon_css,
			'@font-face should not contain any .eot references.'
		);
	}

	/**
	 * Test that the @font-face rule has no .svg references.
	 */
	public function test_flaticon_font_face_no_svg() {
		$this->assertDoesNotMatchRegularExpression(
			'/url\([^)]*\.svg[^)]*\)/',
			$this->flaticon_css,
			'@font-face should not contain any .svg references.'
		);
	}

	// ---------------------------------------------------------------
	// Flaticon CSS: no @media screen and (-webkit-min-device-pixel-ratio:0) block
	// Validates: Requirement 10.3
	// ---------------------------------------------------------------

	/**
	 * Test that the webkit media query block has been removed.
	 */
	public function test_flaticon_no_webkit_media_query() {
		$this->assertStringNotContainsString(
			'-webkit-min-device-pixel-ratio:0',
			$this->flaticon_css,
			'flaticon.css should not contain the @media screen and (-webkit-min-device-pixel-ratio:0) block.'
		);
	}

	// ---------------------------------------------------------------
	// Flaticon CSS: preserves all .flaticon-* icon class definitions
	// Validates: Requirement 10.4
	// ---------------------------------------------------------------

	/**
	 * Test that all 100 .flaticon-* icon class definitions are preserved.
	 *
	 * The original file has 100 icon classes (from \f100 to \f163 in hex = 100 in decimal).
	 */
	public function test_flaticon_preserves_all_icon_classes() {
		preg_match_all( '/\.flaticon-[\w-]+:before\s*\{/', $this->flaticon_css, $matches );
		$count = count( $matches[0] );

		$this->assertSame(
			100,
			$count,
			"Expected 100 .flaticon-* icon class definitions, found {$count}."
		);
	}

	// ---------------------------------------------------------------
	// Header.php: clean <html> tag with language_attributes() and no IE conditional comments
	// Validates: Requirements 12.1, 12.2
	// ---------------------------------------------------------------

	/**
	 * Test that header.php has a clean <html> tag with language_attributes().
	 */
	public function test_header_has_clean_html_tag() {
		$this->assertMatchesRegularExpression(
			'/<html\s+<\?php\s+language_attributes\(\)\s*;\s*\?>>/',
			$this->header_php,
			'header.php should have a clean <html <?php language_attributes(); ?>> tag.'
		);
	}

	/**
	 * Test that header.php has no IE 7 conditional comment.
	 */
	public function test_header_no_ie7_conditional() {
		$this->assertStringNotContainsString(
			'<!--[if IE 7]>',
			$this->header_php,
			'header.php should not contain <!--[if IE 7]> conditional comment.'
		);
	}

	/**
	 * Test that header.php has no IE 8 conditional comment.
	 */
	public function test_header_no_ie8_conditional() {
		$this->assertStringNotContainsString(
			'<!--[if IE 8]>',
			$this->header_php,
			'header.php should not contain <!--[if IE 8]> conditional comment.'
		);
	}

	/**
	 * Test that header.php has no IE exclusion conditional comment.
	 */
	public function test_header_no_ie_exclusion_conditional() {
		$this->assertStringNotContainsString(
			'<!--[if !(IE 7) & !(IE 8)]>',
			$this->header_php,
			'header.php should not contain <!--[if !(IE 7) & !(IE 8)]> conditional comment.'
		);
	}

	/**
	 * Test that header.php has no endif conditional comment.
	 */
	public function test_header_no_endif_conditional() {
		$this->assertStringNotContainsString(
			'<!--<![endif]-->',
			$this->header_php,
			'header.php should not contain <!--<![endif]--> conditional comment.'
		);
	}

	// ---------------------------------------------------------------
	// Header.php: preserves head content (meta charset, viewport, wp_head, body tag)
	// Validates: Requirement 12.3
	// ---------------------------------------------------------------

	/**
	 * Test that header.php preserves meta charset.
	 */
	public function test_header_preserves_meta_charset() {
		$this->assertStringContainsString(
			"bloginfo( 'charset' )",
			$this->header_php,
			'header.php should preserve the meta charset tag.'
		);
	}

	/**
	 * Test that header.php preserves viewport meta tag.
	 */
	public function test_header_preserves_viewport() {
		$this->assertStringContainsString(
			'width=device-width, initial-scale=1',
			$this->header_php,
			'header.php should preserve the viewport meta tag.'
		);
	}

	/**
	 * Test that header.php preserves wp_head() call.
	 */
	public function test_header_preserves_wp_head() {
		$this->assertStringContainsString(
			'wp_head()',
			$this->header_php,
			'header.php should preserve the wp_head() call.'
		);
	}

	/**
	 * Test that header.php preserves the body tag.
	 */
	public function test_header_preserves_body_tag() {
		$this->assertMatchesRegularExpression(
			'/<body\s+<\?php\s+body_class\(\)\s*;\s*\?>/',
			$this->header_php,
			'header.php should preserve the <body> tag with body_class().'
		);
	}
}
