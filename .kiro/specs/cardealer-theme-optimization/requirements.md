# Requirements Document

## Introduction

This specification covers performance optimizations for the CarDealer WordPress theme (by Potenza Global Solutions). The theme currently loads many CSS and JavaScript assets globally on every page, even when they are only needed on specific page types. It also ships legacy font formats, uses the deprecated Google Fonts API v1, and includes dead IE conditional comments. The goal is to reduce page weight and HTTP requests on pages that do not need these assets, without breaking any existing functionality.

All changes are scoped to the theme's asset management layer (`class-cardealer-assets.php`, `class-pgs-assets.php`), the Flaticon CSS file, the header template, and the sidebar CSS reference. No changes to theme options, admin UI, or third-party plugin behavior are in scope.

## Glossary

- **Asset_Manager**: The CarDealer theme's asset registration and enqueue system, composed of `PGS_Assets` and `CarDealer_Assets` classes in `includes/classes/`.
- **Enqueue**: A WordPress action that loads a script or stylesheet on the current page. An asset with `'action' => 'enqueue'` loads globally.
- **Register**: A WordPress action that makes a script or stylesheet available for later conditional enqueue. An asset with `'action' => 'register'` does not load unless explicitly enqueued elsewhere.
- **Vehicle_Detail_Page**: A WordPress singular page for the `cars` or `cardealer_template` post type (`is_singular('cars')` or `is_singular('cardealer_template')`).
- **Inventory_Page**: A WordPress archive page for the `cars` post type, a sold-cars template page, or a vehicle taxonomy page.
- **Gallery_Page**: A Vehicle_Detail_Page that displays a photo gallery using the PhotoSwipe lightbox library.
- **Booking_Page**: A Vehicle_Detail_Page that includes a test-drive or appointment booking form with a time picker.
- **Contact_Form_Page**: A page that renders a CarDealer contact form shortcode or widget.
- **Compare_Feature**: The vehicle comparison feature controlled by the `cardealer-compare` script and its associated UI.
- **Flaticon_CSS**: The `css/frontend/flaticon.css` stylesheet that defines the `@font-face` rule and icon classes for the Flaticon icon font.
- **Google_Fonts_Endpoint**: The external URL used to load Google Fonts, currently `fonts.googleapis.com/css` (API v1).
- **FA_Shims**: The Font Awesome v4 compatibility shim stylesheet (`v4-shims.css`) that maps old FA4 icon names to FA6 equivalents.
- **Sidebar_CSS**: The `css/frontend/sidebar.css` stylesheet for sidebar widget styling.
- **Preloader_Images**: The set of 17 GIF animation files in `images/preloader_img/` used for the page loading animation, of which only one is active at a time based on theme options.

## Requirements

### Requirement 1: Conditionally Load PhotoSwipe JS

**User Story:** As a site visitor, I want pages that do not use the photo gallery lightbox to load faster, so that I experience shorter load times on non-gallery pages.

#### Acceptance Criteria

1. WHEN a visitor loads a Vehicle_Detail_Page, THE Asset_Manager SHALL enqueue the `photoswipe` and `photoswipe-ui-default` JavaScript files.
2. WHEN a visitor loads a page that is not a Vehicle_Detail_Page, THE Asset_Manager SHALL register but not enqueue the `photoswipe` and `photoswipe-ui-default` JavaScript files.
3. WHEN the `photoswipe` JavaScript action is changed from enqueue to register in the asset definition, THE Asset_Manager SHALL preserve all existing properties of the asset entry (handle, src, deps, ver, in_footer, context).

### Requirement 2: Conditionally Load PhotoSwipe CSS

**User Story:** As a site visitor, I want PhotoSwipe stylesheets loaded only on pages that use the lightbox, so that non-gallery pages have fewer CSS resources to download.

#### Acceptance Criteria

1. WHEN a visitor loads a Vehicle_Detail_Page, THE Asset_Manager SHALL enqueue the `photoswipe` and `photoswipe-default-skin` CSS files.
2. WHEN a visitor loads a page that is not a Vehicle_Detail_Page, THE Asset_Manager SHALL register but not enqueue the `photoswipe` and `photoswipe-default-skin` CSS files.
3. WHEN the PhotoSwipe CSS action is changed from enqueue to register in the asset definition, THE Asset_Manager SHALL preserve all existing properties of each CSS asset entry (handle, src, deps, ver, context).

### Requirement 3: Conditionally Load Cookies JS

**User Story:** As a site visitor, I want the jaaulde-cookies library loaded only on frontend pages where it is needed, so that unnecessary JavaScript is not loaded on every page including admin.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `jaaulde-cookies` JavaScript file by default in its asset definition.
2. WHEN a frontend page requires cookie functionality (compare feature or cookie consent), THE Asset_Manager SHALL enqueue the `jaaulde-cookies` JavaScript file on that page.
3. WHEN an admin page is loaded, THE Asset_Manager SHALL not enqueue the `jaaulde-cookies` JavaScript file unless a specific admin feature requires it.

### Requirement 4: Conditionally Load Compare JS

**User Story:** As a site visitor, I want the vehicle compare script loaded only on pages where comparison is available, so that pages without comparison functionality load faster.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `cardealer-compare` JavaScript file by default in its asset definition.
2. WHEN a visitor loads an Inventory_Page or a Vehicle_Detail_Page, THE Asset_Manager SHALL enqueue the `cardealer-compare` JavaScript file.
3. WHEN a visitor loads a page that is not an Inventory_Page or Vehicle_Detail_Page, THE Asset_Manager SHALL not enqueue the `cardealer-compare` JavaScript file.
4. WHEN the `cardealer-compare` script is conditionally enqueued, THE Asset_Manager SHALL preserve all localize data (cardealer_compare_obj) and dependencies (jquery-ui-sortable).

### Requirement 5: Conditionally Load Timepicker CSS

**User Story:** As a site visitor, I want the timepicker stylesheet loaded only on pages with booking forms, so that unrelated pages do not download unused CSS.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `timepicker` CSS file by default in its asset definition.
2. WHEN a visitor loads a Vehicle_Detail_Page or a Booking_Page, THE Asset_Manager SHALL enqueue the `timepicker` CSS file.
3. WHEN a visitor loads a page that does not contain a booking form, THE Asset_Manager SHALL not enqueue the `timepicker` CSS file.

### Requirement 6: Conditionally Load Contact Form CSS

**User Story:** As a site visitor, I want the contact form stylesheet loaded only on pages that display a contact form, so that other pages are not burdened with unused CSS.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `cardealer-contact-form` CSS file by default in its asset definition.
2. WHEN a visitor loads a Contact_Form_Page or a Vehicle_Detail_Page, THE Asset_Manager SHALL enqueue the `cardealer-contact-form` CSS file.
3. WHEN a visitor loads a page that does not contain a contact form, THE Asset_Manager SHALL not enqueue the `cardealer-contact-form` CSS file.

### Requirement 7: Conditionally Load WooCommerce CSS

**User Story:** As a site visitor, I want the CarDealer WooCommerce stylesheet loaded only when WooCommerce pages are active, so that non-shop pages do not load unnecessary CSS.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `cardealer-woocommerce` CSS file by default in its asset definition.
2. WHEN a visitor loads a WooCommerce page (shop, product, cart, checkout, or account page), THE Asset_Manager SHALL enqueue the `cardealer-woocommerce` CSS file.
3. WHEN WooCommerce is not active (the WooCommerce class does not exist), THE Asset_Manager SHALL not enqueue the `cardealer-woocommerce` CSS file.
4. WHEN a visitor loads a non-WooCommerce page, THE Asset_Manager SHALL not enqueue the `cardealer-woocommerce` CSS file.

### Requirement 8: Conditionally Load Owl Carousel CSS

**User Story:** As a site visitor, I want the Owl Carousel stylesheet loaded only on pages that display carousels, so that pages without carousels have fewer CSS resources.

#### Acceptance Criteria

1. THE Asset_Manager SHALL register (not enqueue) the `owl-carousel` CSS file by default in its asset definition.
2. WHEN a visitor loads a page where the `cardealer-owl-carousel` JavaScript is enqueued (Vehicle_Detail_Page, Inventory_Page with carousel style, blog pages, or WooCommerce product pages), THE Asset_Manager SHALL enqueue the `owl-carousel` CSS file.
3. WHEN a visitor loads a page that does not use any carousel, THE Asset_Manager SHALL not enqueue the `owl-carousel` CSS file.

### Requirement 9: Fix Sidebar CSS Minification

**User Story:** As a site owner, I want the sidebar stylesheet to use the minified version in production, so that the CSS file size is reduced for visitors.

#### Acceptance Criteria

1. THE Asset_Manager SHALL reference the Sidebar_CSS source path using the `$suffix` variable (e.g., `'/css/frontend/sidebar' . $suffix . '.css'`).
2. WHEN `SCRIPT_DEBUG` is not enabled, THE Asset_Manager SHALL load `sidebar.min.css` instead of `sidebar.css`.
3. WHEN `SCRIPT_DEBUG` is enabled, THE Asset_Manager SHALL load `sidebar.css`.

### Requirement 10: Modernize Flaticon Font Formats

**User Story:** As a site owner, I want the Flaticon font to use only modern font formats, so that visitors do not download unnecessary legacy font files.

#### Acceptance Criteria

1. THE Flaticon_CSS SHALL declare the `@font-face` rule with only `.woff` and `.ttf` font format sources.
2. THE Flaticon_CSS SHALL not reference `.eot` or `.svg` font format sources in the `@font-face` rule.
3. THE Flaticon_CSS SHALL not contain the `@media screen and (-webkit-min-device-pixel-ratio:0)` block that references the SVG font.
4. WHEN the Flaticon_CSS is updated, THE Flaticon_CSS SHALL preserve all existing icon class definitions unchanged.

### Requirement 11: Upgrade Google Fonts API

**User Story:** As a site owner, I want Google Fonts loaded via the modern API v2 endpoint, so that font loading is faster and uses the latest optimizations (variable fonts, font-display swap).

#### Acceptance Criteria

1. THE Asset_Manager SHALL construct the Google Fonts URL using the `fonts.googleapis.com/css2` endpoint instead of `fonts.googleapis.com/css`.
2. WHEN default fonts are loaded (Open Sans and Roboto), THE Asset_Manager SHALL format the URL using the CSS2 API `family` parameter syntax with `ital,wght@` axis notation.
3. THE Asset_Manager SHALL include `&display=swap` in the Google Fonts URL to enable font-display swap behavior.
4. WHEN custom fonts are configured via Redux theme options, THE Asset_Manager SHALL continue to load those fonts (the google_fonts_url method returns an empty string when Redux handles fonts, so this behavior remains unchanged).

### Requirement 12: Remove IE Conditional Comments

**User Story:** As a site owner, I want legacy Internet Explorer conditional comments removed from the HTML output, so that the markup is cleaner and does not reference a dead browser.

#### Acceptance Criteria

1. THE header.php template SHALL output a single `<html>` opening tag with the `language_attributes()` function, without any IE conditional comment wrappers.
2. THE header.php template SHALL not contain `<!--[if IE 7]>`, `<!--[if IE 8]>`, or `<!--[if !(IE 7) & !(IE 8)]>` conditional comments.
3. WHEN the IE conditional comments are removed, THE header.php template SHALL preserve all other existing `<head>` content, meta tags, and body tag attributes.

### Requirement 13: Evaluate Font Awesome v4 Shims

**User Story:** As a site owner, I want to understand whether the Font Awesome v4 shims stylesheet is still needed, so that it can be conditionally loaded or removed if all icons use FA6 syntax.

#### Acceptance Criteria

1. WHILE the theme codebase contains references to Font Awesome v4 icon class names (e.g., `fa-` prefixed classes that differ from FA6 naming), THE Asset_Manager SHALL continue to enqueue the `font-awesome-shims` CSS file.
2. IF the theme codebase and active plugins do not reference any Font Awesome v4 icon class names, THEN THE Asset_Manager SHALL register (not enqueue) the `font-awesome-shims` CSS file by default.
3. THE Asset_Manager SHALL document (via code comment) which FA4 icon references were found during the audit, to inform future removal.
