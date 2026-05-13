<?php
/**
 * Plugin Name: TCI SEO Audit
 * Description: MU Plugin — SEO audit report (wp-admin) + floating on-page debug panel (frontend, admin-only)
 * Version: 1.2.0
 * Author: TCI Dev
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class TCI_SEO_Audit {

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_menu_page' ] );
        add_action( 'wp_ajax_tci_seo_audit_export', [ $this, 'ajax_export' ] );
        add_action( 'wp_ajax_tci_bulk_strip_title_suffix', [ $this, 'ajax_bulk_strip_title_suffix' ] );
        add_action( 'wp_ajax_tci_bulk_comma_locations', [ $this, 'ajax_bulk_comma_locations' ] );
        add_action( 'wp_footer', [ $this, 'render_page_debug_panel' ], 9999 );
        add_action( 'wp_head', [ $this, 'patch_owl_carousel_undefined' ], 1 );
        add_action( 'template_redirect', [ $this, 'block_null_undefined_requests' ] );
    }

    public function add_menu_page() {
        add_management_page(
            'SEO Audit Report',
            'SEO Audit',
            'manage_options',
            'tci-seo-audit',
            [ $this, 'render_page' ]
        );
    }

    /* ------------------------------------------------------------------ */
    /*  DATA COLLECTORS                                                    */
    /* ------------------------------------------------------------------ */

    private function get_site_info() {
        return [
            'site_url'           => site_url(),
            'home_url'           => home_url(),
            'wp_version'         => get_bloginfo( 'version' ),
            'php_version'        => phpversion(),
            'permalink_structure'=> get_option( 'permalink_structure' ) ?: 'Plain (default)',
            'blogname'           => get_option( 'blogname' ),
            'blogdescription'    => get_option( 'blogdescription' ),
            'search_visibility'  => get_option( 'blog_public' ) ? 'Visible' : 'Discouraged',
            'ssl'                => is_ssl() ? 'Yes' : 'No',
            'multisite'          => is_multisite() ? 'Yes' : 'No',
        ];
    }

    private function get_theme_info() {
        $theme = wp_get_theme();
        return [
            'name'        => $theme->get( 'Name' ),
            'version'     => $theme->get( 'Version' ),
            'parent'      => $theme->parent() ? $theme->parent()->get( 'Name' ) : 'None',
            'text_domain' => $theme->get( 'TextDomain' ),
        ];
    }

    private function get_plugins_info() {
        if ( ! function_exists( 'get_plugins' ) ) {
            require_once ABSPATH . 'wp-admin/includes/plugin.php';
        }
        $all    = get_plugins();
        $active = get_option( 'active_plugins', [] );
        $list   = [];
        foreach ( $all as $file => $data ) {
            $list[] = [
                'name'    => $data['Name'],
                'version' => $data['Version'],
                'active'  => in_array( $file, $active, true ),
                'file'    => $file,
            ];
        }
        return $list;
    }

    private function get_seo_plugin_config() {
        $config = [];

        // Yoast SEO
        if ( defined( 'WPSEO_VERSION' ) ) {
            $config['yoast'] = [
                'version'          => WPSEO_VERSION,
                'titles'           => get_option( 'wpseo_titles', [] ),
                'social'           => get_option( 'wpseo_social', [] ),
                'xml_sitemaps'     => get_option( 'wpseo', [] ),
            ];
        }

        // Rank Math
        if ( class_exists( 'RankMath' ) ) {
            $config['rankmath'] = [
                'version'  => defined( 'RANK_MATH_VERSION' ) ? RANK_MATH_VERSION : 'unknown',
                'general'  => get_option( 'rank-math-options-general', [] ),
                'titles'   => get_option( 'rank-math-options-titles', [] ),
                'sitemap'  => get_option( 'rank-math-options-sitemap', [] ),
            ];
        }

        // All in One SEO
        if ( defined( 'AIOSEO_VERSION' ) ) {
            $config['aioseo'] = [
                'version' => AIOSEO_VERSION,
            ];
        }

        if ( empty( $config ) ) {
            $config['notice'] = 'No recognized SEO plugin detected (Yoast, Rank Math, AIOSEO).';
        }

        return $config;
    }

    private function get_posts_meta_audit( $post_type = 'page', $limit = 200 ) {
        $posts = get_posts( [
            'post_type'      => $post_type,
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $results = [];
        foreach ( $posts as $p ) {
            $meta_title = get_post_meta( $p->ID, '_yoast_wpseo_title', true )
                       ?: get_post_meta( $p->ID, 'rank_math_title', true )
                       ?: '';
            $meta_desc  = get_post_meta( $p->ID, '_yoast_wpseo_metadesc', true )
                       ?: get_post_meta( $p->ID, 'rank_math_description', true )
                       ?: '';
            $focus_kw   = get_post_meta( $p->ID, '_yoast_wpseo_focuskw', true )
                       ?: get_post_meta( $p->ID, 'rank_math_focus_keyword', true )
                       ?: '';
            $canonical   = get_post_meta( $p->ID, '_yoast_wpseo_canonical', true )
                        ?: get_post_meta( $p->ID, 'rank_math_canonical_url', true )
                        ?: '';
            $noindex     = get_post_meta( $p->ID, '_yoast_wpseo_meta-robots-noindex', true )
                        ?: get_post_meta( $p->ID, 'rank_math_robots', true )
                        ?: '';
            $og_title    = get_post_meta( $p->ID, '_yoast_wpseo_opengraph-title', true ) ?: '';
            $og_desc     = get_post_meta( $p->ID, '_yoast_wpseo_opengraph-description', true ) ?: '';

            $title_len = strlen( $meta_title ?: $p->post_title );
            $desc_len  = strlen( $meta_desc );

            $issues = [];
            if ( empty( $meta_title ) )                  $issues[] = 'Missing SEO title';
            if ( empty( $meta_desc ) )                   $issues[] = 'Missing meta description';
            if ( $title_len > 60 )                       $issues[] = 'Title too long (' . $title_len . ' chars)';
            if ( $desc_len > 0 && $desc_len < 50 )       $issues[] = 'Meta desc too short (' . $desc_len . ' chars)';
            if ( $desc_len > 160 )                       $issues[] = 'Meta desc too long (' . $desc_len . ' chars)';
            if ( empty( $focus_kw ) )                    $issues[] = 'No focus keyword';

            $results[] = [
                'id'             => $p->ID,
                'title'          => $p->post_title,
                'url'            => get_permalink( $p->ID ),
                'seo_title'      => $meta_title,
                'seo_title_len'  => $title_len,
                'meta_desc'      => $meta_desc,
                'meta_desc_len'  => $desc_len,
                'focus_keyword'  => $focus_kw,
                'canonical'      => $canonical,
                'noindex'        => $noindex,
                'og_title'       => $og_title,
                'og_desc'        => $og_desc,
                'issues'         => $issues,
            ];
        }
        return $results;
    }

    private function get_image_alt_audit( $limit = 300 ) {
        global $wpdb;
        $images = $wpdb->get_results( $wpdb->prepare(
            "SELECT ID, post_title, guid FROM {$wpdb->posts}
             WHERE post_type = 'attachment' AND post_mime_type LIKE %s
             ORDER BY ID DESC LIMIT %d",
            'image/%', $limit
        ) );

        $missing_alt = [];
        $total       = count( $images );
        $with_alt    = 0;

        foreach ( $images as $img ) {
            $alt = get_post_meta( $img->ID, '_wp_attachment_image_alt', true );
            if ( ! empty( $alt ) ) {
                $with_alt++;
            } else {
                $missing_alt[] = [
                    'id'    => $img->ID,
                    'title' => $img->post_title,
                    'url'   => $img->guid,
                ];
            }
        }

        return [
            'total_images'    => $total,
            'with_alt'        => $with_alt,
            'without_alt'     => $total - $with_alt,
            'alt_coverage'    => $total > 0 ? round( ( $with_alt / $total ) * 100, 1 ) . '%' : 'N/A',
            'missing_alt_list'=> array_slice( $missing_alt, 0, 50 ), // cap output
        ];
    }

    private function get_heading_structure_audit( $limit = 50 ) {
        $posts   = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $results = [];
        foreach ( $posts as $p ) {
            $content = $p->post_content;
            preg_match_all( '/<h([1-6])[^>]*>(.*?)<\/h\1>/si', $content, $matches, PREG_SET_ORDER );

            $headings = [];
            $issues   = [];
            $h1_count = 0;

            foreach ( $matches as $m ) {
                $level = (int) $m[1];
                $text  = wp_strip_all_tags( $m[2] );
                $headings[] = [ 'level' => $level, 'text' => $text ];
                if ( $level === 1 ) $h1_count++;
            }

            if ( $h1_count > 1 )          $issues[] = "Multiple H1 tags ($h1_count found)";
            if ( empty( $headings ) )      $issues[] = 'No headings found in content';

            // Check for skipped levels
            $levels_used = array_unique( array_column( $headings, 'level' ) );
            sort( $levels_used );
            for ( $i = 1; $i < count( $levels_used ); $i++ ) {
                if ( $levels_used[ $i ] - $levels_used[ $i - 1 ] > 1 ) {
                    $issues[] = 'Skipped heading level: H' . $levels_used[ $i - 1 ] . ' → H' . $levels_used[ $i ];
                }
            }

            $results[] = [
                'id'       => $p->ID,
                'title'    => $p->post_title,
                'url'      => get_permalink( $p->ID ),
                'headings' => $headings,
                'issues'   => $issues,
            ];
        }
        return $results;
    }

    private function get_robots_txt() {
        $robots_path = ABSPATH . 'robots.txt';
        if ( file_exists( $robots_path ) ) {
            return file_get_contents( $robots_path );
        }
        // WP virtual robots.txt
        return 'No physical robots.txt found. WordPress generates a virtual one.';
    }

    private function get_sitemap_status() {
        $urls = [
            home_url( '/sitemap.xml' ),
            home_url( '/sitemap_index.xml' ),
            home_url( '/wp-sitemap.xml' ),
        ];
        $results = [];
        foreach ( $urls as $url ) {
            $response = wp_remote_head( $url, [ 'timeout' => 5, 'sslverify' => false ] );
            $code     = is_wp_error( $response ) ? 'Error' : wp_remote_retrieve_response_code( $response );
            $results[ $url ] = $code;
        }
        return $results;
    }

    private function get_content_stats() {
        global $wpdb;

        $thin_content_threshold = 300; // words

        $posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => 200,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $thin    = [];
        $total   = count( $posts );
        $lengths = [];

        foreach ( $posts as $p ) {
            $text       = wp_strip_all_tags( strip_shortcodes( $p->post_content ) );
            $word_count = str_word_count( $text );
            $lengths[]  = $word_count;

            if ( $word_count < $thin_content_threshold ) {
                $thin[] = [
                    'id'         => $p->ID,
                    'title'      => $p->post_title,
                    'url'        => get_permalink( $p->ID ),
                    'word_count' => $word_count,
                    'type'       => $p->post_type,
                ];
            }
        }

        return [
            'total_published'     => $total,
            'avg_word_count'      => $total > 0 ? round( array_sum( $lengths ) / $total ) : 0,
            'thin_content_count'  => count( $thin ),
            'thin_content_pages'  => $thin,
        ];
    }

    private function get_internal_link_audit( $limit = 50 ) {
        $posts   = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );
        $results   = [];

        foreach ( $posts as $p ) {
            preg_match_all( '/<a\s[^>]*href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/si', $p->post_content, $matches, PREG_SET_ORDER );

            $internal = 0;
            $external = 0;
            $nofollow = 0;

            foreach ( $matches as $m ) {
                $href = $m[1];
                $tag  = $m[0];

                if ( strpos( $href, '#' ) === 0 || strpos( $href, 'mailto:' ) === 0 || strpos( $href, 'tel:' ) === 0 ) {
                    continue;
                }

                $link_host = wp_parse_url( $href, PHP_URL_HOST );
                if ( empty( $link_host ) || $link_host === $site_host ) {
                    $internal++;
                } else {
                    $external++;
                }

                if ( stripos( $tag, 'nofollow' ) !== false ) {
                    $nofollow++;
                }
            }

            $issues = [];
            if ( $internal === 0 ) $issues[] = 'No internal links';
            if ( $external === 0 && str_word_count( wp_strip_all_tags( $p->post_content ) ) > 300 ) {
                $issues[] = 'No external links on long content';
            }

            $results[] = [
                'id'        => $p->ID,
                'title'     => $p->post_title,
                'url'       => get_permalink( $p->ID ),
                'internal'  => $internal,
                'external'  => $external,
                'nofollow'  => $nofollow,
                'issues'    => $issues,
            ];
        }
        return $results;
    }

    private function get_schema_markup_audit() {
        // Check theme for JSON-LD or schema references
        $theme_dir   = get_template_directory();
        $schema_refs = [];

        // Check if any schema plugin is active
        $schema_plugins = [
            'schema-and-structured-data-for-wp/structured-data-for-wp.php',
            'wp-schema-pro/wp-schema-pro.php',
            'schema/schema.php',
        ];

        $active = get_option( 'active_plugins', [] );
        $found  = [];
        foreach ( $schema_plugins as $sp ) {
            if ( in_array( $sp, $active, true ) ) {
                $found[] = $sp;
            }
        }

        // Check Yoast/Rank Math schema
        $yoast_schema  = defined( 'WPSEO_VERSION' ) ? 'Yoast provides built-in schema' : null;
        $rm_schema     = class_exists( 'RankMath' ) ? 'Rank Math provides built-in schema' : null;

        return [
            'dedicated_schema_plugins' => $found ?: 'None detected',
            'yoast_schema'             => $yoast_schema,
            'rankmath_schema'          => $rm_schema,
            'note'                     => 'For full schema validation, test pages at https://search.google.com/test/rich-results',
        ];
    }

    private function get_performance_hints() {
        $hints = [];

        // Check if a caching plugin is active
        $cache_plugins = [
            'wp-super-cache/wp-cache.php',
            'w3-total-cache/w3-total-cache.php',
            'wp-fastest-cache/wpFastestCache.php',
            'litespeed-cache/litespeed-cache.php',
            'wp-rocket/wp-rocket.php',
            'autoptimize/autoptimize.php',
        ];
        $active = get_option( 'active_plugins', [] );
        $cache_found = [];
        foreach ( $cache_plugins as $cp ) {
            if ( in_array( $cp, $active, true ) ) {
                $cache_found[] = $cp;
            }
        }
        $hints['caching_plugin'] = $cache_found ?: 'None detected — consider adding a caching plugin';

        // Check lazy loading
        $hints['wp_lazy_loading'] = function_exists( 'wp_lazy_loading_enabled' ) && wp_lazy_loading_enabled( 'img', 'the_content' )
            ? 'Native WP lazy loading enabled'
            : 'Lazy loading may not be active';

        // Check if GZIP/Brotli likely enabled (via headers on home)
        $response = wp_remote_get( home_url(), [ 'timeout' => 5, 'sslverify' => false ] );
        if ( ! is_wp_error( $response ) ) {
            $encoding = wp_remote_retrieve_header( $response, 'content-encoding' );
            $hints['compression'] = $encoding ? "Compression: $encoding" : 'No content-encoding header detected';
        }

        return $hints;
    }

    private function get_duplicate_titles_descriptions() {
        $posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => 300,
        ] );

        $titles = [];
        $descs  = [];

        foreach ( $posts as $p ) {
            $seo_title = get_post_meta( $p->ID, '_yoast_wpseo_title', true )
                      ?: get_post_meta( $p->ID, 'rank_math_title', true )
                      ?: $p->post_title;
            $seo_desc  = get_post_meta( $p->ID, '_yoast_wpseo_metadesc', true )
                      ?: get_post_meta( $p->ID, 'rank_math_description', true )
                      ?: '';

            $seo_title = strtolower( trim( $seo_title ) );
            $seo_desc  = strtolower( trim( $seo_desc ) );

            if ( ! empty( $seo_title ) ) {
                $titles[ $seo_title ][] = [ 'id' => $p->ID, 'title' => $p->post_title, 'url' => get_permalink( $p->ID ) ];
            }
            if ( ! empty( $seo_desc ) ) {
                $descs[ $seo_desc ][] = [ 'id' => $p->ID, 'title' => $p->post_title, 'url' => get_permalink( $p->ID ) ];
            }
        }

        $dup_titles = array_filter( $titles, function( $group ) { return count( $group ) > 1; } );
        $dup_descs  = array_filter( $descs, function( $group ) { return count( $group ) > 1; } );

        return [
            'duplicate_titles'       => $dup_titles,
            'duplicate_descriptions' => $dup_descs,
        ];
    }

    private function get_orphan_pages() {
        // Pages with no internal links pointing to them (basic check via parent)
        $pages = get_posts( [
            'post_type'      => 'page',
            'post_status'    => 'publish',
            'posts_per_page' => 200,
        ] );

        $menu_items = wp_get_nav_menu_items( 'primary' ) ?: [];
        $menu_ids   = array_map( function( $item ) { return (int) $item->object_id; }, $menu_items );

        $orphans = [];
        foreach ( $pages as $p ) {
            if ( $p->post_parent === 0 && ! in_array( $p->ID, $menu_ids, true ) ) {
                // Check if it's the front page or blog page
                if ( $p->ID == get_option( 'page_on_front' ) || $p->ID == get_option( 'page_for_posts' ) ) {
                    continue;
                }
                $orphans[] = [
                    'id'    => $p->ID,
                    'title' => $p->post_title,
                    'url'   => get_permalink( $p->ID ),
                ];
            }
        }
        return $orphans;
    }

    private function get_redirect_info() {
        // Check for common redirect plugins
        $redirect_plugins = [
            'redirection/redirection.php',
            'safe-redirect-manager/safe-redirect-manager.php',
            'simple-301-redirects/wp-simple-301-redirects.php',
        ];
        $active = get_option( 'active_plugins', [] );
        $found  = [];
        foreach ( $redirect_plugins as $rp ) {
            if ( in_array( $rp, $active, true ) ) {
                $found[] = $rp;
            }
        }

        // If Redirection plugin is active, try to get count
        $redirection_count = null;
        if ( in_array( 'redirection/redirection.php', $active, true ) ) {
            global $wpdb;
            $table = $wpdb->prefix . 'redirection_items';
            if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
                $redirection_count = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
            }
        }

        return [
            'redirect_plugins'  => $found ?: 'None detected',
            'redirection_count' => $redirection_count,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  EXTENDED AUDIT COLLECTORS                                          */
    /* ------------------------------------------------------------------ */

    private function get_url_structure_audit( $limit = 200 ) {
        $posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
            'orderby'        => 'title',
            'order'          => 'ASC',
        ] );

        $stop_words = [ 'a','an','the','and','or','but','in','on','at','to','for','of','with','by','is','it','this','that','are','was','be','has','had','do','does','did','will','would','could','should','may','might','shall','can','not','no','so','if','then','than','too','very','just','about','above','after','before','between','into','through','during','from','up','down','out','off','over','under','again','further','once' ];
        $results = [];

        foreach ( $posts as $p ) {
            $url    = get_permalink( $p->ID );
            $path   = wp_parse_url( $url, PHP_URL_PATH );
            $slug   = basename( $path );
            $issues = [];

            if ( strlen( $slug ) > 75 )                          $issues[] = 'Slug too long (' . strlen( $slug ) . ' chars)';
            if ( preg_match( '/[A-Z]/', $slug ) )                $issues[] = 'Uppercase characters in slug';
            if ( preg_match( '/[^a-z0-9\-\/]/', $slug ) )       $issues[] = 'Special characters in slug';
            if ( preg_match( '/--+/', $slug ) )                  $issues[] = 'Consecutive hyphens';
            if ( substr_count( $path, '/' ) > 5 )                $issues[] = 'Deep nesting (' . substr_count( $path, '/' ) . ' levels)';

            $slug_words    = explode( '-', $slug );
            $found_stops   = array_intersect( $slug_words, $stop_words );
            if ( count( $found_stops ) > 2 )                     $issues[] = 'Many stop words in slug: ' . implode( ', ', $found_stops );

            $results[] = [
                'id'     => $p->ID,
                'title'  => $p->post_title,
                'url'    => $url,
                'slug'   => $slug,
                'depth'  => substr_count( trim( $path, '/' ), '/' ),
                'issues' => $issues,
            ];
        }
        return $results;
    }

    private function get_taxonomy_audit() {
        $taxonomies = get_taxonomies( [ 'public' => true ], 'objects' );
        $results    = [];

        foreach ( $taxonomies as $tax ) {
            $terms       = get_terms( [ 'taxonomy' => $tax->name, 'hide_empty' => false ] );
            $empty_terms = [];
            $total       = 0;

            if ( ! is_wp_error( $terms ) ) {
                $total = count( $terms );
                foreach ( $terms as $t ) {
                    if ( $t->count === 0 ) {
                        $empty_terms[] = [ 'name' => $t->name, 'slug' => $t->slug ];
                    }
                }
            }

            $results[ $tax->name ] = [
                'label'       => $tax->label,
                'total_terms' => $total,
                'empty_terms' => count( $empty_terms ),
                'empty_list'  => array_slice( $empty_terms, 0, 30 ),
            ];
        }

        // Tag bloat check
        if ( isset( $results['post_tag'] ) && $results['post_tag']['total_terms'] > 100 ) {
            $results['post_tag']['warning'] = 'Possible tag bloat — ' . $results['post_tag']['total_terms'] . ' tags detected';
        }

        return $results;
    }

    private function get_comment_health() {
        global $wpdb;

        $counts = wp_count_comments();

        // Pages with comments open (usually bad for SEO)
        $pages_open = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts}
             WHERE post_type = 'page' AND post_status = 'publish' AND comment_status = 'open'"
        );

        return [
            'total_comments'   => $counts->total_comments ?? 0,
            'approved'         => $counts->approved ?? 0,
            'pending'          => $counts->moderated ?? 0,
            'spam'             => $counts->spam ?? 0,
            'trash'            => $counts->trash ?? 0,
            'pages_with_comments_open' => $pages_open,
            'issues'           => array_filter( [
                ( $counts->spam ?? 0 ) > 50 ? 'High spam count (' . $counts->spam . ')' : null,
                ( $counts->moderated ?? 0 ) > 20 ? 'Many pending comments (' . $counts->moderated . ')' : null,
                $pages_open > 0 ? "$pages_open pages have comments open (usually unnecessary)" : null,
            ] ),
        ];
    }

    private function get_user_role_audit() {
        $users_by_role = [];
        $roles = wp_roles()->get_names();
        foreach ( $roles as $slug => $label ) {
            $count = count( get_users( [ 'role' => $slug, 'fields' => 'ID' ] ) );
            if ( $count > 0 ) {
                $users_by_role[ $slug ] = [ 'label' => $label, 'count' => $count ];
            }
        }

        // Admin accounts detail
        $admins = get_users( [ 'role' => 'administrator', 'fields' => [ 'ID', 'user_login', 'user_email' ] ] );
        $admin_list = [];
        foreach ( $admins as $a ) {
            $last_login = get_user_meta( $a->ID, 'last_login', true ) ?: 'Unknown';
            $admin_list[] = [
                'id'         => $a->ID,
                'login'      => $a->user_login,
                'email'      => $a->user_email,
                'last_login' => $last_login,
            ];
        }

        $issues = [];
        if ( count( $admins ) > 3 ) $issues[] = 'Many admin accounts (' . count( $admins ) . ')';

        return [
            'roles'      => $users_by_role,
            'admins'     => $admin_list,
            'issues'     => $issues,
        ];
    }

    private function get_database_bloat() {
        global $wpdb;

        $revisions   = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'revision'" );
        $auto_drafts = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'auto-draft'" );
        $trashed     = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_status = 'trash'" );
        $spam_comments = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->comments} WHERE comment_approved = 'spam'" );
        $transients  = (int) $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '_transient_%'" );
        $orphan_meta = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->postmeta} pm
             LEFT JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE p.ID IS NULL"
        );

        $issues = [];
        if ( $revisions > 500 )     $issues[] = "High revision count ($revisions) — consider limiting";
        if ( $auto_drafts > 50 )    $issues[] = "Many auto-drafts ($auto_drafts)";
        if ( $transients > 1000 )   $issues[] = "Excessive transients ($transients)";
        if ( $orphan_meta > 500 )   $issues[] = "Orphaned postmeta rows ($orphan_meta)";

        return [
            'revisions'       => $revisions,
            'auto_drafts'     => $auto_drafts,
            'trashed_posts'   => $trashed,
            'spam_comments'   => $spam_comments,
            'transients'      => $transients,
            'orphaned_postmeta' => $orphan_meta,
            'issues'          => $issues,
        ];
    }

    private function get_security_surface() {
        $checks = [];

        // File editing
        $checks['file_editing_disabled'] = defined( 'DISALLOW_FILE_EDIT' ) && DISALLOW_FILE_EDIT;

        // Debug mode
        $checks['wp_debug'] = defined( 'WP_DEBUG' ) && WP_DEBUG;
        $checks['wp_debug_display'] = defined( 'WP_DEBUG_DISPLAY' ) && WP_DEBUG_DISPLAY;
        $checks['wp_debug_log'] = defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG;

        // XML-RPC
        $checks['xmlrpc_enabled'] = true; // default on, check if filtered off
        if ( has_filter( 'xmlrpc_enabled' ) ) {
            $checks['xmlrpc_enabled'] = apply_filters( 'xmlrpc_enabled', true ) ? true : false;
        }

        // REST API user enumeration
        $checks['rest_api_users_exposed'] = true; // default
        $response = wp_remote_get( rest_url( 'wp/v2/users' ), [ 'timeout' => 5, 'sslverify' => false ] );
        if ( ! is_wp_error( $response ) ) {
            $code = wp_remote_retrieve_response_code( $response );
            $checks['rest_api_users_exposed'] = ( $code === 200 );
        }

        // WP version in generator tag
        $checks['wp_version_exposed'] = ( remove_action( 'wp_head', 'wp_generator' ) === false );

        // Table prefix
        global $wpdb;
        $checks['default_table_prefix'] = ( $wpdb->prefix === 'wp_' );

        $issues = [];
        if ( ! $checks['file_editing_disabled'] )   $issues[] = 'File editing enabled in admin (DISALLOW_FILE_EDIT not set)';
        if ( $checks['wp_debug'] )                   $issues[] = 'WP_DEBUG is ON';
        if ( $checks['wp_debug_display'] )           $issues[] = 'WP_DEBUG_DISPLAY is ON (errors visible to visitors)';
        if ( $checks['xmlrpc_enabled'] )             $issues[] = 'XML-RPC is enabled (potential brute-force vector)';
        if ( $checks['rest_api_users_exposed'] )     $issues[] = 'REST API exposes user list at /wp-json/wp/v2/users';
        if ( $checks['default_table_prefix'] )       $issues[] = 'Using default wp_ table prefix';

        return [
            'checks' => $checks,
            'issues' => $issues,
        ];
    }

    private function get_enqueued_assets_audit() {
        // We need to capture what's enqueued on the front-end
        // Best approach: fetch the homepage HTML and parse <link> and <script> tags
        $response = wp_remote_get( home_url(), [ 'timeout' => 10, 'sslverify' => false ] );
        if ( is_wp_error( $response ) ) {
            return [ 'error' => 'Could not fetch homepage to analyze assets' ];
        }

        $html      = wp_remote_retrieve_body( $response );
        $site_host = wp_parse_url( home_url(), PHP_URL_HOST );

        // CSS files
        preg_match_all( '/<link[^>]+rel=["\']stylesheet["\'][^>]+href=["\']([^"\']+)["\'][^>]*>/i', $html, $css_matches );
        $css_files = [];
        foreach ( $css_matches[1] as $href ) {
            $host = wp_parse_url( $href, PHP_URL_HOST );
            $css_files[] = [
                'url'      => $href,
                'external' => ( ! empty( $host ) && $host !== $site_host ),
            ];
        }

        // JS files
        preg_match_all( '/<script[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $html, $js_matches );
        $js_files = [];
        $render_blocking = 0;
        foreach ( $js_matches[0] as $idx => $tag ) {
            $src  = $js_matches[1][ $idx ];
            $host = wp_parse_url( $src, PHP_URL_HOST );
            $is_async = ( stripos( $tag, 'async' ) !== false );
            $is_defer = ( stripos( $tag, 'defer' ) !== false );
            if ( ! $is_async && ! $is_defer ) $render_blocking++;

            $js_files[] = [
                'url'             => $src,
                'external'        => ( ! empty( $host ) && $host !== $site_host ),
                'async'           => $is_async,
                'defer'           => $is_defer,
                'render_blocking' => ( ! $is_async && ! $is_defer ),
            ];
        }

        $external_css = count( array_filter( $css_files, function( $f ) { return $f['external']; } ) );
        $external_js  = count( array_filter( $js_files, function( $f ) { return $f['external']; } ) );

        $issues = [];
        if ( count( $css_files ) > 15 )  $issues[] = 'Many CSS files (' . count( $css_files ) . ') — consider combining';
        if ( count( $js_files ) > 15 )   $issues[] = 'Many JS files (' . count( $js_files ) . ') — consider combining';
        if ( $render_blocking > 5 )      $issues[] = "$render_blocking render-blocking scripts (no async/defer)";
        if ( $external_css > 5 )         $issues[] = "Many external CSS resources ($external_css)";
        if ( $external_js > 5 )          $issues[] = "Many external JS resources ($external_js)";

        return [
            'css_count'          => count( $css_files ),
            'js_count'           => count( $js_files ),
            'render_blocking_js' => $render_blocking,
            'external_css'       => $external_css,
            'external_js'        => $external_js,
            'css_files'          => $css_files,
            'js_files'           => $js_files,
            'issues'             => $issues,
        ];
    }

    private function get_menu_audit() {
        $menus   = get_nav_menu_locations();
        $results = [];

        foreach ( $menus as $location => $menu_id ) {
            if ( ! $menu_id ) continue;
            $menu_obj = wp_get_nav_menu_object( $menu_id );
            $items    = wp_get_nav_menu_items( $menu_id );

            if ( ! $items ) continue;

            $max_depth = 0;
            $broken    = [];
            foreach ( $items as $item ) {
                // Calculate depth
                $depth = 0;
                $parent = $item->menu_item_parent;
                while ( $parent ) {
                    $depth++;
                    $found = false;
                    foreach ( $items as $check ) {
                        if ( $check->ID == $parent ) {
                            $parent = $check->menu_item_parent;
                            $found  = true;
                            break;
                        }
                    }
                    if ( ! $found ) break;
                }
                if ( $depth > $max_depth ) $max_depth = $depth;

                // Check for broken links (custom URLs only)
                if ( $item->type === 'custom' && ! empty( $item->url ) ) {
                    $check = wp_remote_head( $item->url, [ 'timeout' => 3, 'sslverify' => false, 'redirection' => 0 ] );
                    if ( is_wp_error( $check ) || wp_remote_retrieve_response_code( $check ) >= 400 ) {
                        $broken[] = [ 'title' => $item->title, 'url' => $item->url ];
                    }
                }
            }

            $issues = [];
            if ( count( $items ) > 30 )  $issues[] = 'Large menu (' . count( $items ) . ' items)';
            if ( $max_depth > 3 )        $issues[] = 'Deep nesting (depth ' . $max_depth . ')';
            if ( ! empty( $broken ) )    $issues[] = count( $broken ) . ' potentially broken link(s)';

            $results[ $location ] = [
                'menu_name'    => $menu_obj->name,
                'item_count'   => count( $items ),
                'max_depth'    => $max_depth,
                'broken_links' => $broken,
                'issues'       => $issues,
            ];
        }

        return $results;
    }

    private function get_widget_audit() {
        global $wp_registered_sidebars;
        $sidebars_widgets = wp_get_sidebars_widgets();
        $results = [];

        foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {
            if ( $sidebar_id === 'wp_inactive_widgets' ) {
                $results['inactive_widgets'] = count( $widgets );
                continue;
            }
            $label = isset( $wp_registered_sidebars[ $sidebar_id ] )
                ? $wp_registered_sidebars[ $sidebar_id ]['name']
                : $sidebar_id;
            $results[ $sidebar_id ] = [
                'label'        => $label,
                'widget_count' => count( $widgets ),
                'widgets'      => $widgets,
            ];
        }

        return $results;
    }

    private function get_cron_audit() {
        $crons   = _get_cron_array();
        $results = [];

        if ( ! is_array( $crons ) ) {
            return [ 'error' => 'Could not read cron array' ];
        }

        foreach ( $crons as $timestamp => $hooks ) {
            foreach ( $hooks as $hook => $events ) {
                foreach ( $events as $key => $event ) {
                    $results[] = [
                        'hook'       => $hook,
                        'next_run'   => date( 'Y-m-d H:i:s', $timestamp ),
                        'schedule'   => $event['schedule'] ?: 'one-time',
                        'interval'   => isset( $event['interval'] ) ? $event['interval'] . 's' : null,
                    ];
                }
            }
        }

        // Deduplicate by hook for summary
        $hook_counts = array_count_values( array_column( $results, 'hook' ) );
        arsort( $hook_counts );

        return [
            'total_events'   => count( $results ),
            'unique_hooks'   => count( $hook_counts ),
            'hook_frequency' => array_slice( $hook_counts, 0, 30, true ),
            'events'         => array_slice( $results, 0, 50 ),
        ];
    }

    private function get_post_type_inventory() {
        $post_types = get_post_types( [ 'public' => true ], 'objects' );
        $results    = [];

        foreach ( $post_types as $pt ) {
            $count = wp_count_posts( $pt->name );
            $results[ $pt->name ] = [
                'label'     => $pt->label,
                'published' => $count->publish ?? 0,
                'draft'     => $count->draft ?? 0,
                'has_archive' => $pt->has_archive ? true : false,
                'rewrite'     => $pt->rewrite ?: 'default',
            ];
        }

        // ACF field groups if available
        $acf_groups = [];
        if ( function_exists( 'acf_get_field_groups' ) ) {
            $groups = acf_get_field_groups();
            foreach ( $groups as $g ) {
                $fields = acf_get_fields( $g['key'] );
                $acf_groups[] = [
                    'title'       => $g['title'],
                    'key'         => $g['key'],
                    'field_count' => count( $fields ),
                    'location'    => $g['location'],
                    'active'      => $g['active'],
                ];
            }
        }

        // Top meta keys
        global $wpdb;
        $top_meta = $wpdb->get_results(
            "SELECT meta_key, COUNT(*) as cnt FROM {$wpdb->postmeta}
             WHERE meta_key NOT LIKE '\_%'
             GROUP BY meta_key ORDER BY cnt DESC LIMIT 30"
        );

        return [
            'post_types'  => $results,
            'acf_groups'  => $acf_groups ?: 'ACF not detected',
            'top_meta_keys' => $top_meta,
        ];
    }

    private function get_embed_audit( $limit = 100 ) {
        $posts = get_posts( [
            'post_type'      => [ 'page', 'post' ],
            'post_status'    => 'publish',
            'posts_per_page' => $limit,
        ] );

        $results = [];
        foreach ( $posts as $p ) {
            $content = $p->post_content;
            $embeds  = [];

            // iframes
            preg_match_all( '/<iframe[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $content, $iframe_matches );
            foreach ( $iframe_matches[1] as $src ) {
                $embeds[] = [ 'type' => 'iframe', 'src' => $src ];
            }

            // oEmbed URLs (YouTube, Vimeo, etc. on their own line)
            preg_match_all( '/^(https?:\/\/(?:www\.)?(?:youtube\.com|youtu\.be|vimeo\.com|twitter\.com|instagram\.com|facebook\.com)[^\s]+)$/mi', $content, $oembed_matches );
            foreach ( $oembed_matches[1] as $url ) {
                $embeds[] = [ 'type' => 'oembed', 'src' => $url ];
            }

            // WP embed blocks
            preg_match_all( '/<!-- wp:embed\s+({[^}]+})\s+-->/i', $content, $block_matches );
            foreach ( $block_matches[1] as $json ) {
                $data = json_decode( $json, true );
                if ( isset( $data['url'] ) ) {
                    $embeds[] = [ 'type' => 'wp-embed-block', 'src' => $data['url'] ];
                }
            }

            if ( ! empty( $embeds ) ) {
                $results[] = [
                    'id'     => $p->ID,
                    'title'  => $p->post_title,
                    'url'    => get_permalink( $p->ID ),
                    'embeds' => $embeds,
                ];
            }
        }

        return [
            'pages_with_embeds' => count( $results ),
            'details'           => $results,
        ];
    }

    private function get_404_log() {
        global $wpdb;

        // Redirection plugin 404 log
        $table = $wpdb->prefix . 'redirection_404';
        if ( $wpdb->get_var( "SHOW TABLES LIKE '$table'" ) === $table ) {
            $total = (int) $wpdb->get_var( "SELECT COUNT(*) FROM $table" );
            $recent = $wpdb->get_results(
                "SELECT url, COUNT(*) as hits, MAX(created) as last_hit
                 FROM $table
                 GROUP BY url ORDER BY hits DESC LIMIT 30"
            );
            return [
                'source'     => 'Redirection plugin',
                'total_404s' => $total,
                'top_404s'   => $recent,
            ];
        }

        return [ 'source' => 'No 404 log available (Redirection plugin not active or no data)' ];
    }

    private function get_http_security_headers() {
        $response = wp_remote_get( home_url(), [ 'timeout' => 5, 'sslverify' => false ] );
        if ( is_wp_error( $response ) ) {
            return [ 'error' => 'Could not fetch homepage headers' ];
        }

        $headers_to_check = [
            'strict-transport-security' => 'HSTS — forces HTTPS',
            'x-frame-options'           => 'Clickjacking protection',
            'x-content-type-options'    => 'MIME-type sniffing protection',
            'content-security-policy'   => 'CSP — controls resource loading',
            'referrer-policy'           => 'Controls referrer information',
            'permissions-policy'        => 'Controls browser features',
            'x-xss-protection'         => 'XSS filter (legacy but still useful)',
        ];

        $results = [];
        $missing = [];
        foreach ( $headers_to_check as $header => $desc ) {
            $value = wp_remote_retrieve_header( $response, $header );
            if ( $value ) {
                $results[ $header ] = [ 'present' => true, 'value' => $value, 'description' => $desc ];
            } else {
                $results[ $header ] = [ 'present' => false, 'description' => $desc ];
                $missing[] = "$header — $desc";
            }
        }

        return [
            'headers' => $results,
            'missing' => $missing,
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  ASSET OPTIMIZER RULES (for JSON export)                            */
    /* ------------------------------------------------------------------ */

    public function get_asset_optimizer_rules() {
        return [
            'dashicons' => [
                'handles'     => [ 'dashicons' ],
                'condition'   => 'Dequeue when user is NOT logged in',
                'reason'      => 'Admin icons (35 KB) not needed for visitors',
            ],
            'wp_google_maps' => [
                'handles'     => [ 'wpgmza*' ],
                'condition'   => 'Only load on pages with [wpgmza shortcode, wpgmza block, or wpgmza_map post type',
                'reason'      => 'Maps JS/CSS only needed on map pages',
            ],
            'datatables' => [
                'handles'     => [ 'datatables', 'datatables-responsive', '*dataTables*', '*datatables*' ],
                'condition'   => 'Only load on pages with [wpgmza shortcode or <table class="dataTable"> in content',
                'reason'      => 'DataTables library only needed for store locator / data tables',
            ],
            'wp_reviews_pro' => [
                'handles'     => [ '*wprev*', '*wp-reviews*' ],
                'condition'   => 'Only load on pages with [wprev shortcode or wprev block',
                'reason'      => 'Reviews assets only needed on review pages',
            ],
            'smash_balloon_instagram' => [
                'handles'     => [ '*sbi-*', '*sb-instagram*' ],
                'condition'   => 'Only load on pages with [instagram-feed shortcode or sbi block',
                'reason'      => 'Instagram feed assets only needed on feed pages',
            ],
            'ihover' => [
                'handles'     => [ 'ihover', '*ihover*' ],
                'condition'   => 'Only load on pages with ihover classes in content',
                'reason'      => 'iHover CSS only needed on pages using hover effects',
            ],
            'wpbakery' => [
                'handles'     => [ 'js_composer-front', 'js_composer', '*js_composer*' ],
                'condition'   => 'Only load on pages with [vc_ shortcode patterns',
                'reason'      => 'WPBakery assets only needed on pages built with Visual Composer',
            ],
            'wp_block_library' => [
                'handles'     => [ 'wp-block-library' ],
                'condition'   => 'Only load on pages containing <!-- wp: block markers',
                'reason'      => 'Block editor CSS not needed on classic editor pages',
            ],
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  FULL REPORT ASSEMBLY                                               */
    /* ------------------------------------------------------------------ */

    private function build_full_report() {
        return [
            'generated_at'         => current_time( 'mysql' ),
            'site_info'            => $this->get_site_info(),
            'theme'                => $this->get_theme_info(),
            'plugins'              => $this->get_plugins_info(),
            'seo_plugin_config'    => $this->get_seo_plugin_config(),
            'pages_meta_audit'     => $this->get_posts_meta_audit( 'page' ),
            'posts_meta_audit'     => $this->get_posts_meta_audit( 'post' ),
            'image_alt_audit'      => $this->get_image_alt_audit(),
            'heading_structure'    => $this->get_heading_structure_audit(),
            'content_stats'        => $this->get_content_stats(),
            'internal_links'       => $this->get_internal_link_audit(),
            'duplicate_check'      => $this->get_duplicate_titles_descriptions(),
            'orphan_pages'         => $this->get_orphan_pages(),
            'robots_txt'           => $this->get_robots_txt(),
            'sitemap_status'       => $this->get_sitemap_status(),
            'schema_markup'        => $this->get_schema_markup_audit(),
            'performance_hints'    => $this->get_performance_hints(),
            'redirects'            => $this->get_redirect_info(),
            'url_structure'        => $this->get_url_structure_audit(),
            'taxonomy_audit'       => $this->get_taxonomy_audit(),
            'comment_health'       => $this->get_comment_health(),
            'user_roles'           => $this->get_user_role_audit(),
            'database_bloat'       => $this->get_database_bloat(),
            'security_surface'     => $this->get_security_surface(),
            'enqueued_assets'      => $this->get_enqueued_assets_audit(),
            'menu_audit'           => $this->get_menu_audit(),
            'widget_audit'         => $this->get_widget_audit(),
            'cron_audit'           => $this->get_cron_audit(),
            'post_type_inventory'  => $this->get_post_type_inventory(),
            'embed_audit'          => $this->get_embed_audit(),
            'error_404_log'        => $this->get_404_log(),
            'http_security_headers'=> $this->get_http_security_headers(),
            'asset_optimizer_rules' => $this->get_asset_optimizer_rules(),
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  AJAX EXPORT (JSON download)                                        */
    /* ------------------------------------------------------------------ */

    public function ajax_export() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Unauthorized' );
        }
        check_ajax_referer( 'tci_seo_audit_nonce', 'nonce' );

        $report = $this->build_full_report();

        header( 'Content-Type: application/json' );
        header( 'Content-Disposition: attachment; filename="tci-seo-audit-' . date( 'Y-m-d-His' ) . '.json"' );
        echo json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        wp_die();
    }

    /* ------------------------------------------------------------------ */
    /*  BULK STRIP LONG TITLE SUFFIX                                       */
    /* ------------------------------------------------------------------ */

    public function ajax_bulk_strip_title_suffix() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        check_ajax_referer( 'tci_seo_audit_nonce', 'nonce' );

        $dry_run = isset( $_POST['dry_run'] ) && $_POST['dry_run'] === '1';
        $suffix  = ': Excellence in Commercial Trucking';

        global $wpdb;

        // Find all rank_math_title meta that contain the suffix
        $rows = $wpdb->get_results( $wpdb->prepare(
            "SELECT pm.meta_id, pm.post_id, pm.meta_value, p.post_title, p.post_type
             FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'rank_math_title'
             AND pm.meta_value LIKE %s
             AND p.post_status = 'publish'",
            '%' . $wpdb->esc_like( $suffix ) . '%'
        ) );

        $results = [];
        $updated = 0;

        foreach ( $rows as $row ) {
            $old_title = $row->meta_value;
            $new_title = str_replace( $suffix, '', $old_title );
            $new_title = rtrim( $new_title ); // clean trailing space

            $results[] = [
                'post_id'    => $row->post_id,
                'post_title' => $row->post_title,
                'post_type'  => $row->post_type,
                'old_seo'    => $old_title,
                'new_seo'    => $new_title,
            ];

            if ( ! $dry_run ) {
                $wpdb->update(
                    $wpdb->postmeta,
                    [ 'meta_value' => $new_title ],
                    [ 'meta_id' => $row->meta_id ],
                    [ '%s' ],
                    [ '%d' ]
                );
                $updated++;
            }
        }

        wp_send_json_success( [
            'dry_run' => $dry_run,
            'found'   => count( $results ),
            'updated' => $updated,
            'items'   => $results,
        ] );
    }

    /* ------------------------------------------------------------------ */
    /*  BULK COMMA-NORMALIZE LOCATION TITLES                               */
    /* ------------------------------------------------------------------ */

    public function ajax_bulk_comma_locations() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( 'Unauthorized' );
        }
        check_ajax_referer( 'tci_seo_audit_nonce', 'nonce' );

        $dry_run = isset( $_POST['dry_run'] ) && $_POST['dry_run'] === '1';

        // US state abbreviations
        $states = 'AL|AK|AZ|AR|CA|CO|CT|DE|FL|GA|HI|ID|IL|IN|IA|KS|KY|LA|ME|MD|MA|MI|MN|MS|MO|MT|NE|NV|NH|NJ|NM|NY|NC|ND|OH|OK|OR|PA|RI|SC|SD|TN|TX|UT|VT|VA|WA|WV|WI|WY';

        global $wpdb;

        $rows = $wpdb->get_results(
            "SELECT pm.meta_id, pm.post_id, pm.meta_value, p.post_title
             FROM {$wpdb->postmeta} pm
             JOIN {$wpdb->posts} p ON pm.post_id = p.ID
             WHERE pm.meta_key = 'rank_math_title'
             AND p.post_status = 'publish'"
        );

        $results = [];
        $updated = 0;

        foreach ( $rows as $row ) {
            $title = $row->meta_value;
            // Match "City ST" (no comma) before a pipe or end — e.g. "Bakersfield CA |" or "Bakersfield CA"
            // But NOT "City, ST" (already has comma)
            $pattern = '/^([A-Za-z][A-Za-z .]+?)\s+(' . $states . ')\s*(\||$)/';
            if ( preg_match( $pattern, $title, $m ) ) {
                // Check it doesn't already have a comma
                if ( strpos( $m[0], ',' ) !== false ) {
                    continue;
                }
                $new_title = preg_replace( $pattern, '$1, $2 $3', $title );
                $new_title = preg_replace( '/\s+\|/', ' |', $new_title ); // clean double spaces before pipe

                if ( $new_title === $title ) {
                    continue;
                }

                $results[] = [
                    'post_id'    => $row->post_id,
                    'post_title' => $row->post_title,
                    'old_seo'    => $title,
                    'new_seo'    => $new_title,
                ];

                if ( ! $dry_run ) {
                    $wpdb->update(
                        $wpdb->postmeta,
                        [ 'meta_value' => $new_title ],
                        [ 'meta_id' => $row->meta_id ],
                        [ '%s' ],
                        [ '%d' ]
                    );
                    $updated++;
                }
            }
        }

        wp_send_json_success( [
            'dry_run' => $dry_run,
            'found'   => count( $results ),
            'updated' => $updated,
            'items'   => $results,
        ] );
    }

    /* ------------------------------------------------------------------ */
    /*  ADMIN PAGE RENDER                                                  */
    /* ------------------------------------------------------------------ */

    public function render_page() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return;
        }

        $report = $this->build_full_report();
        $json   = json_encode( $report, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES );
        $nonce  = wp_create_nonce( 'tci_seo_audit_nonce' );
        ?>
        <div class="wrap">
            <h1>TCI SEO Audit Report</h1>
            <p>Generated: <?php echo esc_html( $report['generated_at'] ); ?></p>

            <div style="margin: 15px 0;">
                <a href="<?php echo esc_url( admin_url( 'tools.php?page=tci-seo-audit' ) ); ?>"
                   class="button button-secondary" style="margin-right:8px;">
                    &#x21bb; Refresh Audit
                </a>
                <a href="<?php echo admin_url( 'admin-ajax.php?action=tci_seo_audit_export&nonce=' . $nonce ); ?>"
                   class="button button-primary" download>
                    Download JSON Report
                </a>
                <button id="tci-copy-json" class="button" style="margin-left:8px;">
                    Copy JSON to Clipboard
                </button>
            </div>

            <div style="margin:15px 0; padding:12px 16px; background:#fff8e1; border-left:4px solid #ffb300; max-width:900px;">
                <strong>Bulk Title Suffix Cleanup</strong>
                <p style="margin:6px 0 10px;">Strips <code>: Excellence in Commercial Trucking</code> from all Rank Math SEO titles. Preview first, then apply.</p>
                <button id="tci-suffix-preview" class="button">Preview Changes</button>
                <button id="tci-suffix-apply" class="button button-primary" style="margin-left:8px; display:none;">Apply Changes</button>
                <span id="tci-suffix-status" style="margin-left:12px;"></span>
                <div id="tci-suffix-results" style="margin-top:10px; display:none;">
                    <table class="widefat striped" style="max-width:900px;">
                        <thead><tr><th>Page</th><th>Type</th><th>Current Title</th><th>New Title</th></tr></thead>
                        <tbody id="tci-suffix-tbody"></tbody>
                    </table>
                </div>
            </div>

            <script>
            (function(){
                var nonce = '<?php echo esc_js( $nonce ); ?>';
                var ajaxUrl = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

                function doRequest(dryRun, callback) {
                    var fd = new FormData();
                    fd.append('action', 'tci_bulk_strip_title_suffix');
                    fd.append('nonce', nonce);
                    fd.append('dry_run', dryRun ? '1' : '0');
                    fetch(ajaxUrl, {method:'POST', body:fd, credentials:'same-origin'})
                        .then(function(r){return r.json()})
                        .then(callback)
                        .catch(function(e){
                            document.getElementById('tci-suffix-status').textContent = 'Error: ' + e.message;
                        });
                }

                document.getElementById('tci-suffix-preview').addEventListener('click', function(){
                    this.disabled = true;
                    document.getElementById('tci-suffix-status').textContent = 'Scanning...';
                    doRequest(true, function(resp){
                        document.getElementById('tci-suffix-preview').disabled = false;
                        var d = resp.data;
                        if (!d.found) {
                            document.getElementById('tci-suffix-status').innerHTML = '<span style="color:green;">✓ No titles contain the long suffix.</span>';
                            document.getElementById('tci-suffix-results').style.display = 'none';
                            document.getElementById('tci-suffix-apply').style.display = 'none';
                            return;
                        }
                        document.getElementById('tci-suffix-status').textContent = d.found + ' title(s) found. Review below, then click Apply.';
                        var tbody = document.getElementById('tci-suffix-tbody');
                        tbody.innerHTML = '';
                        d.items.forEach(function(item){
                            var tr = document.createElement('tr');
                            tr.innerHTML = '<td>' + item.post_title + ' <small>(#' + item.post_id + ')</small></td>'
                                + '<td>' + item.post_type + '</td>'
                                + '<td><code style="color:#c62828;font-size:11px;">' + item.old_seo + '</code></td>'
                                + '<td><code style="color:#2e7d32;font-size:11px;">' + item.new_seo + '</code></td>';
                            tbody.appendChild(tr);
                        });
                        document.getElementById('tci-suffix-results').style.display = 'block';
                        document.getElementById('tci-suffix-apply').style.display = 'inline-block';
                    });
                });

                document.getElementById('tci-suffix-apply').addEventListener('click', function(){
                    if (!confirm('This will update ' + document.getElementById('tci-suffix-tbody').rows.length + ' SEO titles in the database. Continue?')) return;
                    this.disabled = true;
                    document.getElementById('tci-suffix-status').textContent = 'Applying...';
                    doRequest(false, function(resp){
                        var d = resp.data;
                        document.getElementById('tci-suffix-status').innerHTML = '<span style="color:green;">✓ Updated ' + d.updated + ' title(s). Refresh audit to verify.</span>';
                        document.getElementById('tci-suffix-apply').style.display = 'none';
                    });
                });
            })();
            </script>

            <div style="margin:15px 0; padding:12px 16px; background:#e3f2fd; border-left:4px solid #1976d2; max-width:900px;">
                <strong>Location Title Comma Normalization</strong>
                <p style="margin:6px 0 10px;">Adds missing commas between city and state in SEO titles (e.g. <code>Bakersfield CA</code> → <code>Bakersfield, CA</code>).</p>
                <button id="tci-comma-preview" class="button">Preview Changes</button>
                <button id="tci-comma-apply" class="button button-primary" style="margin-left:8px; display:none;">Apply Changes</button>
                <span id="tci-comma-status" style="margin-left:12px;"></span>
                <div id="tci-comma-results" style="margin-top:10px; display:none;">
                    <table class="widefat striped" style="max-width:900px;">
                        <thead><tr><th>Page</th><th>Current Title</th><th>New Title</th></tr></thead>
                        <tbody id="tci-comma-tbody"></tbody>
                    </table>
                </div>
            </div>

            <script>
            (function(){
                var nonce = '<?php echo esc_js( $nonce ); ?>';
                var ajaxUrl = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

                function commaRequest(dryRun, callback) {
                    var fd = new FormData();
                    fd.append('action', 'tci_bulk_comma_locations');
                    fd.append('nonce', nonce);
                    fd.append('dry_run', dryRun ? '1' : '0');
                    fetch(ajaxUrl, {method:'POST', body:fd, credentials:'same-origin'})
                        .then(function(r){return r.json()})
                        .then(callback)
                        .catch(function(e){
                            document.getElementById('tci-comma-status').textContent = 'Error: ' + e.message;
                        });
                }

                document.getElementById('tci-comma-preview').addEventListener('click', function(){
                    this.disabled = true;
                    document.getElementById('tci-comma-status').textContent = 'Scanning...';
                    commaRequest(true, function(resp){
                        document.getElementById('tci-comma-preview').disabled = false;
                        var d = resp.data;
                        if (!d.found) {
                            document.getElementById('tci-comma-status').innerHTML = '<span style="color:green;">✓ All location titles already have commas.</span>';
                            document.getElementById('tci-comma-results').style.display = 'none';
                            document.getElementById('tci-comma-apply').style.display = 'none';
                            return;
                        }
                        document.getElementById('tci-comma-status').textContent = d.found + ' title(s) need commas. Review below, then click Apply.';
                        var tbody = document.getElementById('tci-comma-tbody');
                        tbody.innerHTML = '';
                        d.items.forEach(function(item){
                            var tr = document.createElement('tr');
                            tr.innerHTML = '<td>' + item.post_title + ' <small>(#' + item.post_id + ')</small></td>'
                                + '<td><code style="color:#c62828;font-size:11px;">' + item.old_seo + '</code></td>'
                                + '<td><code style="color:#2e7d32;font-size:11px;">' + item.new_seo + '</code></td>';
                            tbody.appendChild(tr);
                        });
                        document.getElementById('tci-comma-results').style.display = 'block';
                        document.getElementById('tci-comma-apply').style.display = 'inline-block';
                    });
                });

                document.getElementById('tci-comma-apply').addEventListener('click', function(){
                    if (!confirm('This will update ' + document.getElementById('tci-comma-tbody').rows.length + ' SEO titles. Continue?')) return;
                    this.disabled = true;
                    document.getElementById('tci-comma-status').textContent = 'Applying...';
                    commaRequest(false, function(resp){
                        var d = resp.data;
                        document.getElementById('tci-comma-status').innerHTML = '<span style="color:green;">✓ Updated ' + d.updated + ' title(s). Refresh audit to verify.</span>';
                        document.getElementById('tci-comma-apply').style.display = 'none';
                    });
                });
            })();
            </script>

            <h2>Quick Summary</h2>
            <table class="widefat striped" style="max-width:700px;">
                <tr><td><strong>Site URL</strong></td><td><?php echo esc_html( $report['site_info']['site_url'] ); ?></td></tr>
                <tr><td><strong>WP Version</strong></td><td><?php echo esc_html( $report['site_info']['wp_version'] ); ?></td></tr>
                <tr><td><strong>SSL</strong></td><td><?php echo esc_html( $report['site_info']['ssl'] ); ?></td></tr>
                <tr><td><strong>Search Visibility</strong></td><td><?php echo esc_html( $report['site_info']['search_visibility'] ); ?></td></tr>
                <tr><td><strong>Permalink Structure</strong></td><td><code><?php echo esc_html( $report['site_info']['permalink_structure'] ); ?></code></td></tr>
                <tr><td><strong>Theme</strong></td><td><?php echo esc_html( $report['theme']['name'] . ' v' . $report['theme']['version'] ); ?></td></tr>
                <tr><td><strong>Active Plugins</strong></td><td><?php echo count( array_filter( $report['plugins'], function($p){ return $p['active']; } ) ); ?></td></tr>
                <tr><td><strong>Image Alt Coverage</strong></td><td><?php echo esc_html( $report['image_alt_audit']['alt_coverage'] ); ?></td></tr>
                <tr><td><strong>Thin Content Pages</strong></td><td><?php echo esc_html( $report['content_stats']['thin_content_count'] ); ?></td></tr>
                <tr><td><strong>CSS Files (homepage)</strong></td><td><?php echo esc_html( $report['enqueued_assets']['css_count'] ?? 'N/A' ); ?></td></tr>
                <tr><td><strong>JS Files (homepage)</strong></td><td><?php echo esc_html( $report['enqueued_assets']['js_count'] ?? 'N/A' ); ?></td></tr>
                <tr><td><strong>Render-Blocking JS</strong></td><td><?php echo esc_html( $report['enqueued_assets']['render_blocking_js'] ?? 'N/A' ); ?></td></tr>
                <tr><td><strong>DB Revisions</strong></td><td><?php echo esc_html( $report['database_bloat']['revisions'] ); ?></td></tr>
                <tr><td><strong>Orphaned Postmeta</strong></td><td><?php echo esc_html( $report['database_bloat']['orphaned_postmeta'] ); ?></td></tr>
                <tr><td><strong>Security Headers Missing</strong></td><td><?php echo count( $report['http_security_headers']['missing'] ?? [] ); ?></td></tr>
                <tr><td><strong>Cron Events</strong></td><td><?php echo esc_html( $report['cron_audit']['total_events'] ?? 'N/A' ); ?></td></tr>
                <tr><td><strong>Pages w/ Embeds</strong></td><td><?php echo esc_html( $report['embed_audit']['pages_with_embeds'] ); ?></td></tr>
            </table>

            <h2>Pages with SEO Issues</h2>
            <?php
            $pages_with_issues = array_filter( $report['pages_meta_audit'], function( $p ) { return ! empty( $p['issues'] ); } );
            if ( $pages_with_issues ) : ?>
                <table class="widefat striped" style="max-width:900px;">
                    <thead><tr><th>Page</th><th>Issues</th></tr></thead>
                    <tbody>
                    <?php foreach ( $pages_with_issues as $p ) : ?>
                        <tr>
                            <td><a href="<?php echo esc_url( $p['url'] ); ?>" target="_blank"><?php echo esc_html( $p['title'] ); ?></a></td>
                            <td><?php echo esc_html( implode( ', ', $p['issues'] ) ); ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <p style="color:green;">✓ No page-level SEO issues detected.</p>
            <?php endif; ?>

            <h2>Full JSON Report</h2>
            <p><em>Copy the JSON below and paste it into chat for analysis:</em></p>
            <textarea id="tci-seo-json" readonly
                      style="width:100%; height:500px; font-family:monospace; font-size:12px; white-space:pre;"><?php
                echo esc_textarea( $json );
            ?></textarea>
        </div>

        <script>
        document.getElementById('tci-copy-json').addEventListener('click', function() {
            var ta = document.getElementById('tci-seo-json');
            ta.select();
            document.execCommand('copy');
            this.textContent = 'Copied!';
            setTimeout(function(){ document.getElementById('tci-copy-json').textContent = 'Copy JSON to Clipboard'; }, 2000);
        });
        </script>
        <?php
    }

    /* ------------------------------------------------------------------ */
    /*  OWL CAROUSEL /undefined & /null 404 PATCH                          */
    /*  Source: wp-google-maps bundles owl.carousel which lazy-loads       */
    /*  images via data-src. Missing values become "/undefined" 404s.      */
    /* ------------------------------------------------------------------ */

    public function patch_owl_carousel_undefined() {
        ?>
        <script>
        (function(){
            /*  Intercept fetch/XHR at the network level to block /null and
                /undefined requests before they leave the browser.
                Also patch Image() constructor and img.src setter.            */

            // 1. Patch the native Image constructor
            var _Image = window.Image;
            window.Image = function(w, h) {
                var img = new _Image(w, h);
                var origSrc = Object.getOwnPropertyDescriptor(HTMLImageElement.prototype, 'src');
                if (origSrc && origSrc.set) {
                    var _set = origSrc.set;
                    Object.defineProperty(img, 'src', {
                        set: function(v) {
                            if (typeof v === 'string' && /\/(null|undefined)\/?(\?.*)?$/i.test(v)) return;
                            if (v === null || v === undefined || v === 'null' || v === 'undefined') return;
                            _set.call(this, v);
                        },
                        get: function() { return origSrc.get.call(this); },
                        configurable: true
                    });
                }
                return img;
            };
            window.Image.prototype = _Image.prototype;

            // 2. Monkey-patch jQuery.fn.attr to block bad src assignments (Owl uses jQuery)
            if (window.jQuery) {
                var _attr = jQuery.fn.attr;
                jQuery.fn.attr = function(name, value) {
                    if (arguments.length === 2 && (name === 'src' || name === 'data-src')) {
                        if (value === null || value === undefined || value === 'null' || value === 'undefined') return this;
                        if (typeof value === 'string' && /\/(null|undefined)\/?(\?.*)?$/i.test(value)) return this;
                    }
                    return _attr.apply(this, arguments);
                };
            }

            // 3. Run after jQuery loads (if it wasn't ready above)
            document.addEventListener('DOMContentLoaded', function() {
                if (window.jQuery && !jQuery.fn.__tci_patched) {
                    jQuery.fn.__tci_patched = true;
                    var _attr2 = jQuery.fn.attr;
                    jQuery.fn.attr = function(name, value) {
                        if (arguments.length === 2 && (name === 'src' || name === 'data-src')) {
                            if (value === null || value === undefined || value === 'null' || value === 'undefined') return this;
                            if (typeof value === 'string' && /\/(null|undefined)\/?(\?.*)?$/i.test(value)) return this;
                        }
                        return _attr2.apply(this, arguments);
                    };
                }

                // Also clean up any damage already done
                document.querySelectorAll('picture.owl-lazy').forEach(function(pic) {
                    pic.classList.remove('owl-lazy');
                    var img = pic.querySelector('img[data-src]');
                    if (img) img.classList.add('owl-lazy');
                });
                document.querySelectorAll('img').forEach(function(img) {
                    var src = img.getAttribute('src') || '';
                    if (/\/(undefined|null)\/?$/i.test(src)) {
                        var ds = img.getAttribute('data-src');
                        img.src = (ds && ds !== 'undefined' && ds !== 'null') ? ds : '';
                    }
                });
            });
        })();
        </script>
        <?php
    }

    /* ------------------------------------------------------------------ */
    /*  SERVER-SIDE: Return blank pixel for /null and /undefined requests   */
    /* ------------------------------------------------------------------ */

    public function block_null_undefined_requests() {
        $uri = $_SERVER['REQUEST_URI'] ?? '';
        if ( preg_match( '#/(null|undefined)/?(\?.*)?$#i', $uri ) ) {
            // Return a 1x1 transparent GIF instead of letting WP serve a full 404 page
            header( 'HTTP/1.1 200 OK' );
            header( 'Content-Type: image/gif' );
            header( 'Cache-Control: public, max-age=31536000' );
            // 1x1 transparent GIF
            echo base64_decode( 'R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7' );
            exit;
        }
    }

    /* ------------------------------------------------------------------ */
    /*  FRONTEND PAGE DEBUG PANEL (admin-only)                             */
    /* ------------------------------------------------------------------ */

    public function render_page_debug_panel() {
        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return;
        }
        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            var issues = [];

            // 1. Find all <a> tags with null, undefined, empty, or # hrefs
            document.querySelectorAll('a').forEach(function (a) {
                var href = a.getAttribute('href');
                var text = (a.textContent || '').trim().substring(0, 60);
                if (href === null || href === '') {
                    issues.push({ type: 'link', severity: 'error', msg: 'Empty/missing href', detail: '"' + text + '"', el: a });
                } else if (href === '#') {
                    if (!a.closest('.mega-menu-item') && !a.closest('.menu-item-has-children')) {
                        issues.push({ type: 'link', severity: 'warn', msg: 'href="#"', detail: '"' + text + '"', el: a });
                    }
                } else if (/\/(null|undefined)\/?$/i.test(href)) {
                    issues.push({ type: 'link', severity: 'error', msg: 'href ends with /null or /undefined', detail: href, el: a });
                } else if (href === 'null' || href === 'undefined') {
                    issues.push({ type: 'link', severity: 'error', msg: 'href is literally "' + href + '"', detail: '"' + text + '"', el: a });
                }
            });

            // 2. Find images missing alt text
            document.querySelectorAll('img').forEach(function (img) {
                var alt = img.getAttribute('alt');
                if (alt === null || alt.trim() === '') {
                    var src = (img.getAttribute('src') || '').split('/').pop().substring(0, 50);
                    issues.push({ type: 'image', severity: 'warn', msg: 'Missing alt text', detail: src, el: img });
                }
            });

            // 3. Check heading hierarchy
            var headings = document.querySelectorAll('h1, h2, h3, h4, h5, h6');
            var h1Count = 0;
            var prevLevel = 0;
            headings.forEach(function (h) {
                var level = parseInt(h.tagName.charAt(1));
                if (level === 1) h1Count++;
                if (prevLevel > 0 && level > prevLevel + 1) {
                    issues.push({ type: 'heading', severity: 'warn', msg: 'Skipped heading level: H' + prevLevel + ' \u2192 H' + level, detail: h.textContent.trim().substring(0, 50), el: h });
                }
                prevLevel = level;
            });
            if (h1Count === 0) {
                issues.push({ type: 'heading', severity: 'error', msg: 'No H1 tag found on page', detail: '', el: null });
            } else if (h1Count > 1) {
                issues.push({ type: 'heading', severity: 'warn', msg: h1Count + ' H1 tags found (should be 1)', detail: '', el: null });
            }

            // 4. Check for duplicate script sources
            var scripts = {};
            document.querySelectorAll('script[src]').forEach(function (s) {
                var src = s.getAttribute('src').split('?')[0];
                if (scripts[src]) {
                    issues.push({ type: 'perf', severity: 'warn', msg: 'Duplicate script loaded', detail: src.split('/').pop() });
                }
                scripts[src] = true;
            });

            // 5. Check meta description length
            var metaDesc = document.querySelector('meta[name="description"]');
            if (metaDesc) {
                var len = (metaDesc.getAttribute('content') || '').length;
                if (len === 0) issues.push({ type: 'seo', severity: 'error', msg: 'Meta description is empty', detail: '' });
                else if (len > 160) issues.push({ type: 'seo', severity: 'warn', msg: 'Meta description too long (' + len + ' chars)', detail: '' });
            } else {
                issues.push({ type: 'seo', severity: 'error', msg: 'No meta description tag found', detail: '' });
            }

            // 6. Check title length
            var titleLen = document.title.length;
            if (titleLen > 60) {
                issues.push({ type: 'seo', severity: 'warn', msg: 'Title tag too long (' + titleLen + ' chars)', detail: document.title.substring(0, 60) + '...' });
            }

            // Build the panel
            var panel = document.createElement('div');
            panel.id = 'tci-debug-panel';
            var errorCount = issues.filter(function (i) { return i.severity === 'error'; }).length;
            var warnCount = issues.filter(function (i) { return i.severity === 'warn'; }).length;

            panel.innerHTML =
                '<div id="tci-debug-toggle" style="cursor:pointer;padding:8px 14px;background:' +
                (errorCount > 0 ? '#dc3545' : warnCount > 0 ? '#fd7e14' : '#28a745') +
                ';color:#fff;font:bold 13px/1.4 system-ui,sans-serif;border-radius:8px 8px 0 0;">' +
                '\uD83D\uDD0D Page Debug: ' + errorCount + ' errors, ' + warnCount + ' warnings' +
                '</div>' +
                '<div id="tci-debug-body" style="display:none;max-height:400px;overflow-y:auto;padding:10px;background:#1a1a2e;color:#e0e0e0;font:12px/1.5 monospace;">' +
                (issues.length === 0 ? '<div style="color:#28a745;">\u2713 No issues detected on this page.</div>' : '') +
                '</div>';

            panel.style.cssText = 'position:fixed;bottom:0;right:20px;z-index:999999;width:480px;border-radius:8px 8px 0 0;box-shadow:0 -2px 20px rgba(0,0,0,0.3);';

            var body = panel.querySelector('#tci-debug-body');
            issues.forEach(function (issue) {
                var row = document.createElement('div');
                row.style.cssText = 'padding:4px 0;border-bottom:1px solid #333;cursor:' + (issue.el ? 'pointer' : 'default');
                var icon = issue.severity === 'error' ? '\uD83D\uDD34' : '\uD83D\uDFE1';
                var badge = '<span style="background:#333;padding:1px 5px;border-radius:3px;font-size:10px;margin-right:6px;">' + issue.type + '</span>';
                row.innerHTML = icon + ' ' + badge + issue.msg + (issue.detail ? ' <span style="color:#888;">\u2014 ' + issue.detail + '</span>' : '');
                if (issue.el) {
                    row.addEventListener('click', function () {
                        issue.el.scrollIntoView({ behavior: 'smooth', block: 'center' });
                        issue.el.style.outline = '3px solid #dc3545';
                        issue.el.style.outlineOffset = '2px';
                        setTimeout(function () { issue.el.style.outline = ''; issue.el.style.outlineOffset = ''; }, 3000);
                    });
                }
                body.appendChild(row);
            });

            panel.querySelector('#tci-debug-toggle').addEventListener('click', function () {
                body.style.display = body.style.display === 'none' ? 'block' : 'none';
            });

            document.body.appendChild(panel);
        });
        </script>
        <?php
    }
}

new TCI_SEO_Audit();

/* ====================================================================== */
/*  TCI ASSET OPTIMIZER                                                    */
/*  Conditionally dequeues frontend assets not needed on the current page  */
/* ====================================================================== */

class TCI_Asset_Optimizer {

    /**
     * Store which assets were dequeued for the debug panel.
     */
    private $dequeued_assets = [];

    /**
     * Configurable optimization rules.
     */
    private $rules = [];

    public function __construct() {
        // Only run on frontend, not admin
        if ( is_admin() ) {
            return;
        }

        $this->init_rules();
        add_action( 'wp_enqueue_scripts', [ $this, 'optimize_assets' ], 999 );
        add_action( 'wp_print_styles', [ $this, 'optimize_styles' ], 999 );

        // Add info to the existing debug panel
        add_action( 'wp_footer', [ $this, 'render_optimizer_debug' ], 9998 );
    }

    /**
     * Initialize the dequeue rules.
     */
    private function init_rules() {
        $this->rules = [
            'dashicons' => [
                'description' => 'Admin icons (35 KB) not needed for logged-out visitors',
                'match_type'  => 'exact',
                'handles'     => [ 'dashicons' ],
                'asset_type'  => 'style',
                'condition'   => 'not_logged_in',
                'plugin'      => 'WordPress Core',
            ],
            'wp_google_maps' => [
                'description' => 'Google Maps assets only needed on map pages',
                'match_type'  => 'prefix',
                'handles'     => [ 'wpgmza' ],
                'asset_type'  => 'both',
                'condition'   => 'content_match',
                'patterns'    => [ '[wpgmza', 'wpgmza' ],
                'post_type'   => 'wpgmza_map',
                'plugin'      => 'WP Google Maps',
            ],
            'datatables' => [
                'description' => 'DataTables library only needed for store locator / data tables',
                'match_type'  => 'mixed',
                'handles'     => [ 'datatables', 'datatables-responsive' ],
                'contains'    => [ 'dataTables', 'datatables' ],
                'asset_type'  => 'both',
                'condition'   => 'content_match',
                'patterns'    => [ '[wpgmza', '<table' ],
                'plugin'      => 'DataTables (via WP Google Maps)',
            ],
            'wp_reviews_pro' => [
                'description' => 'Reviews assets only needed on review pages',
                'match_type'  => 'contains',
                'contains'    => [ 'wprev', 'wp-reviews' ],
                'asset_type'  => 'both',
                'condition'   => 'content_match',
                'patterns'    => [ '[wprev', 'wprev' ],
                'plugin'      => 'WP Reviews Pro',
            ],
            'smash_balloon_instagram' => [
                'description' => 'Instagram feed assets only needed on feed pages',
                'match_type'  => 'contains',
                'contains'    => [ 'sbi-', 'sb-instagram' ],
                'asset_type'  => 'both',
                'condition'   => 'content_match',
                'patterns'    => [ '[instagram-feed', 'sbi' ],
                'plugin'      => 'Smash Balloon Instagram',
            ],
            'ihover' => [
                'description' => 'iHover CSS only needed on pages using hover effects',
                'match_type'  => 'mixed',
                'handles'     => [ 'ihover' ],
                'contains'    => [ 'ihover' ],
                'asset_type'  => 'style',
                'condition'   => 'content_match',
                'patterns'    => [ 'ihover' ],
                'plugin'      => 'iHover',
            ],
            'wpbakery' => [
                'description' => 'WPBakery assets only needed on Visual Composer pages',
                'match_type'  => 'mixed',
                'handles'     => [ 'js_composer-front', 'js_composer' ],
                'contains'    => [ 'js_composer' ],
                'asset_type'  => 'both',
                'condition'   => 'content_match',
                'patterns'    => [ '[vc_' ],
                'plugin'      => 'WPBakery Page Builder',
            ],
            'wp_block_library' => [
                'description' => 'Block editor CSS not needed on classic editor pages',
                'match_type'  => 'exact',
                'handles'     => [ 'wp-block-library' ],
                'asset_type'  => 'style',
                'condition'   => 'content_match',
                'patterns'    => [ '<!-- wp:' ],
                'plugin'      => 'WordPress Core (Gutenberg)',
            ],
        ];
    }

    /**
     * Check if current page content contains any of the given patterns.
     *
     * @param array|string $patterns Patterns to search for.
     * @return bool
     */
    private function page_has_content( $patterns ) {
        global $post;
        if ( ! $post || empty( $post->post_content ) ) {
            return false;
        }
        foreach ( (array) $patterns as $pattern ) {
            if ( stripos( $post->post_content, $pattern ) !== false ) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if a handle matches a rule's handle definitions.
     *
     * @param string $handle The asset handle to check.
     * @param array  $rule   The rule configuration.
     * @return bool
     */
    private function handle_matches_rule( $handle, $rule ) {
        $match_type = $rule['match_type'] ?? 'exact';

        // Exact match
        if ( in_array( $match_type, [ 'exact', 'mixed' ], true ) && ! empty( $rule['handles'] ) ) {
            if ( in_array( $handle, $rule['handles'], true ) ) {
                return true;
            }
        }

        // Prefix match
        if ( $match_type === 'prefix' && ! empty( $rule['handles'] ) ) {
            foreach ( $rule['handles'] as $prefix ) {
                if ( strpos( $handle, $prefix ) === 0 ) {
                    return true;
                }
            }
        }

        // Contains match
        if ( in_array( $match_type, [ 'contains', 'mixed' ], true ) && ! empty( $rule['contains'] ) ) {
            foreach ( $rule['contains'] as $substring ) {
                if ( stripos( $handle, $substring ) !== false ) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Determine if a rule's condition says we should KEEP the asset on this page.
     *
     * @param array $rule The rule configuration.
     * @return bool True if asset should be KEPT (not dequeued).
     */
    private function should_keep_asset( $rule ) {
        $condition = $rule['condition'] ?? '';

        switch ( $condition ) {
            case 'not_logged_in':
                // Keep if user IS logged in (admin bar needs dashicons)
                return is_user_logged_in();

            case 'content_match':
                // Keep if page content matches the patterns
                if ( ! empty( $rule['patterns'] ) && $this->page_has_content( $rule['patterns'] ) ) {
                    return true;
                }
                // Keep if current post type matches
                if ( ! empty( $rule['post_type'] ) ) {
                    global $post;
                    if ( $post && $post->post_type === $rule['post_type'] ) {
                        return true;
                    }
                }
                return false;

            default:
                // Unknown condition — keep the asset to be safe
                return true;
        }
    }

    /**
     * Dequeue a style if it's enqueued and not filtered out.
     *
     * @param string $handle      The style handle.
     * @param string $rule_name   The rule that triggered this.
     * @param string $plugin      Source plugin name.
     * @param string $reason      Reason for dequeuing.
     */
    private function maybe_dequeue_style( $handle, $rule_name, $plugin, $reason ) {
        if ( ! wp_style_is( $handle, 'enqueued' ) ) {
            return;
        }

        /**
         * Filter: tci_asset_optimizer_skip
         * Return true to prevent dequeuing a specific handle.
         *
         * @param bool   $skip    Whether to skip dequeuing. Default false.
         * @param string $handle  The asset handle.
         * @param string $type    'style' or 'script'.
         */
        if ( apply_filters( 'tci_asset_optimizer_skip', false, $handle, 'style' ) ) {
            return;
        }

        wp_dequeue_style( $handle );
        $this->dequeued_assets[] = [
            'handle' => $handle,
            'type'   => 'style',
            'plugin' => $plugin,
            'rule'   => $rule_name,
            'reason' => $reason,
        ];
    }

    /**
     * Dequeue a script if it's enqueued and not filtered out.
     *
     * @param string $handle      The script handle.
     * @param string $rule_name   The rule that triggered this.
     * @param string $plugin      Source plugin name.
     * @param string $reason      Reason for dequeuing.
     */
    private function maybe_dequeue_script( $handle, $rule_name, $plugin, $reason ) {
        if ( ! wp_script_is( $handle, 'enqueued' ) ) {
            return;
        }

        /** This filter is documented above. */
        if ( apply_filters( 'tci_asset_optimizer_skip', false, $handle, 'script' ) ) {
            return;
        }

        wp_dequeue_script( $handle );
        $this->dequeued_assets[] = [
            'handle' => $handle,
            'type'   => 'script',
            'plugin' => $plugin,
            'rule'   => $rule_name,
            'reason' => $reason,
        ];
    }

    /**
     * Process all registered scripts and styles against our rules.
     * Hooked to wp_enqueue_scripts at priority 999.
     */
    public function optimize_assets() {
        global $wp_scripts, $wp_styles;

        foreach ( $this->rules as $rule_name => $rule ) {
            // If the asset should be kept on this page, skip this rule entirely
            if ( $this->should_keep_asset( $rule ) ) {
                continue;
            }

            $asset_type  = $rule['asset_type'] ?? 'both';
            $plugin      = $rule['plugin'] ?? 'Unknown';
            $reason      = $rule['description'] ?? '';

            // Check scripts
            if ( in_array( $asset_type, [ 'script', 'both' ], true ) && ! empty( $wp_scripts->registered ) ) {
                foreach ( $wp_scripts->registered as $handle => $dep ) {
                    if ( $this->handle_matches_rule( $handle, $rule ) ) {
                        $this->maybe_dequeue_script( $handle, $rule_name, $plugin, $reason );
                    }
                }
            }

            // Check styles
            if ( in_array( $asset_type, [ 'style', 'both' ], true ) && ! empty( $wp_styles->registered ) ) {
                foreach ( $wp_styles->registered as $handle => $dep ) {
                    if ( $this->handle_matches_rule( $handle, $rule ) ) {
                        $this->maybe_dequeue_style( $handle, $rule_name, $plugin, $reason );
                    }
                }
            }
        }
    }

    /**
     * Additional style-only pass at wp_print_styles (catches late-registered styles).
     * Hooked to wp_print_styles at priority 999.
     */
    public function optimize_styles() {
        global $wp_styles;

        if ( empty( $wp_styles->registered ) ) {
            return;
        }

        foreach ( $this->rules as $rule_name => $rule ) {
            $asset_type = $rule['asset_type'] ?? 'both';
            if ( ! in_array( $asset_type, [ 'style', 'both' ], true ) ) {
                continue;
            }

            // If the asset should be kept on this page, skip
            if ( $this->should_keep_asset( $rule ) ) {
                continue;
            }

            $plugin = $rule['plugin'] ?? 'Unknown';
            $reason = $rule['description'] ?? '';

            foreach ( $wp_styles->registered as $handle => $dep ) {
                if ( $this->handle_matches_rule( $handle, $rule ) ) {
                    $this->maybe_dequeue_style( $handle, $rule_name, $plugin, $reason );
                }
            }
        }
    }

    /**
     * Render debug output for the Asset Optimizer.
     * Outputs a script block with optimizer data for the debug panel.
     * Only visible to logged-in admins.
     */
    public function render_optimizer_debug() {
        if ( ! is_user_logged_in() || ! current_user_can( 'manage_options' ) ) {
            return;
        }

        global $wp_scripts, $wp_styles;

        $remaining_css = 0;
        $remaining_js  = 0;

        if ( ! empty( $wp_styles->queue ) ) {
            $remaining_css = count( $wp_styles->queue );
        }
        if ( ! empty( $wp_scripts->queue ) ) {
            $remaining_js = count( $wp_scripts->queue );
        }

        $debug_data = [
            'dequeued_count' => count( $this->dequeued_assets ),
            'dequeued'       => $this->dequeued_assets,
            'remaining_css'  => $remaining_css,
            'remaining_js'   => $remaining_js,
        ];

        $json = wp_json_encode( $debug_data, JSON_UNESCAPED_SLASHES );
        ?>
        <script>
        (function(){
            window.tciAssetOptimizer = <?php echo $json; ?>;

            // If the main TCI debug panel exists, it can read window.tciAssetOptimizer.
            // Otherwise, render a standalone floating badge.
            document.addEventListener('DOMContentLoaded', function() {
                var data = window.tciAssetOptimizer;
                var mainPanel = document.getElementById('tci-debug-panel');

                if ( mainPanel ) {
                    // Append optimizer info to the existing debug panel body
                    var body = document.getElementById('tci-debug-body');
                    if ( body ) {
                        var section = document.createElement('div');
                        section.style.cssText = 'margin-top:10px;padding-top:10px;border-top:2px solid #444;';
                        section.innerHTML = '<div style="color:#64b5f6;font-weight:bold;margin-bottom:6px;">\u26A1 Asset Optimizer</div>';
                        section.innerHTML += '<div style="color:#aaa;">Dequeued: <strong style="color:#fff;">' + data.dequeued_count + '</strong> assets | Remaining: ' + data.remaining_css + ' CSS, ' + data.remaining_js + ' JS</div>';

                        if ( data.dequeued.length > 0 ) {
                            data.dequeued.forEach(function(item) {
                                var row = document.createElement('div');
                                row.style.cssText = 'padding:3px 0;border-bottom:1px solid #333;font-size:11px;';
                                var typeBadge = item.type === 'style' ? '\uD83C\uDFA8' : '\u2699\uFE0F';
                                row.innerHTML = typeBadge + ' <code style="color:#ce93d8;">' + item.handle + '</code> <span style="color:#666;">(' + item.plugin + ')</span> <span style="color:#888;">\u2014 ' + item.reason + '</span>';
                                section.appendChild(row);
                            });
                        }

                        body.appendChild(section);
                    }
                } else {
                    // Standalone floating badge
                    if ( data.dequeued_count > 0 ) {
                        var badge = document.createElement('div');
                        badge.id = 'tci-optimizer-badge';
                        badge.style.cssText = 'position:fixed;bottom:10px;left:20px;z-index:999998;background:#1a1a2e;color:#64b5f6;padding:8px 14px;border-radius:6px;font:bold 12px/1.4 system-ui,sans-serif;box-shadow:0 2px 12px rgba(0,0,0,0.3);cursor:pointer;';
                        badge.innerHTML = '\u26A1 ' + data.dequeued_count + ' assets dequeued';
                        badge.title = 'TCI Asset Optimizer: ' + data.dequeued_count + ' assets removed from this page (' + data.remaining_css + ' CSS + ' + data.remaining_js + ' JS remaining)';

                        badge.addEventListener('click', function() {
                            var detail = document.getElementById('tci-optimizer-detail');
                            if ( detail ) {
                                detail.style.display = detail.style.display === 'none' ? 'block' : 'none';
                                return;
                            }
                            detail = document.createElement('div');
                            detail.id = 'tci-optimizer-detail';
                            detail.style.cssText = 'position:fixed;bottom:45px;left:20px;z-index:999998;background:#1a1a2e;color:#e0e0e0;padding:12px;border-radius:8px;font:12px/1.5 monospace;max-height:300px;overflow-y:auto;width:400px;box-shadow:0 2px 20px rgba(0,0,0,0.4);';
                            detail.innerHTML = '<div style="color:#64b5f6;font-weight:bold;margin-bottom:8px;">\u26A1 Asset Optimizer — ' + data.dequeued_count + ' dequeued</div>';
                            detail.innerHTML += '<div style="color:#aaa;margin-bottom:8px;">Remaining: ' + data.remaining_css + ' CSS, ' + data.remaining_js + ' JS</div>';

                            data.dequeued.forEach(function(item) {
                                var row = document.createElement('div');
                                row.style.cssText = 'padding:3px 0;border-bottom:1px solid #333;';
                                var typeBadge = item.type === 'style' ? '\uD83C\uDFA8' : '\u2699\uFE0F';
                                row.innerHTML = typeBadge + ' <code style="color:#ce93d8;">' + item.handle + '</code> <span style="color:#666;">(' + item.plugin + ')</span><br><span style="color:#888;font-size:11px;">' + item.reason + '</span>';
                                detail.appendChild(row);
                            });

                            document.body.appendChild(detail);
                        });

                        document.body.appendChild(badge);
                    }
                }
            });
        })();
        </script>
        <?php
    }
}

new TCI_Asset_Optimizer();

/* ====================================================================== */
/*  TCI IMAGE OPTIMIZER                                                    */
/*  Fixes oversized/unsized images for both mobile and desktop viewports   */
/*  - Adds missing width/height attributes to prevent layout shifts (CLS)  */
/*  - Corrects `sizes` attribute so browsers download appropriately sized  */
/*    images for the viewport (fixes LCP oversized-image penalty)          */
/* ====================================================================== */

class TCI_Image_Optimizer {

    public function __construct() {
        if ( is_admin() ) {
            return;
        }

        // Filter image tags in post content (WP 6.0+)
        add_filter( 'wp_content_img_tag', [ $this, 'fix_content_image_tag' ], 20, 3 );

        // Filter the `sizes` attribute for all responsive images
        add_filter( 'wp_calculate_image_sizes', [ $this, 'responsive_image_sizes' ], 20, 5 );

        // Fallback: filter the_content to catch images missed by wp_content_img_tag
        add_filter( 'the_content', [ $this, 'fix_content_images_fallback' ], 99 );

        // Filter post thumbnails / featured images
        add_filter( 'post_thumbnail_html', [ $this, 'fix_thumbnail_sizes' ], 20, 5 );

        // Add preload hint for LCP image on singular pages
        add_action( 'wp_head', [ $this, 'preload_lcp_image' ], 2 );
    }

    /* ------------------------------------------------------------------ */
    /*  FIX CONTENT IMAGE TAGS (WP 6.0+ filter)                            */
    /*  Ensures width/height attributes are present and sizes is correct    */
    /* ------------------------------------------------------------------ */

    /**
     * Process each <img> tag in post content.
     *
     * @param string $image   The full <img> tag HTML.
     * @param string $context The context (e.g. 'the_content').
     * @param int    $attachment_id The attachment ID.
     * @return string Modified <img> tag.
     */
    public function fix_content_image_tag( $image, $context, $attachment_id ) {
        // 1. Ensure width and height attributes exist
        $image = $this->ensure_dimensions( $image, $attachment_id );

        // 2. Fix the sizes attribute for responsive delivery
        $image = $this->fix_sizes_attribute( $image );

        return $image;
    }

    /* ------------------------------------------------------------------ */
    /*  ENSURE WIDTH/HEIGHT DIMENSIONS                                      */
    /*  Prevents CLS by guaranteeing the browser knows aspect ratio         */
    /* ------------------------------------------------------------------ */

    /**
     * Add width/height attributes if missing.
     *
     * @param string $img_tag       The <img> tag HTML.
     * @param int    $attachment_id The attachment ID (0 if unknown).
     * @return string Modified <img> tag.
     */
    private function ensure_dimensions( $img_tag, $attachment_id = 0 ) {
        // Skip if both width and height already present
        if ( preg_match( '/\bwidth\s*=\s*["\']?\d+/i', $img_tag )
             && preg_match( '/\bheight\s*=\s*["\']?\d+/i', $img_tag ) ) {
            return $img_tag;
        }

        $width  = 0;
        $height = 0;

        // Try to get dimensions from attachment metadata
        if ( $attachment_id > 0 ) {
            $meta = wp_get_attachment_metadata( $attachment_id );
            if ( ! empty( $meta['width'] ) && ! empty( $meta['height'] ) ) {
                $width  = (int) $meta['width'];
                $height = (int) $meta['height'];

                // If a specific size class is used, get that size's dimensions
                if ( preg_match( '/\bwp-image-' . $attachment_id . '\b/', $img_tag )
                     && preg_match( '/\bsize-(\S+)/', $img_tag, $size_match ) ) {
                    $size_name = $size_match[1];
                    if ( ! empty( $meta['sizes'][ $size_name ] ) ) {
                        $width  = (int) $meta['sizes'][ $size_name ]['width'];
                        $height = (int) $meta['sizes'][ $size_name ]['height'];
                    }
                }
            }
        }

        // Fallback: try to extract from src filename (e.g. image-768x1024.jpg)
        if ( ( $width === 0 || $height === 0 ) && preg_match( '/src=["\']([^"\']+)["\']/i', $img_tag, $src_match ) ) {
            $src = $src_match[1];
            if ( preg_match( '/-(\d+)x(\d+)\.\w+/', $src, $dim_match ) ) {
                $width  = (int) $dim_match[1];
                $height = (int) $dim_match[2];
            }
        }

        if ( $width > 0 && $height > 0 ) {
            // Remove any existing (possibly empty) width/height
            $img_tag = preg_replace( '/\s+width\s*=\s*["\'][^"\']*["\']/i', '', $img_tag );
            $img_tag = preg_replace( '/\s+height\s*=\s*["\'][^"\']*["\']/i', '', $img_tag );

            // Insert before the closing >
            $img_tag = preg_replace(
                '/\s*\/?>$/',
                sprintf( ' width="%d" height="%d" />', $width, $height ),
                $img_tag
            );
        }

        return $img_tag;
    }

    /* ------------------------------------------------------------------ */
    /*  FIX SIZES ATTRIBUTE                                                 */
    /*  Replaces generic "(max-width: Xpx) 100vw, Xpx" with viewport-     */
    /*  aware breakpoints so mobile gets smaller images, desktop gets       */
    /*  appropriate sizes.                                                  */
    /* ------------------------------------------------------------------ */

    /**
     * Replace the sizes attribute with viewport-aware breakpoints.
     *
     * @param string $img_tag The <img> tag HTML.
     * @return string Modified <img> tag.
     */
    private function fix_sizes_attribute( $img_tag ) {
        // Only process images that have a srcset (responsive images)
        if ( stripos( $img_tag, 'srcset' ) === false ) {
            return $img_tag;
        }

        // Extract the current width attribute to use as max
        $display_width = 0;
        if ( preg_match( '/\bwidth\s*=\s*["\']?(\d+)/i', $img_tag, $w_match ) ) {
            $display_width = (int) $w_match[1];
        }

        if ( $display_width <= 0 ) {
            return $img_tag;
        }

        // Build a smart sizes attribute based on common layout patterns
        $sizes = $this->build_sizes_attribute( $img_tag, $display_width );

        if ( ! empty( $sizes ) ) {
            // Replace existing sizes attribute
            if ( preg_match( '/\bsizes\s*=\s*["\'][^"\']*["\']/i', $img_tag ) ) {
                $img_tag = preg_replace(
                    '/\bsizes\s*=\s*["\'][^"\']*["\']/i',
                    'sizes="' . esc_attr( $sizes ) . '"',
                    $img_tag
                );
            } else {
                // Add sizes attribute if missing
                $img_tag = preg_replace(
                    '/\s*\/?>$/',
                    ' sizes="' . esc_attr( $sizes ) . '" />',
                    $img_tag
                );
            }
        }

        return $img_tag;
    }

    /**
     * Build a responsive sizes attribute string.
     *
     * Desktop: content area is typically ~1140px max, images in columns
     * Tablet:  content area ~768px
     * Mobile:  images go full-width minus padding (~calc(100vw - 30px))
     *
     * @param string $img_tag       The <img> tag for context (CSS classes, etc.)
     * @param int    $display_width The intrinsic/declared width of the image.
     * @return string The sizes attribute value.
     */
    private function build_sizes_attribute( $img_tag, $display_width ) {
        // Detect column context from WPBakery/VC classes
        $col_fraction = 1; // default: full width

        if ( preg_match( '/\bvc_col-sm-(\d+)\b/', $img_tag, $col_match ) ) {
            $col_fraction = (int) $col_match[1] / 12;
        } elseif ( preg_match( '/\bvc_col-md-(\d+)\b/', $img_tag, $col_match ) ) {
            $col_fraction = (int) $col_match[1] / 12;
        }

        // Check parent context — leadership page images are in ~50% columns on desktop
        // but go full-width on mobile. We'll use a general-purpose approach:
        $max_desktop = min( $display_width, (int) round( 1140 * $col_fraction ) );
        $max_tablet  = min( $display_width, 768 );
        $mobile_size = 'calc(100vw - 30px)';

        // Build the sizes string:
        // Mobile-first: below 768px use nearly full viewport width
        // Tablet: use up to tablet content width
        // Desktop: use calculated column width
        $sizes_parts = [];

        // Mobile (up to 767px): full width minus padding
        $sizes_parts[] = '(max-width: 767px) ' . $mobile_size;

        // Tablet (768px - 1023px)
        if ( $col_fraction < 1 ) {
            // In a column layout, tablet might still be full-width (stacked)
            $sizes_parts[] = '(max-width: 1023px) calc(100vw - 30px)';
        } else {
            $sizes_parts[] = '(max-width: 1023px) ' . $max_tablet . 'px';
        }

        // Desktop: use the calculated max
        $sizes_parts[] = $max_desktop . 'px';

        return implode( ', ', $sizes_parts );
    }

    /* ------------------------------------------------------------------ */
    /*  RESPONSIVE IMAGE SIZES FILTER                                       */
    /*  Hooks into wp_calculate_image_sizes for all WP-generated images     */
    /* ------------------------------------------------------------------ */

    /**
     * Filter the default sizes attribute WordPress generates.
     *
     * @param string       $sizes         The sizes attribute value.
     * @param string|int[] $size          Requested image size (name or array).
     * @param string|null  $image_src     The image source URL.
     * @param array|null   $image_meta    The image metadata.
     * @param int          $attachment_id The attachment ID.
     * @return string Modified sizes attribute.
     */
    public function responsive_image_sizes( $sizes, $size, $image_src, $image_meta, $attachment_id ) {
        // Only override the generic WP default pattern
        // Default WP: "(max-width: {width}px) 100vw, {width}px"
        if ( empty( $sizes ) || strpos( $sizes, '100vw' ) === false ) {
            return $sizes;
        }

        // Get the width for this size
        $width = 0;
        if ( is_array( $size ) ) {
            $width = (int) $size[0];
        } elseif ( is_string( $size ) && ! empty( $image_meta['sizes'][ $size ]['width'] ) ) {
            $width = (int) $image_meta['sizes'][ $size ]['width'];
        } elseif ( ! empty( $image_meta['width'] ) ) {
            $width = (int) $image_meta['width'];
        }

        if ( $width <= 0 ) {
            return $sizes;
        }

        // Replace with viewport-aware sizes
        $new_sizes = sprintf(
            '(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) min(%dpx, calc(100vw - 60px)), %dpx',
            min( $width, 768 ),
            min( $width, 1140 )
        );

        return $new_sizes;
    }

    /* ------------------------------------------------------------------ */
    /*  FALLBACK: FILTER the_content FOR IMAGES WITHOUT WP CLASSES          */
    /*  Catches manually inserted images or those from page builders        */
    /* ------------------------------------------------------------------ */

    /**
     * Process all <img> tags in content that may have been missed.
     *
     * @param string $content The post content.
     * @return string Modified content.
     */
    public function fix_content_images_fallback( $content ) {
        if ( empty( $content ) ) {
            return $content;
        }

        // Find all img tags
        return preg_replace_callback(
            '/<img\b[^>]+>/i',
            [ $this, 'process_img_tag_callback' ],
            $content
        );
    }

    /**
     * Callback for preg_replace on img tags.
     *
     * @param array $matches Regex matches.
     * @return string Modified img tag.
     */
    private function process_img_tag_callback( $matches ) {
        $img_tag = $matches[0];

        // Skip if already processed by wp_content_img_tag (has wp-image- class)
        if ( preg_match( '/\bwp-image-\d+\b/', $img_tag ) ) {
            return $img_tag;
        }

        // Ensure dimensions
        $img_tag = $this->ensure_dimensions( $img_tag, 0 );

        // Fix sizes
        $img_tag = $this->fix_sizes_attribute( $img_tag );

        return $img_tag;
    }

    /* ------------------------------------------------------------------ */
    /*  FIX POST THUMBNAIL SIZES                                            */
    /* ------------------------------------------------------------------ */

    /**
     * Fix sizes attribute on post thumbnails / featured images.
     *
     * @param string       $html          The thumbnail HTML.
     * @param int          $post_id       The post ID.
     * @param int          $thumbnail_id  The thumbnail attachment ID.
     * @param string|int[] $size          The requested size.
     * @param string       $attr          Additional attributes.
     * @return string Modified HTML.
     */
    public function fix_thumbnail_sizes( $html, $post_id, $thumbnail_id, $size, $attr ) {
        if ( empty( $html ) ) {
            return $html;
        }

        return $this->fix_sizes_attribute( $html );
    }

    /* ------------------------------------------------------------------ */
    /*  PRELOAD LCP IMAGE                                                   */
    /*  On singular pages, preload the first content image for faster LCP   */
    /* ------------------------------------------------------------------ */

    /**
     * Output a <link rel="preload"> for the likely LCP image on singular pages.
     */
    public function preload_lcp_image() {
        if ( ! is_singular() ) {
            return;
        }

        global $post;
        if ( empty( $post->post_content ) ) {
            return;
        }

        // Find the first image in content
        if ( ! preg_match( '/<img\b[^>]+src=["\']([^"\']+)["\'][^>]*>/i', $post->post_content, $match ) ) {
            return;
        }

        $src = $match[1];
        $img_tag = $match[0];

        // Only preload if it has fetchpriority="high" or is the first image
        // (WP 6.3+ adds fetchpriority="high" to LCP candidates)
        if ( stripos( $img_tag, 'fetchpriority' ) === false && stripos( $img_tag, 'loading="lazy"' ) !== false ) {
            return; // Skip lazy-loaded images
        }

        // Extract srcset for responsive preload
        $srcset = '';
        if ( preg_match( '/\bsrcset\s*=\s*["\']([^"\']+)["\']/i', $img_tag, $srcset_match ) ) {
            $srcset = $srcset_match[1];
        }

        // Build sizes for preload
        $sizes = '(max-width: 767px) calc(100vw - 30px), (max-width: 1023px) calc(100vw - 60px), 600px';
        if ( preg_match( '/\bsizes\s*=\s*["\']([^"\']+)["\']/i', $img_tag, $sizes_match ) ) {
            $sizes = $sizes_match[1];
        }

        // Determine image type for the `type` attribute
        $type = '';
        if ( preg_match( '/\.webp/i', $src ) ) {
            $type = ' type="image/webp"';
        } elseif ( preg_match( '/\.avif/i', $src ) ) {
            $type = ' type="image/avif"';
        }

        // Output preload link
        if ( ! empty( $srcset ) ) {
            printf(
                '<link rel="preload" as="image" href="%s" imagesrcset="%s" imagesizes="%s"%s fetchpriority="high" />' . "\n",
                esc_url( $src ),
                esc_attr( $srcset ),
                esc_attr( $sizes ),
                $type
            );
        } else {
            printf(
                '<link rel="preload" as="image" href="%s"%s fetchpriority="high" />' . "\n",
                esc_url( $src ),
                $type
            );
        }
    }
}

new TCI_Image_Optimizer();