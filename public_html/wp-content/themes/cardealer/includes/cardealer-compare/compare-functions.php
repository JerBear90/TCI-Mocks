<?php
if ( ! function_exists( 'cardealer_campare_template_chooser' ) ) {
	/**
	 * Set template on search cars in cars campare page
	 *
	 * @param string $template .
	 */
	function cardealer_campare_template_chooser( $template ) {
		global $car_dealer_options;
		if ( ( isset( $car_dealer_options['vehicle_compare_template'] ) && '' !== $car_dealer_options['vehicle_compare_template'] && is_page( $car_dealer_options['vehicle_compare_template'] ) ) ) {
			return locate_template( 'template-parts/compare/page-compare.php' );  // redirect to campare page .
		}
		return $template;
	}
}
add_filter( 'template_include', 'cardealer_campare_template_chooser' );

if ( ! function_exists( 'cardealer_campare_type' ) ) {
	/**
	 * Set template on search cars in cars campare page
	 *
	 * @param string $template .
	 */
	function cardealer_campare_type( ) {
		global $car_dealer_options;
		if ( isset( $car_dealer_options['vehicle_compare_type'] ) ) {
			return $car_dealer_options['vehicle_compare_type'];  // redirect to campare page .
		}
		return;
	}
}

if ( ! function_exists( 'cardealer_campare_page_url' ) ) {
	/**
	 * Set template on search cars in cars campare page
	 */
	function cardealer_campare_page_url( ) {
		global $car_dealer_options;

		$campare_page_id = ( isset( $car_dealer_options['vehicle_compare_template'] ) && ! empty( $car_dealer_options['vehicle_compare_template'] ) ) ? $car_dealer_options['vehicle_compare_template'] : 0;
		$campare_page    = get_post( $campare_page_id );

		if ( ! empty( $campare_page_id ) && ( $campare_page && 'page' === $campare_page->post_type ) ) {
			return get_permalink( $car_dealer_options['vehicle_compare_template'] );  // redirect to campare page .
		}
		return null;
	}
}

if ( ! function_exists( 'cardealer_vehicle_compare_select_vehicles_post_per_page' ) ) {
	/**
	 * Get post count per page in select vehicle field.
	 */
	function cardealer_vehicle_compare_select_vehicles_post_per_page( ) {
		global $car_dealer_options;

		$ppp = 10;

		if ( isset( $car_dealer_options['vehicle_compare_post_per_page'] ) && ! empty( $car_dealer_options['vehicle_compare_post_per_page'] ) ) {
			$ppp = $car_dealer_options['vehicle_compare_post_per_page'];
		}

		$ppp = (int) apply_filters( 'cardealer_vehicle_compare_select_vehicles_post_per_page', $ppp );

		return $ppp;
	}
}

if ( ! function_exists( 'cardealer_campare_field' ) ) {
	/**
	 * Set template on search cars in cars campare page
	 *
	 * @param string $template .
	 */
	function cardealer_get_compare_attr_field() {
		global $car_dealer_options;
		return isset( $car_dealer_options['vehicle_compare_field'] ) ? $car_dealer_options['vehicle_compare_field']: null;
	}
}

if ( ! function_exists( 'cardealer_campare_page_model' ) ) {

	if(cardealer_campare_type() != 'template' ){
		add_action( 'wp_footer', 'cardealer_campare_page_model' );
	}
	/**
	 * Set template on search cars in cars campare page
	 *
	 * @param string $template .
	 */
	function cardealer_campare_page_model( ) {
		ob_start();
		get_template_part( 'template-parts/compare/compare-modal' );
		echo ob_get_clean();
	}
}

add_action( 'wp_ajax_cardealer_get_compare_cars', 'cardealer_get_compare_cars' );
add_action( 'wp_ajax_nopriv_cardealer_get_compare_cars', 'cardealer_get_compare_cars' );
if ( ! function_exists( 'cardealer_get_compare_cars' ) ) {
	/**
	 * Get compare cars based on submitted attribute and attribue value.
	 */
	function cardealer_get_compare_cars() {
		$cars            = array();
		$response        = array();
		$pagination_more = false;
		$max_pages       = 0;

		if ( isset( $_REQUEST['compare_nonce'] ) && wp_verify_nonce( sanitize_key( $_REQUEST['compare_nonce'] ), 'cardealer_get_compare_nonce' ) ) {

			$field_attr     = isset( $_REQUEST['field_attr'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['field_attr'] ) ) : '';
			$field_attr_val = isset( $_REQUEST['field_attr_val'] ) ? sanitize_text_field( wp_unslash( $_REQUEST['field_attr_val'] ) ) : '';
			$ppp            = isset( $_REQUEST['ppp'] ) && ! empty( $_REQUEST['ppp'] ) ? absint( $_REQUEST['ppp'] ) : 10;
			$page           = isset( $_REQUEST['page'] ) && ! empty( $_REQUEST['page'] ) ? absint( $_REQUEST['page'] ) : 1;
			$tax_query      = array();

			if ( 'car_mileage' === $field_attr ) {
				$tax_query[] = array(
					'taxonomy'         => $field_attr,
					'field'            => 'slug',
					'terms'            => $field_attr_val,
					'operator'         => '<=', // Compare operator for less than or equal to
					'type'             => 'NUMERIC',
					'include_children' => false, // Set to true if you want to include child terms
				);
			} else {
				$tax_query[] = array(
					'taxonomy' => $field_attr,
					'field'    => 'slug',
					'terms'    => $field_attr_val,
				);
			}

			$args = array(
				'post_type'      => 'cars',
				'posts_per_page' => $ppp,
				'paged'          => $page,
				'post_status'    => 'publish',
				'tax_query'      => $tax_query,
			);

			$query = new WP_Query( $args );

			if ( $query->have_posts() ) {
				while ( $query->have_posts() ) {
					$query->the_post();
					$cars[] = array(
						'id'    => get_the_ID(),
						'title' => get_the_title(),
					);
				}
				$pagination_more = $query->max_num_pages > $page;
				$max_pages = $query->max_num_pages;
			}

			wp_reset_postdata();

			$cars = array_map( function( $post ) {
				return array(
					'id'    => $post->ID,
					'title' => $post->post_title,
				);
			}, $query->posts );

			$response = array(
				'items'      => $cars,
				'max_pages'  => $max_pages,
				'pagination' => array(
					'more' => $pagination_more,
				)
			);
		}

		wp_send_json( $response );
		wp_die();
	}
}

/**
 * Disable the toolbar in iframe.
 *
 * @param bool $show Whether the admin bar should be shown.
 */
function cardealer_compare_hide_admin_bar( $show ) {
	$is_iframe = cardealer_is_iframe();
	if ( $is_iframe ) {
		$show = false;
	}
	return $show;
}
add_filter( 'show_admin_bar', 'cardealer_compare_hide_admin_bar' );

function cardealer_is_compare_showcase() {
	$compare_showcase = ( isset( $_GET['compare_showcase'] ) && ! empty( $_GET['compare_showcase'] ) ) ? sanitize_text_field( wp_unslash( $_GET['compare_showcase'] ) ) : false;
	$compare_showcase = filter_var( $compare_showcase, FILTER_VALIDATE_BOOLEAN );
	return $compare_showcase;
}

function cardealer_get_compare_ids() {
	$compare_showcase = cardealer_is_compare_showcase();
	$compare_ids      = ( isset( $_GET['car_ids'] ) && ! empty( $_GET['car_ids'] ) ) ? explode(',', sanitize_text_field( wp_unslash( $_GET['car_ids'] ) ) ) : array();

	if ( empty( $compare_ids ) && isset( $_COOKIE['compare_ids'] ) && ! $compare_showcase ) {
		$cookie_cars = sanitize_text_field( $_COOKIE['compare_ids'] );
		$compare_ids = json_decode( $cookie_cars );
	}

	$compare_ids = array_unique( array_filter( $compare_ids ) );

	return $compare_ids;
}

add_action( 'init', 'cardealer_create_pages' );
function cardealer_create_pages() {

	if ( wp_doing_ajax() ) {
		return;
	}

	$create_page_status = get_option( 'cardealer_create_pages_complete', false );

	if ( filter_var( $create_page_status, FILTER_VALIDATE_BOOLEAN ) ) {
		return;
	}

	$options        = get_option( 'car_dealer_options' );
	$new_options    = $options;
	$option_updated = false;

	$pages = apply_filters(
		'cardealer_create_pages',
		array(
			'compare' => array(
				'title'   => esc_html__( 'Compare', 'cardealer' ),
				'option'  => 'vehicle_compare_template',
				'content' => '',
			),
		)
	);

	foreach ( $pages as $slug => $page_data ) {
		$opt_key     = $page_data['option'];
		$create_page = false;

		if ( ! isset( $options[ $opt_key ] ) ) {
			$create_page = true;
		} elseif ( empty( $options[ $opt_key ] ) ) {
			$create_page = true;
		} else {
			$opt_val  = $options[ $opt_key ];
			$opt_post = get_post( $opt_val );
			if (
				$opt_post
				&& 'page' === $opt_post->post_type
				&& ! in_array( $opt_post->post_status, array( 'draft', 'pending', 'trash', 'future', 'auto-draft' ), true )
			) {
				$create_page = false;
			} else {
				$create_page = true;
			}
		}

		if ( $create_page ) {
			$page = get_page_by_path( $slug );
			if ( $page && 'page' === $page->post_type && ! in_array( $page->post_status, array( 'draft', 'pending', 'trash', 'future', 'auto-draft' ) ) ) {
				$new_options[ $opt_key ] = $page->ID;
				$option_updated          = true;
			} else {
				$new_post_data = array(
					'post_status'    => 'publish',
					'post_type'      => 'page',
					'post_author'    => 1,
					'post_name'      => $slug,
					'post_title'     => $page_data['title'],
					'post_content'   => $page_data['content'],
					'comment_status' => 'closed',
				);
				$page_id   = wp_insert_post( $new_post_data );
				if ( $page_id ) {
					$new_options[ $opt_key ] = $page_id;
					$option_updated = true;
				}
			}
		}
	}

	if ( $option_updated ) {
		update_option( 'car_dealer_options', $new_options );
	}
	update_option( 'cardealer_create_pages_complete', 1 );
}
