<?php
// Init
function ct_init() {
	// Get custom post types
	$ptypes = g('post_types');
	// create custom post types

	if( is_array($ptypes) ) foreach( $ptypes as $ptype ) {
		foreach( $ptype as $p=>$type ) if( is_string($type) ) $ptype[$p] = trim($type);
		extract( $ptype );
		if( trim($single) && trim($plural) ) add_custom_post( $single, $plural, $slug, $hierarchical );
		unset($single);
		unset($plural);
	}
	
	// Get custom taxonomies
	$taxonomies = g('taxonomies');
	// Create custom taxonomies
	if( is_array($taxonomies) ) foreach( $taxonomies as $taxonomy ) {
		extract( $taxonomy );
		if( trim($single) && trim($plural) ) add_custom_taxonomy( $single, $plural, $slug, $tax, $hierarchical );
		unset($single);
		unset($plural);
	}
}
add_action( 'init', 'ct_init', 2 );

// Options Page
function ct_options( $options ) {
	$options['custom_taxonomies'] = array(
		'title' => 'Custom Post Types and Taxonomies',
		'post_types' => array(
			'label' => 'Post Types',
			'type' => 'multi',
			'form' => array(
				'single' => array(
					'label' => 'Single label',
					'type' => 'text'
				),
				'plural' => array(
					'label' => 'Plural label',
					'type' => 'text'
				),
				'slug' => array(
					'label' => 'Post Slug (optional)',
					'type' => 'text'
				),
				'page' => array(
					'label' => 'Listing page',
					'type' => 'page'
				),
				'ppp' => array(
					'label' => 'Posts Per Page',
					'type' => 'text',
					'std' => get_option( 'posts_per_page' )
				),
				'hierarchical' => array(
					'label' => 'Hierarchical',
					'type' => 'checkbox'
				)
			)
		),
		'taxonomies' => array(
			'label' => 'Taxonomies',
			'type' => 'multi',
			'form' => array(
				'single' => array(
					'label' => 'Single label',
					'type' => 'text'
				),
				'plural' => array(
					'label' => 'Plural label',
					'type' => 'text'
				),
				'slug' => array(
					'label' => 'Post Slug',
					'type' => 'text'
				),
				'tax' => array(
					'label' => 'Taxonomy Slug',
					'type' => 'text'
				),
				'hierarchical' => array(
					'label' => 'Hierarchical',
					'type' => 'checkbox'
				),
				/*
				'page' => array(
					'label' => 'Listing page',
					'type' => 'page'
				)
				*/
			)
		)
	);
	return $options;
}
//add_filter( 'theme_options', 'ct_options' );

// Add custom posts quicker
if ( ! function_exists('add_custom_post') ) {
	function add_custom_post( $single, $plural, $slug='', $hierarchical=true, $rewrite=true, $has_archive=true ) {
		if ( ! $slug ) $slug = strtolower( str_replace(" ",'', $single ) );
		$labels = array(
			'name' => _x( $plural, 'taxonomy general name' ),
			'singular_name' => _x( $single, 'taxonomy singular name' ),
			'search_items' =>  __( 'Search '.$plural ),
			'popular_items' => __( 'Popular '.$plural ),
			'all_items' => __( 'All '.$plural ),
			'parent' => __( 'Parent '.$single ),
			'parent_item_colon' => __( 'Parent '.$single.':' ),
			'edit_item' => __( 'Edit '.$single ),
			'update_item' => __( 'Update '.$single ),
			'add_new_item' => __( 'Add New '.$single ),
			'new_item_name' => __( 'New '.$single.'Name' ),
		);
		$args = array(
			'labels' => $labels,
			'public' => true,
			'publicly_queryable' => true,
			'show_ui' => true,
			'query_var' => true,
			'rewrite' => $rewrite,
			'capability_type' => 'post',
			'hierarchical' => $hierarchical,
			'menu_position' => 6,
			'has_archive' => $has_archive,
			'supports' => array('title', 'thumbnail', 'editor', 'excerpt', 'author', 'comments', 'parent','page-attributes' ),
		);
		// d($args);
		if( is_string( $rewrite ) ) $args['rewrite'] = array( 'slug' => $rewrite );
		register_post_type( $slug, $args );
	}
}

// Add custom taxonomy quickly
if ( ! function_exists('add_custom_taxonomy') ) {
	function add_custom_taxonomy( $single, $plural, $slug='post', $taxonomy='', $hierarchical=true) {
		$labels = array(
		'name' => _x( $plural, 'taxonomy general name' ),
		'singular_name' => _x( $single, 'taxonomy singular name' ),
		'search_items' =>  __( 'Search '.$plural ),
		'popular_items' => __( 'Popular '.$plural ),
		'all_items' => __( 'All '.$plural ),
		'parent_item' => __( 'Parent '.$single ),
		'parent_item_colon' => __( 'Parent Recording:' ),
		'edit_item' => __( 'Edit '.$single ),
		'update_item' => __( 'Update '.$single ),
		'add_new_item' => __( 'Add New '.$single ),
		'new_item_name' => __( 'New Recording Name' ),
	);
	
	$taxonomy = $taxonomy ? $taxonomy : str_replace( ' ','',strtolower($single) );
		register_taxonomy(
			$taxonomy,
			$slug,
			array(
				'hierarchical' => $hierarchical,
				'query_var' => true,
				'rewrite' => true,
				'labels' => $labels
			)
		);
	}
}

// Shortcode
function custom_posts_list( $atts='' ) {
	// default args
	$defaults = array(
		'posts_per_page' => get_option('posts_per_page'),
		'post_status' => 'publish',
		'post_type' => '',
		'echo' => false,
	);
	// Build args array
	$args = shortcode_atts( $defaults, $atts );
	extract( $args);
	
	// Only add list if post type is set
	if( $args['post_type'] ) {
		// Sort by menu order if hierarchical
		if( is_post_type_hierarchical( $post_type ) ) {
			$args['orderby'] = 'menu_order';
			$args['order'] = 'ASC';
		}

		// query posts
		query_posts($args);

		// use output buffering if echo is false
		if( !$echo ) ob_start();

		// check for listing template for post type, or general listing template, or plugin include file
		if( locate_template( array( 'listing.php', 'listing-'.$post_type.'.php' ) , false ) ) {
			get_template_part('listing', $post_type );
		} else {
			include 'listing.php';
		}
		
		// reset qurest
		wp_reset_query();

		// return output buffer if echo is false
		if( !$echo ) {
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
}
add_shortcode( 'custom_posts', 'custom_posts_list' );
add_shortcode( 'post_list', 'custom_posts_list' );

// CSS
function ct_admin_css() { ?>
	.post_types input {
		width: 33%;
	}
	<?php
}
add_action( 'admin_css', 'ct_admin_css' );

// Page redirect
function ct_page_content( $content ) {
	if( strpos( $content, '[custom_posts' ) || strpos( $content, '[custom_posts' ) === 0 ) return $content;
	global $post;
	
	// get post types
	$ptypes = g('post_types');

	// check to see if we're on a post type page
	if( is_array($ptypes) ) foreach( $ptypes as $ptype ) {
		// get post type page and current
		extract($ptype);
		$current = $post->ID;
		if( !trim($slug) ) $slug = strtolower($single);
		// get posts listing if it matches
		if( $page ) if( $page == $current ) {
			$content .= custom_posts_list( array( 'post_type' => $slug, 'posts_per_page' => $ppp ) );
		}
	}
	return $content;
}
add_filter( 'the_content', 'ct_page_content' );
?>
