# Requirements Document

## Introduction

This specification covers Phase 2 performance and accessibility optimizations for the TCI Transportation website running on the CarDealer WordPress theme. Phase 1 (completed in `.kiro/specs/cardealer-theme-optimization/`) addressed conditional asset loading within the theme's asset management system. This Phase 2 addresses the remaining performance issues identified in a fresh Lighthouse report against the staging site, targeting render-blocking resources, font delivery, image optimization, third-party script management, and accessibility failures.

Current Lighthouse Performance Score: 31. Key bottlenecks include 44 render-blocking resources (~1,640ms savings potential), ~1,892 KiB of unused JavaScript, font files missing `font-display: swap` (~1,070ms savings), unoptimized image delivery, and accessibility violations (color contrast, missing link names, viewport restrictions, heading order).

All code changes are scoped to theme files (`public_html/wp-content/themes/cardealer/` and child theme) and mu-plugins (`public_html/wp-content/mu-plugins/`). Third-party plugin configuration (GTM consolidation, Facebook Pixel optimization) and hosting-level changes (server caching, CDN, TTFB reduction) are documented as recommendations but are out of scope for code implementation.

## Glossary

- **Render_Blocking_Resource**: A CSS or JavaScript file loaded in the document `<head>` without `async`, `defer`, or media query attributes, which blocks the browser from rendering page content until the resource is fully downloaded and parsed.
- **Font_Display_Swap**: A CSS `font-display` descriptor value that instructs the browser to use a fallback font immediately and swap in the custom font once loaded, eliminating invisible text during font loading.
- **Critical_CSS**: The minimal set of CSS rules required to render above-the-fold content, inlined directly in the HTML `<head>` to avoid render-blocking external stylesheet requests.
- **Deferred_Script**: A JavaScript file loaded with the `defer` attribute, which downloads in parallel with HTML parsing and executes after the document is fully parsed.
- **Async_Script**: A JavaScript file loaded with the `async` attribute, which downloads in parallel and executes as soon as available (non-blocking but execution order is not guaranteed).
- **Third_Party_Script**: A JavaScript resource loaded from an external domain (e.g., Google Tag Manager, Facebook, LinkedIn, UserWay, ZoomInfo).
- **LCP**: Largest Contentful Paint — the time at which the largest visible content element (image or text block) finishes rendering.
- **TBT**: Total Blocking Time — the total time between FCP and TTI during which the main thread is blocked long enough to prevent input responsiveness.
- **FCP**: First Contentful Paint — the time at which the first text or image is painted to the screen.
- **TTFB**: Time to First Byte — the time from the browser's request to receiving the first byte of the response from the server.
- **WebP**: A modern image format developed by Google that provides superior lossless and lossy compression compared to PNG and JPEG.
- **AVIF**: A modern image format based on the AV1 video codec that provides better compression than WebP for photographic images.
- **Topbar_Social_Links**: The social media icon links (Facebook, Twitter/X, LinkedIn, Instagram) rendered in the site's top navigation bar via the `cardealer_topbar_layout_content()` function with the `social_profiles` case.
- **WCAG**: Web Content Accessibility Guidelines — the international standard for web accessibility, with Level AA as the typical compliance target.
- **Color_Contrast_Ratio**: The relative luminance ratio between foreground text and its background, measured per WCAG 2.1 Success Criterion 1.4.3 (minimum 4.5:1 for normal text, 3:1 for large text).
- **Accessible_Name**: The programmatic name of an interactive element (link, button) that assistive technologies announce to users, derived from visible text, `aria-label`, `aria-labelledby`, or `title` attributes.
- **MU_Plugin**: A WordPress Must-Use plugin located in `wp-content/mu-plugins/` that is automatically activated and cannot be deactivated through the admin interface.
- **Font_Awesome_CSS**: The `all.min.css` stylesheet from Font Awesome 6.2.1 located at `fonts/font-awesome/css/all.min.css` within the CarDealer theme, which currently uses `font-display: block`.
- **Dashicons_CSS**: The WordPress admin icon font stylesheet (`dashicons.min.css`, 35KB) that is loaded on the frontend when the admin bar is active or when plugins depend on it.
- **JS_Composer_CSS**: The WPBakery Page Builder stylesheet (`js_composer.min.css`, 40KB) loaded render-blocking with 98.9% of its CSS unused on the audited page.
- **Google_Fonts_CSS**: The external Google Fonts stylesheet loaded render-blocking in the document head.
- **Hero_Image**: The primary above-the-fold banner image (`web-slider-01-1.png`, 335KB) displayed on the homepage.

## Requirements

### Requirement 1: Defer Non-Critical JavaScript

**User Story:** As a site visitor, I want JavaScript files that are not needed for initial page rendering to load without blocking the browser, so that the page becomes visible and interactive faster.

#### Acceptance Criteria

1. WHEN the page HTML is rendered, THE MU_Plugin SHALL add the `defer` attribute to all enqueued JavaScript files that are not critical for above-the-fold rendering.
2. THE MU_Plugin SHALL exclude jQuery core (`jquery-core`) and jQuery Migrate (`jquery-migrate`) from deferral to prevent dependency errors in inline scripts.
3. THE MU_Plugin SHALL exclude scripts that already have the `async` attribute from receiving the `defer` attribute.
4. WHEN a script tag is modified with the `defer` attribute, THE MU_Plugin SHALL preserve all other existing attributes of the script tag (src, id, type).
5. IF a deferred script causes a JavaScript error due to execution order dependency, THEN THE MU_Plugin SHALL provide a filter hook (`tci_defer_script_exclusions`) to allow specific script handles to be excluded from deferral.

### Requirement 2: Optimize Render-Blocking CSS Delivery

**User Story:** As a site visitor, I want non-critical CSS files to load without blocking page rendering, so that I see page content faster.

#### Acceptance Criteria

1. WHEN the page HTML is rendered, THE MU_Plugin SHALL convert the Dashicons_CSS stylesheet to non-render-blocking by adding `media="print"` with an `onload` handler that switches to `media="all"`.
2. WHEN the page HTML is rendered and the current user is not logged in, THE MU_Plugin SHALL dequeue the Dashicons_CSS stylesheet entirely.
3. WHEN the page HTML is rendered, THE MU_Plugin SHALL convert the JS_Composer_CSS stylesheet to non-render-blocking by adding `media="print"` with an `onload` handler that switches to `media="all"`.
4. WHEN the page HTML is rendered, THE MU_Plugin SHALL convert the Google_Fonts_CSS stylesheet to non-render-blocking using a preload/swap pattern (`<link rel="preload" as="style">` with `onload="this.rel='stylesheet'"`).
5. THE MU_Plugin SHALL provide a filter hook (`tci_nonblocking_styles`) that accepts an array of stylesheet handles to convert to non-render-blocking loading.

### Requirement 3: Add font-display: swap to Font Awesome

**User Story:** As a site visitor, I want text rendered with Font Awesome icons to be visible immediately using fallback characters, so that I do not see invisible text while icon fonts load.

#### Acceptance Criteria

1. THE MU_Plugin SHALL add an inline style override after the Font_Awesome_CSS that sets `font-display: swap` for all Font Awesome `@font-face` declarations (font families: "Font Awesome 6 Free", "Font Awesome 6 Brands", "Font Awesome 5 Free", "Font Awesome 5 Brands", "FontAwesome").
2. WHEN the Font Awesome font files are loading, THE browser SHALL display fallback text instead of invisible text (FOIT).
3. THE MU_Plugin SHALL not modify the Font Awesome CSS source files directly, using `wp_add_inline_style()` to append the override after the registered stylesheet.

### Requirement 4: Preload LCP Hero Image

**User Story:** As a site visitor, I want the main hero banner image to begin downloading as early as possible, so that the largest visible element renders faster (improving LCP).

#### Acceptance Criteria

1. WHEN the homepage is loaded, THE MU_Plugin SHALL output a `<link rel="preload" as="image">` tag in the `<head>` for the Hero_Image file.
2. THE MU_Plugin SHALL specify the `fetchpriority="high"` attribute on the preload link to signal browser priority.
3. WHEN a WebP or AVIF version of the Hero_Image exists in the same directory, THE MU_Plugin SHALL preload the modern format version instead of the PNG, using the `type` attribute to specify the MIME type.
4. THE MU_Plugin SHALL provide a filter hook (`tci_preload_hero_images`) that accepts an array of page-specific hero image URLs to preload.

### Requirement 5: Serve Hero Image in Modern Format

**User Story:** As a site owner, I want the hero banner image converted from PNG to WebP format, so that the file size is reduced and the page loads faster.

#### Acceptance Criteria

1. THE theme assets SHALL include a WebP version of the Hero_Image (`web-slider-01-1.webp`) in the same directory as the original PNG.
2. WHEN the hero slider markup is rendered, THE template SHALL use a `<picture>` element with a `<source>` tag for WebP format and an `<img>` fallback for the PNG.
3. THE WebP version of the Hero_Image SHALL be at least 30% smaller in file size than the original 335KB PNG.
4. THE `<img>` fallback element SHALL include `width` and `height` attributes matching the image's intrinsic dimensions to prevent layout shift.

### Requirement 6: Properly Size Culture Section Images

**User Story:** As a site visitor, I want images in the culture section to be served at their display dimensions, so that the browser does not download unnecessarily large image files.

#### Acceptance Criteria

1. WHEN culture section images are displayed at 300x300 pixels, THE template SHALL reference image files that are no larger than 600x600 pixels (2x for retina displays).
2. THE template SHALL use the `srcset` and `sizes` attributes on culture section `<img>` elements to allow the browser to select the appropriate image size.
3. THE `<img>` elements for culture section images SHALL include `width` and `height` attributes matching the intended display dimensions to prevent layout shift.
4. THE `<img>` elements for culture section images SHALL include `loading="lazy"` since they appear below the fold.

### Requirement 7: Defer Third-Party Tracking Scripts

**User Story:** As a site owner, I want third-party tracking scripts (analytics, pixels, widgets) to load without blocking page rendering or main thread execution, so that site performance is not degraded by external services.

#### Acceptance Criteria

1. THE MU_Plugin SHALL provide a mechanism to delay the loading of Third_Party_Scripts until after user interaction (scroll, click, or touch) or after a configurable timeout (default: 5 seconds).
2. WHEN the page loads, THE MU_Plugin SHALL not execute Third_Party_Scripts during the critical rendering path (before FCP).
3. THE MU_Plugin SHALL support delaying scripts identified by URL pattern matching (e.g., `googletagmanager.com`, `connect.facebook.net`, `cdn.userway.org`, `snap.licdn.com`, `ws.zoominfo.com`).
4. THE MU_Plugin SHALL provide a filter hook (`tci_delayed_script_patterns`) that accepts an array of URL patterns for scripts to delay.
5. IF a Third_Party_Script is critical for user-facing functionality (e.g., UserWay accessibility widget), THEN THE MU_Plugin SHALL allow that script to be excluded from delay via the filter hook.

### Requirement 8: Consolidate Google Analytics Scripts

**User Story:** As a site owner, I want redundant Google Analytics scripts removed, so that the page does not load multiple tracking libraries that perform the same function.

#### Acceptance Criteria

1. THE MU_Plugin SHALL detect when both legacy `analytics.js` (Universal Analytics) and modern `gtag.js` (GA4) are loaded on the same page.
2. WHEN both analytics libraries are detected, THE MU_Plugin SHALL dequeue the legacy `analytics.js` script since Universal Analytics has been sunset.
3. THE MU_Plugin SHALL detect when multiple Google Tag Manager container scripts are loaded and log a warning in the browser console identifying the duplicate container IDs.
4. THE MU_Plugin SHALL provide a filter hook (`tci_dequeue_analytics_handles`) to control which legacy analytics script handles are removed.

### Requirement 9: Fix Color Contrast for Accessibility

**User Story:** As a site visitor with low vision, I want all text to have sufficient contrast against its background, so that I can read the content without difficulty.

#### Acceptance Criteria

1. THE theme CSS SHALL ensure that white text (#ffffff) on the primary red background achieves a contrast ratio of at least 4.5:1 for normal text per WCAG 2.1 SC 1.4.3.
2. WHEN the primary brand red (#ed1c24) does not meet the 4.5:1 ratio with white text (current ratio: 4.38:1), THE theme CSS SHALL darken the red background color to at least #d91a21 (ratio: 4.52:1) or use an alternative that meets the requirement.
3. THE theme CSS change SHALL apply to all elements using the primary red as a background color with white foreground text (buttons, topbar background, accent sections).
4. THE theme CSS SHALL not alter the brand red color when used in contexts that already meet contrast requirements (e.g., red text on white background, which exceeds 4.5:1).

### Requirement 10: Add Accessible Names to Social Media Links

**User Story:** As a site visitor using a screen reader, I want social media icon links to have descriptive accessible names, so that I know which social platform each link navigates to.

#### Acceptance Criteria

1. WHEN the Topbar_Social_Links are rendered, THE `cardealer_topbar_layout_content()` function SHALL include an `aria-label` attribute on each social link `<a>` element with the platform name (e.g., `aria-label="Facebook"`, `aria-label="LinkedIn"`).
2. WHEN social links are rendered in the footer maintenance template, THE template SHALL include an `aria-label` attribute on each social link `<a>` element with the platform name.
3. WHEN social links are rendered on team member single pages, THE template SHALL include an `aria-label` attribute on each social link `<a>` element with the platform name.
4. THE `aria-label` value SHALL match the `title` field from the social profile configuration data (e.g., "Facebook", "Twitter", "LinkedIn", "Instagram").

### Requirement 11: Remove Viewport Zoom Restriction

**User Story:** As a site visitor with low vision, I want to be able to pinch-to-zoom on the page, so that I can enlarge content to a readable size.

#### Acceptance Criteria

1. THE header.php template SHALL not include `maximum-scale=1` or `user-scalable=no` in the viewport meta tag.
2. THE header.php viewport meta tag SHALL use `width=device-width, initial-scale=1` without zoom-restricting parameters.
3. WHEN the viewport meta tag is updated, THE header.php template SHALL preserve the `maximum-scale=5` value (or remove the maximum-scale parameter entirely) to allow zooming up to at least 500%.

### Requirement 12: Fix Heading Hierarchy in Footer

**User Story:** As a site visitor using a screen reader, I want the page heading structure to follow a logical order without skipping levels, so that I can navigate the page structure predictably.

#### Acceptance Criteria

1. THE footer template SHALL not use `<h6>` elements that skip heading levels from the preceding content hierarchy.
2. WHEN footer section headings are rendered, THE footer template SHALL use heading levels that follow sequentially from the page content (e.g., `<h2>` or `<h3>` for footer section titles, depending on the page's heading structure).
3. IF the footer currently uses `<h6>` for widget titles, THEN THE footer template SHALL change those to `<h4>` or a semantically appropriate level that does not skip from the main content's heading hierarchy.

### Requirement 13: Add Sticky Logo Explicit Dimensions

**User Story:** As a site visitor, I want the sticky header logo to have explicit width and height attributes, so that the browser can reserve the correct space and prevent layout shift when the logo loads.

#### Acceptance Criteria

1. WHEN the sticky header logo `<img>` element is rendered, THE template SHALL include explicit `width` and `height` attributes matching the image's intended display dimensions.
2. THE sticky logo `<img>` element SHALL maintain its current visual appearance (CSS may override dimensions for responsive behavior, but the HTML attributes prevent CLS).
3. THE sticky logo `width` and `height` attribute values SHALL match the image's intrinsic aspect ratio to prevent distortion.

### Requirement 14: Document Out-of-Scope Recommendations

**User Story:** As a site owner, I want a documented list of performance improvements that require hosting-level or third-party plugin changes, so that I can address them separately.

#### Acceptance Criteria

1. THE requirements document SHALL include a Recommendations section documenting hosting-level optimizations (server caching, CDN, TTFB reduction below 600ms).
2. THE requirements document SHALL include recommendations for consolidating the three separate GTM/gtag scripts into a single GTM container.
3. THE requirements document SHALL include a recommendation to remove the legacy `analytics.js` Universal Analytics script from the GTM container or plugin configuration.
4. THE requirements document SHALL include a recommendation to evaluate the UserWay accessibility widget's performance impact (~115KB) against its accessibility benefits.
5. THE requirements document SHALL include a recommendation to implement a Content Security Policy (CSP) header and HSTS header at the server/hosting level.
6. THE requirements document SHALL include a recommendation to address the 55 third-party cookies via cookie consent management.

## Out-of-Scope Recommendations

The following optimizations require changes outside the theme files and mu-plugins, and are documented here for the site owner to address separately:

### Hosting and Server

1. **Reduce TTFB below 600ms** — Current TTFB is 860ms. Implement server-side page caching (e.g., WP Super Cache, WP Rocket, or GoDaddy's built-in caching), enable OPcache for PHP, and consider a CDN (Cloudflare, etc.) to reduce server response time.
2. **Enable Brotli/GZIP compression** — Ensure text-based assets (HTML, CSS, JS) are served with Brotli or GZIP compression at the server level.
3. **Implement HTTP/2 or HTTP/3** — Multiplexed connections reduce the impact of multiple asset requests.

### Third-Party Script Consolidation

4. **Consolidate GTM containers** — Three separate gtag/GTM scripts (~650KB total) should be consolidated into a single GTM container that manages all tags (GA4, Facebook Pixel, LinkedIn, ZoomInfo).
5. **Remove legacy analytics.js** — Universal Analytics was sunset in July 2023. The legacy `analytics.js` script should be removed from whatever plugin or GTM container is loading it.
6. **Evaluate Facebook Pixel loading** — The Facebook Pixel (~143KB) should be loaded via GTM with a delayed trigger rather than directly in the page head.

### Security Headers

7. **Implement CSP header** — Add a Content-Security-Policy header at the server or CDN level to control which domains can load resources.
8. **Enable HSTS** — Add Strict-Transport-Security header to enforce HTTPS connections.
9. **Add COOP header** — Add Cross-Origin-Opener-Policy header for cross-origin isolation.

### Cookie Management

10. **Implement cookie consent** — 55 third-party cookies are set without user consent. Implement a cookie consent management platform that blocks non-essential cookies until consent is given.

### Widget Evaluation

11. **Evaluate UserWay widget** — The UserWay accessibility widget adds ~115KB of JavaScript. Evaluate whether the accessibility features it provides can be achieved through native theme improvements (which this spec partially addresses) and whether the widget can be loaded on-demand rather than on every page.

### SEO (Staging-Specific)

12. **Remove noindex/nofollow on production** — The staging site has `noindex, nofollow` meta tags. Ensure these are removed before or during production deployment.
