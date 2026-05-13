<?php
define( 'DEBUG_IP', '156.57.246.49' );
define( 'TCIDIR', dirname(__FILE__) );
@define( 'LWMWP_API_TOKEN', '' );
// include 'debug.php';
include 'inc/index.php';
include dirname(__FILE__).'/options.php';
if( is_admin() ) include 'lib/waf/plugin.php';
else {
	include 'lib/waf/extras/custom-taxonomies/custom-taxonomies.php';
	include 'debug.php';
}
/**
 * Register a responsive image size for leadership portraits.
 * The page displays these at 382px wide, so a 400px crop avoids
 * serving the full 768px or 1920px source to mobile visitors (~225 KiB savings).
 */
add_action( 'after_setup_theme', function() {
    add_image_size( 'leadership-portrait', 400, 533, true );
} );

function fix_review_dates() {
	if( is_admin() && @$_GET['page'] == 'wp_pro-reviews' ) {
		global $wpdb;
		$table_name = $wpdb->prefix.'wpfb_reviews';
		$q = "SELECT * FROM ".$table_name." ORDER BY created_time ASC";
		$results = $wpdb->get_results( $q );
		// $q = "UPDATE $table_name SET created_time = null WHERE created_time_stamp=0";
		// $wpdb->query( $q );
		// d('results:',$results);
	}
}
add_filter('rewrite_rules_array', function($rules){
    // d($rules);
    return $rules;
});
add_action( 'init', 'fix_review_dates' );
function tci_rewrite() {
	add_rewrite_rule('blog/page/([0-9]{1,})/?$', 'index.php?post_type=post&paged=$matches[1]', 'top');
	add_rewrite_rule('blog/([^/]*)/page/([0-9]{1,})/?$', 'index.php?post_type=post&category_name=$matches[1]&paged=$matches[2]', 'top');
	add_rewrite_rule('blog/author/([^/]*)/?', 'index.php?author_name=$matches[1]', 'top');
	add_rewrite_rule('blog/([^/]*)/?', 'index.php?category=$matches[1]', 'top');
	add_rewrite_rule('blog/([^/]*)/?', 'index.php?name=$matches[1]&page=', 'top');
	
    // add_rewrite_tag('%category%', '([^&]+)');
    // add_rewrite_tag('%ca/%', '([^&]+)');
    // add_rewrite_tag('%state%', '([^&]+)');

    // add_permastruct( 'cars', 'inventory/%listing_id%/%cars%?' );

    global $wp_rewrite,$wp_query;
    

	// $wp_rewrite->permalink_structure = '/blog/%postname%/';
	if( !is_admin() && is_devel() ) {
		// d($wp_rewrite);
		// d($wp_query);
		// die;
	}
    if( is_devel() && isset($_GET['flush_rules']) ) 
    flush_rewrite_rules();
}
add_action( 'init', 'tci_rewrite', 9999 );

function fix_blog_request($sql, $query) {
	if( is_main_query() ) {
		// d($sql);
		// if( strpos($sql,'9') ) die;
		// if( strpos($sql,'mservin') ) die;
	}
	return $sql;
}
add_filter( 'posts_request', 'fix_blog_request', 10, 2 );


function fix_category_links($link_html,$cat='') {
	
	if( $cat->taxonomy == 'category' ) {
		// $link_html = get_bloginfo('url').'/blog/'.$cat->slug;
	}
	// d($cat);
	// d($link_html);
	// die;
   return $link_html;
 
};
add_filter("term_link", "fix_category_links",999,2);

function fix_blog_query($q) {
	// d($q);
	// die;
	preg_match( '|blog/(.*)/?|s', $q->request, $matches );
	if( !empty($matches) ) {
		// d($matches[1]);
		if( $cat = get_category_by_slug($matches[1]) ) {
			// d($cat);
		
			$q->query_vars['category_name'] = $matches[1];
			unset( $q->query_vars['name']);
		}
	}

	preg_match( '|blog/author/(.*)/?|s', $q->request, $matches );
	// d($matches);
	if( !empty($matches) ) {
		// $user = get_user_by( 'login', $matches[1] );
		// d($user->ID);
		// if( $user && !is_wp_error($user) ) $q->query_vars['author'] = $user->ID;
	}
	// d($q);
	// die;
	return $q;
}
add_filter( 'parse_request', 'fix_blog_query' );

function back_button() {
	global  $car_dealer_options;
	$referrer = trailingslashit( $_SERVER['HTTP_REFERER'] );
	// d('referrer:',$referrer);
	$inventory_page = $car_dealer_options['cars_inventory_page'];
	// d('page:',$inventory_page);
	$inventory_url = trailingslashit( get_permalink( $inventory_page ) );
	// d($inventory_url);

	if( strpos( $referrer, $inventory_url) === 0 ) $url = $referrer;
	elseif( strpos( $referrer, 'vehicle-category') ) $url = $referrer;
	elseif( strpos( $referrer, 'location') ) $url = $referrer;
	else $url = $inventory_url;
	// d('url:',$url);
	?>
	<a href="<?php echo $url; ?>">
		<i class="fa fa-chevron-left"></i>
		Back to Search
	</a>
	<?php
	
}

function tci_js() {
	global $wp_query;
	// d($wp_query);
	?>
	<script>
		<?php include dirname(__FILE__).'/scripts.js'; ?>
	</script>
	<?php
}
add_action( 'wp_footer', 'tci_js' );
// Change "cars" to "trucks"
function tci_cardealer_lazyload_filter_title( $title ) {
	return 'Truck Filters';
}
add_filter( 'cardealer_lazyload_filter_title', 'tci_cardealer_lazyload_filter_title' );

function tci_cardealer_filters_taxonomy_array( $data ) {
	foreach( $data as $d=>$datum ) if( $datum=='location' ) unset($data[$d]);
	$data = array_merge( ['vehicle_cat','truck-type','location'], $data );
	// d($data);
	return $data;

}
add_filter( 'cardealer_filters_taxonomy_array', 'tci_cardealer_filters_taxonomy_array' );
add_filter( 'cardealer_get_filters_taxonomy', 'tci_cardealer_filters_taxonomy_array' );

/**
 * Theme functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package CarDealer
 */

/*
 * If your child theme has more than one .css file (eg. ie.css, style.css, main.css) then
 * you will have to make sure to maintain all of the parent theme dependencies.
 *
 * Make sure you're using the correct handle for loading the parent theme's styles.
 * Failure to use the proper tag will result in a CSS file needlessly being loaded twice.
 * This will usually not affect the site appearance, but it's inefficient and extends your page's loading time.
 *
 * @link https://codex.wordpress.org/Child_Themes
 */
function tci_trucks_child_enqueue_styles() {// phpcs:ignore WordPress.WhiteSpace.ControlStructureSpacing.NoSpaceAfterOpenParenthesis

	wp_enqueue_style( 'cardealer-main', get_parent_theme_file_uri( '/css/style.css' ) );

	if ( is_rtl() ) {
		wp_enqueue_style( 'rtl-style', get_parent_theme_file_uri( '/rtl.css' ) );
	}

	wp_enqueue_style(
		'tci-trucks-child-child-style',
		get_stylesheet_directory_uri() . '/style.css',
		array( 'cardealer-main' ),
		wp_get_theme()->get( 'Version' )
	);
	
}
add_action( 'wp_enqueue_scripts', 'tci_trucks_child_enqueue_styles', 11 );
add_filter( 'cardealer_get_library_scripts', function($s) {
	// d($s);
	$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
	$s['cardealer-mega-menu'] = array(
		'handle'    => 'cardealer-mega-menu',
		'src'       => get_stylesheet_directory_uri() . '/mega-menu' . $suffix . '.js',
		'ver'       => CARDEALER_VERSION,
		'deps'      => array( 'jquery' ),
		'in_footer' => true,
		'action'    => 'enqueue',
		'context'   => array(
			'front',
		),
	);
	return $s;
});
function tci_trucks_init() {
	// global $wpdb;
	// $q = "UPDATE $wpdb->posts SET post_content='' WHERE post_type='cars'";
	// $wpdb->query($q);
	// d('error:',$wpdb->last_error);
	// die;
	// add_custom_taxonomy( 'Truck Category', 'Truck Categories', 'cars', 'truck-category' );
	
	
	

	wp_enqueue_script( 'cardealer-mega-header' );
	add_custom_taxonomy( 'Truck Type', 'Truck Types', 'cars', 'truck-type' );
	add_custom_taxonomy( 'Location', 'Locations', 'cars', 'location' );
	add_custom_taxonomy( 'Contact', 'Contacts', 'cars', 'contact' );

	$cat = get_taxonomy( 'vehicle_cat' ); // returns an object

    // make changes to the args
    // in this example there are three changes
    // again, note that it's an object
    if( @is_object($people_category_args) ) @$people_category_args->hierarchical = true;
    

    // re-register the taxonomy
    register_taxonomy( 'vehicle_cat', 'cars', (array) $cat ); 

}
add_action( 'init', 'tci_trucks_init' );

add_action( 'the_post', function() {
	global $post;
	if( is_single() ) {
	}
});

add_filter( 'the_post', function() {
	global $post;
	if( get_post_type() == 'cars' ) {
		// d(json_encode(get_post_custom($post->ID)));
		$images = get_post_meta($post->ID,'car_images',true);
		// d('images:',$images);
		return;
		foreach( $images as $image ) {
			$image = wp_get_attachment_image_src( $image, 'thumbnail' )[0];
			?>
			<a href="<?php echo $image; ?>">
				<img src="<?php echo $image; ?>">
				IMAGE: <?php echo $image; ?>
			<?php
		}
		// $data = get_post_meta( $post->ID, 'data', true );
		// $custom = get_post_custom( $post->ID );
		// d(get_taxonomies());
		
		// d('data:',json_decode( $data, 1 ) );
		// d( 'CUSTOM:',$custom );
		
		// die;
	}
});

function cardealer_get_cars_list_attribute() {
	global $post, $car_dealer_options;
	
	$location = get_the_terms( $post->ID, 'location' );
	if ( empty( $location ) ) {
		return;
	}
	$car_location         = '';
	$car_transmission = '';
	$car_mileage      = '';
	if ( ! is_wp_error( $location ) && isset( $location[0]->name ) ) {
		$parts = explode( '-', $location[0]->name );
		$car_location = count($parts) > 1 ? $parts[1] : $location[0]->name;
	}
	// if ( ! is_wp_error( $location ) && isset( $transmission[0]->name ) ) {
	// 	$car_transmission = $transmission[0]->name;
	// }
	// if ( ! is_wp_error( $location ) && isset( $mileage[0]->name ) ) {
	// 	$car_mileage = $mileage[0]->name;
	// }

	// @codingStandardsIgnoreStart
	$cars_grid = isset( $_COOKIE['cars_grid'] ) ? $_COOKIE['cars_grid'] : '';
	$cars_grid = isset( $_REQUEST['cars_grid'] ) ? $_REQUEST['cars_grid'] : $cars_grid;
	// @codingStandardsIgnoreEnd

	if ( '' === $cars_grid ) {
		$cars_grid = cardealer_get_cars_catlog_style();
	}

	$type    = '';
	$trn_cls = ' class="car-transmission-dots" ';
	if ( '' !== $cars_grid && 'yes' !== $cars_grid ) {
		$trn_cls = ' ';
	}

	$attributs = '<div class="car-list"><ul class="list-inline">';
	if ( ! empty( $car_location ) ) {
		$attributs .= '<li><i class="fas fa-map-marker-alt"></i> ' . esc_html( $car_location ) . '</li>';
	}
	if ( ! empty( $car_transmission ) ) {
		$attributs .= '<li' . $trn_cls . 'title="' . esc_html( $car_transmission ) . '"><i class="fas fa-cog"></i> ' . esc_html( $car_transmission ) . '</li>';
	}
	if ( $car_mileage ) {
		$attributs .= '<li><i class="glyph-icon flaticon-gas-station"></i> ' . esc_html( $car_mileage ) . '</li>';
	}
	$attributs .= '</ul></div>';

	/**
	 * Filters the HTML contents which displays vehicle attributes in inventory page.
	 *
	 * @since 1.0
	 * @param string      $attributs    HTML contents which displays vehicle attributes in inventory page.
	 * @visible           true
	 */
	echo apply_filters( 'cardealer_get_cars_list_attribute', $attributs ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotE
}


function tci_cardealer_get_all_filters( $data ) {
	return [];
	return $data;
}
add_filter( 'cardealer_get_all_filters', 'tci_cardealer_get_all_filters' );

function cardealer_new_get_all_filters( $get_arg ) {
		$is_vehicle_cat = false;
		if ( is_tax( 'vehicle_cat' ) ) {
			$is_vehicle_cat = false;
			global $wp_query;
			$get_arg[] = array(
				'taxonomy' => 'vehicle_cat',
				'field'    => 'slug',
				'terms'    => array( $wp_query->query_vars['vehicle_cat'] ),

			);
		}

		$taxonomys     = cardealer_get_filters_taxonomy();
		
		$args          = cardealer_make_filter_wp_query( $_GET ); // phpcs:ignore WordPress.Security.NonceVerification
		
		$result_filter = array();

		$args_new                  = $args;
		$args_new['fields']        = 'ids';
		$args_new['no_found_rows'] = true;
		$filter_query_args         = array_replace( $args_new, array( 'posts_per_page' => -1 ) );

		$filter_query = new WP_Query( $filter_query_args );
		$tot_result   = $filter_query->post_count;
		if ( $filter_query->have_posts() ) {
			if ( isset( $get_arg ) && ! empty( $get_arg ) && $tot_result > 0 ) {
				foreach ( $taxonomys as $tax ) {
					$tax_args = array(
						'orderby' => 'name',
						'order'   => 'ASC',
						'fields'  => 'all',
					);
					$terms    = wp_get_object_terms( $filter_query->posts, $tax, $tax_args );
					foreach ( $terms as $tdata ) {
						if ( $tdata->taxonomy === $tax ) {
							$result_filter[ $tax ][] = array(
								'term_id'  => $tdata->term_id,
								'slug'     => $tdata->slug,
								'name'     => $tdata->name,
								'taxonomy' => $tdata->taxonomy,
							);
						}
					}
				}
			}
			if ( $is_vehicle_cat ) {
				$args  = array(
					'orderby' => 'name',
					'order'   => 'ASC',
					'fields'  => 'all',
				);
				$terms = wp_get_object_terms( $filter_query->posts, 'vehicle_cat', $tax_args );
				foreach ( $terms as $tdata ) {
					if ( 'vehicle_cat' === $tdata->taxonomy ) {
						$result_filter[ $tax ][] = array(
							'term_id'  => $tdata->term_id,
							'slug'     => $tdata->slug,
							'name'     => $tdata->name,
							'taxonomy' => $tdata->taxonomy,
						);
					}
				}
			}
			wp_reset_postdata();
		}
		$attributs      = '<div class="cars-total-vehicles">';
			$attributs .= '<span class="stripe"><strong><span class="number_of_listings">' . esc_html( $tot_result ) . '</span> ';
			$attributs .= '<span class="listings_grammar">' . esc_html__( 'Vehicles Matching', 'cardealer' ) . '</span></strong></span>';
			$attributs .= '<ul class="stripe-item filter margin-bottom-none" data-all-listings="All Listings">';

		foreach ( $_GET as $gkey => $gval ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( in_array( $gkey, $taxonomys ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				$taxonomy_name = get_taxonomy( $gkey );
				$label         = $taxonomy_name->labels->singular_name;
				if ( 'car_mileage' === $gkey ) {
					$attributs .= '<li id="stripe-item-' . esc_attr( $gkey ) . '" data-type="' . esc_attr( $gkey ) . '" ><a href="javascript:void(0)"><i class="far fa-times-circle"></i> ' . esc_html( $label ) . ' :  <span data-key="' . esc_attr( sanitize_text_field( wp_unslash( $_GET[ $gkey ] ) ) ) . '">' . esc_html( sanitize_text_field( wp_unslash( $_GET[ $gkey ] ) ) ) . '</span></a></li>'; // phpcs:ignore
				} else {
					$term       = get_term_by( 'slug', $gval, $gkey );
					$term_name  = isset( $term->name ) ? $term->name : '';
					$attributs .= '<li id="stripe-item-' . esc_attr( $gkey ) . '" data-type="' . esc_attr( $gkey ) . '" ><a href="javascript:void(0)"><i class="far fa-times-circle"></i> ' . esc_html( $label ) . ' :  <span data-key="' . esc_attr( $gval ) . '">' . esc_html( $term_name ) . '</span></a></li>';
				}
			}
		}
			$attributs .= '</ul>';
		$attributs     .= '</div>';
		$attributs     .= '<div class="listing_sort">';

		$attributs .= '<div class="sort-filters">';
		$t          = 1;

		$is_year_range_active = cardealer_is_year_range_active();
		if ( $is_year_range_active ) {
			$year_range_filters = cardealer_get_year_range_filters( '' );
			$attributs         .= $year_range_filters;
			if ( array_search( 'car_year', $taxonomys ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
				$key = array_search( 'car_year', $taxonomys ); // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict

				unset( $taxonomys[ $key ] );}
		}

		/** Here we create selectbox as per query or default*/
		foreach ( $taxonomys as $tax ) {

			$taxonomy_name = get_taxonomy( $tax );
			$label         = $taxonomy_name->labels->singular_name;
			$attributs    .= '<select data-tax="' . esc_attr( $label ) . '" data-id="' . esc_attr( $tax ) . '" id="sort_' . esc_attr( $tax ) . '" name="' . esc_attr( $tax ) . '" class="select-sort-filters cd-select-box">';
			$attributs    .= '<option value="">' . esc_html( $label ) . '</option>';
			/** Cehck is there any argumet for filter term */
			if ( isset( $get_arg ) && ! empty( $get_arg ) ) {
				$newarr = array();

				if ( ! empty( $result_filter[ $tax ] ) ) {
					foreach ( $result_filter[ $tax ] as $term_data ) {
							$selected = '';
						if ( 'car_mileage' !== $tax ) {
							if ( isset( $_GET[ $tax ] ) && sanitize_text_field( wp_unslash( $_GET[ $tax ] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification
								if ( $_GET[ $tax ] === $term_data['slug'] ) { // phpcs:ignore WordPress.Security.NonceVerification
									$selected = "selected='selected'";
								}
							}

							if ( ! in_array( $term_data['slug'], $newarr ) ) { // phpcs:ignore WordPress.PHP.StrictInArray.MissingTrueStrict
								// $attributs .= '<option value="' . $term_data['slug'] . '" ' . $selected . '>' . $term_data['name'] . '</option>';
								$newarr[]   = $term_data['slug'];
							}
						} else {

							$mileage_array = cardealer_get_mileage_array();
							if ( 'car_mileage' === $tax && 1 === $t ) {
								foreach ( $mileage_array as $mileage ) {
									$selected = '';
									if ( isset( $_GET['car_mileage'] ) && $_GET['car_mileage'] === $mileage ) { // phpcs:ignore WordPress.Security.NonceVerification
										$selected = "selected=''";
									}
									$attributs .= '<option value="' . esc_attr( $mileage ) . '" ' . esc_attr( $selected ) . '>&leq; ' . esc_html( $mileage ) . '</option>';
								}
								$t++;
							}
						}
					}
				}
			} else {
				/** Here we set default terms list */
				$terms = get_terms(
					array(
						'taxonomy'   => $tax,
						'hide_empty' => true,
						'parent' => 0
					)
				);

				foreach ( $terms as $tdata ) {
					if ( 'car_mileage' !== $tax ) {
						$selected = '';
						if ( isset( $_GET[ $tax ] ) && '' !== $_GET[ $tax ] ) { // phpcs:ignore WordPress.Security.NonceVerification
							if ( $_GET[ $tax ] === $tdata->slug ) { // phpcs:ignore WordPress.Security.NonceVerification
								$selected = "selected=''";
							}
						}
						$attributs .= '<option value="' . esc_attr( $tdata->slug ) . '" ' . esc_attr( $selected ) . '>' . esc_html( $tdata->name ) . '</option>';
						if( $tax == 'vehicle_cat' ) {
							// Children
							$children = get_terms(['taxonomy'   => $tax,'hide_empty' => true,'parent' => $tdata->term_id]);
							if( count($children) ) {
								foreach ( $children as $tdata ) {
									$selected = '';
									if ( isset( $_GET[ $tax ] ) && '' !== $_GET[ $tax ] ) if ( $_GET[ $tax ] === $tdata->slug ) $selected = "selected=''";
									$attributs .= '<option value="' . esc_attr( $tdata->slug ) . '" ' . esc_attr( $selected ). '>&dash;' . esc_html( $tdata->name ) . '</option>';
								}

								// Grandchildren
								$grand = get_terms(['taxonomy'   => $tax,'hide_empty' => true,'parent' => $tdata->term_id]);
								if( count($grand) ) {
									foreach ( $grand as $tdata ) {
										$selected = '';
										if ( isset( $_GET[ $tax ] ) && '' !== $_GET[ $tax ] ) if ( $_GET[ $tax ] === $tdata->slug ) $selected = "selected=''";
										$attributs .= '<option value="' . esc_attr( $tdata->slug ) . '" ' . esc_attr( $selected ). '> &dash;&dash;' . esc_html( $tdata->name ) . '</option>';
									}
								}
							}
						}
					} else {

						$mileage_array = cardealer_get_mileage_array();
						if ( 'car_mileage' === $tax && 1 === $t ) {
							foreach ( $mileage_array as $mileage ) {
								$selected = '';
								if ( isset( $_GET['car_mileage'] ) && $_GET['car_mileage'] === $mileage ) { // phpcs:ignore WordPress.Security.NonceVerification
									$selected = "selected=''";
								}
								$attributs .= '<option value="' . esc_attr( $mileage ) . '" ' . esc_attr( $selected ) . '>&leq; ' . esc_html( $mileage ) . '</option>';
							}
							$t++;
						}
					}
				}
			}
			$attributs .= '</select>';
		}
		$attributs .= '<div class=""><a class="button" href="" id="reset_filters">' . esc_html__( 'Reset', 'cardealer' ) . '</a></div>';
		$attributs .= '</div>';
		$attributs .= '<span class="filter-loader"></span></div>';
		return $attributs; 
	}
	
	
	
add_filter( 'get_the_archive_title', function ($title) {

    if ( is_category() ) {

            //being a category your new title should go here
            $title = single_cat_title( '', false );

        } elseif ( is_tag() ) {

            $title = single_tag_title( '', false );

        } elseif ( is_author() ) {

            $title = '<span class="vcard">' . get_the_author() . '</span>' ;

        }

    return $title;

});


function tci_blog_page_title( $title ) {
	d($title);
	return $title;
}
// add_filter( 'document_title_parts', 'tci_blog_page_title', 999 );

function fix_hotspot_jquery_conflict() {
    // Only deregister hotspot's jQuery UI on pages that actually need it
    if ( is_singular( 'cars' ) || is_post_type_archive( 'cars' ) ) {
        wp_deregister_script('jquery-ui');
        wp_enqueue_script('jquery-ui-core');
    }
}
add_action('wp_enqueue_scripts', 'fix_hotspot_jquery_conflict', 100);

/* ==========================================================================
   Lighthouse / Performance / Accessibility Fixes
   ========================================================================== */

/**
 * 1. Accessibility: Add aria-labels to social profile links in the topbar.
 *    The parent theme outputs icon-only links with no accessible name.
 */
add_filter( 'cardealer_social_profiles', function( $social_content ) {
    // Map icon classes to human-readable names
    $icon_label_map = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'X (Twitter)',
        'linkedin'  => 'LinkedIn',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'pinterest' => 'Pinterest',
        'tiktok'    => 'TikTok',
    );

    // For each link, extract the href domain and add an aria-label
    $social_content = preg_replace_callback(
        '/<a\s+href="([^"]+)"\s+target="_blank">(<i[^>]*class="([^"]*)"[^>]*><\/i>)<\/a>/',
        function( $matches ) use ( $icon_label_map ) {
            $url        = $matches[1];
            $icon_html  = $matches[2];
            $classes    = $matches[3];
            $label      = 'Social profile'; // fallback

            foreach ( $icon_label_map as $key => $name ) {
                if ( stripos( $classes, $key ) !== false || stripos( $url, $key ) !== false ) {
                    $label = $name;
                    break;
                }
            }

            return '<a href="' . esc_url( $url ) . '" target="_blank" aria-label="' . esc_attr( $label ) . ' (opens in new tab)" rel="noopener">' . $icon_html . '</a>';
        },
        $social_content
    );

    return $social_content;
}, 20 );

/**
 * 2. Fix console error: "ReferenceError: d is not defined"
 *    The page has inline JS that references a debug function `d()` which
 *    only exists in PHP context. Define a JS no-op early in <head>.
 *    Note: Must not be deferred — needs to run before any inline script that calls d().
 */
add_action( 'wp_head', function() {
    // data-no-minify prevents WP Rocket from moving/deferring this critical inline fix
    echo '<script data-no-minify="1">if(typeof d==="undefined"){var d=function(){}}</script>' . "\n";
}, 1 );

/**
 * 3. Performance: CSS deferral handled by WP Rocket (minify_css + async_css).
 *    Instead, we dequeue stylesheets that are completely unnecessary on most pages.
 *    WP Rocket will minify/combine whatever remains.
 */
add_action( 'wp_enqueue_scripts', function() {
    // Instagram feed CSS — only needed on pages with the [instagram-feed] shortcode
    if ( ! is_page() || ! has_shortcode( get_post()->post_content ?? '', 'instagram-feed' ) ) {
        wp_dequeue_style( 'sbi_styles' );
    }

    // VC Extensions admin icon font — frontend doesn't need admin icons
    wp_dequeue_style( 'vc_extensions_cqbundle_adminicon' );
}, 100 );

/**
 * 4. Performance: Preconnect to critical third-party origins.
 *    WP Rocket handles same-origin preloading (preload_links: 1) but doesn't
 *    auto-preconnect to third-party origins used during initial page load.
 *    GTM is loaded synchronously in <head> via inline script, so preconnect helps.
 */
add_action( 'wp_head', function() {
    echo '<link rel="preconnect" href="https://www.googletagmanager.com" crossorigin>' . "\n";
}, 2 );

/**
 * 5. Performance: Add fetchpriority="high" to above-the-fold hero images.
 *    WP Rocket's lazyload (lazyload: 1) automatically skips images that have
 *    fetchpriority="high", so this also prevents the LCP image from being lazy-loaded.
 */
add_filter( 'wp_get_attachment_image_attributes', function( $attr, $attachment, $size ) {
    if ( is_page() && isset( $attr['class'] ) && strpos( $attr['class'], 'attachment-full' ) !== false ) {
        $attr['fetchpriority'] = 'high';
        // Explicitly prevent lazy loading on LCP candidates
        $attr['loading'] = 'eager';
    }
    return $attr;
}, 10, 3 );

/**
 * 6. Fix: wp-i18n script dependency issue.
 *    The wp-i18n inline script fires before the wp object is available.
 *    WP Rocket's defer_all_js preserves dependency order for registered scripts,
 *    but the inline "after" script (wp-i18n-js-after) still needs wp-hooks loaded first.
 */
add_action( 'wp_enqueue_scripts', function() {
    if ( wp_script_is( 'wp-i18n', 'enqueued' ) || wp_script_is( 'wp-i18n', 'registered' ) ) {
        global $wp_scripts;
        if ( isset( $wp_scripts->registered['wp-i18n'] ) ) {
            $deps = $wp_scripts->registered['wp-i18n']->deps;
            if ( ! in_array( 'wp-hooks', $deps, true ) ) {
                $wp_scripts->registered['wp-i18n']->deps[] = 'wp-hooks';
            }
        }
    }
}, 99 );

add_filter('widget_display_callback', function ($instance, $widget, $args) {
    if ($widget instanceof WP_Widget_Calendar) {
        return false;
    }
    return $instance;
}, 10, 3);

add_action('parse_request', function ($wp) {
    foreach (['m', 'monthnum', 'year'] as $key) {
        if (isset($wp->query_vars[$key]) && is_array($wp->query_vars[$key])) {
            $wp->query_vars[$key] = '';
        }
    }
}, 0);

/**
 * 7. Performance: Exclude critical above-the-fold scripts from WP Rocket Delay JS.
 *    When you enable delay_js in WP Rocket, these scripts should NOT be delayed
 *    because they control visible navigation/layout on first paint.
 */
add_filter( 'rocket_delay_js_exclusions', function( $exclusions ) {
    $exclusions[] = 'mega-menu'; // Mega Menu JS controls visible nav
    $exclusions[] = '/jquery(-migrate)?\.min\.js'; // jQuery core (many inline scripts depend on it)
    $exclusions[] = '/wp-includes\/js\/dist\/hooks(\.min)?\.js/'; // wp-hooks must load before wp-i18n inline
    $exclusions[] = '/wp-includes\/js\/dist\/i18n(\.min)?\.js/'; // wp-i18n depends on wp object from hooks
    return $exclusions;
} );

/**
 * 8. Performance: Exclude hero/LCP images from WP Rocket LazyLoad.
 *    Images above the fold should load immediately. WP Rocket respects
 *    data-no-lazy attribute and the fetchpriority="high" attribute (fix #5),
 *    but this adds belt-and-suspenders exclusion by CSS class.
 */
add_filter( 'rocket_lazyload_excluded_attributes', function( $attributes ) {
    $attributes[] = 'fetchpriority="high"';
    $attributes[] = 'class="custom-logo"';
    return $attributes;
} );

/**
 * 9. Accessibility: Remove maximum-scale=1 from viewport meta tag.
 *    The parent theme sets maximum-scale=1 which prevents pinch-to-zoom,
 *    failing WCAG 2.1 SC 1.4.4 (Resize Text). This filter corrects it.
 */
add_action( 'wp_head', function() {
    // Remove the parent theme's viewport tag and output a corrected one
    ob_start( function( $html ) {
        // Replace any viewport meta that restricts scaling
        $html = preg_replace(
            '/<meta\s+name=["\']viewport["\']\s+content=["\'][^"\']*maximum-scale\s*=\s*1[^"\']*["\']\s*\/?>/i',
            '<meta name="viewport" content="width=device-width, initial-scale=1">',
            $html
        );
        return $html;
    });
}, 0 );

add_action( 'wp_head', function() {
    if ( ob_get_level() ) {
        ob_end_flush();
    }
}, 999 );

/**
 * 10. Performance: Add explicit width/height to the site logo to prevent CLS.
 *    If the theme outputs a logo without dimensions, this ensures the browser
 *    can reserve space before the image loads.
 */
add_filter( 'get_custom_logo', function( $html ) {
    // Only add dimensions if they're missing
    if ( strpos( $html, 'width=' ) === false ) {
        $custom_logo_id = get_theme_mod( 'custom_logo' );
        if ( $custom_logo_id ) {
            $image = wp_get_attachment_image_src( $custom_logo_id, 'full' );
            if ( $image ) {
                $html = preg_replace(
                    '/<img /i',
                    '<img width="' . esc_attr( $image[1] ) . '" height="' . esc_attr( $image[2] ) . '" ',
                    $html,
                    1
                );
            }
        }
    }
    return $html;
}, 20 );

/**
 * 11. Performance: Remove preload for hidden slider images.
 *    web-slider-01-1.png (335KB) is loaded via the theme's slider markup
 *    but doesn't actually display on interior pages. It becomes the false
 *    LCP element because the browser preloads it at high priority.
 *    This filter removes it from WP Rocket's preload list.
 */
add_filter( 'rocket_preload_excluded_images', function( $excluded ) {
    $excluded[] = 'web-slider-01-1';
    return $excluded;
} );

/**
 * 12. Performance: Lazy-load hidden slider images on non-homepage pages.
 *    The slider background image is in the DOM but hidden via CSS on interior
 *    pages. Force it to lazy-load so it doesn't consume bandwidth or become LCP.
 */
add_filter( 'wp_content_img_tag', function( $filtered_image, $context, $attachment_id ) {
    if ( is_front_page() ) {
        return $filtered_image;
    }
    // If this image is the slider background that doesn't display, lazy-load it
    if ( strpos( $filtered_image, 'web-slider-01-1' ) !== false ) {
        $filtered_image = str_replace( 'loading="eager"', 'loading="lazy"', $filtered_image );
        $filtered_image = str_replace( 'fetchpriority="high"', '', $filtered_image );
        if ( strpos( $filtered_image, 'loading=' ) === false ) {
            $filtered_image = str_replace( '<img ', '<img loading="lazy" ', $filtered_image );
        }
    }
    return $filtered_image;
}, 20, 3 );

/**
 * 13. Performance: Remove preload link tags for slider images on interior pages.
 *    Catches any <link rel="preload"> that WP Rocket or the theme injects for
 *    the slider image that isn't visible on the current page.
 */
add_action( 'wp_head', function() {
    if ( is_front_page() ) {
        return;
    }
    // Use output buffering to strip preload links for the hidden slider image
    ob_start( function( $html ) {
        // Remove preload link for the slider image
        $html = preg_replace(
            '/<link[^>]*rel=["\']preload["\'][^>]*web-slider-01-1[^>]*\/?>/i',
            '',
            $html
        );
        return $html;
    });
}, 1 );

add_action( 'wp_head', function() {
    if ( is_front_page() ) {
        return;
    }
    if ( ob_get_level() ) {
        ob_end_flush();
    }
}, 998 );

/**
 * 14. CLS Fix: Correct the `sizes` attribute on leadership page images.
 *    The VC single image shortcode outputs sizes="(max-width: 1920px) 100vw, 1920px"
 *    which tells the browser the image fills the viewport width. In reality, these
 *    portraits display at ~360px in a vc_col-sm-4 column. The wrong sizes attribute
 *    causes the browser to download the largest srcset variant AND miscalculate
 *    the intrinsic size, contributing to CLS.
 */
add_filter( 'wp_content_img_tag', function( $filtered_image, $context, $attachment_id ) {
    // Only apply on the leadership page
    if ( ! is_page( 'leadership' ) ) {
        return $filtered_image;
    }

    // Target leadership portrait images (1920x2560 or 800x1000)
    if ( strpos( $filtered_image, 'width="1920"' ) !== false ||
         strpos( $filtered_image, 'width="800"' ) !== false ||
         strpos( $filtered_image, 'width="1536"' ) !== false ) {

        // Replace the overly broad sizes attribute with one that matches actual display
        // On desktop: ~360px in a 4-col layout. On mobile: full width up to 768px.
        $filtered_image = preg_replace(
            '/sizes="[^"]*"/',
            'sizes="(max-width: 768px) 100vw, 360px"',
            $filtered_image
        );
    }

    return $filtered_image;
}, 25, 3 );

/**
 * 15. Accessibility: Fix heading hierarchy on the leadership page.
 *    The page uses H1 for the page title then jumps to H5 for leadership quotes.
 *    This violates WCAG heading order. We filter the_content to replace H5 with H3
 *    on the leadership page only.
 */
add_filter( 'the_content', function( $content ) {
    if ( ! is_page( 'leadership' ) ) {
        return $content;
    }

    // Replace h5 tags with h3 for proper heading hierarchy (H1 > H2 > H3)
    $content = preg_replace( '/<h5(\s|>)/i', '<h3$1', $content );
    $content = preg_replace( '/<\/h5>/i', '</h3>', $content );

    return $content;
}, 20 );

/**
 * 16. Performance: Add Google Fonts preconnect with crossorigin for faster font loading.
 *    The leadership page loads Montserrat from fonts.gstatic.com. Preconnecting
 *    saves ~100ms on the font request by establishing the connection early.
 *    Note: Only outputs if not already present (theme/plugin may add it).
 */
add_action( 'wp_head', function() {
    // Check if preconnect is already being output by another source
    // The parent theme or WP Rocket cache may already include these
    static $fonts_preconnect_added = false;
    if ( $fonts_preconnect_added ) {
        return;
    }
    $fonts_preconnect_added = true;
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
}, 2 );

/**
 * 17. CLS Fix: Add font-display=swap to Google Fonts URL.
 *    If the theme or a plugin enqueues Google Fonts without &display=swap,
 *    this filter appends it to prevent FOIT (Flash of Invisible Text).
 */
add_filter( 'style_loader_src', function( $src ) {
    if ( strpos( $src, 'fonts.googleapis.com' ) !== false && strpos( $src, 'display=' ) === false ) {
        $src = add_query_arg( 'display', 'swap', $src );
    }
    return $src;
}, 10 );