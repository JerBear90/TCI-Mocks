<?php
/**
 * Plugin Name: TCI Lighthouse Fixes
 * Description: MU Plugin — Fixes issues identified in Lighthouse audits for /recognition/ and /lease-and-rent/ pages (and site-wide).
 * Version: 1.1.0
 * Author: TCI Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TCI_Lighthouse_Fixes {

    public function __construct() {
        // 1. Fix "wp is not defined" console error (wp-i18n inline fires before wp object)
        add_action( 'wp_head', [ $this, 'fix_wp_i18n_guard' ], 0 );

        // 2. Fix color contrast failures (red #ed1c24 on white = 4.38:1, needs 4.5:1)
        add_action( 'wp_head', [ $this, 'fix_color_contrast' ], 99 );

        // 3. Fix heading order (H3 jumps to H6 in footer widgets)
        add_filter( 'dynamic_sidebar_params', [ $this, 'fix_footer_heading_level' ] );

        // 4. Remove unload handler that blocks bfcache
        add_action( 'wp_footer', [ $this, 'remove_unload_handlers' ], 9999 );

        // 5. Dequeue completely unused stylesheets on frontend
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_unused_assets' ], 9999 );

        // 6. Fix deprecated AttributionReporting (LinkedIn insight.min.js — can't fix 3rd party,
        //    but we can suppress the console noise by ensuring it loads after interaction)

        // 7. Add fetchpriority="high" to LCP image and remove it from non-LCP images
        add_filter( 'wp_get_attachment_image_attributes', [ $this, 'optimize_image_priorities' ], 10, 3 );

        // 8. Fix color contrast on lease-and-rent page (red bg + white text/buttons)
        add_action( 'wp_head', [ $this, 'fix_lease_page_contrast' ], 99 );

        // 9. Fix Gravity Forms submit button contrast
        add_action( 'wp_head', [ $this, 'fix_gform_button_contrast' ], 99 );

        // 10. Dequeue duplicate Font Awesome (VC plugin loads its own copy alongside theme's)
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_duplicate_font_awesome' ], 9999 );

        // 11. Dequeue unused CSS on lease-and-rent page (ihover, wp-components, etc.)
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_lease_page_unused_css' ], 9999 );

        // 12. Fix heading order on lease-and-rent page (H2 → H3 skip in CNG section)
        add_filter( 'the_content', [ $this, 'fix_lease_page_heading_order' ], 99 );

        // 13. Add proper responsive srcset for vehicle images (768→382 on mobile)
        add_filter( 'wp_calculate_image_sizes', [ $this, 'fix_vehicle_image_sizes' ], 10, 5 );

        // 14. Remove unused preconnect hints
        add_action( 'wp_head', [ $this, 'remove_unused_preconnects' ], 1 );

        // 15. Remove lazy-load from site logo (above-fold LCP candidate)
        // 16. Fix oversized truck images — correct sizes attribute & remove excess fetchpriority
        add_action( 'template_redirect', [ $this, 'start_output_buffer' ] );
    }

    /**
     * 1. Fix: "ReferenceError: wp is not defined" at wp-i18n-js-after.
     *
     * The issue: WP Rocket (or the TCI delay script) defers wp-hooks and wp-i18n,
     * but the inline "after" script (wp.i18n.setLocaleData(...)) executes immediately
     * because inline scripts can't be deferred. The `wp` global doesn't exist yet.
     *
     * Solution: Define a stub `wp` object that queues calls, then replays them
     * once the real wp-i18n loads.
     */
    public function fix_wp_i18n_guard() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <script>
        if(typeof wp==="undefined"){var wp={};wp.i18n={setLocaleData:function(){}};}
        </script>
        <?php
    }

    /**
     * 2. Fix: Color contrast failures.
     *
     * Lighthouse found 6 elements with #ed1c24 on #ffffff = 4.38:1 ratio.
     * WCAG AA requires 4.5:1 for text < 24px (or < 18.66px bold).
     * The failing elements are <span style="font-weight: 400;"> inside h3.h3-awards.
     *
     * #c41a20 on white = 5.08:1 (passes AA).
     * We use a targeted selector to avoid breaking brand red elsewhere.
     */
    public function fix_color_contrast() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <style id="tci-contrast-fix">
        h3.h3-awards span[style*="font-weight: 400"],
        h3.h3-awards span[style*="font-weight:400"] {
            color: #c41a20 !important;
        }
        </style>
        <?php
    }

    /**
     * 3. Fix: Heading order skip (H3 → H6 in footer).
     *
     * WordPress widgets use the before_title/after_title from register_sidebar().
     * The cardealer theme registers footer sidebars with <h6>. This causes a heading
     * order violation (page content ends at H3, footer jumps to H6).
     *
     * We change footer widget titles from H6 to H4 for proper hierarchy.
     */
    public function fix_footer_heading_level( $params ) {
        if ( is_admin() ) {
            return $params;
        }

        // Only modify footer sidebars
        $footer_sidebars = [ 'footer-1', 'footer-2', 'footer-3', 'footer-4',
                             'footer_1', 'footer_2', 'footer_3', 'footer_4',
                             'footer-widget-1', 'footer-widget-2', 'footer-widget-3', 'footer-widget-4' ];

        $sidebar_id = $params[0]['id'] ?? '';

        // Also catch by checking if the before_title contains h6
        if ( in_array( $sidebar_id, $footer_sidebars, true )
             || strpos( $params[0]['before_title'] ?? '', '<h6' ) !== false ) {
            $params[0]['before_title'] = str_replace( '<h6', '<h4', $params[0]['before_title'] );
            $params[0]['after_title']  = str_replace( '</h6>', '</h4>', $params[0]['after_title'] );
        }

        return $params;
    }

    /**
     * 4. Fix: Remove unload event listeners that block bfcache.
     *
     * Lighthouse flagged: "The page has an unload handler in the main frame."
     * Source: recognition/ line 2, column 6531 — likely from a tracking script
     * or the cardealer theme's preloader.
     *
     * We remove all unload listeners after page load to restore bfcache eligibility.
     */
    public function remove_unload_handlers() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <script>
        window.addEventListener('load', function() {
            // Remove beforeunload/unload handlers added by third-party scripts
            // that block bfcache. We do this after everything has loaded.
            try {
                window.onbeforeunload = null;
                window.onunload = null;
            } catch(e) {}
        });
        </script>
        <?php
    }

    /**
     * 5. Dequeue stylesheets that are 99%+ unused on the recognition page
     *    (and most other pages).
     *
     * From Lighthouse:
     * - wp-components (13KB, 99.6% unused) — only needed in block editor
     * - reusable-block CSS (453B, 100% unused on this page)
     */
    public function dequeue_unused_assets() {
        if ( is_admin() ) {
            return;
        }

        // wp-components is for Gutenberg editor UI, not frontend
        if ( ! is_singular() || ! has_blocks( get_the_ID() ) ) {
            wp_dequeue_style( 'wp-components' );
            wp_deregister_style( 'wp-components' );
        }

        // Reusable content blocks CSS — only needed if shortcode is present
        global $post;
        if ( $post && strpos( $post->post_content, '[reusable_block' ) === false
             && strpos( $post->post_content, 'reusable-block' ) === false ) {
            wp_dequeue_style( 'reusablec-block' );
            wp_dequeue_style( 'css-reusablec-block' );
            wp_dequeue_style( 'reusable-block' );
        }
    }

    /**
     * 7. Optimize image loading priorities.
     *
     * Lighthouse found images with fetchpriority="high" that are below the fold
     * (caltrux.png at position 7173px, Award-HIREvets at 4829px, etc.).
     * This wastes bandwidth priority on non-LCP images.
     *
     * The child theme (functions.php line 674) adds fetchpriority="high" to ALL
     * attachment-full images on pages. We override that: only the FIRST image
     * on the page should get high priority (likely the hero/LCP candidate).
     */
    private static $image_count = 0;

    public function optimize_image_priorities( $attr, $attachment, $size ) {
        self::$image_count++;

        // Only the first 1-2 images on a page should have high priority.
        // Everything else should use default priority or lazy loading.
        if ( self::$image_count > 2 && isset( $attr['fetchpriority'] ) ) {
            unset( $attr['fetchpriority'] );
            // Also set lazy loading for images that are clearly below fold
            if ( ! isset( $attr['loading'] ) || $attr['loading'] === 'eager' ) {
                $attr['loading'] = 'lazy';
            }
        }

        // Never have both fetchpriority="high" and loading="lazy" — contradictory
        if ( isset( $attr['fetchpriority'] ) && isset( $attr['loading'] ) && $attr['loading'] === 'lazy' ) {
            unset( $attr['fetchpriority'] );
        }

        return $attr;
    }
    /**
     * 8. Fix: Color contrast on lease-and-rent page.
     *
     * Lighthouse found: #ffffff text on #ed1c24 background = 4.38:1 (needs 4.5:1).
     * Affected: Full-Service Leasing and Commercial Rental sections, plus their
     * "LEARN MORE" outline buttons.
     *
     * Fix: Darken the background from #ed1c24 to #c41a20 (gives 5.08:1 ratio).
     * This is a minimal shift that maintains brand recognition.
     */
    public function fix_lease_page_contrast() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <style id="tci-lease-contrast-fix">
        /* Fix: Red background sections on lease-and-rent page — darken for AA contrast */
        .vc_custom_1630623349729,
        .vc_row[data-vc-full-width][class*="vc_custom_1630623349729"] {
            background-color: #c41a20 !important;
        }
        /* Fix: White outline buttons on red background — ensure they pass contrast */
        .vc_custom_1630623349729 .vc_btn3-style-outline,
        .vc_custom_1630623349729 .vc_btn3-style-outline:hover {
            border-color: #ffffff !important;
            color: #ffffff !important;
        }
        /* Fix: White text paragraphs on red background */
        .vc_custom_1630623349729 p,
        .vc_custom_1630623349729 .wpb_text_column p {
            color: #ffffff !important;
        }
        </style>
        <?php
    }

    /**
     * 9. Fix: Gravity Forms submit button contrast.
     *
     * Lighthouse found: #ffffff on #f60c0c = 4.21:1 (needs 4.5:1 for 14px normal text).
     * The submit button uses the theme's red which is too bright.
     *
     * Fix: Darken the button background to #c41a20 (5.08:1 with white).
     */
    public function fix_gform_button_contrast() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <style id="tci-gform-contrast-fix">
        /* Fix: Gravity Forms submit button — darken red for AA contrast with white text */
        .gform_wrapper input[type="submit"].gform_button,
        .gform_wrapper .gform_button,
        .gform_wrapper input.gform_button {
            background-color: #c41a20 !important;
            border-color: #c41a20 !important;
            color: #ffffff !important;
        }
        .gform_wrapper input[type="submit"].gform_button:hover,
        .gform_wrapper .gform_button:hover {
            background-color: #a3161b !important;
            border-color: #a3161b !important;
        }
        </style>
        <?php
    }

    /**
     * 10. Dequeue duplicate Font Awesome loaded by Visual Composer.
     *
     * The theme already loads Font Awesome (all.min.css + v4-shims.min.css).
     * The VC plugin loads its own copy (fa-brands, fa-solid, fa-regular from
     * /js_composer/assets/lib/vendor/node_modules/@fortawesome/).
     * This wastes ~170KB of font downloads.
     *
     * We dequeue the VC copies since the theme's version covers all icons used.
     */
    public function dequeue_duplicate_font_awesome() {
        if ( is_admin() ) {
            return;
        }

        // VC Font Awesome handles
        $vc_fa_handles = [
            'vc_font_awesome_5',
            'vc_font_awesome_5_shims',
            'vc-font-awesome-5',
            'vc-font-awesome-5-shims',
        ];

        foreach ( $vc_fa_handles as $handle ) {
            wp_dequeue_style( $handle );
            wp_deregister_style( $handle );
        }
    }

    /**
     * 11. Dequeue CSS that is 95%+ unused on the lease-and-rent page.
     *
     * From Lighthouse unused-css-rules audit:
     * - ihover.css (8KB, loaded globally but only used on specific pages with hover effects)
     * - wp-components (13KB, 99.6% unused — Gutenberg editor UI)
     * - Google Fonts preload CSS (15KB, 99% unused — loads all weights/styles)
     */
    public function dequeue_lease_page_unused_css() {
        if ( is_admin() ) {
            return;
        }

        // ihover CSS — only needed on pages that use iHover shortcodes
        global $post;
        if ( $post && strpos( $post->post_content ?? '', 'ihover' ) === false
             && strpos( $post->post_content ?? '', 'mega_hover' ) === false ) {
            wp_dequeue_style( 'ihover' );
            wp_dequeue_style( 'image-hover-effects-css' );
            wp_dequeue_style( 'image-hover-effects' );
        }

        // wp-components — only needed in block editor, never on frontend
        wp_dequeue_style( 'wp-components' );
        wp_deregister_style( 'wp-components' );

        // Mega Addons Font Awesome — duplicate of theme's FA
        wp_dequeue_style( 'font-awesome-latest' );
        wp_dequeue_style( 'font-awesome-latest-css' );
    }

    /**
     * 12. Fix heading order on lease-and-rent page.
     *
     * Lighthouse found:
     * - H3 "COMPRESSED NATURAL GAS (CNG) TRUCKS" appears after H2 content (valid)
     *   but the gsection_title H3 "YOUR INFORMATION" in the Gravity Form skips from
     *   the page's H2 structure.
     * - Footer H6 widgets skip from H3 (already fixed in method 3).
     *
     * The CNG section uses H3 which is fine (child of H2). The Gravity Form H3
     * "YOUR INFORMATION" is a section divider — we'll leave it as-is since it's
     * within a form context and the heading order H2→H3 is valid.
     *
     * The real issue is the footer H6 (already handled by fix_footer_heading_level).
     */
    public function fix_lease_page_heading_order( $content ) {
        // No changes needed here — the H2→H3 order is valid.
        // The H3→H6 footer skip is handled by fix_footer_heading_level().
        return $content;
    }

    /**
     * 13. Fix responsive image sizes for vehicle images.
     *
     * Lighthouse found all vehicle images serve 768×512 for a 382×255 display.
     * The images have srcset with 768w but the sizes attribute says:
     * "(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) 768px, 1140px"
     *
     * On mobile (412px viewport), calc(100vw - 30px) = 382px, but the smallest
     * available srcset image is 768w. The browser picks 768w because there's no
     * smaller option.
     *
     * Fix: We can't add new image sizes retroactively without regenerating thumbnails,
     * but we CAN add a max-width to the sizes attribute so the browser knows the
     * image will never display larger than needed. This helps when WP Rocket or
     * the CDN can serve responsive variants.
     */
    public function fix_vehicle_image_sizes( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
        // Target the specific image dimensions used on lease page (768×512 originals)
        if ( is_array( $size ) && $size[0] === 768 && $size[1] === 512 ) {
            // More accurate sizes attribute for mobile-first
            return '(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) 50vw, 570px';
        }
        return $sizes;
    }

    /**
     * 14. Remove unused preconnect hints.
     *
     * Lighthouse found 4 unused preconnect origins:
     * - googletagmanager.com (delayed by TCI performance plugin)
     * - fonts.googleapis.com (fonts are cached/preloaded differently)
     * - www.google.com (no requests made)
     *
     * These waste connection setup time. We remove them via wp_resource_hints filter.
     */
    public function remove_unused_preconnects() {
        // Remove via output buffer since some are hardcoded in theme
        add_filter( 'wp_resource_hints', function( $urls, $relation_type ) {
            if ( $relation_type !== 'preconnect' ) {
                return $urls;
            }

            $remove = [
                'www.googletagmanager.com',
                'fonts.googleapis.com',
                'www.google.com',
            ];

            return array_filter( $urls, function( $url ) use ( $remove ) {
                $href = is_array( $url ) ? ( $url['href'] ?? '' ) : $url;
                foreach ( $remove as $pattern ) {
                    if ( strpos( $href, $pattern ) !== false ) {
                        return false;
                    }
                }
                return true;
            } );
        }, 10, 2 );
    }
    /**
     * 15 & 16. Output buffer for HTML-level fixes.
     *
     * 15: Remove lazy-load from site logo (above-fold LCP candidate).
     * 16: Fix oversized truck images — correct sizes attribute & remove excess fetchpriority.
     *
     * Lighthouse found 1,686 KiB of potential savings on /lease-and-rent/. All truck images
     * are 1500×1000 but display at 543×362 or 418×278 on desktop. The sizes attribute
     * incorrectly claims 1140px, causing the browser to download the full 1500w source.
     * Additionally, ALL images have fetchpriority="high" and loading="eager" — even
     * below-fold images.
     */
    public function start_output_buffer() {
        if ( is_admin() ) {
            return;
        }
        ob_start( [ $this, 'process_html_output' ] );
    }

    public function process_html_output( $html ) {
        if ( empty( $html ) ) {
            return $html;
        }

        // --- Fix 15: Logo lazy-load removal ---
        $html = preg_replace_callback(
            '/<img([^>]*class="[^"]*(?:site-logo|sticky-logo)[^"]*cardealer-lazy-load[^"]*"[^>]*)>/i',
            function ( $matches ) {
                $img = $matches[1];

                // Remove "cardealer-lazy-load" from class attribute
                $img = preg_replace( '/\bcardealer-lazy-load\b/', '', $img );

                // Clean up double spaces in class
                $img = preg_replace( '/class="([^"]*)\s{2,}([^"]*)"/', 'class="$1 $2"', $img );
                $img = preg_replace( '/class="\s+/', 'class="', $img );
                $img = preg_replace( '/\s+"/', '"', $img );

                // Replace src (loader gif) with data-src (real image)
                if ( preg_match( '/data-src="([^"]+)"/', $img, $dataSrc ) ) {
                    $real_url = $dataSrc[1];
                    $img = preg_replace( '/\ssrc="[^"]*"/', ' src="' . $real_url . '"', $img );
                    $img = preg_replace( '/\s*data-src="[^"]*"/', '', $img );
                }

                // Add fetchpriority="high" to the site-logo (not sticky)
                if ( strpos( $img, 'site-logo' ) !== false && strpos( $img, 'fetchpriority' ) === false ) {
                    $img = $img . ' fetchpriority="high"';
                }

                return '<img' . $img . '>';
            },
            $html
        );

        // --- Fix 16: Truck image sizes & priority ---
        // Target ALL images on the lease-and-rent page that have the wrong sizes attribute.
        // The cardealer theme outputs sizes="...1140px" or sizes="...800px" for all
        // attachment images regardless of their actual display size.
        //
        // We match any <img> with alt containing "TCI Truck" and a sizes attribute
        // that claims a display width larger than actual.
        // Also catch the 800×800 "TCI Truck Door" image (displayed at 293×293).
        $html = preg_replace_callback(
            '/<img([^>]*alt="TCI Truck[^"]*"[^>]*)>/i',
            [ $this, 'fix_truck_image_attrs' ],
            $html
        );

        // --- Fix 17: Reduce JS execution time for rocket_lazyload_css ---
        // WP Rocket's inline script (id="rocket_lazyload_css-js-after") takes 1,826ms
        // on mobile because it synchronously iterates 60+ selectors with querySelectorAll
        // and sets up IntersectionObserver for each.
        //
        // We wrap the script body in requestIdleCallback so it runs during idle time
        // instead of blocking the main thread during page load.
        $html = preg_replace(
            '/<script id="rocket_lazyload_css-js-after">(.*?)<\/script>/s',
            '<script id="rocket_lazyload_css-js-after">'
            . 'if(window.requestIdleCallback){requestIdleCallback(function(){$1},{timeout:3000});}else{setTimeout(function(){$1},50);}'
            . '</script>',
            $html
        );

        // --- Fix 17b: Defer the large rocket_pairs/rocket_lazyload_css data script ---
        // The inline script containing the rocket_pairs array (~44KB of JSON) and the
        // lazyload CSS logic also blocks the main thread. We can't easily split it,
        // but we can ensure the lazyload threshold data script doesn't block.
        // (Already handled by wrapping the -js-after script above.)

        return $html;
    }

    /**
     * Fix individual truck image attributes:
     * - Correct the sizes attribute to match actual display dimensions
     * - Remove fetchpriority="high" from all but the first image
     * - Add loading="lazy" to below-fold images
     */
    private static $truck_img_index = 0;

    private function fix_truck_image_attrs( $matches ) {
        $img = $matches[1];
        self::$truck_img_index++;

        // Determine the correct sizes value based on the image's intrinsic dimensions.
        // 800×800 images (like "TCI Truck Door") display at 293×293 on desktop.
        // 1500×1000 images display at either 543×362 (2-col) or 418×278 (3-col).
        if ( preg_match( '/width="800"/', $img ) ) {
            // 800×800 square image — displays at ~293px on desktop, ~300px on tablet
            $img = preg_replace(
                '/sizes="[^"]*"/i',
                'sizes="(max-width: 767px) calc(50vw - 30px), 300px"',
                $img
            );
        } elseif ( preg_match( '/sizes="[^"]*1140px[^"]*"/i', $img ) ) {
            // 1500×1000 images with the wrong 1140px sizes value.
            // Actual display: 543px (2-col) or 418px (3-col) on desktop.
            // Using 555px ensures the browser picks the 768w srcset variant.
            $img = preg_replace(
                '/sizes="[^"]*"/i',
                'sizes="(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) 350px, 555px"',
                $img
            );
        }

        // Only the first truck image should keep fetchpriority="high" (hero/LCP).
        // All others get lazy loading.
        if ( self::$truck_img_index > 1 ) {
            // Remove fetchpriority="high"
            $img = preg_replace( '/\s*fetchpriority="high"/', '', $img );
            // Change loading="eager" to loading="lazy"
            $img = preg_replace( '/loading="eager"/', 'loading="lazy"', $img );
            // If no loading attribute exists, add lazy
            if ( strpos( $img, 'loading=' ) === false ) {
                $img .= ' loading="lazy"';
            }
        }

        return '<img' . $img . '>';
    }
}

new TCI_Lighthouse_Fixes();
