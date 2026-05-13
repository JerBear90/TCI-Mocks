<?php
/**
 * Test Suite 6: FA Shims Audit
 *
 * Validates that the Font Awesome v4 shims stylesheet remains globally
 * enqueued and that a code comment documents the audit findings near
 * the asset definition.
 *
 * Requirements: 13.1, 13.3
 */

use PHPUnit\Framework\TestCase;

class Test_FA_Shims_Audit extends TestCase {

	/**
	 * Helper: get a fresh CarDealer_Assets instance and extract styles.
	 */
	private function get_styles() {
		$assets     = new CarDealer_Assets();
		$reflection = new ReflectionMethod( $assets, 'get_styles' );
		$reflection->setAccessible( true );
		return $reflection->invoke( $assets );
	}

	// ---------------------------------------------------------------
	// font-awesome-shims action remains 'enqueue'
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 13.1
	 *
	 * The font-awesome-shims CSS must have action='enqueue' in get_styles()
	 * because plugins and user content may still reference FA4 icon names.
	 */
	public function test_font_awesome_shims_action_is_enqueue() {
		$styles = $this->get_styles();

		$this->assertArrayHasKey( 'font-awesome-shims', $styles, 'font-awesome-shims key must exist in styles array.' );
		$this->assertSame(
			'enqueue',
			$styles['font-awesome-shims']['action'],
			'font-awesome-shims should remain globally enqueued.'
		);
	}

	// ---------------------------------------------------------------
	// Audit comment exists near the font-awesome-shims definition
	// ---------------------------------------------------------------

	/**
	 * Validates: Requirements 13.3
	 *
	 * A code comment block must exist near the font-awesome-shims asset
	 * definition in class-cardealer-assets.php documenting the audit
	 * findings: FA6 syntax used, standard FA6 icon names, plugins may
	 * reference FA4 names, shims remain globally enqueued.
	 */
	public function test_audit_comment_exists_near_shims_definition() {
		$source_file = dirname( __DIR__ ) . '/includes/classes/class-cardealer-assets.php';
		$this->assertFileExists( $source_file, 'class-cardealer-assets.php must exist.' );

		$contents = file_get_contents( $source_file );
		$lines    = explode( "\n", $contents );

		// Find the line that contains the 'font-awesome-shims' array key definition.
		$definition_line = null;
		foreach ( $lines as $index => $line ) {
			if ( preg_match( "/['\"]font-awesome-shims['\"]\s*=>\s*array/", $line ) ) {
				$definition_line = $index;
				break;
			}
		}

		$this->assertNotNull( $definition_line, "'font-awesome-shims' array key must exist in source file." );

		// Look at the 30 lines preceding the definition for the audit comment block.
		$start   = max( 0, $definition_line - 30 );
		$context = implode( "\n", array_slice( $lines, $start, $definition_line - $start ) );

		// The comment should document key audit findings.
		$this->assertStringContainsString(
			'FA6',
			$context,
			'Audit comment must mention FA6 syntax.'
		);
		$this->assertStringContainsString(
			'FA4',
			$context,
			'Audit comment must mention FA4 legacy names.'
		);
		$this->assertStringContainsString(
			'plugin',
			strtolower( $context ),
			'Audit comment must mention plugins as a reason to keep shims.'
		);
		$this->assertStringContainsString(
			'enqueue',
			strtolower( $context ),
			'Audit comment must document the decision to keep shims enqueued.'
		);
	}
}
