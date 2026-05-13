# Implementation Plan: CarDealer Theme Optimization

## Overview

This plan implements performance optimizations for the CarDealer WordPress theme by converting 10 globally-enqueued assets to conditional loading, fixing the sidebar CSS minification bug, modernizing the Flaticon font-face declaration, upgrading Google Fonts to API v2, removing IE conditional comments from header.php, and auditing Font Awesome v4 shims. A PHP test suite using WP_Mock/Brain\Monkey validates all conditional logic and asset definitions.

## Tasks

- [x] 1. Set up test framework and project scaffolding
  - [x] 1.1 Create `composer.json` in the theme root (`public_html/wp-content/themes/cardealer/`) with `wp-mock/wp-mock` (which includes Brain\Monkey) as a dev dependency, and PHPUnit 9.x
    - _Requirements: Testing Strategy (Design)_
  - [x] 1.2 Create `phpunit.xml` in the theme root with a test suite pointing to `tests/` directory, bootstrap file, and appropriate configuration
    - _Requirements: Testing Strategy (Design)_
  - [x] 1.3 Create `tests/bootstrap.php` that loads Composer autoload, initializes WP_Mock, and requires `class-cardealer-assets.php` with necessary constants/mocks (CARDEALER_URL, CARDEALER_VERSION, SCRIPT_DEBUG)
    - _Requirements: Testing Strategy (Design)_
  - [x] 1.4 Run `composer install` and verify PHPUnit runs with zero tests
    - _Requirements: Testing Strategy (Design)_

- [x] 2. Change asset definitions from enqueue to register
  - [x] 2.1 In `get_scripts()`, change `photoswipe`, `photoswipe-ui-default`, `jaaulde-cookies`, and `cardealer-compare` from `'action' => 'enqueue'` to `'action' => 'register'`
    - Preserve all other properties (handle, src, deps, ver, in_footer, context, localize) exactly as-is
    - _Requirements: 1.3, 2.3, 3.1, 4.1_
  - [x] 2.2 In `get_styles()`, change `photoswipe`, `photoswipe-default-skin`, `timepicker`, `cardealer-contact-form`, `cardealer-woocommerce`, and `owl-carousel` from `'action' => 'enqueue'` to `'action' => 'register'`
    - Preserve all other properties (handle, src, deps, ver, context) exactly as-is
    - _Requirements: 2.3, 5.1, 6.1, 7.1, 8.1_
  - [x] 2.3 Fix `cardealer-sidebar` CSS src to use `$suffix` variable: change `'/css/frontend/sidebar.css'` to `'/css/frontend/sidebar' . $suffix . '.css'`
    - _Requirements: 9.1, 9.2, 9.3_
  - [x] 2.4 Write Test Suite 3: Asset Definition Integrity tests (`tests/test-asset-definitions.php`)
    - Test that `photoswipe`, `photoswipe-ui-default`, `jaaulde-cookies`, `cardealer-compare` JS default to `'register'`
    - Test that `photoswipe`, `photoswipe-default-skin`, `timepicker`, `cardealer-contact-form`, `cardealer-woocommerce`, `owl-carousel` CSS default to `'register'`
    - Test that `cardealer-sidebar` src contains `.min.css` when SCRIPT_DEBUG is false, and `sidebar.css` when true
    - Test that `photoswipe` JS preserves all original properties (handle, src, deps, ver, in_footer, context)
    - _Requirements: 1.3, 2.3, 3.1, 4.1, 5.1, 6.1, 7.1, 8.1, 9.1, 9.2, 9.3_

- [x] 3. Implement conditional enqueue hooks for scripts
  - [x] 3.1 Extend `vehicle_detail_page_scripts()` to conditionally enqueue `photoswipe`, `photoswipe-ui-default`, `jaaulde-cookies`, and `cardealer-compare` when `is_singular('cars') || is_singular('cardealer_template')`
    - Add four `if` blocks matching the existing pattern in the method
    - _Requirements: 1.1, 1.2, 3.2, 4.2_
  - [x] 3.2 Extend `inventory_scripts()` to conditionally enqueue `jaaulde-cookies` and `cardealer-compare` when on inventory pages (existing condition: `is_post_type_archive('cars') || is_page_template('templates/sold-cars.php') || cardealer_is_tax_page()`)
    - _Requirements: 3.2, 4.2_
  - [x] 3.3 Write Test Suite 1: Conditional Script Loading tests (`tests/test-conditional-scripts.php`)
    - Test PhotoSwipe JS enqueued on Vehicle Detail Page (mock `is_singular('cars')` → true)
    - Test PhotoSwipe JS remains registered on non-Vehicle Detail Page
    - Test Compare JS enqueued on Inventory Page (mock `is_post_type_archive('cars')` → true)
    - Test Compare JS enqueued on Vehicle Detail Page
    - Test Compare JS remains registered on unrelated page
    - Test Compare JS preserves localize data (`cardealer_compare_obj`) and deps (`jquery-ui-sortable`)
    - Test Cookies JS enqueued on Inventory Page
    - Test Cookies JS remains registered on non-inventory/non-detail page
    - _Requirements: 1.1, 1.2, 3.2, 3.3, 4.2, 4.3, 4.4_

- [x] 4. Implement conditional enqueue hooks for styles
  - [x] 4.1 Extend `vehicle_detail_page_styles()` to conditionally enqueue `photoswipe`, `photoswipe-default-skin`, `timepicker`, and `cardealer-contact-form` CSS when `is_singular('cars') || is_singular('cardealer_template')`
    - _Requirements: 2.1, 5.2, 6.2_
  - [x] 4.2 Extend `additional_styles()` to conditionally enqueue `cardealer-woocommerce` CSS when `class_exists('WooCommerce')` and on WooCommerce pages (`is_woocommerce() || is_cart() || is_checkout() || is_account_page()`)
    - _Requirements: 7.2, 7.3, 7.4_
  - [x] 4.3 Extend `additional_styles()` (or `blog_styles()` / `inventory_styles()`) to conditionally enqueue `owl-carousel` CSS on pages where `cardealer-owl-carousel` JS is enqueued — piggyback on the same page conditions: vehicle detail, inventory carousel, blog, WooCommerce product pages
    - _Requirements: 8.2, 8.3_
  - [x] 4.4 Extend `additional_styles()` to also enqueue `cardealer-contact-form` CSS on pages with contact form shortcode (if detectable), ensuring coverage beyond just Vehicle Detail Pages
    - _Requirements: 6.2, 6.3_
  - [x] 4.5 Write Test Suite 2: Conditional Style Loading tests (`tests/test-conditional-styles.php`)
    - Test PhotoSwipe CSS enqueued on Vehicle Detail Page
    - Test PhotoSwipe CSS remains registered on non-Vehicle Detail Page
    - Test Timepicker CSS enqueued on Vehicle Detail Page
    - Test Timepicker CSS remains registered on non-Vehicle Detail Page
    - Test Contact Form CSS enqueued on Vehicle Detail Page
    - Test Contact Form CSS remains registered on non-Vehicle Detail Page
    - Test WooCommerce CSS enqueued on WooCommerce page (mock `class_exists('WooCommerce')` → true, `is_woocommerce()` → true)
    - Test WooCommerce CSS remains registered when WooCommerce inactive
    - Test WooCommerce CSS remains registered on non-WooCommerce page
    - Test Owl Carousel CSS enqueued on blog page
    - Test Owl Carousel CSS remains registered on non-carousel page
    - _Requirements: 2.1, 2.2, 5.2, 5.3, 6.2, 6.3, 7.2, 7.3, 7.4, 8.2, 8.3_

- [x] 5. Checkpoint - Verify conditional loading
  - Ensure all tests pass, ask the user if questions arise.

- [x] 6. Upgrade Google Fonts URL to API v2
  - [x] 6.1 Rewrite the `google_fonts_url()` method to use `fonts.googleapis.com/css2` endpoint with `ital,wght@` axis notation for Open Sans and Roboto, and append `&display=swap`
    - When `$fonts` is empty (Redux handles fonts), continue returning empty string — no change needed for that path
    - Build each font family as a separate `family=` parameter
    - Open Sans weights/styles: `ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800`
    - Roboto weights/styles: `ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,700;1,900`
    - _Requirements: 11.1, 11.2, 11.3, 11.4_
  - [x] 6.2 Write Test Suite 4: Google Fonts URL tests (`tests/test-google-fonts.php`)
    - Test URL uses `css2` endpoint
    - Test URL uses `ital,wght@` axis notation for both font families
    - Test URL includes `display=swap`
    - Test URL contains two separate `family=` parameters
    - Test Redux active returns empty string
    - _Requirements: 11.1, 11.2, 11.3, 11.4_

- [x] 7. Modernize Flaticon font and clean up header.php
  - [x] 7.1 Update `css/frontend/flaticon.css`: remove `.eot` sources (including `?#iefix`), remove `.svg#Flaticon` source, remove the `@media screen and (-webkit-min-device-pixel-ratio:0)` block. Keep only `.woff` and `.ttf` formats. Preserve all icon class definitions unchanged.
    - _Requirements: 10.1, 10.2, 10.3, 10.4_
  - [x] 7.2 Update `css/frontend/flaticon.min.css` with the same changes (if the file exists), or note that it needs regeneration
    - _Requirements: 10.1, 10.2, 10.3_
  - [x] 7.3 In `header.php`, replace the IE conditional comment block (`<!--[if IE 7]>` through `<!--<![endif]-->`) with a single clean `<html <?php language_attributes(); ?>>` tag. Preserve all other head content, meta tags, and body tag.
    - _Requirements: 12.1, 12.2, 12.3_
  - [x] 7.4 Write Test Suite 5: Static File Verification tests (`tests/test-static-files.php`)
    - Test flaticon.css `@font-face` contains only `.woff` and `.ttf` formats
    - Test flaticon.css has no `.eot` or `.svg` references in `@font-face`
    - Test flaticon.css has no `@media screen and (-webkit-min-device-pixel-ratio:0)` block
    - Test flaticon.css preserves all `.flaticon-*` icon class definitions (count matches original)
    - Test header.php has clean `<html` tag with `language_attributes()` and no IE conditional comments
    - Test header.php preserves head content (meta charset, viewport, wp_head, body tag)
    - _Requirements: 10.1, 10.2, 10.3, 10.4, 12.1, 12.2, 12.3_

- [x] 8. Font Awesome v4 Shims audit and documentation
  - [x] 8.1 Add a code comment block near the `font-awesome-shims` asset definition in `get_styles()` documenting the audit findings: theme uses FA6 syntax (`fas`, `far`, `fab`), standard FA6 icon names, but plugins/user content may reference FA4 names — shims should remain globally enqueued for now
    - _Requirements: 13.1, 13.3_
  - [x] 8.2 Write Test Suite 6: FA Shims Audit tests (`tests/test-fa-shims-audit.php`)
    - Test `font-awesome-shims` has `action='enqueue'` in `get_styles()`
    - Test a code comment exists near the `font-awesome-shims` definition documenting audit findings
    - _Requirements: 13.1, 13.3_

- [x] 9. Final checkpoint - Ensure all tests pass
  - Ensure all tests pass, ask the user if questions arise.

## Notes

- Tasks marked with `*` are optional and can be skipped for faster MVP
- Each task references specific requirements for traceability
- Checkpoints ensure incremental validation
- The design explicitly states property-based testing does not apply to this feature — all tests are unit tests using WP_Mock/Brain\Monkey
- No changes to `class-pgs-assets.php`, theme options, admin UI, or plugin behavior
- Key files modified: `class-cardealer-assets.php`, `flaticon.css`, `flaticon.min.css` (if exists), `header.php`
