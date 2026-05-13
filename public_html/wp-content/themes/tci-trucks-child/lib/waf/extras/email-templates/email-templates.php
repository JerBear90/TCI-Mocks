<?php
/* 
	Email Templating system (prefix "emt")
	Creates a custom post type used for creating email templates
*/

// Email sending functions
include 'send.php';
include 'defaults.php';
include 'email-options.php';
// Site specific config
include 'init.php';

// helpers
include 'helpers/index.php';
// Email All
include 'email-all.php';

// Email Log Admin Page
include 'email-log.php';

// Display emails as post type
function emt_template_redirect() {
	global $post;
	if( get_post_type( @$post->ID ) == 'email' ) {
		extract( emt_get_email_content( $post->ID ) ); ?>
		<html>
			<head><title><?php the_title(); ?></title></head>
			<body style="background:#ccc;display:flex;align-items:center;justify-content:center;">
				<div class="body-wrap" style="float:left;border:1px solid #ccc;background:#fff;">
					<?php echo $body; ?>
				</div>
			</body>
		</html>
		<?php
		die;
	}
}
add_action( 'template_redirect', 'emt_template_redirect' );

// Init (create post type & metabox)
function emt_init() {
	// Create Email Template Post TYpe
	$slug = 'email';
	$single = 'Email Template';
	$plural = 'Email Templates';
	
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
	$args = array(
		'labels' => $labels,
		'public' => true,
		'publicly_queryable' => true,
		'show_ui' => true,
		'query_var' => true,
		'rewrite' => true,
		'capability_type' => 'post',
		'hierarchical' => false,
		'menu_position' => 6,
		'supports' => array('title', 'editor' )
	);
	register_post_type( $slug, $args );
}
add_action( 'init', 'emt_init' );

// Get a list of EMT Actions
function emt_get_actions_list() {
	$options = [];
	$emt_actions = emt_get_actions();
	$options[] = '--';
	if( is_array( $emt_actions ) ) foreach( $emt_actions as $e=>$emt_action ) {
		$options[$e] = $emt_action['title'];
	}
	return $options;
}

// Get a list of variables
function emt_get_variables_list( $action ) {
	$emt_actions = emt_get_actions();
	$return = '';
	if( @is_array($emt_actions[$action]['variables']) ) foreach( $emt_actions[$action]['variables'] as $variable=>$label ) {
		$return .= '<strong>{'.$variable.'}</strong>: '.$label.'<br>';
	}

	$return = '<div class="value">'.@$return.'</div>';
	return $return;
}

// Get a list of html templates
function emt_get_html_template_list() {
	$paths = apply_filters( 'emt_paths', [dirname(__FILE__).'/html-templates/'] );
	foreach( $paths as $path ) {
		$files = scandir( $path );
		unset($files[0],$files[1]);
		foreach( $files as $file ) {
			$filepath = $path.'/'.$file;
			$results[$filepath] = $file;
		}
	}
	return $results;
}

// Email Actions javascript
function emt_admin_head() {
	?>
	<script type="text/javascript">
		jQuery(document).ready( function($) {
			var admin_ajax = '<?php bloginfo('url') ?>/wp-admin/admin-ajax.php';
			$('.emt_action select').change( function() {
				$('.emt_variables .value').html('').addClass('loading');
				var data = {
					action : 'update-emt-variables',
					emt_action : $(this).val()
				};
				d(data);
				$.post( admin_ajax, data, function(response) {
					d('response: ');
					d(response);
					$('.emt_variables .value').html(response).removeClass('loading');
				});
			});
			$('.emt_action select').change();
			$('#test .metaboxes-metabox input[type=submit]').click( function(e) {
				e.preventDefault();
				$('#test .metaboxes-metabox .messages').html('').addClass('loading');
				var data = {
					action : 'send-test-email',
					emt_action : $('.emt_action select').val(),
					to : $('#test .metaboxes-metabox .to input').val()
				};
				d(data);
				$.post( admin_ajax, data, function(response) {
					d('response: ');
					d(response);
					$('#test .metaboxes-metabox .messages').html(response).removeClass('loading');
				});
			});
			
		});
	</script>
	<?php
}
add_action( 'admin_head', 'emt_admin_head' );

function emt_ajax_update_emt_variables() {
	global $metaboxes;
	extract( $_POST );
	// Get list of email actions for metabox
	$emt_actions = emt_get_actions_list();

	// Setup metabox
	$empty = $metaboxes['email']['variables']['html'];
		// d('action',$emt_action);
	if( $emt_action ) {
		echo emt_get_variables_list( $emt_action );
	} else {
		echo $empty;
	}
	exit;
}
add_action( 'wp_ajax_update-emt-variables', 'emt_ajax_update_emt_variables' );

// Send a test email
function emt_ajax_send_test_email() {
	extract( $_REQUEST );
	$args = array(
		'to' => $to,
		'user_id' => get_current_user_id(),
		'action' => $emt_action
	);
	emt_send_action_email( $args );
	echo '<div class="notice">Test Complete</div>';
	exit;
}
add_action( 'wp_ajax_send-test-email', 'emt_ajax_send_test_email' );

// email columns
function email_columns($defaults) {
	global $post_ID;
	if( $_GET['post_type'] == 'email' ) {
		$options = array(
			'cb' => $defaults['cb'],
			'title' => $defaults['title'],
			'emt_action' => 'Used For',
			'emt_subject' => 'Email Subject',
			'emt_html_template' => 'HTML Template'
		);
	} else {
		$options = $defaults;
	}
    return $options;
}
add_filter( 'manage_email_posts_columns', 'email_columns', 9999 );

// List Providers for each event
function email_custom_column($column_name, $post_id) {
	// Action
	if( $_GET['post_type'] == 'email' && $column_name == 'emt_action' ) {
	
		$emt_action = get_post_meta( $post_id, 'emt_action', true );
		$emt_actions = emt_get_actions_list();
		echo @$emt_actions[$emt_action];
	}
	
	// Subject
	if( $_GET['post_type'] == 'email' && $column_name == 'emt_subject' ) {
		echo get_post_meta( $post_id, 'emt_subject', true );
	}
	
	// HTML Template
	if( $_GET['post_type'] == 'email' && $column_name == 'emt_html_template' ) {
		echo get_post_meta( $post_id, 'emt_html_template', true );
	}
}
add_action('manage_posts_custom_column', 'email_custom_column', 9999, 2);
