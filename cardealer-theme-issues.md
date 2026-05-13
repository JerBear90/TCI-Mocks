# CarDealer Theme Optimization — Issues List

## High Impact

### 1. PhotoSwipe JS loaded globally
- **Files:** `photoswipe.js`, `photoswipe-ui-default.js`
- **Location:** `class-cardealer-assets.php` → `get_scripts()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page. Only needed on Vehicle Detail Pages (gallery lightbox).
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue in `vehicle_detail_page_scripts()`.

### 2. PhotoSwipe CSS loaded globally
- **Files:** `photoswipe.css`, `default-skin.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page. Only needed on Vehicle Detail Pages.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue in `vehicle_detail_page_styles()`.

### 3. Compare JS loaded globally
- **File:** `compare.js`
- **Location:** `class-cardealer-assets.php` → `get_scripts()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page with its localize data and `jquery-ui-sortable` dependency. Only needed on Inventory and Vehicle Detail pages.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue in `vehicle_detail_page_scripts()` and `inventory_scripts()`.

### 4. Cookies JS loaded globally (front + admin)
- **File:** `cookies.js` (jaaulde-cookies)
- **Location:** `class-cardealer-assets.php` → `get_scripts()` — `'action' => 'enqueue'`, context: `['front', 'admin']`
- **Problem:** Enqueued on every page including admin. Only needed where compare/cookie features are active.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue on pages that need cookie functionality.

### 5. Owl Carousel CSS loaded globally
- **File:** `owl-carousel.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page. Only needed on pages with carousels (vehicle detail, inventory carousel, blog, product pages).
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue alongside the `cardealer-owl-carousel` JS.

### 6. WooCommerce CSS loaded globally
- **File:** `woocommerce.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page even on non-WooCommerce pages.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue only on WooCommerce pages (`is_woocommerce()`, `is_cart()`, `is_checkout()`, `is_account_page()`).

---

## Medium Impact

### 7. Timepicker CSS loaded globally
- **File:** `jquery.timepicker.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page. Only needed on Vehicle Detail Pages with booking forms.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue in `vehicle_detail_page_styles()`.

### 8. Contact Form CSS loaded globally
- **File:** `contact-form.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`
- **Problem:** Enqueued on every frontend page. Only needed on pages with contact forms or Vehicle Detail Pages.
- **Fix:** Change to `'action' => 'register'`, conditionally enqueue on relevant pages.

### 9. Flaticon uses legacy font formats
- **File:** `css/frontend/flaticon.css`
- **Problem:** `@font-face` references `.eot` (IE 6–8) and `.svg` (deprecated) formats. Also has a broken SVG path in the `@media screen` block (`../fonts/` instead of `../../fonts/`).
- **Fix:** Remove `.eot` and `.svg` sources, remove the `@media` SVG block, keep `.woff` and `.ttf` only.

### 10. Google Fonts uses legacy API v1
- **File:** `class-cardealer-assets.php` → `google_fonts_url()`
- **Problem:** Uses `fonts.googleapis.com/css` (v1). The v2 endpoint serves optimized formats (woff2) and supports `font-display: swap` natively.
- **Fix:** Update to `fonts.googleapis.com/css2` with `ital,wght@` axis notation and `&display=swap`.

---

## Low Impact

### 11. Sidebar CSS always loads unminified
- **File:** `css/frontend/sidebar.css`
- **Location:** `class-cardealer-assets.php` → `get_styles()`, `cardealer-sidebar` entry
- **Problem:** Source path is hardcoded as `sidebar.css` without the `$suffix` variable. Every other frontend CSS uses `$suffix` to load `.min.css` in production.
- **Fix:** Change src to `'/css/frontend/sidebar' . $suffix . '.css'`.

### 12. IE conditional comments in header.php
- **File:** `header.php`
- **Problem:** Contains `<!--[if IE 7]>`, `<!--[if IE 8]>`, and `<!--[if !(IE 7) & !(IE 8)]>` conditional comments wrapping the `<html>` tag. IE is end-of-life.
- **Fix:** Replace with a single `<html <?php language_attributes(); ?>>` tag.

### 13. Font Awesome v4 shims loaded globally
- **File:** `v4-shims.css` (font-awesome-shims)
- **Location:** `class-cardealer-assets.php` → `get_styles()` — `'action' => 'enqueue'`, context: `['front', 'admin']`
- **Problem:** Maps old FA4 class names to FA6. May be unnecessary if all icons already use FA6 syntax.
- **Fix:** Audit codebase for FA4 references. If none found, change to `'action' => 'register'`. If found, keep and document.

---

## Informational (No Action Required)

### 14. Duplicate Bootstrap versions
- **Files:** `js/library/bootstrap/` (v3.3.7, frontend), `js/library/bootstrap-5/` (v5, admin only)
- **Status:** Bootstrap 5 is registered for admin context only. No conflict, but adds to theme package size.

### 15. 17 preloader GIF images
- **Directory:** `images/preloader_img/`
- **Status:** Only one is active at a time (selected via theme options). All 17 ship with the theme. Adds to package size but not to page load.

### 16. Dynamic CSS is inline
- **File:** `includes/dynamic_css.php`
- **Status:** Dynamic CSS (color schemes, typography) is output via `wp_add_inline_style()`. This is appropriate for small dynamic CSS — avoids an extra HTTP request.
