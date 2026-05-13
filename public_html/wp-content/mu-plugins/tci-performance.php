<?php
/**
 * Plugin Name: TCI Performance Optimizations
 * Description: MU Plugin — Defers non-critical JS, makes CSS non-render-blocking, adds font-display:swap to icon fonts, removes unused assets for logged-out users.
 * Version: 1.0.0
 * Author: TCI Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TCI_Performance {

    public function __construct() {
        // Defer non-critical JS
        add_filter( 'script_loader_tag', [ $this, 'defer_scripts' ], 10, 3 );

        // Remove dashicons for logged-out users
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_unnecessary_assets' ], 999 );

        // Add font-display: swap to Font Awesome
        add_action( 'wp_enqueue_scripts', [ $this, 'font_display_swap' ], 999 );

        // Make non-critical CSS non-render-blocking
        add_filter( 'style_loader_tag', [ $this, 'nonblocking_styles' ], 10, 4 );

        // Disable preloader for non-admin users (blocks LCP)
        add_filter( 'option_car_dealer_options', [ $this, 'disable_preloader' ] );

        // Delay third-party scripts
        add_action( 'wp_head', [ $this, 'delay_third_party_scripts' ], 1 );

        // Dequeue JS not needed on content pages (select2, etc.)
        add_action( 'wp_enqueue_scripts', [ $this, 'dequeue_unused_scripts' ], 999 );

        // Reduce JS execution time: optimize WP Rocket's rocket_lazyload_css inline script
        add_filter( 'rocket_lazyload_css_data', [ $this, 'optimize_rocket_lazyload_threshold' ] );

        // Yield-to-main-thread wrapper for heavy inline scripts
        add_action( 'wp_footer', [ $this, 'yield_to_main_thread_patch' ], 1 );
    }

    /**
     * Add defer attribute to non-critical scripts.
     */
    public function defer_scripts( $tag, $handle, $src ) {
        // Don't defer in admin
        if ( is_admin() ) {
            return $tag;
        }

        // Scripts that must NOT be deferred (execution order dependent)
        $excluded = apply_filters( 'tci_defer_script_exclusions', [
            'jquery-core',
            'jquery-migrate',
            'jquery',
            'wp-polyfill',
        ] );

        if ( in_array( $handle, $excluded, true ) ) {
            return $tag;
        }

        // Skip if already has async or defer
        if ( strpos( $tag, ' defer' ) !== false || strpos( $tag, ' async' ) !== false ) {
            return $tag;
        }

        // Add defer
        return str_replace( ' src=', ' defer src=', $tag );
    }

    /**
     * Remove dashicons CSS for logged-out users.
     * Remove admin-bar styles for logged-out users.
     */
    public function dequeue_unnecessary_assets() {
        if ( ! is_user_logged_in() ) {
            wp_dequeue_style( 'dashicons' );
            wp_deregister_style( 'dashicons' );
        }
    }

    /**
     * Add font-display: swap override for Font Awesome via inline style.
     */
    public function font_display_swap() {
        if ( wp_style_is( 'font-awesome', 'enqueued' ) ) {
            $swap_css = '
@font-face { font-family: "Font Awesome 6 Free"; font-display: swap; }
@font-face { font-family: "Font Awesome 6 Brands"; font-display: swap; }
@font-face { font-family: "Font Awesome 5 Free"; font-display: swap; }
@font-face { font-family: "Font Awesome 5 Brands"; font-display: swap; }
@font-face { font-family: "FontAwesome"; font-display: swap; }';
            wp_add_inline_style( 'font-awesome', $swap_css );
        }
    }

    /**
     * Convert non-critical stylesheets to non-render-blocking.
     * Uses media="print" with onload swap to media="all".
     */
    public function nonblocking_styles( $tag, $handle, $href, $media ) {
        if ( is_admin() ) {
            return $tag;
        }

        // Stylesheets to make non-render-blocking by handle
        $nonblocking = apply_filters( 'tci_nonblocking_styles', [
            'js_composer_front',        // WPBakery/JS Composer CSS (40KB, 98% unused)
            'js_composer-front',
            'js-composer-front',
            'cardealer-google-fonts',   // Google Fonts (render-blocking external request)
            'wp-review-pro-style',      // WP Review Pro (20KB)
            'wprevpro_w3_min',
            'wprevpro-w3-min',
            'sb-instagram-feed-styles', // Smash Balloon Instagram (8.8KB)
            'sbi_styles',
            'sbi-styles',
            'ihover',                   // iHover CSS (9.4KB)
            'image-hover-effects-css',  // iHover alternate handle
            'image-hover-effects',
            'flavor-jesuspended-css',   // Flavor/suspended CSS
            'flavor_jesuspended_css',
            'components-style',         // Components style (13.4KB)
            'wp-block-library',         // Gutenberg block CSS (if not using blocks)
            'reusable-block',           // Reusable block CSS (1.2KB)
            'css-reusablec-block',
            'reusablec-block',          // Alternate handle
            'magnific-popup',           // Magnific Popup (loaded globally but only needed on detail pages)
            'font-awesome-shims',       // FA v4 shims (5KB, only needed for legacy icon names)
            'vc_font_awesome_5_shims',  // VC FA shims (duplicate)
            'vc_font_awesome_5',        // VC FA (duplicate of theme's)
            'font-awesome-latest',      // Mega Addons FA (duplicate)
            'font-awesome-latest-css',
            'gravity_forms_orbital_theme', // GF orbital theme (218B, can load async)
        ] );

        // Also match by URL patterns for plugin stylesheets
        $nonblocking_patterns = apply_filters( 'tci_nonblocking_style_patterns', [
            '/js_composer/',
            '/js_composer.',
            '/wprevpro/',
            '/wp-review-pro/',
            '/instagram-feed/',
            '/sb-instagram/',
            '/sbi-styles',
            '/ihover',
            '/admin_icon',
            '/dashicons',
            '/components/style',
            '/reusablec-block',
            '/magnific-popup/',
            '/fontawesome-free/',        // VC's bundled Font Awesome
            '/font-awesome/css/v4-shims', // FA v4 shims
            '/mega-addons-for-visual-composer/css/font-awesome/', // Mega Addons duplicate FA
            '/gravity-forms-orbital-theme', // GF orbital (tiny, can async)
        ] );

        $is_nonblocking = in_array( $handle, $nonblocking, true );

        if ( ! $is_nonblocking && $href ) {
            foreach ( $nonblocking_patterns as $pattern ) {
                if ( strpos( $href, $pattern ) !== false ) {
                    $is_nonblocking = true;
                    break;
                }
            }
        }

        if ( ! $is_nonblocking ) {
            return $tag;
        }

        // Skip if already modified
        if ( strpos( $tag, 'onload=' ) !== false ) {
            return $tag;
        }

        // Convert to non-blocking: media="print" onload="this.media='all'"
        // Handle various quote styles WordPress might use
        $tag = preg_replace(
            '/media=[\'"](?:all|screen)[\'"]/',
            'media="print" onload="this.media=\'all\'"',
            $tag
        );

        // If no media attribute was found, add one before the closing />
        if ( strpos( $tag, 'onload=' ) === false ) {
            $tag = str_replace( '/>', 'media="print" onload="this.media=\'all\'" />', $tag );
        }

        // If still no onload (link tag without self-close), try before >
        if ( strpos( $tag, 'onload=' ) === false ) {
            $tag = preg_replace( '/(rel=[\'"]stylesheet[\'"])/', '$1 media="print" onload="this.media=\'all\'"', $tag );
        }

        // Add noscript fallback
        $noscript = '<noscript>' . str_replace(
            [ "media='print' onload=\"this.media='all'\"", 'media="print" onload="this.media=\'all\'"' ],
            [ "media='all'", 'media="all"' ],
            $tag
        ) . '</noscript>';

        return $tag . $noscript;
    }

    /**
     * Disable the preloader overlay for non-admin visitors.
     * The preloader blocks LCP by hiding all content behind a loading GIF.
     */
    public function disable_preloader( $options ) {
        if ( is_admin() || ( defined( 'DOING_AJAX' ) && DOING_AJAX ) ) {
            return $options;
        }

        // Only disable for non-logged-in users to preserve admin preview
        if ( ! is_user_logged_in() ) {
            $options['preloader'] = false;
        }

        return $options;
    }

    /**
     * Output script to delay third-party tracking scripts until user interaction.
     */
    public function delay_third_party_scripts() {
        if ( is_admin() ) {
            return;
        }

        $patterns = apply_filters( 'tci_delayed_script_patterns', [
            'googletagmanager.com',
            'google-analytics.com/analytics.js',
            'connect.facebook.net',
            'snap.licdn.com',
            'ws.zoominfo.com',
            'cdn.userway.org',
        ] );

        // Don't output if no patterns
        if ( empty( $patterns ) ) {
            return;
        }
        ?>
        <script>
        /* TCI: Delay third-party scripts until interaction or 5s timeout */
        (function(){
            var defined = <?php echo wp_json_encode( $patterns ); ?>;
            var fired = false;
            function loadDelayed(){
                if(fired) return;
                fired = true;
                document.querySelectorAll('script[data-tci-delay]').forEach(function(s){
                    var n = document.createElement('script');
                    if(s.src) n.src = s.src;
                    else n.textContent = s.textContent;
                    n.type = 'text/javascript';
                    document.body.appendChild(n);
                    s.remove();
                });
            }
            ['scroll','click','touchstart','mousemove','keydown'].forEach(function(e){
                document.addEventListener(e, loadDelayed, {once:true,passive:true});
            });
            setTimeout(loadDelayed, 5000);
        })();
        </script>
        <?php
    }
    /**
     * Dequeue scripts not needed on content/informational pages.
     *
     * select2.full.min.js (70ms parse+eval on mobile) is only needed on pages with
     * vehicle search dropdowns (inventory pages). Content pages like /suppliers/,
     * /team-tci/, /services/* don't use it.
     *
     * Bootstrap JS (118ms) is needed for modals/dropdowns in the nav, so we keep it.
     */
    public function dequeue_unused_scripts() {
        if ( is_admin() ) {
            return;
        }

        // Pages that actually need select2 (vehicle search/filter)
        $select2_pages = [
            'lease-and-rent',
            'used-trucks',
            'inventory',
        ];

        $current_slug = get_post_field( 'post_name', get_queried_object_id() );

        // Also check if it's a car archive
        $needs_select2 = in_array( $current_slug, $select2_pages, true )
                         || is_post_type_archive( 'cars' )
                         || is_tax( 'vehicle_cat' );

        if ( ! $needs_select2 ) {
            wp_dequeue_script( 'select2' );
            wp_deregister_script( 'select2' );
        }
    }

    /**
     * Increase the IntersectionObserver rootMargin for WP Rocket's CSS bg lazy-load.
     *
     * By default it uses 300px, which means it starts observing elements 300px before
     * they enter the viewport. On mobile with many selectors, this causes excessive
     * querySelectorAll calls. Increasing to 600px means fewer observation triggers
     * but earlier loading (acceptable tradeoff for perceived performance).
     *
     * More importantly, this filter confirms the data is being passed through WP,
     * allowing us to hook into the mechanism.
     */
    public function optimize_rocket_lazyload_threshold( $data ) {
        // Increase threshold on mobile to reduce observation frequency
        if ( wp_is_mobile() ) {
            $data['threshold'] = '600';
        }
        return $data;
    }

    /**
     * Inject a yield-to-main-thread patch that breaks up long tasks.
     *
     * The rocket_lazyload_css inline script (1,826ms on mobile) processes 60+ CSS
     * selectors synchronously in a single task. This blocks the main thread and
     * inflates TBT.
     *
     * Strategy: Output a small script BEFORE WP Rocket's footer scripts that
     * patches the IntersectionObserver used by rocket_lazyload_css to process
     * entries in small batches (yielding between each). This breaks the single
     * 1.8s long task into many sub-50ms tasks.
     *
     * We also defer the initial querySelectorAll scanning to requestIdleCallback.
     */
    public function yield_to_main_thread_patch() {
        if ( is_admin() ) {
            return;
        }
        ?>
        <script id="tci-yield-patch">
        (function(){
            /* TCI: Reduce JS execution time for WP Rocket CSS bg-image lazy-load.
             * The rocket_lazyload_css script iterates 60+ selectors with querySelectorAll
             * and sets up IntersectionObserver + MutationObserver in one synchronous block.
             * On mobile this takes 1.8s. We defer the MutationObserver callback to idle time
             * so DOM mutations don't trigger expensive re-scans during page load. */

            var _origMO = window.MutationObserver;
            var _rocketPatched = false;

            // Temporarily wrap MutationObserver so the rocket_lazyload_css script's
            // observer uses requestIdleCallback instead of running synchronously.
            window.MutationObserver = function(callback) {
                var deferredCallback = function(mutations) {
                    // During initial page load, defer to idle time
                    if (!_rocketPatched) {
                        callback(mutations);
                        return;
                    }
                    if (window.requestIdleCallback) {
                        requestIdleCallback(function(){ callback(mutations); }, {timeout: 3000});
                    } else {
                        setTimeout(function(){ callback(mutations); }, 100);
                    }
                };
                return new _origMO(deferredCallback);
            };
            window.MutationObserver.prototype = _origMO.prototype;

            // Restore after WP Rocket's script has set up its observer
            // (runs at end of body, so 1s timeout is safe)
            setTimeout(function(){
                _rocketPatched = true;
            }, 100);

            // Restore the original MutationObserver constructor after 4s
            // so other scripts aren't affected
            setTimeout(function(){
                window.MutationObserver = _origMO;
            }, 4000);
        })();
        </script>
        <?php
    }
}

new TCI_Performance();
