# Lighthouse Optimization Recap — TCI Trucks Child Theme

## style.css — 9 Additions

### 1. CLS: VC Image Aspect-Ratio
Reserves space for Visual Composer single images using explicit `aspect-ratio` values. Uses `attr(width) / attr(height)` with explicit fallbacks for known portrait dimensions (3:4 for 1920×2560/1536×2048, 4:5 for 800×1000).

### 2. CLS: VC Row Containment
`contain: layout style` on `.vc_row` prevents child content from triggering layout shifts in surrounding elements.

### 3. CLS: VC Column Min-Height
`min-height: 1px` on `.wpb_column` stops columns from collapsing to zero then expanding.

### 4. CLS: Leadership Image Column Sizing
Forces `width: 100%; height: auto` on images inside `vc_col-sm-4` columns so the browser knows the display dimensions before the image loads.

### 5. CLS: Menu Min-Height
`min-height: 45px` on the mega menu container prevents the nav from collapsing then expanding when JS initializes.

### 6. Color Contrast
Changed the nav active/hover/focus color from `#ed1c24` to `#c41019` (4.56:1 on white, passes WCAG AA). Covers both mega-menu selectors AND the fallback `ul#primary-menu` / `ul.menu-links` selectors. Same treatment on the CTA phone button and the Carnegie quote section background.

### 7. Touch Targets
Social icons in the topbar get `min-width/min-height: 24px` with flexbox centering to meet WCAG 2.2 AA tap area requirements.

### 8. Montserrat Font Fallback
`@font-face` override using Arial with adjusted metrics (`size-adjust: 113%`, `ascent-override: 84.5%`) to reduce CLS from the web font swap. Also declares `font-display: swap` on Montserrat and applies the fallback font stack to body/headings.

### 9. Font Stack Application
Applies `font-family: 'Montserrat', 'Montserrat-fallback', Arial, sans-serif` to body, text columns, rows, and all heading levels so text is visible immediately with correct metrics.

---

## functions.php — 17 Additions

### 1. Social Link Aria-Labels
Filters `cardealer_social_profiles` to inject `aria-label` and `rel="noopener"` on icon-only social links.

### 2. JS `d()` No-Op
Outputs a tiny inline script in `<head>` (with `data-no-minify`) defining `d()` as a no-op so debug calls don't throw console errors.

### 3. Dequeue Unused CSS
Removes `sbi_styles` (Instagram feed) on pages without the shortcode, and `vc_extensions_adminicon` everywhere on the frontend.

### 4. GTM Preconnect
Adds `<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>` early in `<head>`.

### 5. Fetchpriority + Eager on Hero Images
Filters `wp_get_attachment_image_attributes` to add `fetchpriority="high"` and `loading="eager"` on full-size page images (LCP candidates).

### 6. wp-i18n Dependency Fix
Ensures `wp-hooks` is listed as a dependency of `wp-i18n` so the inline localization script doesn't fire before the wp object exists.

### 7. Delay JS Exclusions
Tells WP Rocket not to delay mega-menu JS, jQuery core, `wp-hooks`, or `wp-i18n` (all needed before first paint or by inline scripts).

### 8. LazyLoad Exclusions
Tells WP Rocket to skip lazy-loading any image with `fetchpriority="high"` or the `custom-logo` class.

### 9. Viewport Maximum-Scale Removal
Output-buffers `<head>` to replace the parent theme's `maximum-scale=1` viewport tag with one that allows pinch-to-zoom (WCAG 1.4.4).

### 10. Logo Dimensions
Filters `get_custom_logo` to inject explicit `width`/`height` attributes if missing, preventing CLS on the site logo.

### 11. Slider Image Preload Exclusion
Removes `web-slider-01-1.png` from WP Rocket's preload list so the hidden 335KB image doesn't become a false LCP element.

### 12. Slider Image Lazy-Load on Interior Pages
Forces `loading="lazy"` and strips `fetchpriority="high"` from the slider image on non-homepage pages where it's in the DOM but hidden.

### 13. Slider Preload Link Removal
Output-buffers `<head>` on interior pages to strip any `<link rel="preload">` tag referencing the hidden slider image.

### 14. Leadership Image Sizes Attribute Fix (NEW)
Filters `wp_content_img_tag` on the leadership page to replace `sizes="(max-width: 1920px) 100vw, 1920px"` with `sizes="(max-width: 768px) 100vw, 360px"`. This tells the browser the actual display width so it picks the correct srcset variant (~850 KiB savings) and calculates intrinsic size correctly (prevents CLS).

### 15. Leadership Heading Hierarchy Fix (NEW)
Filters `the_content` on the leadership page to replace `<h5>` tags with `<h3>`, fixing the heading order violation (H1 → H5 becomes H1 → H3).

### 16. Google Fonts Preconnect (NEW)
Outputs `<link rel="preconnect">` for `fonts.googleapis.com` and `fonts.gstatic.com` early in `<head>` to reduce font loading latency.

### 17. Google Fonts Display Swap (NEW)
Filters `style_loader_src` to append `&display=swap` to any Google Fonts URL that doesn't already have it, preventing Flash of Invisible Text.

---

## WP Rocket Admin Settings Enabled

- **Delay JS** (`delay_js: 1`)
- **Optimize CSS Delivery / Async CSS** (`async_css: 1`)

These work hand-in-hand with fixes 7 and 8 above.

---

## Results

| Metric | Before | After |
|--------|--------|-------|
| Mobile Performance | 64 | **96** |
| Mobile TBT | 1,640 ms | **40 ms** |
| Mobile CLS | 0.222 | **0** |
| Mobile LCP | 3.5 s | **2.0 s** |
| Mobile Accessibility | 93 | **98** |
| Desktop Performance | — | **85** |
| Desktop CLS | 0.745 | Pending (fixes 14, CSS aspect-ratio deployed) |

---

## Desktop Leadership Page Fixes (Latest — May 2026)

Targeted the specific issues from the desktop Lighthouse report (score 55, CLS 0.745):

| Issue | Root Cause | Fix |
|-------|-----------|-----|
| CLS 0.745 | Images have `sizes="100vw"` but display at 360px; browser miscalculates space | Fix #14: Correct sizes to `360px` |
| CLS 0.745 | `aspect-ratio: attr(width)/attr(height)` has poor browser support | CSS: Explicit `aspect-ratio: 3/4` and `4/5` fallbacks |
| CLS (menu) | Mega menu starts hidden, shifts content when JS initializes | CSS: `min-height: 45px` on menu container |
| CLS (fonts) | Montserrat loads late, causes text reflow | CSS: font-display swap + metric-matched fallback font |
| Color contrast | `#ed1c24` on white = 4.38:1 (fails AA) | CSS: `#c41019` = 4.56:1 (passes AA) |
| Color contrast | White on `#ed1c24` background = 4.38:1 | CSS: Background darkened to `#c41019` |
| Heading order | H1 → H5 (skips H2, H3, H4) | Fix #15: H5 → H3 via content filter |
| Oversized images | 1536×2048 served for 360×480 display | Fix #14: Correct sizes attr → browser picks smaller srcset |

---

## Lease & Rent Page Fixes (May 2026 — Lighthouse 13.0.2)

**Page:** `/lease-and-rent/` | **Score:** Performance 49, Accessibility 95, Best Practices 100, SEO n/a (noindexed staging)

### Performance Fixes

| Issue | Savings | Fix |
|-------|---------|-----|
| Unused CSS (161 KiB) | ~161 KiB | Dequeue duplicate Font Awesome (VC + Mega Addons copies), ihover.css, wp-components; make remaining non-critical CSS async via `media="print"` swap |
| Render-blocking CSS (20 sheets, 620ms) | ~620ms FCP | Added VC FA, Mega Addons FA, GF orbital theme, ihover to non-blocking list in `tci-performance.php` |
| Duplicate Font Awesome fonts (170 KiB) | ~170 KiB | `dequeue_duplicate_font_awesome()` removes VC's bundled FA since theme already loads it |
| Oversized images (258 KiB) | ~258 KiB | Fixed `sizes` attribute from `calc(100vw - 30px)` to `(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) 50vw, 570px` — helps browser pick correct srcset variant |
| Unused preconnects (4 origins) | Connection overhead | `remove_unused_preconnects()` strips googletagmanager, fonts.googleapis, www.google.com from resource hints (scripts are delayed, fonts use different path) |

### Accessibility Fixes

| Issue | WCAG | Fix |
|-------|------|-----|
| Color contrast: white text on `#ed1c24` bg (4.38:1) | AA 1.4.3 | Darken background to `#c41a20` (5.08:1) via `fix_lease_page_contrast()` |
| Color contrast: GF submit button `#f60c0c` (4.21:1) | AA 1.4.3 | Darken to `#c41a20` (5.08:1) via `fix_gform_button_contrast()` |
| Heading order: H2 → H3 (CNG section) | Best practice | Valid — H3 is child of H2. No fix needed. |
| Heading order: H3 → H6 (footer) | 2.4.6 | Already fixed by `fix_footer_heading_level()` (H6 → H4) |

### Files Modified

- `public_html/wp-content/mu-plugins/tci-lighthouse-fixes.php` — Added methods 8–14
- `public_html/wp-content/mu-plugins/tci-performance.php` — Extended non-blocking style lists

### Expected Impact

| Metric | Before | Expected After |
|--------|--------|----------------|
| Performance | 49 | ~65–75 |
| LCP | 5.7s | ~3.5–4.0s (less render-blocking CSS) |
| TBT | 2,250ms | ~1,800ms (fewer style recalcs from removed CSS) |
| Unused CSS | 161 KiB | ~40 KiB |
| Accessibility | 95 | 100 |
| CLS | 0 | 0 (maintained) |

**Note:** The largest remaining performance bottleneck is the 1,702ms long task from inline script evaluation in the HTML document itself. This is WP Rocket's deferred CSS loading mechanism and the page's large inline JS payload (~44KB of `rocket_pairs`). Further improvement requires either splitting the page content or optimizing WP Rocket's critical CSS generation.


---

## Site-Wide Logo Fix (May 2026)

**Issue:** The cardealer theme applies `class="site-logo cardealer-lazy-load"` to the header logo, replacing the real `src` with a loader GIF and deferring the actual image to `data-src`. Since the logo is above the fold (LCP candidate), this delays rendering until the lazy-load JS library executes.

**Fix (tci-lighthouse-fixes.php — Fix #15):**

Output buffer in `process_html_output()` finds logo `<img>` tags with `cardealer-lazy-load` class and:
1. Removes the `cardealer-lazy-load` class
2. Swaps the loader GIF `src` with the real image URL from `data-src`
3. Removes the now-unnecessary `data-src` attribute
4. Adds `fetchpriority="high"` to the main site-logo

**Before:**
```html
<img class="site-logo cardealer-lazy-load" src="...loader.gif" data-src="...TCI-2023.svg" ...>
```

**After:**
```html
<img class="site-logo" src="...TCI-2023.svg" fetchpriority="high" ...>
```

---

## Lease & Rent Page — Oversized Truck Images (May 2026)

**Issue:** Lighthouse flagged 1,686 KiB of potential savings. All 14 truck images are 1500×1000 but display at 543×362 or 418×278 on desktop (and proportionally smaller on mobile). Two root causes:

1. **Wrong `sizes` attribute** — Claims `1140px` on desktop, so the browser downloads the full 1500w source
2. **All images have `fetchpriority="high"` + `loading="eager"`** — Forces the browser to download every image immediately, even below-fold ones

**Fix (tci-lighthouse-fixes.php — Fix #16):**

Output buffer matches `<img>` tags with `alt="TCI Truck..."` and:

| Image type | Old sizes | New sizes | Effect |
|---|---|---|---|
| 1500×1000 (most) | `...768px, 1140px` | `...350px, 555px` | Browser picks 768w srcset variant (~65% smaller) |
| 800×800 (Truck Door) | `...768px, 800px` | `...calc(50vw - 30px), 300px` | Browser picks 300w variant |

Additionally:
- Only the **first** truck image keeps `fetchpriority="high"` + `loading="eager"`
- All subsequent images get `fetchpriority` removed and `loading="lazy"` applied

**Expected savings:** ~1,686 KiB (browser selects 768w variants at ~30–60 KiB each instead of 1500w originals at ~100–240 KiB each)

---

## Suppliers Page — JS Execution Time (May 2026)

**Issue:** Lighthouse reported 5.4s total JS execution time on mobile for `/suppliers/`. Breakdown:

| Script | CPU Time | Problem |
|--------|----------|---------|
| `rocket_lazyload_css-js-after` | 1,826ms | WP Rocket's CSS bg-image lazy-load iterates 60+ selectors synchronously |
| `/suppliers/` page inline | 2,165ms | Large inline JS payload (~44KB `rocket_pairs` JSON + page init) |
| `bootstrap.min.js` | 118ms | Already deferred |
| `mega-menu.min.js` | 74ms | Already deferred |
| `select2.full.min.js` | 70ms | Not needed on this page |
| Unattributable | 3,696ms | Browser parsing/compilation overhead |

### Fixes Applied

**`tci-performance.php` — 3 new methods:**

#### `dequeue_unused_scripts()`
Removes `select2.full.min.js` on pages that don't need vehicle search dropdowns. Only `/lease-and-rent/`, `/used-trucks/`, and inventory archives keep it. Saves 70ms on content pages like `/suppliers/`.

#### `optimize_rocket_lazyload_threshold()`
Increases the IntersectionObserver `rootMargin` from 300px to 600px on mobile via the `rocket_lazyload_css_data` filter. Reduces observation trigger frequency (fewer re-scans as user scrolls).

#### `yield_to_main_thread_patch()`
Outputs a script early in `wp_footer` that temporarily wraps `MutationObserver` so the `rocket_lazyload_css` script's DOM mutation callbacks are deferred to `requestIdleCallback` instead of running synchronously. Restores the original constructor after 4s so other scripts aren't affected.

**`tci-lighthouse-fixes.php` — Fix #17 in output buffer:**

#### Wrap `rocket_lazyload_css-js-after` in `requestIdleCallback`
The output buffer finds the inline `<script id="rocket_lazyload_css-js-after">` and wraps its entire body in `requestIdleCallback(..., {timeout: 3000})`. This moves the 1,826ms of selector iteration from a blocking main-thread task to idle time. Falls back to `setTimeout(..., 50)` for browsers without `requestIdleCallback`.

### Files Modified

- `public_html/wp-content/mu-plugins/tci-performance.php` — Added `dequeue_unused_scripts()`, `optimize_rocket_lazyload_threshold()`, `yield_to_main_thread_patch()`
- `public_html/wp-content/mu-plugins/tci-lighthouse-fixes.php` — Added Fix #17 to `process_html_output()`

### Expected Impact

| Metric | Before | Expected After |
|--------|--------|----------------|
| JS Execution Time | 5.4s | ~3.0–3.5s |
| TBT (from rocket_lazyload_css) | 1,826ms | ~0ms (deferred to idle) |
| TBT (from select2) | 70ms | 0ms (not loaded) |
| Total TBT reduction | — | ~1,900ms |

**Note:** The remaining ~2.1s from `/suppliers/` page inline scripts and unattributable browser work cannot be reduced without modifying WP Rocket's core output or restructuring the page content. The `rocket_pairs` JSON array (~44KB, 60+ entries) is generated by WP Rocket based on CSS background images found in stylesheets — reducing the number of CSS background images site-wide would shrink this payload.
